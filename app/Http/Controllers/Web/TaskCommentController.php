<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\TaskComment;
use App\Notifications\TaskCommentNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TaskCommentController extends Controller
{
    /**
     * Ensure the user is authorized to view or comment on this task.
     */
    protected function authorizeTaskAccess(Task $task): void
    {
        $user = auth()->user();

        // Admins and Managers have organization-wide collaboration rights
        if ($user->hasRole('admin') || $user->hasRole('manager')) {
            return;
        }

        // Employees can only access tasks assigned directly to them
        if ($task->assigned_to !== $user->id) {
            abort(403, 'You are not authorized to view or comment on this task.');
        }
    }

    /**
     * Get all comments for a task (JSON).
     */
    public function index(Task $task): JsonResponse
    {
        $this->authorizeTaskAccess($task);

        $comments = $task->comments()
            ->with(['user.roles'])
            ->oldest()
            ->get()
            ->map(function ($c) {
                return [
                    'id' => $c->id,
                    'comment' => $c->comment,
                    'created_at' => $c->created_at->diffForHumans(),
                    'user' => [
                        'id' => $c->user->id,
                        'name' => $c->user->name,
                        'role' => $c->user->roles->first()?->name ?? 'member',
                        'initials' => strtoupper(substr($c->user->name, 0, 2)),
                    ],
                ];
            });

        return response()->json([
            'task' => [
                'id' => $task->id,
                'title' => $task->title,
                'status' => $task->status,
                'priority' => $task->priority,
                'due_date' => $task->due_date?->format('M d, Y'),
                'assigned_to_name' => $task->assignedTo?->name ?? 'Unassigned',
            ],
            'comments' => $comments,
        ]);
    }

    /**
     * Store a new comment on a task.
     */
    public function store(Request $request, Task $task): JsonResponse|RedirectResponse
    {
        $this->authorizeTaskAccess($task);

        $validated = $request->validate([
            'comment' => ['required', 'string', 'min:1', 'max:2000'],
        ]);

        $commenter = auth()->user();

        $comment = TaskComment::create([
            'task_id' => $task->id,
            'user_id' => $commenter->id,
            'comment' => $validated['comment'],
        ]);

        // Notify the assigned employee if someone else commented
        if ($task->assigned_to && $task->assigned_to !== $commenter->id) {
            $task->assignedTo?->notify(new TaskCommentNotification($task, $comment, $commenter));
        }

        // Notify the task creator if someone else commented
        if ($task->created_by && $task->created_by !== $commenter->id && $task->created_by !== $task->assigned_to) {
            $task->creator?->notify(new TaskCommentNotification($task, $comment, $commenter));
        }

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Comment posted successfully!',
                'comment' => [
                    'id' => $comment->id,
                    'comment' => $comment->comment,
                    'created_at' => $comment->created_at->diffForHumans(),
                    'user' => [
                        'id' => $commenter->id,
                        'name' => $commenter->name,
                        'role' => $commenter->roles->first()?->name ?? 'member',
                        'initials' => strtoupper(substr($commenter->name, 0, 2)),
                    ],
                ],
            ], 201);
        }

        return back()->with('status', 'Comment posted successfully!')
            ->with('active_comment_task_id', $task->id);
    }
}
