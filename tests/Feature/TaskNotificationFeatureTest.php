<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use App\Notifications\TaskAssignedNotification;
use App\Notifications\TaskStatusUpdatedNotification;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class TaskNotificationFeatureTest extends TestCase
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

    public function test_assignee_receives_notification_when_task_is_created(): void
    {
        Notification::fake();

        $this->actingAs($this->manager, 'sanctum')
            ->postJson('/api/v1/tasks', [
                'title' => 'Build Notification Service',
                'priority' => 'high',
                'assigned_to' => $this->employee1->id,
                'due_date' => now()->addDays(3)->format('Y-m-d'),
            ]);

        Notification::assertSentTo(
            $this->employee1,
            TaskAssignedNotification::class,
            function (TaskAssignedNotification $notification) {
                return $notification->task->title === 'Build Notification Service';
            }
        );
    }

    public function test_new_assignee_receives_notification_when_task_is_reassigned(): void
    {
        Notification::fake();

        $task = Task::factory()->create([
            'assigned_to' => $this->employee1->id,
            'created_by' => $this->manager->id,
        ]);

        $this->actingAs($this->manager, 'sanctum')
            ->putJson("/api/v1/tasks/{$task->id}", [
                'assigned_to' => $this->employee2->id,
            ]);

        Notification::assertSentTo(
            $this->employee2,
            TaskAssignedNotification::class
        );
    }

    public function test_creator_receives_notification_when_status_is_updated_by_assignee(): void
    {
        Notification::fake();

        $task = Task::factory()->create([
            'status' => 'pending',
            'assigned_to' => $this->employee1->id,
            'created_by' => $this->manager->id,
        ]);

        $this->actingAs($this->employee1, 'sanctum')
            ->putJson("/api/v1/tasks/{$task->id}", [
                'status' => 'completed',
            ]);

        Notification::assertSentTo(
            $this->manager,
            TaskStatusUpdatedNotification::class,
            function (TaskStatusUpdatedNotification $notification) {
                return $notification->oldStatus === 'pending'
                    && $notification->newStatus === 'completed';
            }
        );
    }

    public function test_assignee_receives_notification_when_status_is_updated_by_manager(): void
    {
        Notification::fake();

        $task = Task::factory()->create([
            'status' => 'pending',
            'assigned_to' => $this->employee1->id,
            'created_by' => $this->manager->id,
        ]);

        $this->actingAs($this->manager, 'sanctum')
            ->putJson("/api/v1/tasks/{$task->id}", [
                'status' => 'in-progress',
            ]);

        Notification::assertSentTo(
            $this->employee1,
            TaskStatusUpdatedNotification::class,
            function (TaskStatusUpdatedNotification $notification) {
                return $notification->newStatus === 'in-progress';
            }
        );
    }
}
