<?php

namespace Tests\Feature;

use App\Models\LessonSchedule;
use App\Models\Permission;
use App\Models\Role;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TeacherDataIsolationTest extends TestCase
{
    use RefreshDatabase;

    public function test_teacher_students_page_only_lists_students_in_assigned_classes(): void
    {
        $teacher = $this->createTeacher();
        $assignedClass = $this->createClass('Assigned class');
        $otherClass = $this->createClass('Other class');
        $this->assignTeacherToClass($teacher, $assignedClass);
        $assignedStudent = $this->createStudent($assignedClass, 'Assigned student');
        $otherStudent = $this->createStudent($otherClass, 'Other student');

        $response = $this->actingAs($teacher->user)->get('/teacher/students');

        $response->assertOk()
            ->assertSee($assignedStudent->name)
            ->assertDontSee($otherStudent->name);
    }

    public function test_teacher_cannot_open_attendance_for_an_unassigned_class(): void
    {
        $teacher = $this->createTeacher();
        $assignedClass = $this->createClass('Assigned class');
        $otherClass = $this->createClass('Other class');
        $this->assignTeacherToClass($teacher, $assignedClass);

        $this->actingAs($teacher->user)
            ->get('/teacher/attendance?class_id='.$otherClass->id)
            ->assertForbidden();
    }

    public function test_teacher_can_access_their_homeroom_class_without_a_lesson_schedule(): void
    {
        $teacher = $this->createTeacher();
        $class = $this->createClass('Homeroom class');
        $class->update(['teacher_id' => $teacher->id]);
        $student = $this->createStudent($class, 'Homeroom student');

        $response = $this->actingAs($teacher->user)
            ->get('/teacher/attendance?class_id='.$class->id);

        $response->assertOk()->assertSee($student->name);
    }

    public function test_teacher_cannot_save_attendance_for_an_unassigned_class(): void
    {
        $teacher = $this->createTeacher();
        $otherClass = $this->createClass('Other class');
        $student = $this->createStudent($otherClass, 'Other student');

        $response = $this->actingAs($teacher->user)->post('/teacher/attendance', [
            'school_class_id' => $otherClass->id,
            'date' => '2026-06-01',
            'attendances' => [$student->id => 'Hadir'],
        ]);

        $response->assertForbidden();
        $this->assertDatabaseCount('class_attendances', 0);
    }

    public function test_teacher_can_save_attendance_for_a_student_in_an_assigned_class(): void
    {
        $teacher = $this->createTeacher();
        $class = $this->createClass('Assigned class');
        $this->assignTeacherToClass($teacher, $class);
        $student = $this->createStudent($class, 'Assigned student');

        $response = $this->actingAs($teacher->user)->post('/teacher/attendance', [
            'school_class_id' => $class->id,
            'date' => '2026-06-01',
            'attendances' => [$student->id => 'Hadir'],
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('class_attendances', [
            'teacher_id' => $teacher->id,
            'class_id' => $class->id,
            'student_id' => $student->id,
            'status' => 'Hadir',
        ]);
    }

    public function test_teacher_cannot_save_attendance_with_a_malformed_student_id_key(): void
    {
        $teacher = $this->createTeacher();
        $class = $this->createClass('Assigned class');
        $this->assignTeacherToClass($teacher, $class);
        $student = $this->createStudent($class, 'Assigned student');

        $response = $this->actingAs($teacher->user)->post('/teacher/attendance', [
            'school_class_id' => $class->id,
            'date' => '2026-06-01',
            'attendances' => [$student->id.'x' => 'Hadir'],
        ]);

        $response->assertForbidden();
        $this->assertDatabaseCount('class_attendances', 0);
    }

    public function test_teacher_cannot_save_grades_for_a_student_outside_the_selected_class(): void
    {
        $teacher = $this->createTeacher();
        $assignedClass = $this->createClass('Assigned class');
        $otherClass = $this->createClass('Other class');
        $this->assignTeacherToClass($teacher, $assignedClass);
        $otherStudent = $this->createStudent($otherClass, 'Other student');

        $response = $this->actingAs($teacher->user)->post('/teacher/grades', [
            'class_id' => $assignedClass->id,
            'subject_name' => 'Matematika',
            'semester' => 'Ganjil',
            'action' => 'draft',
            'grades' => [
                $otherStudent->id => [
                    'assignment_score' => 80,
                    'quiz_score' => 80,
                    'mid_score' => 80,
                    'final_score' => 80,
                ],
            ],
        ]);

        $response->assertForbidden();
        $this->assertDatabaseCount('assessments', 0);
    }

    public function test_teacher_can_save_valid_grades_for_an_assigned_student(): void
    {
        $teacher = $this->createTeacher();
        $class = $this->createClass('Assigned class');
        $this->assignTeacherToClass($teacher, $class);
        $student = $this->createStudent($class, 'Assigned student');

        $response = $this->actingAs($teacher->user)->post('/teacher/grades', [
            'class_id' => $class->id,
            'subject_name' => 'Matematika',
            'semester' => 'Ganjil',
            'action' => 'draft',
            'grades' => [
                $student->id => [
                    'assignment_score' => 80,
                    'quiz_score' => 90,
                    'mid_score' => 70,
                    'final_score' => 100,
                ],
            ],
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('assessments', [
            'teacher_id' => $teacher->id,
            'student_id' => $student->id,
            'class_id' => $class->id,
            'subject_name' => 'Matematika',
            'semester' => 'Ganjil',
            'status' => 'draft',
        ]);
    }

    public function test_teacher_cannot_save_grades_with_a_malformed_student_id_key(): void
    {
        $teacher = $this->createTeacher();
        $class = $this->createClass('Assigned class');
        $this->assignTeacherToClass($teacher, $class);
        $student = $this->createStudent($class, 'Assigned student');

        $response = $this->actingAs($teacher->user)->post('/teacher/grades', [
            'class_id' => $class->id,
            'subject_name' => 'Matematika',
            'semester' => 'Ganjil',
            'action' => 'draft',
            'grades' => [
                $student->id.'x' => [
                    'assignment_score' => 80,
                    'quiz_score' => 80,
                    'mid_score' => 80,
                    'final_score' => 80,
                ],
            ],
        ]);

        $response->assertForbidden();
        $this->assertDatabaseCount('assessments', 0);
    }

    public function test_teacher_cannot_verify_permission_for_a_student_in_an_unassigned_class(): void
    {
        $teacher = $this->createTeacher();
        $assignedClass = $this->createClass('Assigned class');
        $otherClass = $this->createClass('Other class');
        $this->assignTeacherToClass($teacher, $assignedClass);
        $student = $this->createStudent($otherClass, 'Other student');
        $parent = User::factory()->create([
            'role_id' => Role::query()->firstOrCreate(['name' => 'Parent'])->id,
        ]);
        $permission = Permission::query()->create([
            'student_id' => $student->id,
            'parent_user_id' => $parent->id,
            'type' => 'Izin',
            'start_date' => '2026-06-01',
            'end_date' => '2026-06-01',
            'reason' => 'Family matter',
            'letter_file' => 'permission_letters/letter.pdf',
            'status' => 'Pending',
        ]);

        $response = $this->actingAs($teacher->user)
            ->post('/teacher/permission/'.$permission->id.'/verify', ['status' => 'Rejected']);

        $response->assertForbidden();
        $this->assertDatabaseHas('permissions', [
            'id' => $permission->id,
            'status' => 'Pending',
        ]);
    }

    public function test_teacher_cannot_view_schedules_without_a_teacher_profile(): void
    {
        $teacherUser = User::factory()->create([
            'role_id' => Role::query()->firstOrCreate(['name' => 'Teacher'])->id,
        ]);
        $teacher = $this->createTeacher();
        $class = $this->createClass('Private class');
        $this->assignTeacherToClass($teacher, $class);

        $this->actingAs($teacherUser)
            ->get('/teacher/schedules')
            ->assertForbidden();
    }

    public function test_parent_cannot_submit_permission_for_another_parents_student(): void
    {
        $parent = User::factory()->create([
            'name' => 'Orang Tua Budi',
            'role_id' => Role::query()->firstOrCreate(['name' => 'Parent'])->id,
        ]);
        $class = $this->createClass('Class 1');
        $this->createStudent($class, 'Budi child', ['father_name' => 'Budi']);
        $otherStudent = $this->createStudent($class, 'Other child', ['father_name' => 'Other']);

        $response = $this->actingAs($parent)->post('/parent/permission/store', [
            'student_id' => $otherStudent->id,
        ]);

        $response->assertForbidden();
        $this->assertDatabaseCount('permissions', 0);
    }

    public function test_parent_can_submit_permission_for_their_linked_student(): void
    {
        Storage::fake('public');
        $parent = User::factory()->create([
            'name' => 'Orang Tua Budi',
            'role_id' => Role::query()->firstOrCreate(['name' => 'Parent'])->id,
        ]);
        $class = $this->createClass('Class 1');
        $student = $this->createStudent($class, 'Budi child', ['father_name' => 'Budi']);

        $response = $this->actingAs($parent)->post('/parent/permission/store', [
            'student_id' => $student->id,
            'type' => 'Izin',
            'start_date' => '2026-06-01',
            'end_date' => '2026-06-01',
            'reason' => 'Family matter',
            'letter_file' => UploadedFile::fake()->createWithContent('letter.pdf', "%PDF-1.4\n"),
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('permissions', [
            'student_id' => $student->id,
            'parent_user_id' => $parent->id,
            'type' => 'Izin',
            'status' => 'Pending',
        ]);

        $permission = Permission::query()->firstOrFail();
        Storage::disk('public')->assertExists($permission->letter_file);
    }

    private function createTeacher(): Teacher
    {
        $user = User::factory()->create([
            'role_id' => Role::query()->firstOrCreate(['name' => 'Teacher'])->id,
        ]);

        return Teacher::query()->create([
            'user_id' => $user->id,
            'nip' => 'NIP-001',
            'name' => 'Test Teacher',
            'subject' => 'Matematika',
        ]);
    }

    private function createClass(string $name): SchoolClass
    {
        return SchoolClass::query()->create([
            'name' => $name,
            'grade_level' => '10',
        ]);
    }

    private function createStudent(SchoolClass $class, string $name, array $attributes = []): Student
    {
        return Student::query()->create(array_merge([
            'class_id' => $class->id,
            'name' => $name,
            'gender' => 'L',
        ], $attributes));
    }

    private function assignTeacherToClass(Teacher $teacher, SchoolClass $class): void
    {
        LessonSchedule::query()->create([
            'class_id' => $class->id,
            'teacher_id' => $teacher->id,
            'subject' => 'Matematika',
            'day' => 'Senin',
            'start_time' => '08:00:00',
            'end_time' => '09:00:00',
        ]);
    }
}
