<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use App\Notifications\TaskOverdueNotification;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class OverdueTaskRemindersFeatureTest extends TestCase
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

    public function test_remind_overdue_command_notifies_assignees_of_past_due_tasks(): void
    {
        Notification::fake();

        // 1 overdue task assigned to employee1
        $overdueTask = Task::factory()->create([
            'title' => 'Critical Server Patch',
            'status' => 'in-progress',
            'due_date' => now()->subDay()->format('Y-m-d'),
            'assigned_to' => $this->employee1->id,
            'created_by' => $this->admin->id,
        ]);

        // 1 future task assigned to employee2 (should not trigger reminder)
        $futureTask = Task::factory()->create([
            'title' => 'Future Roadmap Planning',
            'status' => 'pending',
            'due_date' => now()->addDays(5)->format('Y-m-d'),
            'assigned_to' => $this->employee2->id,
            'created_by' => $this->admin->id,
        ]);

        // 1 completed task with past due date (should not trigger reminder)
        $completedTask = Task::factory()->create([
            'title' => 'Past Completed Task',
            'status' => 'completed',
            'due_date' => now()->subDays(3)->format('Y-m-d'),
            'assigned_to' => $this->employee2->id,
            'created_by' => $this->admin->id,
        ]);

        $this->artisan('tasks:remind-overdue')
            ->expectsOutput('Successfully dispatched 1 overdue reminder notifications.')
            ->assertSuccessful();

        Notification::assertSentTo(
            $this->employee1,
            TaskOverdueNotification::class,
            function (TaskOverdueNotification $notification) use ($overdueTask) {
                return $notification->task->id === $overdueTask->id;
            }
        );

        Notification::assertNotSentTo($this->employee2, TaskOverdueNotification::class);
    }
}
