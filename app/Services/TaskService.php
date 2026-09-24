<?php

namespace App\Services;

use App\Models\Task;
use App\Repositories\TaskRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

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

    /**
     * Export tasks as a streaming CSV download.
     *
     * @param  array<string, mixed>  $filters
     */
    public function exportTasksCsv(array $filters = []): StreamedResponse
    {
        $user = Auth::user();
        $role = $user ? $user->getRoleNames()->first() : null;

        $fileName = 'task_report_'.now()->format('Y_m_d_His').'.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $query = $this->taskRepository->getTasksQueryForUser($user->id, $role, $filters);

        $callback = function () use ($query) {
            $file = fopen('php://output', 'w');
            // Write UTF-8 BOM for Microsoft Excel compatibility
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // Write CSV column headers
            fputcsv($file, [
                'ID',
                'Title',
                'Description',
                'Priority',
                'Status',
                'Assignee Name',
                'Assignee Email',
                'Created By',
                'Due Date',
                'Created At',
                'Updated At',
            ]);

            // Stream rows in chunks of 200 to prevent memory exhaustion
            $query->chunk(200, function ($tasks) use ($file) {
                foreach ($tasks as $task) {
                    fputcsv($file, [
                        $task->id,
                        $task->title,
                        $task->description ?? '',
                        strtoupper($task->priority),
                        strtoupper($task->status),
                        $task->assignedTo?->name ?? 'Unassigned',
                        $task->assignedTo?->email ?? '',
                        $task->creator?->name ?? 'System',
                        $task->due_date ? $task->due_date->format('Y-m-d') : '',
                        $task->created_at->format('Y-m-d H:i:s'),
                        $task->updated_at->format('Y-m-d H:i:s'),
                    ]);
                }
            });

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
