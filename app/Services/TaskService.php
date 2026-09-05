<?php

namespace App\Services;

use App\Repositories\TaskRepository;
use Illuminate\Support\Facades\Auth;

class TaskService
{
    protected $taskRepository;

    public function __construct(TaskRepository $taskRepository)
    {
        $this->taskRepository = $taskRepository;
    }

    public function createTask(array $data)
    {
        // Logic: Automatically set the 'created_by' to the logged-in user
        $data['created_by'] = Auth::id();
        
        return $this->taskRepository->create($data);
    }

    public function getTasks()
    {
        $user = Auth::user();
        $role = $user->getRoleNames()->first(); // Get the user's role

        return $this->taskRepository->getAllForUser($user->id, $role);
    }
}