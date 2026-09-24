<?php

namespace App\Observers;

use App\Models\Task;
use App\Models\TaskHistory;
use Illuminate\Support\Facades\Auth;

class TaskObserver
{
    /**
     * Handle the Task "updated" event.
     */
    public function updated(Task $task): void
    {
        if ($task->wasChanged('status')) {
            TaskHistory::create([
                'task_id' => $task->id,
                'changed_by' => Auth::id() ?? $task->assigned_to ?? $task->created_by,
                'old_status' => (string) $task->getOriginal('status'),
                'new_status' => (string) $task->status,
                'changed_at' => now(),
            ]);
        }
    }
}
