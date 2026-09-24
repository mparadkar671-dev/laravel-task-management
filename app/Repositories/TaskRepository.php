<?php

namespace App\Repositories;

use App\Models\Task;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class TaskRepository
{
    public function create(array $data): Task
    {
        return Task::create($data);
    }

    public function findById(int|string $id, array $with = ['assignedTo', 'creator']): Task
    {
        return Task::with($with)->findOrFail($id);
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    public function getTasksForUser(int|string $userId, ?string $role, array $filters = []): LengthAwarePaginator
    {
        $query = Task::with(['assignedTo', 'creator']);

        // Employees only see tasks assigned to them
        if (! in_array($role, ['admin', 'manager'], true)) {
            $query->where('assigned_to', $userId);
        }

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['priority'])) {
            $query->where('priority', $filters['priority']);
        }

        if (! empty($filters['due_date'])) {
            $query->whereDate('due_date', $filters['due_date']);
        }

        if (! empty($filters['assigned_to'])) {
            $query->where('assigned_to', $filters['assigned_to']);
        }

        if (! empty($filters['search'])) {
            $searchTerm = '%'.$filters['search'].'%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', $searchTerm)
                    ->orWhere('description', 'like', $searchTerm);
            });
        }

        $sortBy = $filters['sort_by'] ?? 'created_at';
        $allowedSorts = ['title', 'priority', 'status', 'due_date', 'created_at'];
        if (! in_array($sortBy, $allowedSorts, true)) {
            $sortBy = 'created_at';
        }

        $sortOrder = strtolower($filters['sort_order'] ?? 'desc') === 'asc' ? 'asc' : 'desc';
        $query->orderBy($sortBy, $sortOrder);

        $perPage = isset($filters['per_page']) ? (int) $filters['per_page'] : 15;

        return $query->paginate($perPage);
    }

    /**
     * Backward-compatible method returning collection or paginator.
     */
    public function getAllForUser(int|string $userId, ?string $role)
    {
        return $this->getTasksForUser($userId, $role);
    }

    public function update(Task $task, array $data): Task
    {
        $task->update($data);

        return $task->fresh(['assignedTo', 'creator']);
    }

    public function delete(Task $task): bool
    {
        return (bool) $task->delete();
    }
}
