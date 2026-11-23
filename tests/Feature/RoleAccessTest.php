<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    public static function dashboardAccessCases(): array
    {
        return [
            'admin can access admin dashboard' => ['/admin/dashboard', 'Admin', 'ok'],
            'admin retains cross-dashboard access' => ['/teacher/dashboard', 'Admin', 'ok'],
            'teacher can access teacher dashboard' => ['/teacher/dashboard', 'Teacher', 'ok'],
            'teacher alias can access teacher dashboard' => ['/teacher/dashboard', 'Guru', 'ok'],
            'principal can access principal dashboard' => ['/principal/dashboard', 'Principal', 'ok'],
            'principal alias can access principal dashboard' => ['/principal/dashboard', 'Kepsek', 'ok'],
            'parent can access parent dashboard' => ['/parent/dashboard', 'Parent', 'ok'],
            'parent alias can access parent dashboard' => ['/parent/dashboard', 'Ortu', 'ok'],
            'teacher is forbidden from admin dashboard' => ['/admin/dashboard', 'Teacher', 'forbidden'],
            'principal is forbidden from admin dashboard' => ['/admin/dashboard', 'Principal', 'forbidden'],
            'parent is forbidden from admin dashboard' => ['/admin/dashboard', 'Parent', 'forbidden'],
            'principal is forbidden from teacher dashboard' => ['/teacher/dashboard', 'Principal', 'forbidden'],
            'parent is forbidden from teacher dashboard' => ['/teacher/dashboard', 'Parent', 'forbidden'],
            'teacher is forbidden from principal dashboard' => ['/principal/dashboard', 'Teacher', 'forbidden'],
            'parent is forbidden from principal dashboard' => ['/principal/dashboard', 'Parent', 'forbidden'],
            'teacher is forbidden from parent dashboard' => ['/parent/dashboard', 'Teacher', 'forbidden'],
            'principal is forbidden from parent dashboard' => ['/parent/dashboard', 'Principal', 'forbidden'],
        ];
    }

    #[DataProvider('dashboardAccessCases')]
    public function test_dashboard_routes_enforce_role_access(string $path, string $roleName, string $expectedAccess): void
    {
        $role = Role::query()->firstOrCreate(['name' => $roleName]);
        $user = User::factory()->create(['role_id' => $role->id]);

        $response = $this->actingAs($user)->get($path);

        if ($expectedAccess === 'ok') {
            $response->assertOk();

            return;
        }

        $response->assertForbidden();
    }
}
