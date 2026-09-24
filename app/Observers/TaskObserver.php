<?php

namespace App\Observers;

use App\Models\Task;
use App\Models\TaskHistory;
use App\Models\User;
use App\Notifications\TaskAssignedNotification;
use App\Notifications\TaskStatusUpdatedNotification;
use Illuminate\Support\Facades\Auth;

class TaskObserver
{
    /**
     * Handle the Task "created" event.
     */
    public function created(Task $task): void
    {
        if ($task->assignedTo) {
            $task->assignedTo->notify(new TaskAssignedNotification($task));
        }
    }

    /**
     * Handle the Task "updated" event.
     */
    public function updated(Task $task): void
    {
        $actor = Auth::user();
        $actorId = $actor?->id ?? $task->assigned_to ?? $task->created_by;

        // If the task was reassigned, notify the newly assigned user
        if ($task->wasChanged('assigned_to') && $task->assigned_to) {
            $newAssignee = User::find($task->assigned_to);
            $newAssignee?->notify(new TaskAssignedNotification($task));
        }

        // If status changed, record in history and send notifications
        if ($task->wasChanged('status')) {
            $oldStatus = (string) $task->getOriginal('status');
            $newStatus = (string) $task->status;

            TaskHistory::create([
                'task_id' => $task->id,
                'changed_by' => $actorId,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'changed_at' => now(),
            ]);

            // Notify the creator if someone else made the update
            if ($task->created_by && $task->created_by !== $actorId) {
                $creator = User::find($task->created_by);
                $creator?->notify(new TaskStatusUpdatedNotification(
                    $task,
                    $oldStatus,
                    $newStatus,
                    $actor?->name
                ));
            }

            // Notify the assignee if someone else made the update
            if ($task->assigned_to && $task->assigned_to !== $actorId) {
                $assignee = User::find($task->assigned_to);
                $assignee?->notify(new TaskStatusUpdatedNotification(
                    $task,
                    $oldStatus,
                    $newStatus,
                    $actor?->name
                ));
            }
        }
    }
}
