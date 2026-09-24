<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'TaskFlow Platform') | Enterprise Task Governance</title>
    <meta name="description" content="Enterprise Multi-Page Task Management Platform with Role-Based Access Control.">

    <!-- PWA & Mobile Optimization -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#0b0f19">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="TaskFlow">
    <link rel="apple-touch-icon" href="/icons/icon.svg">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">

    <style>
        :root {
            --bg-base: #0b0f19;
            --bg-sidebar: #0e1322;
            --bg-surface: #111827;
            --bg-surface-elevated: #1a2234;
            --border: rgba(255, 255, 255, 0.08);
            --border-hover: rgba(99, 102, 241, 0.4);
            --primary: #6366f1;
            --primary-gradient: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #ec4899 100%);
            --accent-cyan: #06b6d4;
            --accent-emerald: #10b981;
            --accent-amber: #f59e0b;
            --accent-rose: #f43f5e;
            --text-main: #f9fafb;
            --text-muted: #9ca3af;
            --text-dim: #6b7280;
            --font-sans: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            --font-mono: 'JetBrains Mono', monospace;
            --radius-xl: 16px;
            --radius-lg: 12px;
            --radius-md: 8px;
            --radius-sm: 6px;
            --shadow-card: 0 10px 25px -5px rgba(0, 0, 0, 0.4);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            background-color: var(--bg-base);
            color: var(--text-main);
            font-family: var(--font-sans);
            min-height: 100vh;
            display: flex;
            background-image: 
                radial-gradient(circle at 10% 10%, rgba(99, 102, 241, 0.08) 0%, transparent 40%),
                radial-gradient(circle at 90% 20%, rgba(236, 72, 153, 0.06) 0%, transparent 45%);
            background-attachment: fixed;
        }

        /* Sidebar Navigation */
        .sidebar {
            width: 270px;
            background: var(--bg-sidebar);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            padding: 1.5rem 1.25rem;
            position: sticky;
            top: 0;
            height: 100vh;
            flex-shrink: 0;
        }

        /* Mobile Overlay & Navigation Drawer */
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(4px);
            z-index: 998;
        }

        .sidebar-overlay.active {
            display: block;
        }

        .mobile-menu-btn {
            display: none;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            color: var(--text-main);
            padding: 0.45rem 0.6rem;
            cursor: pointer;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }

        .mobile-menu-btn:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        .mobile-bottom-nav {
            display: none;
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 64px;
            background: rgba(14, 19, 34, 0.95);
            border-top: 1px solid var(--border);
            backdrop-filter: blur(16px);
            z-index: 990;
            justify-content: space-around;
            align-items: center;
            padding: 0 0.5rem;
        }

        .mobile-nav-link {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 0.2rem;
            color: var(--text-muted);
            text-decoration: none;
            font-size: 0.7rem;
            font-weight: 600;
            background: none;
            border: none;
            cursor: pointer;
            padding: 0.4rem 0.6rem;
            border-radius: var(--radius-sm);
            transition: all 0.2s ease;
        }

        .mobile-nav-link.active,
        .mobile-nav-link:hover {
            color: #818cf8;
        }

        .mobile-nav-link .nav-icon {
            font-size: 1.15rem;
        }

        @media (max-width: 900px) {
            body {
                flex-direction: column;
                padding-bottom: 64px;
            }

            .sidebar {
                position: fixed;
                left: -300px;
                top: 0;
                bottom: 0;
                width: 280px;
                z-index: 999;
                transition: left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                box-shadow: 10px 0 35px rgba(0, 0, 0, 0.8);
            }

            .sidebar.mobile-open {
                left: 0;
            }

            .mobile-menu-btn {
                display: flex;
            }

            .mobile-bottom-nav {
                display: flex;
            }

            .top-navbar {
                padding: 0.85rem 1rem;
            }

            .main-body {
                padding: 1rem;
            }
        }

        .brand-logo {
            display: flex;
            align-items: center;
            gap: 0.85rem;
            text-decoration: none;
            color: var(--text-main);
            margin-bottom: 2rem;
            padding: 0 0.5rem;
        }

        .brand-icon {
            width: 38px;
            height: 38px;
            border-radius: var(--radius-md);
            background: var(--primary-gradient);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            color: #fff;
            box-shadow: 0 4px 14px rgba(99, 102, 241, 0.4);
        }

        .brand-name {
            font-size: 1.2rem;
            font-weight: 800;
            letter-spacing: -0.02em;
        }

        .role-portal-tag {
            font-size: 0.72rem;
            text-transform: uppercase;
            font-weight: 800;
            letter-spacing: 0.05em;
            padding: 0.2rem 0.6rem;
            border-radius: 999px;
            display: inline-block;
            margin-top: 0.2rem;
        }

        .portal-admin { background: rgba(168, 85, 247, 0.15); color: #c084fc; border: 1px solid rgba(168, 85, 247, 0.3); }
        .portal-manager { background: rgba(14, 165, 233, 0.15); color: #38bdf8; border: 1px solid rgba(14, 165, 233, 0.3); }
        .portal-employee { background: rgba(16, 185, 129, 0.15); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.3); }

        .nav-section-title {
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            color: var(--text-dim);
            letter-spacing: 0.08em;
            margin: 1.25rem 0 0.5rem 0.5rem;
        }

        .nav-menu {
            display: flex;
            flex-direction: column;
            gap: 0.35rem;
            list-style: none;
            flex: 1;
        }

        .nav-item a {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.65rem 0.85rem;
            color: var(--text-muted);
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 600;
            border-radius: var(--radius-md);
            transition: all 0.2s ease;
        }

        .nav-item a:hover {
            color: var(--text-main);
            background: rgba(255, 255, 255, 0.05);
        }

        .nav-item.active a {
            background: var(--bg-surface-elevated);
            color: #ffffff;
            border: 1px solid var(--border);
        }

        .sidebar-footer {
            padding-top: 1.25rem;
            border-top: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .user-card {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.5rem;
        }

        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: var(--primary-gradient);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.85rem;
            font-weight: 700;
        }

        .user-info {
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .user-name {
            font-size: 0.88rem;
            font-weight: 700;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .user-email {
            font-size: 0.75rem;
            color: var(--text-dim);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Main Content Layout */
        .content-wrapper {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
            overflow-y: auto;
        }

        .top-navbar {
            padding: 1rem 2rem;
            border-bottom: 1px solid var(--border);
            background: rgba(17, 24, 39, 0.5);
            backdrop-filter: blur(12px);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }

        .page-header-title {
            font-size: 1.35rem;
            font-weight: 800;
            letter-spacing: -0.02em;
        }

        .top-actions {
            display: flex;
            align-items: center;
            gap: 0.85rem;
        }

        /* In-App Notifications Dropdown */
        .notification-btn-wrap {
            position: relative;
        }

        .notification-btn {
            position: relative;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            color: var(--text-main);
            width: 38px;
            height: 38px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .notification-btn:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: var(--border-hover);
        }

        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #ef4444;
            color: #ffffff;
            font-size: 0.65rem;
            font-weight: 800;
            padding: 0.15rem 0.4rem;
            border-radius: 999px;
            line-height: 1;
            box-shadow: 0 0 10px rgba(239, 68, 68, 0.7);
        }

        .notification-dropdown {
            display: none;
            position: absolute;
            right: 0;
            top: 46px;
            width: 340px;
            background: rgba(17, 24, 39, 0.98);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.75);
            backdrop-filter: blur(20px);
            z-index: 1000;
            overflow: hidden;
        }

        .notification-dropdown.active {
            display: block;
        }

        .notif-header {
            padding: 0.85rem 1.1rem;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.825rem;
            font-weight: 700;
        }

        .notif-body {
            max-height: 320px;
            overflow-y: auto;
        }

        .notif-item {
            padding: 0.85rem 1.1rem;
            border-bottom: 1px solid var(--border);
            font-size: 0.825rem;
            transition: background 0.15s ease;
        }

        .notif-item:hover {
            background: rgba(255, 255, 255, 0.04);
        }

        .notif-item.unread {
            background: rgba(99, 102, 241, 0.08);
        }

        .notif-footer {
            padding: 0.75rem 1.1rem;
            text-align: center;
            border-top: 1px solid var(--border);
            font-size: 0.8rem;
            background: rgba(0, 0, 0, 0.2);
        }

        .main-body {
            padding: 2rem;
            flex: 1;
        }

        /* Buttons & Utilities */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.6rem 1.15rem;
            border-radius: var(--radius-md);
            font-weight: 600;
            font-size: 0.875rem;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
            border: none;
        }

        .btn-primary {
            background: var(--primary-gradient);
            color: #ffffff;
            box-shadow: 0 4px 14px rgba(99, 102, 241, 0.35);
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(99, 102, 241, 0.45);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--border);
            color: var(--text-main);
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        .btn-danger {
            background: rgba(244, 63, 94, 0.15);
            border: 1px solid rgba(244, 63, 94, 0.3);
            color: #fb7185;
        }

        .btn-danger:hover {
            background: rgba(244, 63, 94, 0.25);
        }

        .btn-sm {
            padding: 0.35rem 0.75rem;
            font-size: 0.8rem;
        }

        /* Cards & Metrics */
        .card {
            background: var(--bg-surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-xl);
            padding: 1.5rem;
            box-shadow: var(--shadow-card);
            margin-bottom: 1.5rem;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.25rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: var(--bg-surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 1.25rem;
            display: flex;
            flex-direction: column;
            gap: 0.35rem;
        }

        .stat-label {
            font-size: 0.8rem;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 800;
            letter-spacing: -0.02em;
        }

        /* Tables */
        .table-responsive {
            overflow-x: auto;
            border-radius: var(--radius-lg);
            border: 1px solid var(--border);
            background: var(--bg-surface);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            font-size: 0.875rem;
        }

        thead {
            background: rgba(0, 0, 0, 0.3);
            border-bottom: 1px solid var(--border);
        }

        th {
            padding: 0.9rem 1.2rem;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
        }

        td {
            padding: 0.9rem 1.2rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.04);
        }

        tbody tr:hover {
            background: rgba(255, 255, 255, 0.02);
        }

        /* Priority & Status Badges */
        .badge {
            font-size: 0.72rem;
            font-weight: 700;
            padding: 0.2rem 0.55rem;
            border-radius: 4px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            display: inline-block;
        }

        .badge-high { background: rgba(244, 63, 94, 0.15); color: #fb7185; border: 1px solid rgba(244, 63, 94, 0.3); }
        .badge-medium { background: rgba(245, 158, 11, 0.15); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.3); }
        .badge-low { background: rgba(148, 163, 184, 0.15); color: #94a3b8; border: 1px solid rgba(148, 163, 184, 0.3); }

        .badge-pending { background: rgba(245, 158, 11, 0.15); color: #fbbf24; }
        .badge-in-progress { background: rgba(6, 182, 212, 0.15); color: #22d3ee; }
        .badge-completed { background: rgba(16, 185, 129, 0.15); color: #34d399; }

        /* Alerts */
        .alert {
            padding: 1rem 1.25rem;
            border-radius: var(--radius-md);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 0.9rem;
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.12);
            border: 1px solid rgba(16, 185, 129, 0.3);
            color: #34d399;
        }

        .alert-danger {
            background: rgba(244, 63, 94, 0.12);
            border: 1px solid rgba(244, 63, 94, 0.3);
            color: #fb7185;
        }

        /* Forms & Inputs */
        .form-group {
            margin-bottom: 1.15rem;
        }

        .form-label {
            display: block;
            font-size: 0.825rem;
            font-weight: 600;
            color: var(--text-muted);
            margin-bottom: 0.4rem;
        }

        .form-control {
            width: 100%;
            background: var(--bg-base);
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            padding: 0.65rem 0.85rem;
            color: var(--text-main);
            font-family: inherit;
            font-size: 0.875rem;
            outline: none;
            transition: border-color 0.2s ease;
        }

        .form-control:focus {
            border-color: var(--primary);
        }

        .input-password-wrap {
            position: relative;
            display: flex;
            align-items: center;
            width: 100%;
        }

        .input-password-wrap .form-control {
            padding-right: 2.75rem;
        }

        .btn-eye-toggle {
            position: absolute;
            right: 0.75rem;
            background: transparent;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            padding: 0.3rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: var(--radius-sm);
            transition: color 0.15s ease, background-color 0.15s ease;
        }

        .btn-eye-toggle:hover {
            color: #ffffff;
            background: rgba(255, 255, 255, 0.08);
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        /* Modals */
        .modal {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.75);
            backdrop-filter: blur(8px);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 100;
            padding: 1.5rem;
        }

        .modal.active {
            display: flex;
        }

        .modal-dialog {
            background: var(--bg-surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-xl);
            width: 100%;
            max-width: 580px;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.6);
        }

        .modal-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-body {
            padding: 1.5rem;
            max-height: 75vh;
            overflow-y: auto;
        }

        .modal-footer {
            padding: 1rem 1.5rem;
            border-top: 1px solid var(--border);
            display: flex;
            justify-content: flex-end;
            gap: 0.75rem;
            background: rgba(0, 0, 0, 0.15);
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>

    @php
        $user = auth()->user();
        $roleName = $user ? ($user->roles->first()?->name ?? 'employee') : 'guest';
    @endphp

    <!-- Sidebar Navigation -->
    <aside class="sidebar">
        <a href="{{ route('dashboard') }}" class="brand-logo">
            <div class="brand-icon">✓</div>
            <div>
                <div class="brand-name">TaskFlow</div>
                <span class="role-portal-tag portal-{{ $roleName }}">
                    {{ strtoupper($roleName) }} PORTAL
                </span>
            </div>
        </a>

        <div class="nav-section-title">Navigation</div>
        <ul class="nav-menu">
            @if($roleName === 'admin')
                <li class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <a href="{{ route('admin.dashboard') }}">
                        <span>📊</span>
                        <span>Overview</span>
                    </a>
                </li>
                <li class="nav-item {{ request()->routeIs('admin.tasks') ? 'active' : '' }}">
                    <a href="{{ route('admin.tasks') }}">
                        <span>📋</span>
                        <span>All Tasks</span>
                    </a>
                </li>
                <li class="nav-item {{ request()->routeIs('admin.users') ? 'active' : '' }}">
                    <a href="{{ route('admin.users') }}">
                        <span>👥</span>
                        <span>Team & Workload</span>
                    </a>
                </li>
            @elseif($roleName === 'manager')
                <li class="nav-item {{ request()->routeIs('manager.dashboard') ? 'active' : '' }}">
                    <a href="{{ route('manager.dashboard') }}">
                        <span>📊</span>
                        <span>Team Dashboard</span>
                    </a>
                </li>
                <li class="nav-item {{ request()->routeIs('manager.tasks') ? 'active' : '' }}">
                    <a href="{{ route('manager.tasks') }}">
                        <span>📋</span>
                        <span>Department Tasks</span>
                    </a>
                </li>
            @else
                <li class="nav-item {{ request()->routeIs('employee.dashboard') ? 'active' : '' }}">
                    <a href="{{ route('employee.dashboard') }}">
                        <span>📌</span>
                        <span>My Assigned Tasks</span>
                    </a>
                </li>
            @endif

            <div class="nav-section-title">Account & API</div>
            <li class="nav-item {{ request()->routeIs('profile') ? 'active' : '' }}">
                <a href="{{ route('profile') }}">
                    <span>👤</span>
                    <span>My Profile & Security</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('docs') }}" target="_blank">
                    <span>📖</span>
                    <span>Swagger Docs</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="/openapi.json" target="_blank">
                    <span>⚡</span>
                    <span>OpenAPI Spec</span>
                </a>
            </li>
        </ul>

        <div class="sidebar-footer">
            <div class="user-card">
                <div class="user-avatar">
                    {{ strtoupper(substr($user->name, 0, 2)) }}
                </div>
                <div class="user-info">
                    <span class="user-name">{{ $user->name }}</span>
                    <span class="user-email">{{ $user->email }}</span>
                </div>
            </div>

            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-secondary btn-sm" style="width: 100%; justify-content: center;">
                    <span>🚪 Log out</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- Sidebar Overlay for Mobile -->
    <div class="sidebar-overlay" id="sidebar-overlay" onclick="toggleMobileSidebar()"></div>

    <!-- Main Content Area -->
    <div class="content-wrapper">
        <header class="top-navbar">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <button type="button" class="mobile-menu-btn" onclick="toggleMobileSidebar()" aria-label="Toggle navigation menu">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                        <line x1="3" y1="12" x2="21" y2="12"></line>
                        <line x1="3" y1="6" x2="21" y2="6"></line>
                        <line x1="3" y1="18" x2="21" y2="18"></line>
                    </svg>
                </button>
                <h1 class="page-header-title">@yield('page-title', 'Dashboard')</h1>
            </div>
            <div class="top-actions">
                @php
                    $unreadCount = $user ? $user->unreadNotifications->count() : 0;
                    $recentNotifs = $user ? $user->notifications()->take(5)->get() : collect();
                @endphp

                <!-- In-App Notification Bell -->
                <div class="notification-btn-wrap" id="notification-wrapper">
                    <button type="button" class="notification-btn" onclick="toggleNotificationDropdown()" aria-label="Notifications" title="Notifications">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                            <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                        </svg>
                        @if($unreadCount > 0)
                            <span class="notification-badge">{{ $unreadCount }}</span>
                        @endif
                    </button>

                    <!-- Dropdown -->
                    <div class="notification-dropdown" id="notification-dropdown">
                        <div class="notif-header">
                            <span>Notifications ({{ $unreadCount }} unread)</span>
                            @if($unreadCount > 0)
                                <form action="{{ route('notifications.markAllRead') }}" method="POST" style="margin: 0;">
                                    @csrf
                                    <button type="submit" style="background: none; border: none; color: #818cf8; cursor: pointer; font-size: 0.75rem; font-weight: 600;">
                                        Mark all read
                                    </button>
                                </form>
                            @endif
                        </div>

                        <div class="notif-body">
                            @forelse($recentNotifs as $notif)
                                <div class="notif-item {{ $notif->read_at ? '' : 'unread' }}">
                                    <div style="font-weight: {{ $notif->read_at ? '400' : '700' }}; color: {{ $notif->read_at ? 'var(--text-muted)' : 'var(--text-main)' }};">
                                        {{ $notif->data['message'] ?? $notif->data['title'] ?? 'Task Alert' }}
                                    </div>
                                    <div style="font-size: 0.72rem; color: var(--text-dim); margin-top: 0.25rem;">
                                        {{ $notif->created_at->diffForHumans() }}
                                    </div>
                                </div>
                            @empty
                                <div style="padding: 1.5rem 1rem; text-align: center; color: var(--text-dim); font-size: 0.8rem;">
                                    No notifications right now
                                </div>
                            @endforelse
                        </div>

                        <div class="notif-footer">
                            <a href="{{ route('profile') }}" style="color: #818cf8; text-decoration: none; font-weight: 600;">
                                View All Notifications & Security ➔
                            </a>
                        </div>
                    </div>
                </div>

                @yield('top-actions')
            </div>
        </header>

        <main class="main-body">
            @if(session('status'))
                <div class="alert alert-success">
                    <span>✅</span>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <span>⚠️</span>
                    <div>
                        @foreach($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <!-- Mobile Bottom Navigation Bar -->
    <nav class="mobile-bottom-nav">
        @if($roleName === 'admin')
            <a href="{{ route('admin.dashboard') }}" class="mobile-nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <span class="nav-icon">📊</span>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('admin.tasks') }}" class="mobile-nav-link {{ request()->routeIs('admin.tasks') ? 'active' : '' }}">
                <span class="nav-icon">📋</span>
                <span>Tasks</span>
            </a>
            <a href="{{ route('admin.users') }}" class="mobile-nav-link {{ request()->routeIs('admin.users') ? 'active' : '' }}">
                <span class="nav-icon">👥</span>
                <span>Team</span>
            </a>
        @elseif($roleName === 'manager')
            <a href="{{ route('manager.dashboard') }}" class="mobile-nav-link {{ request()->routeIs('manager.dashboard') ? 'active' : '' }}">
                <span class="nav-icon">📊</span>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('manager.tasks') }}" class="mobile-nav-link {{ request()->routeIs('manager.tasks') ? 'active' : '' }}">
                <span class="nav-icon">📋</span>
                <span>Tasks</span>
            </a>
        @else
            <a href="{{ route('employee.dashboard') }}" class="mobile-nav-link {{ request()->routeIs('employee.dashboard') ? 'active' : '' }}">
                <span class="nav-icon">📌</span>
                <span>My Tasks</span>
            </a>
        @endif
        <a href="{{ route('profile') }}" class="mobile-nav-link {{ request()->routeIs('profile') ? 'active' : '' }}">
            <span class="nav-icon">👤</span>
            <span>Profile</span>
        </a>
        <button type="button" class="mobile-nav-link" onclick="toggleMobileSidebar()">
            <span class="nav-icon">☰</span>
            <span>Menu</span>
        </button>
    </nav>

    <!-- Scripts -->
    <script>
        function openModal(id) {
            document.getElementById(id).classList.add('active');
        }
        function closeModal(id) {
            document.getElementById(id).classList.remove('active');
        }
        function toggleMobileSidebar() {
            const sidebar = document.querySelector('.sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            if (sidebar && overlay) {
                sidebar.classList.toggle('mobile-open');
                overlay.classList.toggle('active');
            }
        }
        function toggleNotificationDropdown() {
            const dropdown = document.getElementById('notification-dropdown');
            if (dropdown) {
                dropdown.classList.toggle('active');
            }
        }

        // Close notification dropdown when clicking outside
        document.addEventListener('click', function(e) {
            const wrapper = document.getElementById('notification-wrapper');
            const dropdown = document.getElementById('notification-dropdown');
            if (wrapper && dropdown && !wrapper.contains(e.target)) {
                dropdown.classList.remove('active');
            }
        });

        function togglePassword(inputId, btn) {
            const input = document.getElementById(inputId);
            const eyeOpen = btn.querySelector('.eye-open');
            const eyeClosed = btn.querySelector('.eye-closed');

            if (input.type === 'password') {
                input.type = 'text';
                if (eyeOpen) eyeOpen.style.display = 'none';
                if (eyeClosed) eyeClosed.style.display = 'block';
                btn.setAttribute('aria-label', 'Hide password');
            } else {
                input.type = 'password';
                if (eyeOpen) eyeOpen.style.display = 'block';
                if (eyeClosed) eyeClosed.style.display = 'none';
                btn.setAttribute('aria-label', 'Show password');
            }
        }
    </script>
    @stack('scripts')
</body>
</html>
