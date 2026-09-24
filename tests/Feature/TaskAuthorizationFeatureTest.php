<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskAuthorizationFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $manager;

    protected User $employee1;

    protected User $employee2;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');

        $this->manager = User::factory()->create();
        $this->manager->assignRole('manager');

        $this->employee1 = User::factory()->create();
        $this->employee1->assignRole('employee');

        $this->employee2 = User::factory()->create();
        $this->employee2->assignRole('employee');
    }

    public function test_employee_cannot_create_task(): void
    {
        $response = $this->actingAs($this->employee1, 'sanctum')
            ->postJson('/api/v1/tasks', [
                'title' => 'Unauthorized Task',
                'priority' => 'low',
                'assigned_to' => $this->employee1->id,
                'due_date' => now()->addDays(5)->format('Y-m-d'),
            ]);

        $response->assertStatus(403);
    }

    public function test_employee_cannot_delete_task(): void
    {
        $task = Task::factory()->create([
            'assigned_to' => $this->employee1->id,
            'created_by' => $this->manager->id,
        ]);

        $response = $this->actingAs($this->employee1, 'sanctum')
            ->deleteJson("/api/v1/tasks/{$task->id}");

        $response->assertStatus(403);
    }

    public function test_employee_cannot_view_task_assigned_to_another_user(): void
    {
        $task = Task::factory()->create([
            'assigned_to' => $this->employee2->id,
            'created_by' => $this->manager->id,
        ]);

        $response = $this->actingAs($this->employee1, 'sanctum')
            ->getJson("/api/v1/tasks/{$task->id}");

        $response->assertStatus(403);
    }

    public function test_employee_can_view_task_assigned_to_them(): void
    {
        $task = Task::factory()->create([
            'assigned_to' => $this->employee1->id,
            'created_by' => $this->manager->id,
        ]);

        $response = $this->actingAs($this->employee1, 'sanctum')
            ->getJson("/api/v1/tasks/{$task->id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $task->id);
    }

    public function test_employee_can_update_status_of_assigned_task(): void
    {
        $task = Task::factory()->create([
            'assigned_to' => $this->employee1->id,
            'created_by' => $this->manager->id,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->employee1, 'sanctum')
            ->putJson("/api/v1/tasks/{$task->id}", [
                'status' => 'in-progress',
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.status', 'in-progress');

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'status' => 'in-progress',
        ]);
    }

    public function test_employee_cannot_update_prohibited_fields_on_assigned_task(): void
    {
        $task = Task::factory()->create([
            'title' => 'Original Title',
            'assigned_to' => $this->employee1->id,
            'created_by' => $this->manager->id,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->employee1, 'sanctum')
            ->putJson("/api/v1/tasks/{$task->id}", [
                'title' => 'Hacked Title',
                'status' => 'in-progress',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title']);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Original Title',
        ]);
    }

    public function test_employee_index_only_returns_tasks_assigned_to_them(): void
    {
        // 2 tasks assigned to employee1
        Task::factory()->count(2)->create([
            'assigned_to' => $this->employee1->id,
            'created_by' => $this->manager->id,
        ]);

        // 3 tasks assigned to employee2
        Task::factory()->count(3)->create([
            'assigned_to' => $this->employee2->id,
            'created_by' => $this->manager->id,
        ]);

        $response = $this->actingAs($this->employee1, 'sanctum')
            ->getJson('/api/v1/tasks');

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');
    }
}
