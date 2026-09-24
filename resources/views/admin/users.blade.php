@extends('layouts.web')

@section('title', 'Team Governance')
@section('page-title', 'Team & Workload Governance')

@section('content')
    <div class="card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <div>
                <h2 style="font-size: 1.15rem; font-weight: 700;">Platform Members & Task Distribution</h2>
                <p style="font-size: 0.85rem; color: var(--text-muted); margin-top: 0.2rem;">Track role allocations and assigned task volume across your organization.</p>
            </div>
            <span class="badge badge-medium" style="font-size: 0.8rem; padding: 0.35rem 0.75rem;">{{ $users->count() }} Total Users</span>
        </div>

        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Role</th>
                        <th>Total Assigned</th>
                        <th>Workload Breakdown</th>
                        <th>Member Since</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                        @php
                            $role = $user->roles->first()?->name ?? 'None';
                        @endphp
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center; gap: 0.75rem;">
                                    <div class="user-avatar" style="width: 32px; height: 32px; font-size: 0.75rem;">
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
                                <div style="font-weight: 700; font-size: 1.1rem;">
                                    {{ $user->total_assigned_tasks }}
                                </div>
                            </td>
                            <td>
                                <div style="display: flex; gap: 0.4rem; align-items: center;">
                                    <span class="badge badge-pending">{{ $user->pending_tasks }} Pending</span>
                                    <span class="badge badge-in-progress">{{ $user->in_progress_tasks }} In-Progress</span>
                                    <span class="badge badge-completed">{{ $user->completed_tasks }} Done</span>
                                </div>
                            </td>
                            <td>
                                <span style="font-size: 0.8rem; color: var(--text-muted);">
                                    {{ $user->created_at?->format('M d, Y') ?? 'N/A' }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
