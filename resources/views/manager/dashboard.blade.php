@extends('layouts.web')

@section('title', 'Manager Dashboard')
@section('page-title', 'Department & Team Dashboard')

@section('top-actions')
    <button class="btn btn-primary" onclick="openModal('create-task-modal')">
        <span>+ Delegate Task</span>
    </button>
@endsection

@section('content')
    <!-- Department Metrics Strip -->
    <div class="stats-grid">
        <div class="stat-card">
            <span class="stat-label">Department Tasks</span>
            <div class="stat-value" style="color: #38bdf8;">{{ $stats['total_tasks'] }}</div>
        </div>

        <div class="stat-card">
            <span class="stat-label">Pending Review</span>
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
            <span class="stat-label">Past Due</span>
            <div class="stat-value" style="color: #fb7185;">{{ $stats['overdue_tasks'] }}</div>
        </div>
    </div>

    <!-- 2 Column Section: Left Team Members, Right Recent Tasks -->
    <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 1.5rem; align-items: start;">
        
        <!-- Left: Team Workload Allocation -->
        <div class="card">
            <h2 style="font-size: 1.15rem; font-weight: 700; margin-bottom: 1.25rem;">Team Member Workload</h2>

            <div style="display: flex; flex-direction: column; gap: 1rem;">
                @forelse($teamMembers as $employee)
                    <div style="padding: 1rem; background: rgba(0, 0, 0, 0.2); border: 1px solid var(--border); border-radius: var(--radius-md);">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                            <div style="font-weight: 700; font-size: 0.95rem;">{{ $employee->name }}</div>
                            <span class="badge badge-medium">{{ $employee->total_tasks }} Tasks</span>
                        </div>
                        <div style="font-size: 0.78rem; color: var(--text-dim); margin-bottom: 0.65rem;">{{ $employee->email }}</div>
                        <div style="display: flex; gap: 0.4rem; font-size: 0.75rem;">
                            <span class="badge badge-pending">{{ $employee->pending_tasks }} Pending</span>
                            <span class="badge badge-in-progress">{{ $employee->in_progress_tasks }} Active</span>
                            <span class="badge badge-completed">{{ $employee->completed_tasks }} Done</span>
                        </div>
                    </div>
                @empty
                    <div style="text-align: center; color: var(--text-dim); padding: 1.5rem;">No employees registered yet.</div>
                @endforelse
            </div>
        </div>

        <!-- Right: Recent Tasks -->
        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
                <h2 style="font-size: 1.15rem; font-weight: 700;">Department Task Queue</h2>
                <a href="{{ route('manager.tasks') }}" class="btn btn-secondary btn-sm">Manage All</a>
            </div>

            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Task</th>
                            <th>Priority</th>
                            <th>Status</th>
                            <th>Assigned Employee</th>
                            <th>Due Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentTasks as $task)
                            <tr>
                                <td>
                                    <div style="font-weight: 700;">{{ $task->title }}</div>
                                    <div style="font-size: 0.78rem; color: var(--text-dim);">Created by {{ $task->creator?->name ?? 'Manager' }}</div>
                                </td>
                                <td><span class="badge badge-{{ $task->priority }}">{{ $task->priority }}</span></td>
                                <td><span class="badge badge-{{ $task->status }}">{{ $task->status }}</span></td>
                                <td>{{ $task->assignedTo?->name ?? 'Unassigned' }}</td>
                                <td>
                                    {{ $task->due_date?->format('Y-m-d') ?? '-' }}
                                    @if($task->due_date && $task->due_date->isPast() && $task->status !== 'completed')
                                        <span class="badge badge-high" style="font-size: 0.65rem;">Overdue</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="text-align: center; color: var(--text-dim); padding: 2rem;">No department tasks yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- Create Task Modal -->
    <div class="modal" id="create-task-modal">
        <div class="modal-dialog">
            <div class="modal-header">
                <h3 style="font-size: 1.15rem; font-weight: 700;">Delegate New Task</h3>
                <button type="button" onclick="closeModal('create-task-modal')" style="background:none; border:none; color:var(--text-muted); font-size:1.4rem; cursor:pointer;">&times;</button>
            </div>
            <form action="{{ route('manager.tasks.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label" for="task-title">Title *</label>
                        <input type="text" id="task-title" name="title" class="form-control" required placeholder="e.g. Build Frontend Component">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="task-desc">Description</label>
                        <textarea id="task-desc" name="description" class="form-control" rows="3" placeholder="Provide instructions..."></textarea>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="task-priority">Priority *</label>
                            <select id="task-priority" name="priority" class="form-control" required>
                                <option value="low">Low</option>
                                <option value="medium" selected>Medium</option>
                                <option value="high">High</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="task-status">Status *</label>
                            <select id="task-status" name="status" class="form-control" required>
                                <option value="pending" selected>Pending</option>
                                <option value="in-progress">In Progress</option>
                                <option value="completed">Completed</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="task-due-date">Due Date *</label>
                            <input type="date" id="task-due-date" name="due_date" class="form-control" value="{{ now()->addDays(5)->format('Y-m-d') }}" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="task-assignee">Assign to Employee *</label>
                            <select id="task-assignee" name="assigned_to" class="form-control" required>
                                @foreach($employees as $e)
                                    <option value="{{ $e->id }}">{{ $e->name }} ({{ $e->email }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('create-task-modal')">Cancel</button>
                    <button type="submit" class="btn btn-primary">Assign Task</button>
                </div>
            </form>
        </div>
    </div>
@endsection
