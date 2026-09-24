<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TaskFlow Enterprise | Task Management Platform</title>
    <meta name="description" content="Production-ready Task Management Workspace with Kanban Board, Table View, Audit Trails, and Role-Based Permissions.">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">

    <style>
        :root {
            --bg-base: #0b0f19;
            --bg-surface: #111827;
            --bg-surface-elevated: #1a2234;
            --bg-glass: rgba(17, 24, 39, 0.75);
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
            --radius-xl: 18px;
            --radius-lg: 12px;
            --radius-md: 8px;
            --radius-sm: 6px;
            --shadow-card: 0 10px 25px -5px rgba(0, 0, 0, 0.4), 0 8px 10px -6px rgba(0, 0, 0, 0.3);
            --shadow-glow: 0 0 20px rgba(99, 102, 241, 0.25);
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
            line-height: 1.5;
            min-height: 100vh;
            background-image: 
                radial-gradient(circle at 10% 10%, rgba(99, 102, 241, 0.12) 0%, transparent 40%),
                radial-gradient(circle at 90% 20%, rgba(236, 72, 153, 0.1) 0%, transparent 45%),
                radial-gradient(circle at 50% 90%, rgba(6, 182, 212, 0.08) 0%, transparent 50%);
            background-attachment: fixed;
            display: flex;
            flex-direction: column;
        }

        /* Top Persona Bar */
        .persona-bar {
            background: rgba(10, 14, 23, 0.9);
            border-bottom: 1px solid var(--border);
            padding: 0.5rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 0.85rem;
            flex-wrap: wrap;
            gap: 0.5rem;
            position: sticky;
            top: 0;
            z-index: 50;
            backdrop-filter: blur(12px);
        }

        .persona-label {
            color: var(--text-muted);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .persona-selector {
            display: flex;
            gap: 0.4rem;
            align-items: center;
            flex-wrap: wrap;
        }

        .persona-btn {
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid var(--border);
            color: var(--text-muted);
            padding: 0.3rem 0.75rem;
            border-radius: var(--radius-sm);
            font-size: 0.785rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
        }

        .persona-btn:hover {
            background: rgba(255, 255, 255, 0.1);
            color: var(--text-main);
        }

        .persona-btn.active {
            background: var(--primary);
            border-color: var(--primary);
            color: #ffffff;
            box-shadow: 0 0 12px rgba(99, 102, 241, 0.4);
        }

        /* Main Navigation */
        .app-header {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid var(--border);
            background: var(--bg-glass);
            backdrop-filter: blur(16px);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1.5rem;
            flex-wrap: wrap;
        }

        .brand-container {
            display: flex;
            align-items: center;
            gap: 0.85rem;
            text-decoration: none;
            color: var(--text-main);
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
            font-size: 1.25rem;
            font-weight: 800;
            letter-spacing: -0.02em;
        }

        .api-pulse {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.25rem 0.65rem;
            border-radius: 999px;
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.25);
            font-size: 0.75rem;
            font-weight: 600;
            color: #34d399;
        }

        .pulse-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background-color: var(--accent-emerald);
            box-shadow: 0 0 8px var(--accent-emerald);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.4; transform: scale(0.8); }
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .nav-link-btn {
            color: var(--text-muted);
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.5rem 0.85rem;
            border-radius: var(--radius-md);
            transition: all 0.2s ease;
        }

        .nav-link-btn:hover {
            color: var(--text-main);
            background: rgba(255, 255, 255, 0.05);
        }

        .user-profile-chip {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            background: var(--bg-surface-elevated);
            border: 1px solid var(--border);
            padding: 0.35rem 0.85rem 0.35rem 0.45rem;
            border-radius: 999px;
        }

        .user-avatar {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: var(--primary-gradient);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: 700;
        }

        .user-info-text {
            display: flex;
            flex-direction: column;
            line-height: 1.2;
        }

        .user-name-text {
            font-size: 0.85rem;
            font-weight: 700;
        }

        .role-badge {
            font-size: 0.68rem;
            text-transform: uppercase;
            font-weight: 800;
            letter-spacing: 0.04em;
        }

        .role-admin { color: #c084fc; }
        .role-manager { color: #60a5fa; }
        .role-employee { color: #34d399; }

        /* Main Workspace Container */
        .workspace {
            max-width: 1440px;
            margin: 0 auto;
            padding: 2rem 1.5rem;
            width: 100%;
            flex: 1;
        }

        /* Analytics Stats Strip */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
            gap: 1.25rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: var(--bg-glass);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 1.25rem;
            position: relative;
            overflow: hidden;
            backdrop-filter: blur(12px);
            transition: all 0.25s ease;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            border-color: var(--border-hover);
        }

        .stat-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            font-size: 0.825rem;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .stat-icon {
            width: 32px;
            height: 32px;
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 800;
            letter-spacing: -0.02em;
        }

        .stat-footer {
            margin-top: 0.35rem;
            font-size: 0.75rem;
            color: var(--text-dim);
        }

        .icon-total { background: rgba(99, 102, 241, 0.15); color: #818cf8; }
        .icon-pending { background: rgba(245, 158, 11, 0.15); color: #fbbf24; }
        .icon-progress { background: rgba(6, 182, 212, 0.15); color: #22d3ee; }
        .icon-completed { background: rgba(16, 185, 129, 0.15); color: #34d399; }
        .icon-overdue { background: rgba(244, 63, 94, 0.15); color: #fb7185; }

        .stat-card.overdue-alert {
            border-color: rgba(244, 63, 94, 0.4);
            background: rgba(244, 63, 94, 0.05);
        }

        /* Controls & Filters Bar */
        .controls-bar {
            background: var(--bg-surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 1rem 1.25rem;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .search-box {
            position: relative;
            flex: 1;
            min-width: 240px;
        }

        .search-icon {
            position: absolute;
            left: 0.85rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-dim);
            pointer-events: none;
        }

        .search-input {
            width: 100%;
            background: var(--bg-base);
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            padding: 0.65rem 0.85rem 0.65rem 2.4rem;
            color: var(--text-main);
            font-size: 0.875rem;
            outline: none;
            transition: all 0.2s ease;
        }

        .search-input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.2);
        }

        .filter-group {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            flex-wrap: wrap;
        }

        .select-filter {
            background: var(--bg-base);
            border: 1px solid var(--border);
            color: var(--text-main);
            padding: 0.65rem 1rem;
            border-radius: var(--radius-md);
            font-size: 0.85rem;
            outline: none;
            cursor: pointer;
            transition: border-color 0.2s ease;
        }

        .select-filter:focus {
            border-color: var(--primary);
        }

        .view-switchers {
            display: flex;
            background: var(--bg-base);
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            padding: 3px;
        }

        .view-btn {
            background: transparent;
            border: none;
            color: var(--text-muted);
            padding: 0.4rem 0.85rem;
            border-radius: var(--radius-sm);
            font-size: 0.825rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        .view-btn.active {
            background: var(--bg-surface-elevated);
            color: var(--text-main);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.65rem 1.25rem;
            border-radius: var(--radius-md);
            font-weight: 600;
            font-size: 0.875rem;
            cursor: pointer;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
            border: none;
            text-decoration: none;
        }

        .btn-primary {
            background: var(--primary-gradient);
            color: #ffffff;
            box-shadow: 0 4px 14px rgba(99, 102, 241, 0.35);
        }

        .btn-primary:hover:not(:disabled) {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(99, 102, 241, 0.5);
        }

        .btn-primary:disabled {
            opacity: 0.45;
            cursor: not-allowed;
            transform: none !important;
            box-shadow: none !important;
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

        /* Kanban Board */
        .kanban-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.5rem;
            align-items: start;
        }

        @media (max-width: 1024px) {
            .kanban-grid {
                grid-template-columns: 1fr;
            }
        }

        .kanban-column {
            background: rgba(17, 24, 39, 0.6);
            border: 1px solid var(--border);
            border-radius: var(--radius-xl);
            padding: 1.25rem;
            min-height: 500px;
            display: flex;
            flex-direction: column;
            backdrop-filter: blur(10px);
        }

        .column-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.25rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid var(--border);
        }

        .column-title-wrap {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            font-weight: 700;
            font-size: 0.95rem;
        }

        .col-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
        }

        .dot-pending { background-color: var(--accent-amber); box-shadow: 0 0 8px var(--accent-amber); }
        .dot-progress { background-color: var(--accent-cyan); box-shadow: 0 0 8px var(--accent-cyan); }
        .dot-completed { background-color: var(--accent-emerald); box-shadow: 0 0 8px var(--accent-emerald); }

        .column-badge {
            background: rgba(255, 255, 255, 0.06);
            padding: 0.2rem 0.55rem;
            border-radius: 999px;
            font-size: 0.75rem;
            font-weight: 700;
            color: var(--text-muted);
        }

        .cards-container {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            flex: 1;
        }

        /* Task Card */
        .task-card {
            background: var(--bg-surface-elevated);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 1.15rem;
            box-shadow: var(--shadow-card);
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
        }

        .task-card:hover {
            transform: translateY(-3px);
            border-color: var(--border-hover);
            box-shadow: 0 12px 24px -6px rgba(0, 0, 0, 0.5);
        }

        .card-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 0.65rem;
            gap: 0.5rem;
        }

        .priority-badge {
            font-size: 0.7rem;
            font-weight: 700;
            padding: 0.2rem 0.55rem;
            border-radius: 4px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .priority-high { background: rgba(244, 63, 94, 0.15); color: #fb7185; border: 1px solid rgba(244, 63, 94, 0.3); }
        .priority-medium { background: rgba(245, 158, 11, 0.15); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.3); }
        .priority-low { background: rgba(148, 163, 184, 0.15); color: #94a3b8; border: 1px solid rgba(148, 163, 184, 0.3); }

        .overdue-tag {
            font-size: 0.7rem;
            font-weight: 700;
            padding: 0.2rem 0.5rem;
            border-radius: 4px;
            background: rgba(239, 68, 68, 0.2);
            color: #f87171;
            border: 1px solid rgba(239, 68, 68, 0.4);
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            animation: pulse 2s infinite;
        }

        .card-title {
            font-size: 1rem;
            font-weight: 700;
            margin-bottom: 0.45rem;
            line-height: 1.35;
            color: var(--text-main);
        }

        .card-desc {
            font-size: 0.825rem;
            color: var(--text-muted);
            margin-bottom: 0.85rem;
            line-height: 1.5;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .card-meta {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 0.785rem;
            color: var(--text-dim);
            padding-top: 0.75rem;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            margin-top: 0.65rem;
        }

        .card-user {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            color: var(--text-muted);
        }

        .mini-avatar {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: var(--primary);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.65rem;
            font-weight: 700;
        }

        .card-actions {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            margin-top: 0.75rem;
        }

        .action-icon-btn {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--border);
            color: var(--text-muted);
            padding: 0.3rem 0.6rem;
            border-radius: var(--radius-sm);
            font-size: 0.75rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
        }

        .action-icon-btn:hover {
            background: rgba(255, 255, 255, 0.1);
            color: var(--text-main);
        }

        .quick-status-select {
            background: var(--bg-base);
            border: 1px solid var(--border);
            color: var(--text-muted);
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
            border-radius: var(--radius-sm);
            outline: none;
            cursor: pointer;
            margin-left: auto;
        }

        /* Table View */
        .table-view-container {
            background: var(--bg-surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-xl);
            overflow: hidden;
            display: none;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            font-size: 0.875rem;
        }

        thead {
            background: rgba(0, 0, 0, 0.25);
            border-bottom: 1px solid var(--border);
        }

        th {
            padding: 1rem 1.25rem;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
        }

        td {
            padding: 1rem 1.25rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.04);
            color: var(--text-main);
        }

        tbody tr:hover {
            background: rgba(255, 255, 255, 0.02);
        }

        /* Modals */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.75);
            backdrop-filter: blur(8px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 100;
            opacity: 0;
            pointer-events: none;
            transition: all 0.25s ease;
            padding: 1.5rem;
        }

        .modal-overlay.active {
            opacity: 1;
            pointer-events: auto;
        }

        .modal-card {
            background: var(--bg-surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-xl);
            width: 100%;
            max-width: 580px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.6);
            overflow: hidden;
            transform: scale(0.95);
            transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .modal-overlay.active .modal-card {
            transform: scale(1);
        }

        .modal-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .modal-title {
            font-size: 1.15rem;
            font-weight: 700;
        }

        .modal-close-btn {
            background: transparent;
            border: none;
            color: var(--text-dim);
            font-size: 1.25rem;
            cursor: pointer;
            padding: 0.25rem;
            line-height: 1;
        }

        .modal-close-btn:hover {
            color: var(--text-main);
        }

        .modal-body {
            padding: 1.5rem;
            max-height: 75vh;
            overflow-y: auto;
        }

        .form-group {
            margin-bottom: 1.2rem;
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
            font-size: 0.9rem;
            outline: none;
            transition: border-color 0.2s ease;
        }

        .form-control:focus {
            border-color: var(--primary);
        }

        .form-control:disabled {
            background: rgba(255, 255, 255, 0.03);
            color: var(--text-dim);
            cursor: not-allowed;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .modal-footer {
            padding: 1rem 1.5rem;
            border-top: 1px solid var(--border);
            display: flex;
            justify-content: flex-end;
            gap: 0.75rem;
            background: rgba(0, 0, 0, 0.15);
        }

        /* Audit Timeline */
        .timeline {
            position: relative;
            padding-left: 1.5rem;
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 5px;
            top: 6px;
            bottom: 6px;
            width: 2px;
            background: var(--border);
        }

        .timeline-item {
            position: relative;
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            left: -1.5rem;
            top: 6px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: var(--primary);
            border: 2px solid var(--bg-surface);
        }

        .timeline-time {
            font-size: 0.75rem;
            color: var(--text-dim);
            font-family: var(--font-mono);
        }

        .timeline-content {
            font-size: 0.875rem;
            margin-top: 0.2rem;
        }

        /* Toast Container */
        .toast-container {
            position: fixed;
            bottom: 1.5rem;
            right: 1.5rem;
            display: flex;
            flex-direction: column;
            gap: 0.65rem;
            z-index: 200;
        }

        .toast {
            background: rgba(17, 24, 39, 0.95);
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            padding: 0.85rem 1.25rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 0.875rem;
            backdrop-filter: blur(12px);
            animation: slideIn 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            max-width: 380px;
        }

        .toast-success { border-left: 4px solid var(--accent-emerald); }
        .toast-error { border-left: 4px solid var(--accent-rose); }
        .toast-info { border-left: 4px solid var(--primary); }

        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }

        /* Empty state */
        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            color: var(--text-dim);
        }

        .empty-icon {
            font-size: 2.5rem;
            margin-bottom: 0.75rem;
            opacity: 0.6;
        }
    </style>
</head>
<body>

    <!-- Quick Persona Bar -->
    <div class="persona-bar">
        <div class="persona-label">
            <span>🎭 Quick Switch Role & Persona:</span>
        </div>
        <div class="persona-selector">
            <button class="persona-btn active" id="btn-persona-admin" onclick="switchPersona('admin')">
                <span>👑 Admin User</span>
            </button>
            <button class="persona-btn" id="btn-persona-manager" onclick="switchPersona('manager')">
                <span>💼 Manager User</span>
            </button>
            <button class="persona-btn" id="btn-persona-emp1" onclick="switchPersona('employee1')">
                <span>🧑‍💻 Employee One</span>
            </button>
            <button class="persona-btn" id="btn-persona-emp2" onclick="switchPersona('employee2')">
                <span>👩‍💻 Employee Two</span>
            </button>
        </div>
    </div>

    <!-- Application Header -->
    <header class="app-header">
        <div style="display: flex; align-items: center; gap: 1rem;">
            <a href="/" class="brand-container">
                <div class="brand-icon">✓</div>
                <div>
                    <div class="brand-name">TaskFlow Enterprise</div>
                </div>
            </a>
            <div class="api-pulse">
                <span class="pulse-dot"></span>
                <span>API Online (v1)</span>
            </div>
        </div>

        <div class="header-actions">
            <a href="/docs" class="nav-link-btn" target="_blank" rel="noopener">
                <span>📖 Swagger UI</span>
            </a>
            <a href="/openapi.json" class="nav-link-btn" target="_blank" rel="noopener">
                <span>⚡ OpenAPI 3.0</span>
            </a>
            <div class="user-profile-chip" id="profile-chip">
                <div class="user-avatar" id="avatar-initials">AU</div>
                <div class="user-info-text">
                    <span class="user-name-text" id="display-user-name">Admin User</span>
                    <span class="role-badge role-admin" id="display-user-role">ADMIN</span>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Workspace -->
    <main class="workspace">
        
        <!-- Live Analytics Stats Strip -->
        <section class="stats-grid" id="stats-section">
            <div class="stat-card">
                <div class="stat-header">
                    <span class="stat-label">Total Tasks</span>
                    <div class="stat-icon icon-total">📊</div>
                </div>
                <div class="stat-value" id="stat-total">-</div>
                <div class="stat-footer">Role-Scoped Visibility</div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <span class="stat-label">Pending</span>
                    <div class="stat-icon icon-pending">⏳</div>
                </div>
                <div class="stat-value" id="stat-pending">-</div>
                <div class="stat-footer">Awaiting Action</div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <span class="stat-label">In Progress</span>
                    <div class="stat-icon icon-progress">🚀</div>
                </div>
                <div class="stat-value" id="stat-progress">-</div>
                <div class="stat-footer">Active Development</div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <span class="stat-label">Completed</span>
                    <div class="stat-icon icon-completed">✅</div>
                </div>
                <div class="stat-value" id="stat-completed">-</div>
                <div class="stat-footer">Successfully Delivered</div>
            </div>

            <div class="stat-card" id="overdue-card">
                <div class="stat-header">
                    <span class="stat-label">Overdue Tasks</span>
                    <div class="stat-icon icon-overdue">⚠️</div>
                </div>
                <div class="stat-value" id="stat-overdue">-</div>
                <div class="stat-footer">Past Due Date</div>
            </div>
        </section>

        <!-- Controls & Filter Bar -->
        <section class="controls-bar">
            <!-- Search -->
            <div class="search-box">
                <svg class="search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input type="text" id="search-input" class="search-input" placeholder="Search tasks by title or keyword..." oninput="debounceSearch()">
            </div>

            <!-- Priority Filter -->
            <div class="filter-group">
                <select id="filter-priority" class="select-filter" onchange="fetchTasks()">
                    <option value="">All Priorities</option>
                    <option value="high">High Priority</option>
                    <option value="medium">Medium Priority</option>
                    <option value="low">Low Priority</option>
                </select>

                <!-- Sorting -->
                <select id="sort-by" class="select-filter" onchange="fetchTasks()">
                    <option value="due_date">Sort: Due Date</option>
                    <option value="priority">Sort: Priority</option>
                    <option value="status">Sort: Status</option>
                    <option value="title">Sort: Title</option>
                    <option value="created_at">Sort: Created Date</option>
                </select>

                <!-- View Switcher -->
                <div class="view-switchers">
                    <button class="view-btn active" id="btn-kanban" onclick="setViewMode('kanban')">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="18" rx="1"/><rect x="14" y="3" width="7" height="18" rx="1"/></svg>
                        <span>Kanban</span>
                    </button>
                    <button class="view-btn" id="btn-table" onclick="setViewMode('table')">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
                        <span>Table</span>
                    </button>
                </div>

                <!-- Create Task Button -->
                <button class="btn btn-primary" id="btn-create-task" onclick="openCreateModal()">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    <span>New Task</span>
                </button>
            </div>
        </section>

        <!-- View 1: Kanban Board View -->
        <section class="kanban-grid" id="kanban-view">
            <!-- Column 1: Pending -->
            <div class="kanban-column">
                <div class="column-header">
                    <div class="column-title-wrap">
                        <span class="col-dot dot-pending"></span>
                        <span>Pending</span>
                    </div>
                    <span class="column-badge" id="badge-pending-count">0</span>
                </div>
                <div class="cards-container" id="column-pending"></div>
            </div>

            <!-- Column 2: In Progress -->
            <div class="kanban-column">
                <div class="column-header">
                    <div class="column-title-wrap">
                        <span class="col-dot dot-progress"></span>
                        <span>In Progress</span>
                    </div>
                    <span class="column-badge" id="badge-progress-count">0</span>
                </div>
                <div class="cards-container" id="column-in-progress"></div>
            </div>

            <!-- Column 3: Completed -->
            <div class="kanban-column">
                <div class="column-header">
                    <div class="column-title-wrap">
                        <span class="col-dot dot-completed"></span>
                        <span>Completed</span>
                    </div>
                    <span class="column-badge" id="badge-completed-count">0</span>
                </div>
                <div class="cards-container" id="column-completed"></div>
            </div>
        </section>

        <!-- View 2: Data Table View -->
        <section class="table-view-container" id="table-view">
            <table>
                <thead>
                    <tr>
                        <th>Title & Description</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Assignee</th>
                        <th>Due Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="tasks-table-body">
                    <!-- Populated dynamically via JS -->
                </tbody>
            </table>
        </section>

    </main>

    <!-- Create / Edit Task Modal -->
    <div class="modal-overlay" id="task-modal">
        <div class="modal-card">
            <div class="modal-header">
                <h3 class="modal-title" id="modal-task-title">Create New Task</h3>
                <button class="modal-close-btn" onclick="closeModal('task-modal')">&times;</button>
            </div>
            <form id="task-form" onsubmit="handleTaskFormSubmit(event)">
                <div class="modal-body">
                    <input type="hidden" id="form-task-id">

                    <div class="form-group">
                        <label class="form-label" for="task-title-input">Task Title *</label>
                        <input type="text" id="task-title-input" class="form-control" required placeholder="e.g. Integrate Stripe Webhooks">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="task-desc-input">Description</label>
                        <textarea id="task-desc-input" class="form-control" rows="3" placeholder="Provide detailed instructions or acceptance criteria..."></textarea>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="task-priority-input">Priority *</label>
                            <select id="task-priority-input" class="form-control" required>
                                <option value="low">Low</option>
                                <option value="medium" selected>Medium</option>
                                <option value="high">High</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="task-status-input">Status *</label>
                            <select id="task-status-input" class="form-control" required>
                                <option value="pending" selected>Pending</option>
                                <option value="in-progress">In Progress</option>
                                <option value="completed">Completed</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="task-due-date-input">Due Date *</label>
                            <input type="date" id="task-due-date-input" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="task-assignee-input">Assignee *</label>
                            <select id="task-assignee-input" class="form-control" required>
                                <!-- Populated dynamically with users -->
                            </select>
                        </div>
                    </div>

                    <div id="employee-permission-notice" style="display:none; font-size: 0.8rem; color: #fbbf24; background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.25); padding: 0.6rem 0.85rem; border-radius: 6px;">
                        ℹ️ <strong>Employee Role Notice:</strong> You are authorized to update the <strong>Status</strong> of your assigned task. Other task details can only be altered by an Admin or Manager.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('task-modal')">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="btn-save-task">Save Task</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Task Audit History Modal -->
    <div class="modal-overlay" id="history-modal">
        <div class="modal-card">
            <div class="modal-header">
                <h3 class="modal-title">Task Status Audit Trail</h3>
                <button class="modal-close-btn" onclick="closeModal('history-modal')">&times;</button>
            </div>
            <div class="modal-body">
                <div id="history-timeline" class="timeline">
                    <!-- Populated dynamically via JS -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('history-modal')">Close</button>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal-overlay" id="delete-modal">
        <div class="modal-card" style="max-width: 440px;">
            <div class="modal-header">
                <h3 class="modal-title">Delete Task Confirmation</h3>
                <button class="modal-close-btn" onclick="closeModal('delete-modal')">&times;</button>
            </div>
            <div class="modal-body">
                <p style="color: var(--text-muted); font-size: 0.925rem;">Are you sure you want to permanently delete this task? This action cannot be reversed.</p>
                <input type="hidden" id="delete-task-id">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('delete-modal')">Cancel</button>
                <button type="button" class="btn btn-danger" onclick="executeDeleteTask()">Confirm Delete</button>
            </div>
        </div>
    </div>

    <!-- Floating Toast Notification System -->
    <div class="toast-container" id="toast-container"></div>

    <script>
        // Preset test accounts seeded in database
        const personas = {
            admin: { email: 'admin@example.com', password: 'password', name: 'Admin User', role: 'admin' },
            manager: { email: 'manager@example.com', password: 'password', name: 'Manager User', role: 'manager' },
            employee1: { email: 'employee1@example.com', password: 'password', name: 'Employee One', role: 'employee' },
            employee2: { email: 'employee2@example.com', password: 'password', name: 'Employee Two', role: 'employee' }
        };

        let currentPersonaKey = 'admin';
        let authToken = '';
        let currentUser = null;
        let cachedUsers = [];
        let cachedTasks = [];
        let viewMode = 'kanban';
        let searchTimeout = null;

        // Initialize Application
        document.addEventListener('DOMContentLoaded', async () => {
            await switchPersona('admin');
        });

        // Switch Persona & Re-authenticate
        async function switchPersona(key) {
            currentPersonaKey = key;
            const targetPersona = personas[key];

            // Update Persona Buttons UI
            document.querySelectorAll('.persona-btn').forEach(btn => btn.classList.remove('active'));
            const activeBtn = document.getElementById('btn-persona-' + (key === 'employee1' ? 'emp1' : (key === 'employee2' ? 'emp2' : key)));
            if (activeBtn) activeBtn.classList.add('active');

            showToast(`Authenticating as ${targetPersona.name}...`, 'info');

            try {
                // Execute Live Login via API
                const res = await fetch('/api/v1/login', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify({ email: targetPersona.email, password: targetPersona.password })
                });

                const data = await res.json();
                if (!res.ok) throw new Error(data.message || 'Authentication failed');

                authToken = data.data.token;
                currentUser = data.data.user;

                // Update Header User Chip
                updateHeaderProfile(targetPersona);

                // Fetch Users for assignment
                await fetchUsersList();

                // Fetch real-time statistics
                await fetchStatistics();

                // Fetch tasks for the current role
                await fetchTasks();

                showToast(`Switched persona to ${targetPersona.name} (${targetPersona.role.toUpperCase()})`, 'success');
            } catch (err) {
                showToast(`Auth error: ${err.message}`, 'error');
            }
        }

        function updateHeaderProfile(persona) {
            document.getElementById('display-user-name').textContent = persona.name;
            const initials = persona.name.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase();
            document.getElementById('avatar-initials').textContent = initials;

            const roleBadge = document.getElementById('display-user-role');
            roleBadge.textContent = persona.role.toUpperCase();
            roleBadge.className = `role-badge role-${persona.role}`;

            // Adjust Create Task button permission
            const createBtn = document.getElementById('btn-create-task');
            if (persona.role === 'employee') {
                createBtn.disabled = true;
                createBtn.title = 'Spatie RBAC: Only Admin & Manager roles can create tasks.';
            } else {
                createBtn.disabled = false;
                createBtn.title = 'Create a new task';
            }
        }

        // Fetch users for dropdown assignment
        async function fetchUsersList() {
            try {
                const res = await fetch('/api/v1/users', {
                    headers: { 'Authorization': `Bearer ${authToken}`, 'Accept': 'application/json' }
                });
                const json = await res.json();
                if (res.ok) {
                    cachedUsers = json.data || [];
                    populateAssigneeDropdown(cachedUsers);
                }
            } catch (err) {
                console.error('Failed to load users:', err);
            }
        }

        function populateAssigneeDropdown(users) {
            const select = document.getElementById('task-assignee-input');
            select.innerHTML = users.map(u => {
                const roleName = u.roles && u.roles.length ? ` (${u.roles[0].name})` : '';
                return `<option value="${u.id}">${u.name}${roleName}</option>`;
            }).join('');
        }

        // Fetch Real-time Task Statistics
        async function fetchStatistics() {
            try {
                const res = await fetch('/api/v1/tasks/statistics', {
                    headers: { 'Authorization': `Bearer ${authToken}`, 'Accept': 'application/json' }
                });
                const json = await res.json();
                if (res.ok && json.data) {
                    const stats = json.data;
                    document.getElementById('stat-total').textContent = stats.total_tasks;
                    document.getElementById('stat-pending').textContent = stats.pending_tasks;
                    document.getElementById('stat-progress').textContent = stats.in_progress_tasks;
                    document.getElementById('stat-completed').textContent = stats.completed_tasks;
                    document.getElementById('stat-overdue').textContent = stats.overdue_tasks;

                    const overdueCard = document.getElementById('overdue-card');
                    if (stats.overdue_tasks > 0) {
                        overdueCard.classList.add('overdue-alert');
                    } else {
                        overdueCard.classList.remove('overdue-alert');
                    }
                }
            } catch (err) {
                console.error('Failed to load statistics:', err);
            }
        }

        // Debounced Search Handler
        function debounceSearch() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(fetchTasks, 300);
        }

        // Fetch Tasks with filters & sorting
        async function fetchTasks() {
            const search = document.getElementById('search-input').value.trim();
            const priority = document.getElementById('filter-priority').value;
            const sortBy = document.getElementById('sort-by').value;

            let url = `/api/v1/tasks?sort_by=${sortBy}&sort_order=asc`;
            if (search) url += `&search=${encodeURIComponent(search)}`;
            if (priority) url += `&priority=${priority}`;

            try {
                const res = await fetch(url, {
                    headers: { 'Authorization': `Bearer ${authToken}`, 'Accept': 'application/json' }
                });
                const json = await res.json();

                if (res.ok) {
                    cachedTasks = json.data || [];
                    renderTasks();
                } else {
                    showToast(json.message || 'Error fetching tasks', 'error');
                }
            } catch (err) {
                showToast(`Fetch error: ${err.message}`, 'error');
            }
        }

        // Render Tasks into Active View
        function renderTasks() {
            if (viewMode === 'kanban') {
                renderKanbanView();
            } else {
                renderTableView();
            }
        }

        // Render Kanban View
        function renderKanbanView() {
            const pendingCol = document.getElementById('column-pending');
            const progressCol = document.getElementById('column-in-progress');
            const completedCol = document.getElementById('column-completed');

            pendingCol.innerHTML = '';
            progressCol.innerHTML = '';
            completedCol.innerHTML = '';

            let countPending = 0;
            let countProgress = 0;
            let countCompleted = 0;

            cachedTasks.forEach(task => {
                const card = createTaskCardElement(task);
                if (task.status === 'pending') {
                    pendingCol.appendChild(card);
                    countPending++;
                } else if (task.status === 'in-progress') {
                    progressCol.appendChild(card);
                    countProgress++;
                } else if (task.status === 'completed') {
                    completedCol.appendChild(card);
                    countCompleted++;
                }
            });

            document.getElementById('badge-pending-count').textContent = countPending;
            document.getElementById('badge-progress-count').textContent = countProgress;
            document.getElementById('badge-completed-count').textContent = countCompleted;

            if (countPending === 0) pendingCol.innerHTML = '<div class="empty-state"><div class="empty-icon">📂</div><div>No pending tasks</div></div>';
            if (countProgress === 0) progressCol.innerHTML = '<div class="empty-state"><div class="empty-icon">🚀</div><div>No active tasks</div></div>';
            if (countCompleted === 0) completedCol.innerHTML = '<div class="empty-state"><div class="empty-icon">✨</div><div>No completed tasks</div></div>';
        }

        // Create Task Card Element
        function createTaskCardElement(task) {
            const card = document.createElement('div');
            card.className = 'task-card';

            const isOverdue = task.due_date && new Date(task.due_date) < new Date() && task.status !== 'completed';
            const assigneeName = task.assigned_to ? task.assigned_to.name : 'Unassigned';
            const assigneeInitials = assigneeName.split(' ').map(n => n[0]).join('').substring(0, 2);

            const isEmployee = currentPersonaKey.startsWith('employee');

            card.innerHTML = `
                <div class="card-top">
                    <span class="priority-badge priority-${task.priority}">${task.priority}</span>
                    ${isOverdue ? '<span class="overdue-tag">⚠️ Overdue</span>' : ''}
                </div>
                <h4 class="card-title">${escapeHtml(task.title)}</h4>
                <p class="card-desc">${escapeHtml(task.description || 'No additional description provided.')}</p>
                <div class="card-meta">
                    <div class="card-user">
                        <div class="mini-avatar">${assigneeInitials}</div>
                        <span>${escapeHtml(assigneeName)}</span>
                    </div>
                    <div>📅 ${task.due_date ? task.due_date.substring(0, 10) : 'No due date'}</div>
                </div>
                <div class="card-actions">
                    <button class="action-icon-btn" onclick="openHistoryModal(${task.id})">📜 History</button>
                    <button class="action-icon-btn" onclick="openEditModal(${task.id})">✏️ Edit</button>
                    ${!isEmployee ? `<button class="action-icon-btn" style="color:#fb7185;" onclick="openDeleteModal(${task.id})">🗑️</button>` : ''}
                    <select class="quick-status-select" onchange="quickUpdateStatus(${task.id}, this.value)">
                        <option value="pending" ${task.status === 'pending' ? 'selected' : ''}>Pending</option>
                        <option value="in-progress" ${task.status === 'in-progress' ? 'selected' : ''}>In-Progress</option>
                        <option value="completed" ${task.status === 'completed' ? 'selected' : ''}>Completed</option>
                    </select>
                </div>
            `;
            return card;
        }

        // Render Table View
        function renderTableView() {
            const tbody = document.getElementById('tasks-table-body');
            tbody.innerHTML = '';

            const isEmployee = currentPersonaKey.startsWith('employee');

            if (cachedTasks.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" style="text-align:center; padding: 2.5rem; color:var(--text-dim);">No tasks match the active criteria.</td></tr>';
                return;
            }

            cachedTasks.forEach(task => {
                const tr = document.createElement('tr');
                const isOverdue = task.due_date && new Date(task.due_date) < new Date() && task.status !== 'completed';
                const assigneeName = task.assigned_to ? task.assigned_to.name : 'Unassigned';

                tr.innerHTML = `
                    <td>
                        <div style="font-weight:700;">${escapeHtml(task.title)}</div>
                        <div style="font-size:0.8rem; color:var(--text-muted);">${escapeHtml(task.description || '')}</div>
                    </td>
                    <td><span class="priority-badge priority-${task.priority}">${task.priority}</span></td>
                    <td>
                        <select class="quick-status-select" onchange="quickUpdateStatus(${task.id}, this.value)">
                            <option value="pending" ${task.status === 'pending' ? 'selected' : ''}>Pending</option>
                            <option value="in-progress" ${task.status === 'in-progress' ? 'selected' : ''}>In-Progress</option>
                            <option value="completed" ${task.status === 'completed' ? 'selected' : ''}>Completed</option>
                        </select>
                    </td>
                    <td>👤 ${escapeHtml(assigneeName)}</td>
                    <td>
                        <div>${task.due_date ? task.due_date.substring(0, 10) : '-'}</div>
                        ${isOverdue ? '<span class="overdue-tag" style="margin-top:0.25rem;">⚠️ Overdue</span>' : ''}
                    </td>
                    <td>
                        <div style="display:flex; gap:0.35rem;">
                            <button class="action-icon-btn" onclick="openHistoryModal(${task.id})">📜</button>
                            <button class="action-icon-btn" onclick="openEditModal(${task.id})">✏️</button>
                            ${!isEmployee ? `<button class="action-icon-btn" style="color:#fb7185;" onclick="openDeleteModal(${task.id})">🗑️</button>` : ''}
                        </div>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        }

        // View Mode Switcher
        function setViewMode(mode) {
            viewMode = mode;
            document.getElementById('btn-kanban').classList.toggle('active', mode === 'kanban');
            document.getElementById('btn-table').classList.toggle('active', mode === 'table');

            document.getElementById('kanban-view').style.display = mode === 'kanban' ? 'grid' : 'none';
            document.getElementById('table-view').style.display = mode === 'table' ? 'block' : 'none';
            renderTasks();
        }

        // Quick Status Update
        async function quickUpdateStatus(taskId, newStatus) {
            try {
                const res = await fetch(`/api/v1/tasks/${taskId}`, {
                    method: 'PUT',
                    headers: {
                        'Authorization': `Bearer ${authToken}`,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ status: newStatus })
                });

                const data = await res.json();
                if (res.ok) {
                    showToast(`Task status updated to ${newStatus.toUpperCase()}`, 'success');
                    await fetchStatistics();
                    await fetchTasks();
                } else {
                    showToast(data.message || 'Status update failed', 'error');
                    fetchTasks(); // reset select state
                }
            } catch (err) {
                showToast(`Network error: ${err.message}`, 'error');
            }
        }

        // Open Create Task Modal
        function openCreateModal() {
            document.getElementById('modal-task-title').textContent = 'Create New Task';
            document.getElementById('form-task-id').value = '';
            document.getElementById('task-title-input').value = '';
            document.getElementById('task-desc-input').value = '';
            document.getElementById('task-priority-input').value = 'medium';
            document.getElementById('task-status-input').value = 'pending';

            // Set default due date to 5 days ahead
            const future = new Date();
            future.setDate(future.getDate() + 5);
            document.getElementById('task-due-date-input').value = future.toISOString().split('T')[0];

            enableFormFields(true);
            document.getElementById('employee-permission-notice').style.display = 'none';

            openModal('task-modal');
        }

        // Open Edit Task Modal
        function openEditModal(taskId) {
            const task = cachedTasks.find(t => t.id === taskId);
            if (!task) return;

            document.getElementById('modal-task-title').textContent = `Edit Task #${task.id}`;
            document.getElementById('form-task-id').value = task.id;
            document.getElementById('task-title-input').value = task.title;
            document.getElementById('task-desc-input').value = task.description || '';
            document.getElementById('task-priority-input').value = task.priority;
            document.getElementById('task-status-input').value = task.status;
            document.getElementById('task-due-date-input').value = task.due_date ? task.due_date.substring(0, 10) : '';

            if (task.assigned_to) {
                document.getElementById('task-assignee-input').value = task.assigned_to.id;
            }

            const isEmployee = currentPersonaKey.startsWith('employee');
            if (isEmployee) {
                enableFormFields(false); // disable all except status
                document.getElementById('task-status-input').disabled = false;
                document.getElementById('employee-permission-notice').style.display = 'block';
            } else {
                enableFormFields(true);
                document.getElementById('employee-permission-notice').style.display = 'none';
            }

            openModal('task-modal');
        }

        function enableFormFields(enabled) {
            document.getElementById('task-title-input').disabled = !enabled;
            document.getElementById('task-desc-input').disabled = !enabled;
            document.getElementById('task-priority-input').disabled = !enabled;
            document.getElementById('task-status-input').disabled = !enabled;
            document.getElementById('task-due-date-input').disabled = !enabled;
            document.getElementById('task-assignee-input').disabled = !enabled;
        }

        // Handle Task Form Submit
        async function handleTaskFormSubmit(e) {
            e.preventDefault();
            const taskId = document.getElementById('form-task-id').value;
            const isEdit = Boolean(taskId);
            const isEmployee = currentPersonaKey.startsWith('employee');

            let payload = {};
            if (isEdit && isEmployee) {
                // Employees are strictly restricted to updating only status
                payload = {
                    status: document.getElementById('task-status-input').value
                };
            } else {
                payload = {
                    title: document.getElementById('task-title-input').value,
                    description: document.getElementById('task-desc-input').value,
                    priority: document.getElementById('task-priority-input').value,
                    status: document.getElementById('task-status-input').value,
                    due_date: document.getElementById('task-due-date-input').value,
                    assigned_to: parseInt(document.getElementById('task-assignee-input').value)
                };
            }

            const url = isEdit ? `/api/v1/tasks/${taskId}` : '/api/v1/tasks';
            const method = isEdit ? 'PUT' : 'POST';

            try {
                const res = await fetch(url, {
                    method: method,
                    headers: {
                        'Authorization': `Bearer ${authToken}`,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });

                const data = await res.json();
                if (res.ok) {
                    showToast(isEdit ? 'Task updated successfully' : 'Task created successfully', 'success');
                    closeModal('task-modal');
                    await fetchStatistics();
                    await fetchTasks();
                } else {
                    showToast(data.message || 'Validation error saving task', 'error');
                }
            } catch (err) {
                showToast(`Error: ${err.message}`, 'error');
            }
        }

        // Audit History Modal
        async function openHistoryModal(taskId) {
            const timeline = document.getElementById('history-timeline');
            timeline.innerHTML = '<div style="color:var(--text-dim); text-align:center; padding:1.5rem;">Loading audit records...</div>';
            openModal('history-modal');

            try {
                const res = await fetch(`/api/v1/tasks/${taskId}/history`, {
                    headers: { 'Authorization': `Bearer ${authToken}`, 'Accept': 'application/json' }
                });
                const history = await res.json();

                if (res.ok && Array.isArray(history) && history.length > 0) {
                    timeline.innerHTML = history.map(h => {
                        const actor = h.changed_by ? h.changed_by.name : 'System';
                        const timeStr = h.changed_at ? new Date(h.changed_at).toLocaleString() : '';
                        return `
                            <div class="timeline-item">
                                <div class="timeline-time">${timeStr} • By ${escapeHtml(actor)}</div>
                                <div class="timeline-content">
                                    Transitioned: <span class="priority-badge priority-low">${h.old_status}</span> ➔ <span class="priority-badge priority-medium">${h.new_status}</span>
                                </div>
                            </div>
                        `;
                    }).join('');
                } else {
                    timeline.innerHTML = '<div style="color:var(--text-dim); text-align:center; padding:1.5rem;">No status changes recorded for this task yet.</div>';
                }
            } catch (err) {
                timeline.innerHTML = `<div style="color:#fb7185; text-align:center; padding:1.5rem;">Error loading history: ${err.message}</div>`;
            }
        }

        // Delete Task Modal
        function openDeleteModal(taskId) {
            document.getElementById('delete-task-id').value = taskId;
            openModal('delete-modal');
        }

        async function executeDeleteTask() {
            const taskId = document.getElementById('delete-task-id').value;
            try {
                const res = await fetch(`/api/v1/tasks/${taskId}`, {
                    method: 'DELETE',
                    headers: { 'Authorization': `Bearer ${authToken}`, 'Accept': 'application/json' }
                });

                if (res.ok) {
                    showToast('Task permanently deleted', 'success');
                    closeModal('delete-modal');
                    await fetchStatistics();
                    await fetchTasks();
                } else {
                    const data = await res.json();
                    showToast(data.message || 'Delete operation failed', 'error');
                }
            } catch (err) {
                showToast(`Error: ${err.message}`, 'error');
            }
        }

        // Generic Modal Utilities
        function openModal(id) {
            document.getElementById(id).classList.add('active');
        }

        function closeModal(id) {
            document.getElementById(id).classList.remove('active');
        }

        // Toast Notifications
        function showToast(message, type = 'info') {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            toast.className = `toast toast-${type}`;

            let icon = 'ℹ️';
            if (type === 'success') icon = '✅';
            if (type === 'error') icon = '❌';

            toast.innerHTML = `<span>${icon}</span><span>${escapeHtml(message)}</span>`;
            container.appendChild(toast);

            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateY(10px)';
                toast.style.transition = 'all 0.3s ease';
                setTimeout(() => toast.remove(), 300);
            }, 3500);
        }

        function escapeHtml(text) {
            if (!text) return '';
            const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
            return text.toString().replace(/[&<>"']/g, m => map[m]);
        }
    </script>
</body>
</html>
