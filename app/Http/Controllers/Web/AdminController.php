<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\TaskHistory;
use App\Models\User;
use App\Services\TaskService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminController extends Controller
{
    public function __construct(
        protected TaskService $taskService
    ) {}

    /**
     * Executive Admin Dashboard.
     */
    public function dashboard(): View
    {
        $stats = $this->taskService->getStatistics();

        $usersCount = [
            'total' => User::count(),
            'admins' => User::role('admin')->count(),
            'managers' => User::role('manager')->count(),
            'employees' => User::role('employee')->count(),
        ];

        $recentTasks = Task::with(['assignedTo', 'creator'])
            ->latest()
            ->take(6)
            ->get();

        $recentHistories = TaskHistory::with(['task', 'user'])
            ->orderBy('changed_at', 'desc')
            ->orderBy('id', 'desc')
            ->take(8)
            ->get();

        $users = User::with('roles')->get();

        return view('admin.dashboard', compact('stats', 'usersCount', 'recentTasks', 'recentHistories', 'users'));
    }

    /**
     * All Tasks Management View with Filtering & Sorting.
     */
    public function tasks(Request $request): View
    {
        $query = Task::with(['assignedTo', 'creator'])->withCount('comments');

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
        $users = User::with('roles')->get();

        return view('admin.tasks', compact('tasks', 'users'));
    }

    /**
     * Export all/filtered tasks to CSV.
     */
    public function export(Request $request): StreamedResponse
    {
        return $this->taskService->exportTasksCsv($request->all());
    }

    /**
     * Store a newly created task.
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

        return back()->with('status', 'Task successfully created and delegated!');
    }

    /**
     * Update specified task details.
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
     * Delete specified task.
     */
    public function deleteTask(Task $task): RedirectResponse
    {
        $taskId = $task->id;
        $task->delete();

        return back()->with('status', "Task #{$taskId} was permanently deleted.");
    }

    /**
     * Users & Workload Governance View.
     */
    public function users(): View
    {
        $users = User::with(['roles', 'tasks'])
            ->withCount([
                'tasks as total_assigned_tasks',
                'tasks as pending_tasks' => fn ($q) => $q->where('status', 'pending'),
                'tasks as in_progress_tasks' => fn ($q) => $q->where('status', 'in-progress'),
                'tasks as completed_tasks' => fn ($q) => $q->where('status', 'completed'),
            ])
            ->get();

        return view('admin.users', compact('users'));
    }

    /**
     * Admin creates a new team member (Manager or Employee).
     */
    public function storeUser(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', PasswordRule::defaults()],
            'role' => ['required', 'in:manager,employee'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $user->assignRole($validated['role']);

        return back()->with('status', "Team member {$user->name} created with ".strtoupper($validated['role']).' role!');
    }

    /**
     * Admin updates a user's role (Promote to Manager or Demote to Employee).
     */
    public function updateUserRole(Request $request, User $user): RedirectResponse
    {
        // Enforce Single Admin rule: Primary Administrator cannot be demoted or altered
        if ($user->hasRole('admin')) {
            return back()->withErrors(['role' => 'The Primary Administrator account role cannot be modified.']);
        }

        $validated = $request->validate([
            'role' => ['required', 'in:manager,employee'],
        ]);

        $user->syncRoles($validated['role']);

        $roleTitle = strtoupper($validated['role']);

        return back()->with('status', "{$user->name} is now designated as {$roleTitle} with corresponding authority!");
    }

    /**
     * Admin deletes a user.
     */
    public function deleteUser(User $user): RedirectResponse
    {
        if ($user->hasRole('admin') || $user->id === auth()->id()) {
            return back()->withErrors(['user' => 'The Primary Administrator account cannot be deleted.']);
        }

        $userName = $user->name;
        $user->delete();

        return back()->with('status', "User {$userName} was removed from the platform.");
    }

    /**
     * Admin dispatches a password reset & verification email to a team member.
     */
    public function sendUserResetLink(User $user): RedirectResponse
    {
        $status = Password::sendResetLink(['email' => $user->email]);

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('status', "Password reset link sent to {$user->name} ({$user->email}) successfully!");
        }

        return back()->withErrors(['email' => __($status)]);
    }
}
