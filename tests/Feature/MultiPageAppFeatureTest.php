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
            ->assertSee('Sign In to Workspace')
            ->assertSee('admin@example.com');
    }

    public function test_guest_can_view_register_page(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200)
            ->assertSee('Create Account')
            ->assertSee('Assign Role');
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

    public function test_user_can_register_and_is_assigned_role(): void
    {
        $response = $this->post('/register', [
            'name' => 'Alice Employee',
            'email' => 'alice@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'employee',
        ]);

        $response->assertRedirect('/employee/dashboard');

        $user = User::where('email', 'alice@example.com')->first();
        $this->assertNotNull($user);
        $this->assertTrue($user->hasRole('employee'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_employee_cannot_access_admin_dashboard(): void
    {
        $employee = User::factory()->create();
        $employee->assignRole('employee');

        $response = $this->actingAs($employee)->get('/admin/dashboard');

        $response->assertStatus(403);
    }

    public function test_admin_can_access_admin_dashboard(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $response->assertStatus(200)
            ->assertSee('Executive Admin Dashboard');
    }

    public function test_manager_can_access_manager_dashboard(): void
    {
        $manager = User::factory()->create();
        $manager->assignRole('manager');

        $response = $this->actingAs($manager)->get('/manager/dashboard');

        $response->assertStatus(200)
            ->assertSee('Department &amp; Team Dashboard', false);
    }

    public function test_employee_can_access_employee_dashboard(): void
    {
        $employee = User::factory()->create();
        $employee->assignRole('employee');

        $response = $this->actingAs($employee)->get('/employee/dashboard');

        $response->assertStatus(200)
            ->assertSee('Employee Focus Board');
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

    public function test_admin_can_create_task_via_web(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $assignee = User::factory()->create();
        $assignee->assignRole('employee');

        $response = $this->actingAs($admin)->post('/admin/tasks', [
            'title' => 'Web Admin Created Task',
            'description' => 'Created via web interface form',
            'priority' => 'high',
            'status' => 'pending',
            'due_date' => now()->addDays(3)->format('Y-m-d'),
            'assigned_to' => $assignee->id,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('tasks', [
            'title' => 'Web Admin Created Task',
            'created_by' => $admin->id,
            'assigned_to' => $assignee->id,
        ]);
    }

    public function test_authenticated_user_can_logout_via_web(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $response->assertRedirect('/login');
        $this->assertGuest();
    }
}
