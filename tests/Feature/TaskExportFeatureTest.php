<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskExportFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    public function test_guest_cannot_export_tasks(): void
    {
        $response = $this->get('/admin/tasks/export');
        $response->assertRedirect('/login');

        $responseManager = $this->get('/manager/tasks/export');
        $responseManager->assertRedirect('/login');
    }

    public function test_admin_can_export_tasks_to_csv(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $assignee = User::factory()->create(['name' => 'Alice Engineer', 'email' => 'alice@company.com']);

        $task = Task::factory()->create([
            'title' => 'Critical System Architecture Review',
            'status' => 'in-progress',
            'priority' => 'high',
            'assigned_to' => $assignee->id,
            'created_by' => $admin->id,
        ]);

        $response = $this->actingAs($admin)->get('/admin/tasks/export');

        $response->assertStatus(200)
            ->assertHeader('Content-Type', 'text/csv; charset=UTF-8');

        $this->assertTrue(str_contains(
            $response->headers->get('Content-Disposition') ?? '',
            'attachment; filename="task_report_'
        ));

        // Stream output assertions
        ob_start();
        $response->sendContent();
        $content = ob_get_clean();

        $this->assertStringContainsString('Critical System Architecture Review', $content);
        $this->assertStringContainsString('Alice Engineer', $content);
        $this->assertStringContainsString('HIGH', $content);
        $this->assertStringContainsString('IN-PROGRESS', $content);
    }

    public function test_manager_can_export_tasks_to_csv(): void
    {
        $manager = User::factory()->create();
        $manager->assignRole('manager');

        $task = Task::factory()->create([
            'title' => 'Quarterly Team Deliverable',
            'status' => 'pending',
            'priority' => 'medium',
        ]);

        $response = $this->actingAs($manager)->get('/manager/tasks/export');

        $response->assertStatus(200)
            ->assertHeader('Content-Type', 'text/csv; charset=UTF-8');

        ob_start();
        $response->sendContent();
        $content = ob_get_clean();

        $this->assertStringContainsString('Quarterly Team Deliverable', $content);
    }

    public function test_export_respects_filter_parameters(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        Task::factory()->create([
            'title' => 'Completed Task Alpha',
            'status' => 'completed',
        ]);

        Task::factory()->create([
            'title' => 'Pending Task Beta',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($admin)->get('/admin/tasks/export?status=completed');

        $response->assertStatus(200);

        ob_start();
        $response->sendContent();
        $content = ob_get_clean();

        $this->assertStringContainsString('Completed Task Alpha', $content);
        $this->assertStringNotContainsString('Pending Task Beta', $content);
    }

    public function test_employee_is_forbidden_from_exporting_admin_or_manager_reports(): void
    {
        $employee = User::factory()->create();
        $employee->assignRole('employee');

        $this->actingAs($employee)->get('/admin/tasks/export')->assertStatus(403);
        $this->actingAs($employee)->get('/manager/tasks/export')->assertStatus(403);
    }
}
