@extends('layouts.web')

@section('title', 'Department Tasks')
@section('page-title', 'Department Task Board')

@section('top-actions')
    <button class="btn btn-primary" onclick="openModal('create-task-modal')">
        <span>+ Delegate Task</span>
    </button>
@endsection

@section('content')
    <!-- Filter Control Card -->
    <div class="card" style="padding: 1.25rem;">
        <form action="{{ route('manager.tasks') }}" method="GET" style="display: flex; gap: 0.75rem; flex-wrap: wrap; align-items: center;">
            <div style="flex: 1; min-width: 220px;">
                <input type="text" name="search" class="form-control" placeholder="Search tasks..." value="{{ request('search') }}">
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

            <div>
                <select name="assigned_to" class="form-control" onchange="this.form.submit()">
                    <option value="">All Employees</option>
                    @foreach($employees as $e)
                        <option value="{{ $e->id }}" {{ request('assigned_to') == $e->id ? 'selected' : '' }}>
                            {{ $e->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="btn btn-secondary">Filter</button>
            @if(request()->anyFilled(['search', 'status', 'priority', 'assigned_to']))
                <a href="{{ route('manager.tasks') }}" class="btn btn-secondary" style="color: #fb7185;">Reset</a>
            @endif
        </form>
    </div>

    <!-- Tasks Table Card -->
    <div class="card">
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Task</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Assigned Employee</th>
                        <th>Due Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tasks as $task)
                        <tr>
                            <td>
                                <div style="font-weight: 700; font-size: 0.95rem;">{{ $task->title }}</div>
                                <div style="font-size: 0.82rem; color: var(--text-muted); margin-top: 0.2rem;">
                                    {{ Str::limit($task->description, 80) }}
                                </div>
                            </td>
                            <td><span class="badge badge-{{ $task->priority }}">{{ $task->priority }}</span></td>
                            <td><span class="badge badge-{{ $task->status }}">{{ $task->status }}</span></td>
                            <td>👤 {{ $task->assignedTo?->name ?? 'Unassigned' }}</td>
                            <td>
                                <div>{{ $task->due_date?->format('Y-m-d') ?? '-' }}</div>
                                @if($task->due_date && $task->due_date->isPast() && $task->status !== 'completed')
                                    <span class="badge badge-high" style="font-size: 0.68rem; margin-top: 0.2rem;">Overdue</span>
                                @endif
                            </td>
                            <td>
                                <div style="display: flex; gap: 0.4rem;">
                                    <button class="btn btn-secondary btn-sm" onclick="openEditTaskModal({{ json_encode($task) }})">
                                        ✏️ Edit
                                    </button>
                                    <form action="{{ route('manager.tasks.destroy', $task) }}" method="POST" onsubmit="return confirm('Remove Task #{{ $task->id }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">🗑️</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; color: var(--text-dim); padding: 3rem;">
                                No department tasks found.
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
                        <input type="text" id="task-title" name="title" class="form-control" required placeholder="e.g. Write Unit Tests for Payment Flow">
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

    <!-- Edit Task Modal -->
    <div class="modal" id="edit-task-modal">
        <div class="modal-dialog">
            <div class="modal-header">
                <h3 style="font-size: 1.15rem; font-weight: 700;" id="edit-modal-title">Edit Task</h3>
                <button type="button" onclick="closeModal('edit-task-modal')" style="background:none; border:none; color:var(--text-muted); font-size:1.4rem; cursor:pointer;">&times;</button>
            </div>
            <form id="edit-task-form" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label" for="edit-task-title">Title *</label>
                        <input type="text" id="edit-task-title" name="title" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="edit-task-desc">Description</label>
                        <textarea id="edit-task-desc" name="description" class="form-control" rows="3"></textarea>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="edit-task-priority">Priority *</label>
                            <select id="edit-task-priority" name="priority" class="form-control" required>
                                <option value="low">Low</option>
                                <option value="medium">Medium</option>
                                <option value="high">High</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="edit-task-status">Status *</label>
                            <select id="edit-task-status" name="status" class="form-control" required>
                                <option value="pending">Pending</option>
                                <option value="in-progress">In Progress</option>
                                <option value="completed">Completed</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="edit-task-due">Due Date *</label>
                            <input type="date" id="edit-task-due" name="due_date" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="edit-task-assignee">Assignee *</label>
                            <select id="edit-task-assignee" name="assigned_to" class="form-control" required>
                                @foreach($employees as $e)
                                    <option value="{{ $e->id }}">{{ $e->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('edit-task-modal')">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Task</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function openEditTaskModal(task) {
            document.getElementById('edit-modal-title').textContent = `Edit Task #${task.id}`;
            document.getElementById('edit-task-form').action = `/manager/tasks/${task.id}`;
            document.getElementById('edit-task-title').value = task.title;
            document.getElementById('edit-task-desc').value = task.description || '';
            document.getElementById('edit-task-priority').value = task.priority;
            document.getElementById('edit-task-status').value = task.status;
            document.getElementById('edit-task-due').value = task.due_date ? task.due_date.substring(0, 10) : '';
            document.getElementById('edit-task-assignee').value = task.assigned_to;
            openModal('edit-task-modal');
        }
    </script>
@endpush
