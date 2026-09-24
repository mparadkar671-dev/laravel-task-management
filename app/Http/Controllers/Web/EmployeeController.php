<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmployeeController extends Controller
{
    public function __construct(
        protected TaskService $taskService
    ) {}

    /**
     * Dedicated Employee Personal Task Workspace.
     * Displays only tasks assigned to the authenticated employee.
     */
    public function dashboard(Request $request): View
    {
        $user = auth()->user();
        $stats = $this->taskService->getStatistics();

        // Query only tasks assigned to this employee
        $query = Task::with('creator')->where('assigned_to', $user->id);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
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
            : 'due_date';
        $sortOrder = $request->sort_order === 'desc' ? 'desc' : 'asc';

        $tasks = $query->orderBy($sortBy, $sortOrder)->paginate(10)->withQueryString();

        return view('employee.dashboard', compact('stats', 'tasks', 'user'));
    }

    /**
     * Update task status (Employee privilege).
     * Enforces strict authorization: employees can ONLY update status of tasks assigned to them.
     */
    public function updateStatus(Request $request, Task $task): RedirectResponse
    {
        abort_unless(
            $task->assigned_to === auth()->id(),
            403,
            'Access denied. You may only update the status of tasks assigned to you.'
        );

        $validated = $request->validate([
            'status' => ['required', 'in:pending,in-progress,completed'],
        ]);

        $task->update([
            'status' => $validated['status'],
        ]);

        return back()->with('status', "Task #{$task->id} status updated to '".strtoupper($validated['status'])."'.");
    }
}
