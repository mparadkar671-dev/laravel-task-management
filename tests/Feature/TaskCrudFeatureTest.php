<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskCrudFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $manager;

    protected User $employee;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');

        $this->manager = User::factory()->create();
        $this->manager->assignRole('manager');

        $this->employee = User::factory()->create();
        $this->employee->assignRole('employee');
    }

    public function test_admin_can_create_a_task(): void
    {
        $payload = [
            'title' => 'Set up CI/CD pipeline',
            'description' => 'Automate test running and deployment.',
            'priority' => 'high',
            'status' => 'pending',
            'assigned_to' => $this->employee->id,
            'due_date' => now()->addDays(5)->format('Y-m-d'),
        ];

        $response = $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/v1/tasks', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('data.title', 'Set up CI/CD pipeline')
            ->assertJsonPath('data.priority', 'high')
            ->assertJsonPath('data.assigned_to.id', $this->employee->id)
            ->assertJsonPath('data.created_by.id', $this->admin->id);

        $this->assertDatabaseHas('tasks', [
            'title' => 'Set up CI/CD pipeline',
            'created_by' => $this->admin->id,
            'assigned_to' => $this->employee->id,
        ]);
    }

    public function test_manager_can_create_a_task(): void
    {
        $payload = [
            'title' => 'Design Architecture Diagram',
            'priority' => 'medium',
            'assigned_to' => $this->employee->id,
            'due_date' => now()->addDays(3)->format('Y-m-d'),
        ];

        $response = $this->actingAs($this->manager, 'sanctum')
            ->postJson('/api/v1/tasks', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('data.title', 'Design Architecture Diagram')
            ->assertJsonPath('data.created_by.id', $this->manager->id);
    }

    public function test_task_creation_validates_required_fields(): void
    {
        $response = $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/v1/tasks', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title', 'priority', 'assigned_to', 'due_date']);
    }

    public function test_user_can_view_a_single_task(): void
    {
        $task = Task::factory()->create([
            'assigned_to' => $this->employee->id,
            'created_by' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson("/api/v1/tasks/{$task->id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $task->id)
            ->assertJsonPath('data.title', $task->title);
    }

    public function test_manager_can_update_a_task(): void
    {
        $task = Task::factory()->create([
            'assigned_to' => $this->employee->id,
            'created_by' => $this->admin->id,
            'status' => 'pending',
            'priority' => 'low',
        ]);

        $response = $this->actingAs($this->manager, 'sanctum')
            ->putJson("/api/v1/tasks/{$task->id}", [
                'title' => 'Updated Task Title',
                'priority' => 'high',
                'status' => 'in-progress',
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.title', 'Updated Task Title')
            ->assertJsonPath('data.priority', 'high')
            ->assertJsonPath('data.status', 'in-progress');

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Updated Task Title',
            'priority' => 'high',
            'status' => 'in-progress',
        ]);
    }

    public function test_admin_can_delete_a_task(): void
    {
        $task = Task::factory()->create([
            'assigned_to' => $this->employee->id,
            'created_by' => $this->manager->id,
        ]);

        $response = $this->actingAs($this->admin, 'sanctum')
            ->deleteJson("/api/v1/tasks/{$task->id}");

        $response->assertStatus(200)
            ->assertJson(['message' => 'Task deleted successfully']);

        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    public function test_can_retrieve_task_history(): void
    {
        $task = Task::factory()->create([
            'assigned_to' => $this->employee->id,
            'created_by' => $this->admin->id,
            'status' => 'pending',
        ]);

        // Change status to in-progress
        $this->actingAs($this->admin, 'sanctum')
            ->putJson("/api/v1/tasks/{$task->id}", ['status' => 'in-progress']);

        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson("/api/v1/tasks/{$task->id}/history");

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.old_status', 'pending')
            ->assertJsonPath('data.0.new_status', 'in-progress');
    }
}
