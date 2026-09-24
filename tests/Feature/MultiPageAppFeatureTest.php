<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class MultiPageAppFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    public function test_guest_can_view_login_page(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200)
            ->assertSee('Sign In');
    }

    public function test_guest_can_view_register_page_with_admin_initialization_prompt_when_no_admin_exists(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200)
            ->assertSee('Platform Setup')
            ->assertSee('Initialize Administrator');
    }

    public function test_first_user_who_registers_becomes_the_single_admin(): void
    {
        $response = $this->post('/register', [
            'name' => 'Primary Administrator',
            'email' => 'admin@company.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/admin/dashboard');

        $user = User::where('email', 'admin@company.com')->first();
        $this->assertNotNull($user);
        $this->assertTrue($user->hasRole('admin'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_subsequent_user_who_registers_becomes_employee(): void
    {
        // First user registers as admin
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        // Second user registers
        $response = $this->post('/register', [
            'name' => 'Regular Employee',
            'email' => 'employee@company.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/employee/dashboard');

        $user = User::where('email', 'employee@company.com')->first();
        $this->assertNotNull($user);
        $this->assertTrue($user->hasRole('employee'));
        $this->assertFalse($user->hasRole('admin'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_admin_can_login_and_redirects_to_admin_dashboard(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'),
        ]);
        $admin->assignRole('admin');

        $response = $this->post('/login', [
            'email' => 'admin@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/admin/dashboard');
        $this->assertAuthenticatedAs($admin);
    }

    public function test_manager_can_login_and_redirects_to_manager_dashboard(): void
    {
        $manager = User::factory()->create([
            'email' => 'manager@example.com',
            'password' => Hash::make('password123'),
        ]);
        $manager->assignRole('manager');

        $response = $this->post('/login', [
            'email' => 'manager@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/manager/dashboard');
        $this->assertAuthenticatedAs($manager);
    }

    public function test_employee_can_login_and_redirects_to_employee_dashboard(): void
    {
        $employee = User::factory()->create([
            'email' => 'employee@example.com',
            'password' => Hash::make('password123'),
        ]);
        $employee->assignRole('employee');

        $response = $this->post('/login', [
            'email' => 'employee@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/employee/dashboard');
        $this->assertAuthenticatedAs($employee);
    }

    public function test_admin_can_create_a_manager_directly(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->post('/admin/users', [
            'name' => 'Department Manager',
            'email' => 'dept.manager@example.com',
            'password' => 'password123',
            'role' => 'manager',
        ]);

        $response->assertRedirect();
        $manager = User::where('email', 'dept.manager@example.com')->first();
        $this->assertNotNull($manager);
        $this->assertTrue($manager->hasRole('manager'));
    }

    public function test_admin_can_promote_an_employee_to_manager(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $employee = User::factory()->create();
        $employee->assignRole('employee');

        $response = $this->actingAs($admin)->post("/admin/users/{$employee->id}/role", [
            'role' => 'manager',
        ]);

        $response->assertRedirect();
        $employee->refresh();
        $this->assertTrue($employee->hasRole('manager'));
        $this->assertFalse($employee->hasRole('employee'));

        // Verify the promoted employee now has access to the Manager Dashboard
        $managerResponse = $this->actingAs($employee)->get('/manager/dashboard');
        $managerResponse->assertStatus(200);
    }

    public function test_admin_can_demote_a_manager_to_employee(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $manager = User::factory()->create();
        $manager->assignRole('manager');

        $response = $this->actingAs($admin)->post("/admin/users/{$manager->id}/role", [
            'role' => 'employee',
        ]);

        $response->assertRedirect();
        $manager->refresh();
        $this->assertTrue($manager->hasRole('employee'));
        $this->assertFalse($manager->hasRole('manager'));
    }

    public function test_admin_cannot_be_demoted(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->post("/admin/users/{$admin->id}/role", [
            'role' => 'employee',
        ]);

        $response->assertSessionHasErrors(['role']);
        $admin->refresh();
        $this->assertTrue($admin->hasRole('admin'));
    }

    public function test_employee_cannot_access_admin_dashboard(): void
    {
        $employee = User::factory()->create();
        $employee->assignRole('employee');

        $response = $this->actingAs($employee)->get('/admin/dashboard');

        $response->assertStatus(403);
    }

    public function test_employee_can_update_status_of_assigned_task(): void
    {
        $employee = User::factory()->create();
        $employee->assignRole('employee');

        $task = Task::factory()->create([
            'assigned_to' => $employee->id,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($employee)->patch("/employee/tasks/{$task->id}/status", [
            'status' => 'in-progress',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'status' => 'in-progress',
        ]);
    }

    public function test_employee_cannot_update_status_of_unassigned_task(): void
    {
        $employee1 = User::factory()->create();
        $employee1->assignRole('employee');

        $employee2 = User::factory()->create();
        $employee2->assignRole('employee');

        $task = Task::factory()->create([
            'assigned_to' => $employee2->id,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($employee1)->patch("/employee/tasks/{$task->id}/status", [
            'status' => 'completed',
        ]);

        $response->assertStatus(403);
    }

    public function test_authenticated_user_can_logout_via_web(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $response->assertRedirect('/login');
        $this->assertGuest();
    }
}
