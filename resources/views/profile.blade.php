@extends('layouts.web')

@section('title', 'My Profile & Security')
@section('page-title', 'Account Settings & Security')

@section('content')
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; align-items: start;">
        <!-- Left Column: Profile & Role Overview -->
        <div>
            <!-- Account Summary Card -->
            <div class="card" style="margin-bottom: 1.5rem;">
                <div style="display: flex; align-items: center; gap: 1.25rem; margin-bottom: 1.5rem;">
                    <div style="width: 64px; height: 64px; border-radius: var(--radius-md); background: var(--primary-gradient); display: flex; align-items: center; justify-content: center; font-size: 1.5rem; font-weight: 800; color: #fff; box-shadow: 0 4px 14px rgba(99, 102, 241, 0.4);">
                        {{ strtoupper(substr($user->name, 0, 2)) }}
                    </div>
                    <div>
                        <h2 style="font-size: 1.25rem; font-weight: 800;">{{ $user->name }}</h2>
                        <div style="color: var(--text-muted); font-size: 0.85rem; margin-top: 0.2rem;">{{ $user->email }}</div>
                        <div style="margin-top: 0.5rem; display: flex; gap: 0.5rem; align-items: center;">
                            @foreach($roles as $role)
                                <span class="badge badge-medium" style="text-transform: uppercase;">
                                    🛡️ {{ $role }}
                                </span>
                            @endforeach
                            <span style="font-size: 0.75rem; color: var(--text-dim);">Member since {{ $user->created_at->format('M Y') }}</span>
                        </div>
                    </div>
                </div>

                <div style="border-top: 1px solid var(--border); padding-top: 1rem;">
                    <h3 style="font-size: 0.85rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.75rem; letter-spacing: 0.05em;">
                        Role Permissions & Authorities
                    </h3>
                    <div style="display: flex; flex-wrap: wrap; gap: 0.4rem;">
                        @forelse($permissions as $perm)
                            <span style="background: rgba(255, 255, 255, 0.05); border: 1px solid var(--border); border-radius: 4px; padding: 0.2rem 0.5rem; font-size: 0.75rem; color: var(--text-muted);">
                                ✓ {{ $perm }}
                            </span>
                        @empty
                            <span style="font-size: 0.8rem; color: var(--text-dim);">Default role assignment active</span>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Edit Personal Details Card -->
            <div class="card">
                <h3 style="font-size: 1.05rem; font-weight: 700; margin-bottom: 0.5rem;">Update Personal Details</h3>
                <p style="font-size: 0.825rem; color: var(--text-muted); margin-bottom: 1.25rem;">
                    Keep your contact email and name current for task assignment alerts.
                </p>

                <form action="{{ route('profile.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label class="form-label" for="profile-name">Full Name *</label>
                        <input type="text" id="profile-name" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="profile-email">Work Email *</label>
                        <input type="email" id="profile-email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                    </div>

                    <div style="display: flex; justify-content: flex-end; margin-top: 1rem;">
                        <button type="submit" class="btn btn-primary">
                            <span>Save Profile</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Right Column: Change Password & Security -->
        <div>
            <!-- Change Password Card -->
            <div class="card" style="margin-bottom: 1.5rem;">
                <h3 style="font-size: 1.05rem; font-weight: 700; margin-bottom: 0.5rem;">Change Password</h3>
                <p style="font-size: 0.825rem; color: var(--text-muted); margin-bottom: 1.25rem;">
                    Ensure your account is using a strong password with at least 8 characters, numbers, and symbols.
                </p>

                <form action="{{ route('profile.password') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label class="form-label" for="current_password">Current Password *</label>
                        <div class="input-password-wrap">
                            <input type="password" id="current_password" name="current_password" class="form-control" required placeholder="Enter current password" autocomplete="current-password">
                            <button type="button" class="btn-eye-toggle" onclick="togglePassword('current_password', this)" aria-label="Toggle password visibility">
                                <svg class="eye-open" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                                <svg class="eye-closed" style="display:none;" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                                    <line x1="1" y1="1" x2="23" y2="23"></line>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="new_password">New Password *</label>
                        <div class="input-password-wrap">
                            <input type="password" id="new_password" name="password" class="form-control" required placeholder="Min. 8 characters" autocomplete="new-password">
                            <button type="button" class="btn-eye-toggle" onclick="togglePassword('new_password', this)" aria-label="Toggle password visibility">
                                <svg class="eye-open" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                                <svg class="eye-closed" style="display:none;" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                                    <line x1="1" y1="1" x2="23" y2="23"></line>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="new_password_confirmation">Confirm New Password *</label>
                        <div class="input-password-wrap">
                            <input type="password" id="new_password_confirmation" name="password_confirmation" class="form-control" required placeholder="Repeat new password" autocomplete="new-password">
                            <button type="button" class="btn-eye-toggle" onclick="togglePassword('new_password_confirmation', this)" aria-label="Toggle password visibility">
                                <svg class="eye-open" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                                <svg class="eye-closed" style="display:none;" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                                    <line x1="1" y1="1" x2="23" y2="23"></line>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div style="display: flex; justify-content: flex-end; margin-top: 1rem;">
                        <button type="submit" class="btn btn-primary">
                            <span>Update Password</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- In-App Notifications History -->
            <div class="card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                    <h3 style="font-size: 1.05rem; font-weight: 700;">Recent Notifications</h3>
                    @if(auth()->user()->unreadNotifications->count() > 0)
                        <form action="{{ route('notifications.markAllRead') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-secondary btn-sm" style="font-size: 0.75rem;">
                                Mark all as read
                            </button>
                        </form>
                    @endif
                </div>

                <div>
                    @forelse(auth()->user()->notifications()->take(5)->get() as $notification)
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.75rem 0; border-bottom: 1px solid var(--border); font-size: 0.85rem;">
                            <div>
                                <div style="font-weight: {{ $notification->read_at ? '400' : '700' }}; color: {{ $notification->read_at ? 'var(--text-muted)' : 'var(--text-main)' }};">
                                    {{ $notification->data['message'] ?? $notification->data['title'] ?? 'Task Alert' }}
                                </div>
                                <div style="font-size: 0.725rem; color: var(--text-dim); margin-top: 0.2rem;">
                                    {{ $notification->created_at->diffForHumans() }}
                                </div>
                            </div>
                            @if(!$notification->read_at)
                                <form action="{{ route('notifications.markRead', $notification->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-secondary btn-sm" style="font-size: 0.7rem; padding: 0.2rem 0.5rem;">
                                        Dismiss
                                    </button>
                                </form>
                            @endif
                        </div>
                    @empty
                        <div style="text-align: center; color: var(--text-dim); padding: 1.5rem 0; font-size: 0.85rem;">
                            No notifications yet. You'll be alerted when tasks are assigned or updated.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
