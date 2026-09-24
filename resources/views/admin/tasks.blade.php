@extends('layouts.web')

@section('title', 'Manage All Tasks')
@section('page-title', 'Platform Task Governance')

@section('top-actions')
    <a href="{{ route('admin.tasks.export', request()->query()) }}" class="btn btn-secondary" title="Export current filtered view to CSV">
        <span>📊 Export CSV</span>
    </a>
    <button class="btn btn-primary" onclick="openModal('create-task-modal')">
        <span>+ New Task</span>
    </button>
@endsection

@section('content')
    <!-- Filter Control Card -->
    <div class="card" style="padding: 1.25rem;">
        <form action="{{ route('admin.tasks') }}" method="GET" style="display: flex; gap: 0.75rem; flex-wrap: wrap; align-items: center;">
            <!-- Search -->
            <div style="flex: 1; min-width: 220px;">
                <input type="text" name="search" class="form-control" placeholder="Search by title or description..." value="{{ request('search') }}">
            </div>

            <!-- Status Filter -->
            <div>
                <select name="status" class="form-control" onchange="this.form.submit()">
                    <option value="">All Statuses</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="in-progress" {{ request('status') === 'in-progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                </select>
            </div>

            <!-- Priority Filter -->
            <div>
                <select name="priority" class="form-control" onchange="this.form.submit()">
                    <option value="">All Priorities</option>
                    <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>High Priority</option>
                    <option value="medium" {{ request('priority') === 'medium' ? 'selected' : '' }}>Medium Priority</option>
                    <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>Low Priority</option>
                </select>
            </div>

            <!-- Assignee Filter -->
            <div>
                <select name="assigned_to" class="form-control" onchange="this.form.submit()">
                    <option value="">All Assignees</option>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}" {{ request('assigned_to') == $u->id ? 'selected' : '' }}>
                            {{ $u->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Sort By -->
            <div>
                <select name="sort_by" class="form-control" onchange="this.form.submit()">
                    <option value="created_at" {{ request('sort_by') === 'created_at' ? 'selected' : '' }}>Newest First</option>
                    <option value="due_date" {{ request('sort_by') === 'due_date' ? 'selected' : '' }}>Due Date</option>
                    <option value="priority" {{ request('sort_by') === 'priority' ? 'selected' : '' }}>Priority</option>
                    <option value="status" {{ request('sort_by') === 'status' ? 'selected' : '' }}>Status</option>
                    <option value="title" {{ request('sort_by') === 'title' ? 'selected' : '' }}>Title</option>
                </select>
            </div>

            <button type="submit" class="btn btn-secondary">Filter</button>
            @if(request()->anyFilled(['search', 'status', 'priority', 'assigned_to', 'sort_by']))
                <a href="{{ route('admin.tasks') }}" class="btn btn-secondary" style="color: #fb7185;">Reset</a>
            @endif
        </form>
    </div>

    <!-- Tasks Table Card -->
    <div class="card">
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Task Details</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Assignee</th>
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
                                <div style="font-size: 0.75rem; color: var(--text-dim); margin-top: 0.35rem;">
                                    Created by {{ $task->creator?->name ?? 'Admin' }} • {{ $task->created_at->diffForHumans() }}
                                </div>
                            </td>
                            <td><span class="badge badge-{{ $task->priority }}">{{ $task->priority }}</span></td>
                            <td><span class="badge badge-{{ $task->status }}">{{ $task->status }}</span></td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 0.4rem;">
                                    <span>👤</span>
                                    <span>{{ $task->assignedTo?->name ?? 'Unassigned' }}</span>
                                </div>
                            </td>
                            <td>
                                <div>{{ $task->due_date?->format('Y-m-d') ?? '-' }}</div>
                                @if($task->due_date && $task->due_date->isPast() && $task->status !== 'completed')
                                    <span class="badge badge-high" style="font-size: 0.68rem; margin-top: 0.2rem;">Overdue</span>
                                @endif
                            </td>
                            <td>
                                <div style="display: flex; gap: 0.4rem; align-items: center;">
                                    <button type="button" class="btn btn-secondary btn-sm" onclick="openTaskCommentsModal({{ $task->id }}, '{{ addslashes($task->title) }}')" title="Discussion & Notes">
                                        <span>💬 <span id="task-comment-count-{{ $task->id }}">{{ $task->comments_count ?? 0 }}</span></span>
                                    </button>
                                    <button class="btn btn-secondary btn-sm" onclick="openEditTaskModal({{ json_encode($task) }})">
                                        ✏️ Edit
                                    </button>
                                    <form action="{{ route('admin.tasks.destroy', $task) }}" method="POST" onsubmit="return confirm('Permanently delete Task #{{ $task->id }}?');">
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
                                No tasks found matching your filters.
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
                <h3 style="font-size: 1.15rem; font-weight: 700;">Create New Task</h3>
                <button type="button" onclick="closeModal('create-task-modal')" style="background:none; border:none; color:var(--text-muted); font-size:1.4rem; cursor:pointer;">&times;</button>
            </div>
            <form action="{{ route('admin.tasks.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label" for="new-task-title">Task Title *</label>
                        <input type="text" id="new-task-title" name="title" class="form-control" required placeholder="e.g. Conduct Performance Auditing">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="new-task-desc">Description</label>
                        <textarea id="new-task-desc" name="description" class="form-control" rows="3" placeholder="Provide instructions..."></textarea>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="new-task-priority">Priority *</label>
                            <select id="new-task-priority" name="priority" class="form-control" required>
                                <option value="low">Low</option>
                                <option value="medium" selected>Medium</option>
                                <option value="high">High</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="new-task-status">Status *</label>
                            <select id="new-task-status" name="status" class="form-control" required>
                                <option value="pending" selected>Pending</option>
                                <option value="in-progress">In Progress</option>
                                <option value="completed">Completed</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="new-task-due">Due Date *</label>
                            <input type="date" id="new-task-due" name="due_date" class="form-control" value="{{ now()->addDays(5)->format('Y-m-d') }}" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="new-task-assignee">Assignee *</label>
                            <select id="new-task-assignee" name="assigned_to" class="form-control" required>
                                @foreach($users as $u)
                                    <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->roles->first()?->name ?? 'User' }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('create-task-modal')">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Task</button>
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
                        <label class="form-label" for="edit-task-title">Task Title *</label>
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
                                @foreach($users as $u)
                                    <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->roles->first()?->name ?? 'User' }})</option>
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

    <!-- Task Collaboration & Comments Modal -->
    @include('partials.task-comments-modal')
@endsection

@push('scripts')
    <script>
        function openEditTaskModal(task) {
            document.getElementById('edit-modal-title').textContent = `Edit Task #${task.id}`;
            document.getElementById('edit-task-form').action = `/admin/tasks/${task.id}`;
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
