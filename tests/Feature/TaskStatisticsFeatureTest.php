<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskStatisticsFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $employee1;

    protected User $employee2;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');

        $this->employee1 = User::factory()->create();
        $this->employee1->assignRole('employee');

        $this->employee2 = User::factory()->create();
        $this->employee2->assignRole('employee');
    }

    public function test_admin_receives_organization_wide_statistics(): void
    {
        // 2 tasks for employee1
        Task::factory()->create([
            'status' => 'pending',
            'priority' => 'high',
            'assigned_to' => $this->employee1->id,
            'created_by' => $this->admin->id,
        ]);
        Task::factory()->create([
            'status' => 'completed',
            'priority' => 'low',
            'assigned_to' => $this->employee1->id,
            'created_by' => $this->admin->id,
        ]);

        // 1 overdue task for employee2
        Task::factory()->create([
            'status' => 'in-progress',
            'priority' => 'high',
            'due_date' => now()->subDays(2)->format('Y-m-d'),
            'assigned_to' => $this->employee2->id,
            'created_by' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/v1/tasks/statistics');

        $response->assertStatus(200)
            ->assertJsonPath('data.total_tasks', 3)
            ->assertJsonPath('data.pending_tasks', 1)
            ->assertJsonPath('data.in_progress_tasks', 1)
            ->assertJsonPath('data.completed_tasks', 1)
            ->assertJsonPath('data.overdue_tasks', 1)
            ->assertJsonPath('data.priority_breakdown.high', 2)
            ->assertJsonPath('data.priority_breakdown.low', 1);
    }

    public function test_employee_receives_personal_scoped_statistics(): void
    {
        // 2 tasks for employee1
        Task::factory()->create([
            'status' => 'pending',
            'priority' => 'medium',
            'assigned_to' => $this->employee1->id,
            'created_by' => $this->admin->id,
        ]);
        Task::factory()->create([
            'status' => 'completed',
            'priority' => 'low',
            'assigned_to' => $this->employee1->id,
            'created_by' => $this->admin->id,
        ]);

        // 3 tasks for employee2 (should NOT count in employee1's stats)
        Task::factory()->count(3)->create([
            'status' => 'pending',
            'assigned_to' => $this->employee2->id,
            'created_by' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->employee1, 'sanctum')
            ->getJson('/api/v1/tasks/statistics');

        $response->assertStatus(200)
            ->assertJsonPath('data.total_tasks', 2)
            ->assertJsonPath('data.pending_tasks', 1)
            ->assertJsonPath('data.completed_tasks', 1);
    }
}
