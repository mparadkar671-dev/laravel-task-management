<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskFilterFeatureTest extends TestCase
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

    public function test_can_filter_tasks_by_status(): void
    {
        Task::factory()->create([
            'status' => 'pending',
            'assigned_to' => $this->employee->id,
            'created_by' => $this->admin->id,
        ]);

        Task::factory()->create([
            'status' => 'completed',
            'assigned_to' => $this->employee->id,
            'created_by' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/v1/tasks?status=completed');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.status', 'completed');
    }

    public function test_can_filter_tasks_by_priority(): void
    {
        Task::factory()->create([
            'priority' => 'low',
            'assigned_to' => $this->employee->id,
            'created_by' => $this->admin->id,
        ]);

        Task::factory()->create([
            'priority' => 'high',
            'assigned_to' => $this->employee->id,
            'created_by' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/v1/tasks?priority=high');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.priority', 'high');
    }

    public function test_can_filter_tasks_by_due_date(): void
    {
        $targetDate = now()->addDays(7)->format('Y-m-d');
        $otherDate = now()->addDays(14)->format('Y-m-d');

        Task::factory()->create([
            'due_date' => $targetDate,
            'assigned_to' => $this->employee->id,
            'created_by' => $this->admin->id,
        ]);

        Task::factory()->create([
            'due_date' => $otherDate,
            'assigned_to' => $this->employee->id,
            'created_by' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson("/api/v1/tasks?due_date={$targetDate}");

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.due_date', $targetDate);
    }

    public function test_can_search_tasks_by_title_or_description(): void
    {
        Task::factory()->create([
            'title' => 'Kubernetes Cluster Deployment',
            'description' => 'Deploy microservices onto cluster.',
            'assigned_to' => $this->employee->id,
            'created_by' => $this->admin->id,
        ]);

        Task::factory()->create([
            'title' => 'Database Backup Script',
            'description' => 'Write backup cronjob.',
            'assigned_to' => $this->employee->id,
            'created_by' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/v1/tasks?search=Kubernetes');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.title', 'Kubernetes Cluster Deployment');
    }

    public function test_can_paginate_tasks(): void
    {
        Task::factory()->count(10)->create([
            'assigned_to' => $this->employee->id,
            'created_by' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/v1/tasks?per_page=3');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure([
                'data',
                'links',
                'meta' => [
                    'current_page',
                    'total',
                    'per_page',
                ],
            ]);
    }
}
