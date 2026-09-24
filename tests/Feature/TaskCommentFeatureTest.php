<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\TaskComment;
use App\Models\User;
use App\Notifications\TaskCommentNotification;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class TaskCommentFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    public function test_guest_cannot_view_or_post_task_comments(): void
    {
        $task = Task::factory()->create();

        $responseGet = $this->get("/tasks/{$task->id}/comments");
        $responseGet->assertRedirect('/login');

        $responsePost = $this->post("/tasks/{$task->id}/comments", [
            'comment' => 'This is a test comment.',
        ]);
        $responsePost->assertRedirect('/login');
    }

    public function test_admin_can_post_comment_on_any_task(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $task = Task::factory()->create();

        $response = $this->actingAs($admin)->post("/tasks/{$task->id}/comments", [
            'comment' => 'Executive review note.',
        ]);

        $response->assertSessionHas('status', 'Comment posted successfully!');
        $this->assertDatabaseHas('task_comments', [
            'task_id' => $task->id,
            'user_id' => $admin->id,
            'comment' => 'Executive review note.',
        ]);
    }

    public function test_manager_can_post_comment_on_tasks(): void
    {
        $manager = User::factory()->create();
        $manager->assignRole('manager');

        $task = Task::factory()->create();

        $response = $this->actingAs($manager)->post("/tasks/{$task->id}/comments", [
            'comment' => 'Manager instructions for the sprint.',
        ]);

        $response->assertSessionHas('status', 'Comment posted successfully!');
        $this->assertDatabaseHas('task_comments', [
            'task_id' => $task->id,
            'user_id' => $manager->id,
            'comment' => 'Manager instructions for the sprint.',
        ]);
    }

    public function test_assigned_employee_can_comment_on_their_task(): void
    {
        $employee = User::factory()->create();
        $employee->assignRole('employee');

        $task = Task::factory()->create(['assigned_to' => $employee->id]);

        $response = $this->actingAs($employee)->post("/tasks/{$task->id}/comments", [
            'comment' => 'Work has begun on this deliverable.',
        ]);

        $response->assertSessionHas('status', 'Comment posted successfully!');
        $this->assertDatabaseHas('task_comments', [
            'task_id' => $task->id,
            'user_id' => $employee->id,
            'comment' => 'Work has begun on this deliverable.',
        ]);
    }

    public function test_unassigned_employee_cannot_comment_or_view_others_task(): void
    {
        $employee1 = User::factory()->create();
        $employee1->assignRole('employee');

        $employee2 = User::factory()->create();
        $employee2->assignRole('employee');

        $task = Task::factory()->create(['assigned_to' => $employee1->id]);

        $responseGet = $this->actingAs($employee2)->getJson("/tasks/{$task->id}/comments");
        $responseGet->assertStatus(403);

        $responsePost = $this->actingAs($employee2)->post("/tasks/{$task->id}/comments", [
            'comment' => 'Unauthorized comment attempt.',
        ]);
        $responsePost->assertStatus(403);
    }

    public function test_posting_comment_notifies_the_assigned_employee(): void
    {
        Notification::fake();

        $manager = User::factory()->create();
        $manager->assignRole('manager');

        $employee = User::factory()->create();
        $employee->assignRole('employee');

        $task = Task::factory()->create([
            'created_by' => $manager->id,
            'assigned_to' => $employee->id,
        ]);

        $this->actingAs($manager)->post("/tasks/{$task->id}/comments", [
            'comment' => 'Please prioritize this deliverable today.',
        ]);

        Notification::assertSentTo(
            $employee,
            TaskCommentNotification::class,
            function ($notification) use ($task) {
                return $notification->task->id === $task->id;
            }
        );
    }

    public function test_api_can_fetch_and_post_task_comments(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $task = Task::factory()->create();

        TaskComment::factory()->create([
            'task_id' => $task->id,
            'user_id' => $admin->id,
            'comment' => 'Initial comment',
        ]);

        $responseGet = $this->actingAs($admin, 'sanctum')->getJson("/api/v1/tasks/{$task->id}/comments");
        $responseGet->assertStatus(200)
            ->assertJsonStructure([
                'task' => ['id', 'title', 'status', 'priority'],
                'comments' => [
                    '*' => ['id', 'comment', 'created_at', 'user' => ['id', 'name', 'role', 'initials']],
                ],
            ]);

        $responsePost = $this->actingAs($admin, 'sanctum')->postJson("/api/v1/tasks/{$task->id}/comments", [
            'comment' => 'API created comment',
        ]);
        $responsePost->assertStatus(201)
            ->assertJsonPath('comment.comment', 'API created comment');
    }
}
