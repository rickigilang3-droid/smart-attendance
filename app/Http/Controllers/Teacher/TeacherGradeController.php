<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeacherGradeController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $teacher = Teacher::where('user_id', $user->id)->first();
        abort_unless($teacher, 403);

        $classIds = $teacher->assignedClassIds();
        $classes = SchoolClass::whereIn('id', $classIds)->get();
        $selectedClassId = $request->get('class_id', $classes->first()->id ?? null);
        $subjectName = $request->get('subject_name', 'Matematika');
        $semester = $request->get('semester', 'Ganjil');

        $students = collect();
        $assessments = collect();

        if ($selectedClassId) {
            abort_unless($classIds->contains((int) $selectedClassId), 403);

            $students = Student::where('class_id', $selectedClassId)->orderBy('name', 'asc')->get();
            $assessments = Assessment::where('class_id', $selectedClassId)
                ->where('teacher_id', $teacher->id)
                ->where('subject_name', $subjectName)
                ->where('semester', $semester)
                ->get()
                ->keyBy('student_id');
        }

        return view('teacher.grades.index', compact(
            'classes',
            'selectedClassId',
            'subjectName',
            'semester',
            'students',
            'assessments'
        ));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $teacher = Teacher::where('user_id', $user->id)->firstOrFail();

        $validated = $request->validate([
            'class_id' => 'required|integer|exists:classes,id',
            'subject_name' => 'required|string|max:255',
            'semester' => 'required|in:Ganjil,Genap',
            'action' => 'sometimes|in:draft,submit',
            'grades' => 'required|array',
            'grades.*.assignment_score' => 'sometimes|numeric|min:0|max:100',
            'grades.*.quiz_score' => 'sometimes|numeric|min:0|max:100',
            'grades.*.mid_score' => 'sometimes|numeric|min:0|max:100',
            'grades.*.final_score' => 'sometimes|numeric|min:0|max:100',
            'grades.*.teacher_notes' => 'nullable|string|max:1000',
        ]);

        $classId = (int) $validated['class_id'];
        abort_unless($teacher->assignedClassIds()->contains($classId), 403);

        $studentIds = array_keys($validated['grades']);
        $normalizedStudentIds = array_map(
            static fn ($studentId): string => (string) (int) $studentId,
            $studentIds
        );
        $submittedStudentIds = array_map('strval', $studentIds);
        abort_unless(
            $normalizedStudentIds === $submittedStudentIds
                && count($normalizedStudentIds) === count(array_unique($normalizedStudentIds)),
            403
        );
        $studentIds = array_map('intval', $studentIds);
        $studentsInClass = Student::where('class_id', $classId)
            ->whereIn('id', $studentIds)
            ->count();
        abort_unless($studentsInClass === count($studentIds), 403);

        $status = ($validated['action'] ?? 'draft') === 'submit' ? 'submitted' : 'draft';

        foreach ($validated['grades'] as $studentId => $scoreData) {
            $assignment = (float) ($scoreData['assignment_score'] ?? 0);
            $quiz = (float) ($scoreData['quiz_score'] ?? 0);
            $mid = (float) ($scoreData['mid_score'] ?? 0);
            $final = (float) ($scoreData['final_score'] ?? 0);

            // Rumus Kalkulasi Nilai Akhir (20% Tugas, 20% Formatif, 30% UTS, 30% UAS)
            $finalGrade = ($assignment * 0.20) + ($quiz * 0.20) + ($mid * 0.30) + ($final * 0.30);

            // Menentukan Predikat
            $letterGrade = 'D';
            if ($finalGrade >= 88) {
                $letterGrade = 'A';
            } elseif ($finalGrade >= 78) {
                $letterGrade = 'B';
            } elseif ($finalGrade >= 68) {
                $letterGrade = 'C';
            }

            Assessment::updateOrCreate(
                [
                    'teacher_id' => $teacher->id,
                    'student_id' => $studentId,
                    'class_id' => $classId,
                    'subject_name' => $validated['subject_name'],
                    'semester' => $validated['semester'],
                ],
                [
                    'academic_year' => '2025/2026',
                    'assignment_score' => $assignment,
                    'quiz_score' => $quiz,
                    'mid_score' => $mid,
                    'final_score' => $final,
                    'final_grade' => $finalGrade,
                    'letter_grade' => $letterGrade,
                    'teacher_notes' => $scoreData['teacher_notes'] ?? null,
                    'status' => $status,
                ]
            );
        }

        $msg = $status === 'submitted'
            ? 'Nilai berhasil disimpan dan dikirim ke Portal Admin!'
            : 'Nilai berhasil disimpan sebagai Draft.';

        return redirect()->back()->with('success', $msg);
    }
}
