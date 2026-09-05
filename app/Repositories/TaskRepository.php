<?php

namespace App\Repositories;

use App\Models\Task;

class TaskRepository
{
    public function create(array $data)
    {
        return Task::create($data);
    }

    public function findById($id)
    {
        return Task::findOrFail($id);
    }

    public function getAllForUser($userId, $role)
    {
        // Admin and Manager can see all tasks
        if (in_array($role, ['admin', 'manager'])) {
            return Task::with('assignedTo')->get();
        }

        // Employees only see tasks assigned to them
        return Task::where('assigned_to', $userId)->with('assignedTo')->get();
    }
}