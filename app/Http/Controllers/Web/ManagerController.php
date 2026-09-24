<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\User;
use App\Services\TaskService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ManagerController extends Controller
{
    public function __construct(
        protected TaskService $taskService
    ) {}

    /**
     * Manager Team & Department Dashboard.
     */
    public function dashboard(): View
    {
        $stats = $this->taskService->getStatistics();

        $teamMembers = User::role('employee')
            ->withCount([
                'tasks as total_tasks',
                'tasks as pending_tasks' => fn ($q) => $q->where('status', 'pending'),
                'tasks as in_progress_tasks' => fn ($q) => $q->where('status', 'in-progress'),
                'tasks as completed_tasks' => fn ($q) => $q->where('status', 'completed'),
            ])
            ->get();

        $recentTasks = Task::with(['assignedTo', 'creator'])
            ->latest()
            ->take(6)
            ->get();

        $employees = User::role('employee')->get();

        return view('manager.dashboard', compact('stats', 'teamMembers', 'recentTasks', 'employees'));
    }

    /**
     * Manager Task Delegation & Board.
     */
    public function tasks(Request $request): View
    {
        $query = Task::with(['assignedTo', 'creator']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('assigned_to')) {
            $query->where('assigned_to', $request->assigned_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $sortBy = in_array($request->sort_by, ['due_date', 'priority', 'status', 'title', 'created_at'])
            ? $request->sort_by
            : 'created_at';
        $sortOrder = $request->sort_order === 'asc' ? 'asc' : 'desc';

        $tasks = $query->orderBy($sortBy, $sortOrder)->paginate(10)->withQueryString();
        $employees = User::role('employee')->get();

        return view('manager.tasks', compact('tasks', 'employees'));
    }

    /**
     * Store new task and delegate to an employee.
     */
    public function storeTask(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'priority' => ['required', 'in:low,medium,high'],
            'status' => ['required', 'in:pending,in-progress,completed'],
            'due_date' => ['required', 'date'],
            'assigned_to' => ['required', 'exists:users,id'],
        ]);

        $validated['created_by'] = auth()->id();

        Task::create($validated);

        return back()->with('status', 'Task successfully created and assigned to employee!');
    }

    /**
     * Update task details.
     */
    public function updateTask(Request $request, Task $task): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'priority' => ['required', 'in:low,medium,high'],
            'status' => ['required', 'in:pending,in-progress,completed'],
            'due_date' => ['required', 'date'],
            'assigned_to' => ['required', 'exists:users,id'],
        ]);

        $task->update($validated);

        return back()->with('status', "Task #{$task->id} updated successfully!");
    }

    /**
     * Delete department task.
     */
    public function deleteTask(Task $task): RedirectResponse
    {
        $taskId = $task->id;
        $task->delete();

        return back()->with('status', "Task #{$taskId} was removed.");
    }
}
