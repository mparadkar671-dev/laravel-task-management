<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Roles
        $this->call(RoleSeeder::class);

        // 2. Seed Users
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
            ]
        );
        $admin->syncRoles('admin');

        $manager = User::firstOrCreate(
            ['email' => 'manager@example.com'],
            [
                'name' => 'Manager User',
                'password' => Hash::make('password'),
            ]
        );
        $manager->syncRoles('manager');

        $employee1 = User::firstOrCreate(
            ['email' => 'employee1@example.com'],
            [
                'name' => 'Employee One',
                'password' => Hash::make('password'),
            ]
        );
        $employee1->syncRoles('employee');

        $employee2 = User::firstOrCreate(
            ['email' => 'employee2@example.com'],
            [
                'name' => 'Employee Two',
                'password' => Hash::make('password'),
            ]
        );
        $employee2->syncRoles('employee');

        // 3. Seed Sample Tasks
        Task::firstOrCreate(
            ['title' => 'Initial Server Setup'],
            [
                'description' => 'Configure production deployment server and environment variables.',
                'priority' => 'high',
                'status' => 'completed',
                'due_date' => now()->addDays(2)->format('Y-m-d'),
                'assigned_to' => $employee1->id,
                'created_by' => $admin->id,
            ]
        );

        Task::firstOrCreate(
            ['title' => 'Develop Authentication Module'],
            [
                'description' => 'Implement Sanctum token authentication with role-based authorization.',
                'priority' => 'high',
                'status' => 'in-progress',
                'due_date' => now()->addDays(5)->format('Y-m-d'),
                'assigned_to' => $employee1->id,
                'created_by' => $manager->id,
            ]
        );

        Task::firstOrCreate(
            ['title' => 'Design Task Analytics Dashboard'],
            [
                'description' => 'Create frontend wireframes and mockups for task progress tracking.',
                'priority' => 'medium',
                'status' => 'pending',
                'due_date' => now()->addDays(10)->format('Y-m-d'),
                'assigned_to' => $employee2->id,
                'created_by' => $manager->id,
            ]
        );

        Task::firstOrCreate(
            ['title' => 'Write API Documentation'],
            [
                'description' => 'Document all REST endpoints including request and response payloads.',
                'priority' => 'low',
                'status' => 'pending',
                'due_date' => now()->addDays(14)->format('Y-m-d'),
                'assigned_to' => $employee2->id,
                'created_by' => $admin->id,
            ]
        );

        Task::firstOrCreate(
            ['title' => 'Submit Monthly Compliance Audit'],
            [
                'description' => 'Review quarterly security policies and submit compliance checklist.',
                'priority' => 'high',
                'status' => 'pending',
                'due_date' => now()->subDays(2)->format('Y-m-d'),
                'assigned_to' => $employee1->id,
                'created_by' => $manager->id,
            ]
        );
    }
}
