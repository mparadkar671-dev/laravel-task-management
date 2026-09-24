@extends('layouts.web')

@section('title', 'Team & Role Governance')
@section('page-title', 'Team Members & Role Governance')

@section('top-actions')
    <button class="btn btn-primary" onclick="openModal('create-user-modal')">
        <span>+ Add Team Member</span>
    </button>
@endsection

@section('content')
    <div class="card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
            <div>
                <h2 style="font-size: 1.15rem; font-weight: 700;">Role Authorization & Workload Management</h2>
                <p style="font-size: 0.85rem; color: var(--text-muted); margin-top: 0.2rem;">
                    As the Administrator, you have full authority to appoint Managers, promote Employees, and balance team assignments.
                </p>
            </div>
            <span class="badge badge-medium" style="font-size: 0.8rem; padding: 0.35rem 0.75rem;">{{ $users->count() }} Registered Members</span>
        </div>

        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Member Details</th>
                        <th>Current Role</th>
                        <th>Manage Role & Rights</th>
                        <th>Workload</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                        @php
                            $role = $user->roles->first()?->name ?? 'None';
                            $isAdmin = ($role === 'admin');
                            $isManager = ($role === 'manager');
                            $isEmployee = ($role === 'employee');
                        @endphp
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center; gap: 0.75rem;">
                                    <div class="user-avatar" style="width: 34px; height: 34px; font-size: 0.78rem;">
                                        {{ strtoupper(substr($user->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <div style="font-weight: 700;">{{ $user->name }}</div>
                                        <div style="font-size: 0.78rem; color: var(--text-dim);">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>

                            <td>
                                <span class="role-portal-tag portal-{{ $role }}">
                                    {{ strtoupper($role) }}
                                </span>
                            </td>

                            <td>
                                @if($isAdmin)
                                    <span style="font-size: 0.8rem; color: #c084fc; font-weight: 700;">
                                        👑 Primary Administrator (Permanent)
                                    </span>
                                @elseif($isEmployee)
                                    <form action="{{ route('admin.users.updateRole', $user) }}" method="POST" style="display: inline;">
                                        @csrf
                                        <input type="hidden" name="role" value="manager">
                                        <button type="submit" class="btn btn-secondary btn-sm" style="border-color: rgba(14, 165, 233, 0.4); color: #38bdf8;" onclick="return confirm('Promote {{ $user->name }} to Manager? They will immediately receive team delegation and task management rights.');">
                                            <span>Promote to Manager ➔</span>
                                        </button>
                                    </form>
                                @elseif($isManager)
                                    <form action="{{ route('admin.users.updateRole', $user) }}" method="POST" style="display: inline;">
                                        @csrf
                                        <input type="hidden" name="role" value="employee">
                                        <button type="submit" class="btn btn-secondary btn-sm" style="border-color: rgba(245, 158, 11, 0.4); color: #fbbf24;" onclick="return confirm('Demote {{ $user->name }} back to Employee? Their access will be restricted to personally assigned tasks.');">
                                            <span>Demote to Employee</span>
                                        </button>
                                    </form>
                                @endif
                            </td>

                            <td>
                                <div style="display: flex; gap: 0.35rem; align-items: center; font-size: 0.78rem;">
                                    <span class="badge badge-pending">{{ $user->pending_tasks }} Pending</span>
                                    <span class="badge badge-in-progress">{{ $user->in_progress_tasks }} Active</span>
                                    <span class="badge badge-completed">{{ $user->completed_tasks }} Done</span>
                                    <span style="color: var(--text-dim); margin-left: 0.25rem;">({{ $user->total_assigned_tasks }} total)</span>
                                </div>
                            </td>

                            <td>
                                @if(!$isAdmin)
                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('Permanently remove {{ $user->name }} from the platform?');" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" title="Delete User">🗑️ Delete</button>
                                    </form>
                                @else
                                    <span style="color: var(--text-dim); font-size: 0.78rem;">Locked</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Create User Modal -->
    <div class="modal" id="create-user-modal">
        <div class="modal-dialog">
            <div class="modal-header">
                <h3 style="font-size: 1.15rem; font-weight: 700;">Add New Team Member</h3>
                <button type="button" onclick="closeModal('create-user-modal')" style="background:none; border:none; color:var(--text-muted); font-size:1.4rem; cursor:pointer;">&times;</button>
            </div>
            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label" for="member-name">Full Name *</label>
                        <input type="text" id="member-name" name="name" class="form-control" required placeholder="e.g. Jane Doe">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="member-email">Email Address *</label>
                        <input type="email" id="member-email" name="email" class="form-control" required placeholder="jane@company.com">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="member-role">Assign Role *</label>
                        <select id="member-role" name="role" class="form-control" required>
                            <option value="manager">💼 Manager (Team Overview & Task Delegation)</option>
                            <option value="employee" selected>🧑‍💻 Employee (Personal Assigned Tasks)</option>
                        </select>
                        <small style="color: var(--text-dim); font-size: 0.75rem; display: block; margin-top: 0.25rem;">
                            Note: You can add 1, 2, or multiple Managers, or promote employees later at any time.
                        </small>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="member-password">Initial Password *</label>
                        <input type="password" id="member-password" name="password" class="form-control" required placeholder="Min. 8 characters">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('create-user-modal')">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Member</button>
                </div>
            </form>
        </div>
    </div>
@endsection
