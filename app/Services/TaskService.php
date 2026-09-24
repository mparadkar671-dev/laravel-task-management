<?php

namespace App\Services;

use App\Models\Task;
use App\Repositories\TaskRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class TaskService
{
    public function __construct(
        protected TaskRepository $taskRepository
    ) {}

    public function createTask(array $data): Task
    {
        // Automatically set the 'created_by' to the authenticated user
        $data['created_by'] = Auth::id();

        return $this->taskRepository->create($data);
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    public function getTasks(array $filters = []): LengthAwarePaginator
    {
        $user = Auth::user();
        $role = $user ? $user->getRoleNames()->first() : null;

        return $this->taskRepository->getTasksForUser($user->id, $role, $filters);
    }

    public function findById(int|string $id, array $with = ['assignedTo', 'creator']): Task
    {
        return $this->taskRepository->findById($id, $with);
    }

    public function updateTask(Task|int|string $task, array $data): Task
    {
        if (! ($task instanceof Task)) {
            $task = $this->taskRepository->findById($task);
        }

        return $this->taskRepository->update($task, $data);
    }

    public function deleteTask(Task|int|string $task): bool
    {
        if (! ($task instanceof Task)) {
            $task = $this->taskRepository->findById($task);
        }

        return $this->taskRepository->delete($task);
    }

    public function getTaskHistories(Task|int|string $task): Collection
    {
        if (! ($task instanceof Task)) {
            $task = $this->taskRepository->findById($task, ['histories.user']);
        } else {
            $task->loadMissing('histories.user');
        }

        return $task->histories()->with('user')->orderBy('changed_at', 'desc')->orderBy('id', 'desc')->get();
    }

    /**
     * @return array<string, mixed>
     */
    public function getStatistics(): array
    {
        $user = Auth::user();
        $role = $user ? $user->getRoleNames()->first() : null;

        return $this->taskRepository->getStatisticsForUser($user->id, $role);
    }
}
