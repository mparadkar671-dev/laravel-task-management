<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskObserverFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $employee;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');

        $this->employee = User::factory()->create();
        $this->employee->assignRole('employee');
    }

    public function test_updating_status_creates_task_history_record(): void
    {
        $task = Task::factory()->create([
            'status' => 'pending',
            'assigned_to' => $this->employee->id,
            'created_by' => $this->admin->id,
        ]);

        $this->actingAs($this->employee, 'sanctum')
            ->putJson("/api/v1/tasks/{$task->id}", [
                'status' => 'in-progress',
            ]);

        $this->assertDatabaseHas('task_histories', [
            'task_id' => $task->id,
            'changed_by' => $this->employee->id,
            'old_status' => 'pending',
            'new_status' => 'in-progress',
        ]);
    }

    public function test_updating_non_status_field_does_not_create_task_history(): void
    {
        $task = Task::factory()->create([
            'title' => 'Initial Title',
            'status' => 'pending',
            'assigned_to' => $this->employee->id,
            'created_by' => $this->admin->id,
        ]);

        $this->actingAs($this->admin, 'sanctum')
            ->putJson("/api/v1/tasks/{$task->id}", [
                'title' => 'Modified Title',
            ]);

        $this->assertDatabaseCount('task_histories', 0);
    }

    public function test_multiple_status_changes_create_chronological_history(): void
    {
        $task = Task::factory()->create([
            'status' => 'pending',
            'assigned_to' => $this->employee->id,
            'created_by' => $this->admin->id,
        ]);

        // 1st transition: pending -> in-progress
        $this->actingAs($this->employee, 'sanctum')
            ->putJson("/api/v1/tasks/{$task->id}", [
                'status' => 'in-progress',
            ]);

        // 2nd transition: in-progress -> completed
        $this->actingAs($this->admin, 'sanctum')
            ->putJson("/api/v1/tasks/{$task->id}", [
                'status' => 'completed',
            ]);

        $this->assertDatabaseCount('task_histories', 2);

        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson("/api/v1/tasks/{$task->id}/history");

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.0.old_status', 'in-progress')
            ->assertJsonPath('data.0.new_status', 'completed')
            ->assertJsonPath('data.0.changed_by.id', $this->admin->id)
            ->assertJsonPath('data.1.old_status', 'pending')
            ->assertJsonPath('data.1.new_status', 'in-progress')
            ->assertJsonPath('data.1.changed_by.id', $this->employee->id);
    }
}
