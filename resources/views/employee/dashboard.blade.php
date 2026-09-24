@extends('layouts.web')

@section('title', 'My Tasks Workspace')
@section('page-title', 'Employee Focus Board')

@section('top-actions')
    <div style="font-size: 0.825rem; color: var(--text-dim); display: flex; align-items: center; gap: 0.4rem;">
        <span>🔒 Personal Assignment Scope</span>
    </div>
@endsection

@section('content')
    <!-- Personal Productivity Metrics -->
    <div class="stats-grid">
        <div class="stat-card">
            <span class="stat-label">My Assigned Tasks</span>
            <div class="stat-value" style="color: #34d399;">{{ $stats['total_tasks'] }}</div>
        </div>

        <div class="stat-card">
            <span class="stat-label">Pending</span>
            <div class="stat-value" style="color: #fbbf24;">{{ $stats['pending_tasks'] }}</div>
        </div>

        <div class="stat-card">
            <span class="stat-label">In Progress</span>
            <div class="stat-value" style="color: #22d3ee;">{{ $stats['in_progress_tasks'] }}</div>
        </div>

        <div class="stat-card">
            <span class="stat-label">Completed</span>
            <div class="stat-value" style="color: #34d399;">{{ $stats['completed_tasks'] }}</div>
        </div>

        <div class="stat-card" style="{{ $stats['overdue_tasks'] > 0 ? 'border-color: rgba(244, 63, 94, 0.4); background: rgba(244, 63, 94, 0.05);' : '' }}">
            <span class="stat-label">Overdue</span>
            <div class="stat-value" style="color: #fb7185;">{{ $stats['overdue_tasks'] }}</div>
        </div>
    </div>

    <!-- Rights & Permissions Clarification Banner -->
    <div class="card" style="background: rgba(16, 185, 129, 0.05); border-color: rgba(16, 185, 129, 0.2); padding: 1rem 1.25rem; margin-bottom: 1.5rem;">
        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <span style="font-size: 1.3rem;">ℹ️</span>
            <div>
                <div style="font-weight: 700; font-size: 0.9rem; color: #34d399;">Employee Role Permissions</div>
                <div style="font-size: 0.8rem; color: var(--text-muted); margin-top: 0.15rem;">
                    You have view access to tasks assigned specifically to you and are authorized to advance task status. New tasks are created and delegated by your Manager or Admin.
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Bar -->
    <div class="card" style="padding: 1.25rem;">
        <form action="{{ route('employee.dashboard') }}" method="GET" style="display: flex; gap: 0.75rem; flex-wrap: wrap; align-items: center;">
            <div style="flex: 1; min-width: 220px;">
                <input type="text" name="search" class="form-control" placeholder="Search my tasks..." value="{{ request('search') }}">
            </div>

            <div>
                <select name="status" class="form-control" onchange="this.form.submit()">
                    <option value="">All Statuses</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="in-progress" {{ request('status') === 'in-progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                </select>
            </div>

            <div>
                <select name="priority" class="form-control" onchange="this.form.submit()">
                    <option value="">All Priorities</option>
                    <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>High Priority</option>
                    <option value="medium" {{ request('priority') === 'medium' ? 'selected' : '' }}>Medium Priority</option>
                    <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>Low Priority</option>
                </select>
            </div>

            <button type="submit" class="btn btn-secondary">Filter</button>
            @if(request()->anyFilled(['search', 'status', 'priority']))
                <a href="{{ route('employee.dashboard') }}" class="btn btn-secondary" style="color: #fb7185;">Reset</a>
            @endif
        </form>
    </div>

    <!-- Tasks List & Status Progression -->
    <div class="card">
        <h2 style="font-size: 1.15rem; font-weight: 700; margin-bottom: 1.25rem;">My Active Deliverables</h2>

        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Task Name & Instructions</th>
                        <th>Priority</th>
                        <th>Current Status</th>
                        <th>Advance Status</th>
                        <th>Due Date</th>
                        <th>Team Notes</th>
                        <th>Created By</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tasks as $task)
                        <tr>
                            <td>
                                <div style="font-weight: 700; font-size: 0.95rem;">{{ $task->title }}</div>
                                <div style="font-size: 0.825rem; color: var(--text-muted); margin-top: 0.25rem;">
                                    {{ $task->description ?? 'No specific instructions provided.' }}
                                </div>
                            </td>
                            <td><span class="badge badge-{{ $task->priority }}">{{ $task->priority }}</span></td>
                            <td><span class="badge badge-{{ $task->status }}">{{ $task->status }}</span></td>
                            <td>
                                <!-- Quick Status Transition Form -->
                                <form action="{{ route('employee.tasks.updateStatus', $task) }}" method="POST" style="display: flex; gap: 0.4rem; align-items: center;">
                                    @csrf
                                    @method('PATCH')
                                    <select name="status" class="form-control" style="padding: 0.35rem 0.6rem; font-size: 0.8rem; width: auto;" onchange="this.form.submit()">
                                        <option value="pending" {{ $task->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="in-progress" {{ $task->status === 'in-progress' ? 'selected' : '' }}>In Progress</option>
                                        <option value="completed" {{ $task->status === 'completed' ? 'selected' : '' }}>Completed</option>
                                    </select>
                                </form>
                            </td>
                            <td>
                                <div>{{ $task->due_date?->format('M d, Y') ?? 'No date' }}</div>
                                @if($task->due_date && $task->due_date->isPast() && $task->status !== 'completed')
                                    <span class="badge badge-high" style="font-size: 0.65rem; margin-top: 0.2rem;">Overdue</span>
                                @endif
                            </td>
                            <td>
                                <button type="button" class="btn btn-secondary btn-sm" onclick="openTaskCommentsModal({{ $task->id }}, '{{ addslashes($task->title) }}')" title="Discussion & Notes">
                                    <span>💬 <span id="task-comment-count-{{ $task->id }}">{{ $task->comments_count ?? 0 }}</span></span>
                                </button>
                            </td>
                            <td>
                                <span style="font-size: 0.85rem; color: var(--text-muted);">
                                    {{ $task->creator?->name ?? 'Management' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; color: var(--text-dim); padding: 3rem;">
                                🎉 You currently have no tasks assigned in this view.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div style="margin-top: 1.5rem;">
            {{ $tasks->links() }}
        </div>
    </div>

    <!-- Task Collaboration & Comments Modal -->
    @include('partials.task-comments-modal')
@endsection
