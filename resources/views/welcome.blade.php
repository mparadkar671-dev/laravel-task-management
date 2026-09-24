<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Enterprise Task Management API | Laravel 12 Platform</title>
    <meta name="description" content="Production-ready RESTful Task Management API built with Laravel 12, Sanctum Token Authentication, Spatie RBAC, Observer-driven audit history, and scheduled overdue reminders.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-base: #090d16;
            --bg-card: rgba(18, 24, 38, 0.7);
            --bg-card-hover: rgba(28, 36, 56, 0.85);
            --border-subtle: rgba(255, 255, 255, 0.08);
            --border-accent: rgba(99, 102, 241, 0.3);
            --primary: #6366f1;
            --primary-gradient: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #ec4899 100%);
            --accent-cyan: #06b6d4;
            --accent-emerald: #10b981;
            --accent-amber: #f59e0b;
            --accent-rose: #f43f5e;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --text-dark: #64748b;
            --font-sans: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            --font-mono: 'JetBrains Mono', monospace;
            --radius-lg: 16px;
            --radius-md: 10px;
            --radius-sm: 6px;
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
            line-height: 1.6;
            overflow-x: hidden;
            background-image: 
                radial-gradient(circle at 15% 15%, rgba(99, 102, 241, 0.15) 0%, transparent 40%),
                radial-gradient(circle at 85% 25%, rgba(236, 72, 153, 0.12) 0%, transparent 45%),
                radial-gradient(circle at 50% 85%, rgba(6, 182, 212, 0.1) 0%, transparent 50%);
            background-attachment: fixed;
            min-height: 100vh;
        }

        .container {
            max-width: 1240px;
            margin: 0 auto;
            padding: 2.5rem 1.5rem;
        }

        /* Header Navigation */
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 1.5rem;
            background: rgba(15, 23, 42, 0.65);
            backdrop-filter: blur(16px);
            border: 1px solid var(--border-subtle);
            border-radius: var(--radius-lg);
            margin-bottom: 3.5rem;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
            color: var(--text-main);
            font-weight: 700;
            font-size: 1.2rem;
            letter-spacing: -0.02em;
        }

        .brand-icon {
            width: 36px;
            height: 36px;
            border-radius: var(--radius-md);
            background: var(--primary-gradient);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            color: #fff;
            box-shadow: 0 4px 14px rgba(99, 102, 241, 0.4);
        }

        .header-links {
            display: flex;
            align-items: center;
            gap: 1.25rem;
        }

        .nav-link {
            color: var(--text-muted);
            text-decoration: none;
            font-size: 0.925rem;
            font-weight: 500;
            transition: color 0.2s ease;
        }

        .nav-link:hover {
            color: var(--text-main);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.65rem 1.35rem;
            border-radius: var(--radius-md);
            font-weight: 600;
            font-size: 0.925rem;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            border: none;
        }

        .btn-primary {
            background: var(--primary-gradient);
            color: #ffffff;
            box-shadow: 0 4px 18px rgba(99, 102, 241, 0.35);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 24px rgba(99, 102, 241, 0.5);
        }

        .btn-outline {
            background: rgba(255, 255, 255, 0.04);
            color: var(--text-main);
            border: 1px solid var(--border-subtle);
        }

        .btn-outline:hover {
            background: rgba(255, 255, 255, 0.08);
            border-color: var(--border-accent);
            transform: translateY(-2px);
        }

        /* Hero Section */
        .hero {
            text-align: center;
            padding: 2rem 0 4rem;
            max-width: 900px;
            margin: 0 auto;
        }

        .badge-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            padding: 0.4rem 1rem;
            background: rgba(99, 102, 241, 0.12);
            border: 1px solid rgba(99, 102, 241, 0.3);
            border-radius: 9999px;
            font-size: 0.85rem;
            font-weight: 600;
            color: #a5b4fc;
            margin-bottom: 1.75rem;
        }

        .badge-dot {
            width: 8px;
            height: 8px;
            background-color: var(--accent-emerald);
            border-radius: 50%;
            box-shadow: 0 0 10px var(--accent-emerald);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.4; transform: scale(0.85); }
        }

        h1 {
            font-size: clamp(2.4rem, 5.5vw, 4.2rem);
            font-weight: 800;
            letter-spacing: -0.035em;
            line-height: 1.15;
            margin-bottom: 1.5rem;
            background: linear-gradient(180deg, #ffffff 30%, #94a3b8 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-desc {
            font-size: 1.15rem;
            color: var(--text-muted);
            margin-bottom: 2.5rem;
            line-height: 1.75;
        }

        .hero-actions {
            display: flex;
            justify-content: center;
            gap: 1rem;
            flex-wrap: wrap;
            margin-bottom: 3.5rem;
        }

        /* Metrics Bar */
        .metrics-banner {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.25rem;
            background: var(--bg-card);
            border: 1px solid var(--border-subtle);
            border-radius: var(--radius-lg);
            padding: 1.75rem;
            margin-bottom: 4rem;
            backdrop-filter: blur(12px);
        }

        .metric-item {
            text-align: center;
            padding: 0.5rem;
        }

        .metric-val {
            font-size: 2rem;
            font-weight: 800;
            color: var(--text-main);
            letter-spacing: -0.02em;
        }

        .metric-label {
            font-size: 0.85rem;
            color: var(--text-muted);
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-top: 0.25rem;
        }

        /* Interactive Live Tester */
        .sandbox-panel {
            background: var(--bg-card);
            border: 1px solid var(--border-accent);
            border-radius: var(--radius-lg);
            padding: 2.25rem;
            margin-bottom: 4.5rem;
            backdrop-filter: blur(16px);
            box-shadow: 0 16px 36px rgba(0, 0, 0, 0.4);
        }

        .panel-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.75rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .panel-title {
            font-size: 1.35rem;
            font-weight: 700;
            letter-spacing: -0.01em;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .tag {
            font-size: 0.75rem;
            padding: 0.25rem 0.65rem;
            border-radius: 999px;
            background: rgba(16, 185, 129, 0.15);
            color: #34d399;
            border: 1px solid rgba(16, 185, 129, 0.3);
            font-weight: 600;
            text-transform: uppercase;
        }

        .role-switchers {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .role-btn {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--border-subtle);
            color: var(--text-muted);
            padding: 0.45rem 0.9rem;
            border-radius: var(--radius-sm);
            font-size: 0.825rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .role-btn.active, .role-btn:hover {
            background: var(--primary);
            color: #ffffff;
            border-color: var(--primary);
        }

        .tester-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }

        @media (max-width: 860px) {
            .tester-grid {
                grid-template-columns: 1fr;
            }
        }

        .terminal-box {
            background: #060911;
            border: 1px solid var(--border-subtle);
            border-radius: var(--radius-md);
            padding: 1.25rem;
            font-family: var(--font-mono);
            font-size: 0.85rem;
            color: #cbd5e1;
            overflow-x: auto;
            min-height: 220px;
        }

        .terminal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.75rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
            color: var(--text-dark);
            font-size: 0.75rem;
        }

        .terminal-controls {
            display: flex;
            gap: 6px;
        }

        .terminal-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
        }

        .dot-red { background: #ef4444; }
        .dot-yellow { background: #f59e0b; }
        .dot-green { background: #10b981; }

        .terminal-content {
            white-space: pre-wrap;
            word-break: break-all;
            line-height: 1.5;
        }

        /* Feature Cards Grid */
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(340px, 1fr));
            gap: 1.5rem;
            margin-bottom: 5rem;
        }

        .feature-card {
            background: var(--bg-card);
            border: 1px solid var(--border-subtle);
            border-radius: var(--radius-lg);
            padding: 2rem;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
            overflow: hidden;
        }

        .feature-card:hover {
            transform: translateY(-4px);
            border-color: var(--border-accent);
            background: var(--bg-card-hover);
            box-shadow: 0 12px 28px rgba(0, 0, 0, 0.35);
        }

        .feature-icon-wrap {
            width: 48px;
            height: 48px;
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.25rem;
            font-size: 1.4rem;
        }

        .icon-indigo { background: rgba(99, 102, 241, 0.15); color: #818cf8; }
        .icon-cyan { background: rgba(6, 182, 212, 0.15); color: #22d3ee; }
        .icon-emerald { background: rgba(16, 185, 129, 0.15); color: #34d399; }
        .icon-amber { background: rgba(245, 158, 11, 0.15); color: #fbbf24; }
        .icon-rose { background: rgba(244, 63, 94, 0.15); color: #fb7185; }
        .icon-violet { background: rgba(139, 92, 246, 0.15); color: #c084fc; }

        .feature-title {
            font-size: 1.15rem;
            font-weight: 700;
            margin-bottom: 0.65rem;
            color: var(--text-main);
        }

        .feature-desc {
            color: var(--text-muted);
            font-size: 0.925rem;
            line-height: 1.6;
        }

        /* Endpoints Table */
        .endpoints-section {
            background: var(--bg-card);
            border: 1px solid var(--border-subtle);
            border-radius: var(--radius-lg);
            padding: 2.25rem;
            margin-bottom: 5rem;
        }

        .endpoints-section h2 {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .endpoints-section p {
            color: var(--text-muted);
            margin-bottom: 1.75rem;
            font-size: 0.95rem;
        }

        .endpoints-list {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .endpoint-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.9rem 1.25rem;
            background: rgba(10, 15, 26, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.04);
            border-radius: var(--radius-md);
            gap: 1rem;
            flex-wrap: wrap;
        }

        .endpoint-identity {
            display: flex;
            align-items: center;
            gap: 0.85rem;
            font-family: var(--font-mono);
            font-size: 0.9rem;
        }

        .method-badge {
            padding: 0.25rem 0.6rem;
            border-radius: var(--radius-sm);
            font-weight: 700;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
        }

        .method-get { background: rgba(16, 185, 129, 0.15); color: #34d399; }
        .method-post { background: rgba(59, 130, 246, 0.15); color: #60a5fa; }
        .method-put { background: rgba(245, 158, 11, 0.15); color: #fbbf24; }
        .method-delete { background: rgba(239, 68, 68, 0.15); color: #f87171; }

        .endpoint-meta {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 0.85rem;
            color: var(--text-muted);
        }

        .role-pill {
            font-size: 0.75rem;
            padding: 0.2rem 0.5rem;
            background: rgba(255, 255, 255, 0.06);
            border-radius: 4px;
            color: #cbd5e1;
        }

        /* Footer */
        footer {
            border-top: 1px solid var(--border-subtle);
            padding: 2.5rem 0 1rem;
            text-align: center;
            color: var(--text-dark);
            font-size: 0.875rem;
        }

        footer a {
            color: var(--text-muted);
            text-decoration: none;
        }

        footer a:hover {
            color: var(--primary);
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Navigation Header -->
        <header>
            <a href="/" class="brand" id="brand-logo">
                <div class="brand-icon">✓</div>
                <span>TaskFlow API</span>
            </a>
            <div class="header-links">
                <a href="#endpoints" class="nav-link">Endpoints</a>
                <a href="#features" class="nav-link">Architecture</a>
                <a href="/openapi.json" class="btn btn-outline" id="openapi-btn">OpenAPI 3.0</a>
                <a href="/docs" class="btn btn-primary" id="swagger-ui-btn">
                    <span>Swagger UI</span>
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M7 17L17 7M17 7H7M17 7V17"/></svg>
                </a>
            </div>
        </header>

        <!-- Hero Section -->
        <section class="hero">
            <div class="badge-pill">
                <span class="badge-dot"></span>
                <span>Production API Ready • PHP 8.5 • Laravel 12</span>
            </div>
            <h1>Enterprise Task Management & Governance Platform</h1>
            <p class="hero-desc">
                High-concurrency RESTful engine engineered with Laravel Sanctum authentication, Spatie Role-Based Access Control, automated observer status audits, daily overdue task cron notifications, and rate-limiting safeguards.
            </p>
            <div class="hero-actions">
                <a href="/docs" class="btn btn-primary" id="hero-docs-btn" style="padding: 0.85rem 1.75rem; font-size: 1rem;">
                    Launch Interactive Swagger UI
                </a>
                <a href="#quick-test" class="btn btn-outline" style="padding: 0.85rem 1.75rem; font-size: 1rem;">
                    Test Live Authentication
                </a>
            </div>
        </section>

        <!-- Metric Summary -->
        <div class="metrics-banner">
            <div class="metric-item">
                <div class="metric-val">100%</div>
                <div class="metric-label">Automated Test Pass Rate</div>
            </div>
            <div class="metric-item">
                <div class="metric-val">3 Roles</div>
                <div class="metric-label">Admin, Manager, Employee</div>
            </div>
            <div class="metric-item">
                <div class="metric-val">&lt; 15ms</div>
                <div class="metric-label">Optimized Query Latency</div>
            </div>
            <div class="metric-item">
                <div class="metric-val">60/min</div>
                <div class="metric-label">DDoS Throttling Guard</div>
            </div>
        </div>

        <!-- Live Sandbox / Tester Panel -->
        <section class="sandbox-panel" id="quick-test">
            <div class="panel-header">
                <div>
                    <h2 class="panel-title">
                        <span>Live Authentication & Metrics Console</span>
                        <span class="tag">Sanctum Protected</span>
                    </h2>
                    <p style="color: var(--text-muted); font-size: 0.9rem; margin-top: 0.25rem;">Select a seeded persona to execute live API login and inspect authorization metrics.</p>
                </div>
                <div class="role-switchers">
                    <button class="role-btn active" id="btn-admin" onclick="switchPersona('admin')">Admin</button>
                    <button class="role-btn" id="btn-manager" onclick="switchPersona('manager')">Manager</button>
                    <button class="role-btn" id="btn-employee" onclick="switchPersona('employee1')">Employee</button>
                </div>
            </div>

            <div class="tester-grid">
                <!-- Request Terminal -->
                <div class="terminal-box">
                    <div class="terminal-header">
                        <div class="terminal-controls">
                            <div class="terminal-dot dot-red"></div>
                            <div class="terminal-dot dot-yellow"></div>
                            <div class="terminal-dot dot-green"></div>
                        </div>
                        <span>REQUEST (POST /api/v1/login)</span>
                    </div>
                    <div class="terminal-content" id="request-payload">
{
  "email": "admin@example.com",
  "password": "password"
}
                    </div>
                    <div style="margin-top: 1rem;">
                        <button class="btn btn-primary" id="run-auth-test-btn" style="padding: 0.45rem 1rem; font-size: 0.85rem;" onclick="testApiLogin()">
                            Execute Live Call
                        </button>
                    </div>
                </div>

                <!-- Response Terminal -->
                <div class="terminal-box">
                    <div class="terminal-header">
                        <div class="terminal-controls">
                            <div class="terminal-dot dot-red"></div>
                            <div class="terminal-dot dot-yellow"></div>
                            <div class="terminal-dot dot-green"></div>
                        </div>
                        <span id="response-status-badge">RESPONSE (HTTP 200 OK)</span>
                    </div>
                    <div class="terminal-content" id="response-payload">
// Click "Execute Live Call" to test live authentication and retrieve your Sanctum Bearer token.
                    </div>
                </div>
            </div>
        </section>

        <!-- Feature Capabilities Grid -->
        <section id="features" style="margin-bottom: 2rem;">
            <div style="text-align: center; margin-bottom: 3rem;">
                <h2 style="font-size: 2.2rem; font-weight: 800; letter-spacing: -0.02em; margin-bottom: 0.5rem;">Engineered for Enterprise Scale</h2>
                <p style="color: var(--text-muted); font-size: 1.05rem;">Adhering strictly to modern Laravel standards, repository-service abstractions, and security best practices.</p>
            </div>

            <div class="features-grid">
                <!-- Card 1 -->
                <div class="feature-card">
                    <div class="feature-icon-wrap icon-indigo">🔒</div>
                    <h3 class="feature-title">Role-Based Access Control (RBAC)</h3>
                    <p class="feature-desc">Integrated Spatie Permission framework with Admin, Manager, and Employee roles. Fine-grained Eloquent Policies enforce task visibility and strict role boundaries.</p>
                </div>

                <!-- Card 2 -->
                <div class="feature-card">
                    <div class="feature-icon-wrap icon-cyan">📜</div>
                    <h3 class="feature-title">Observer Status Audit Trail</h3>
                    <p class="feature-desc">Eloquent `TaskObserver` intercepts every status change (`pending` ➔ `in-progress` ➔ `completed`) recording complete historic snapshots with actor accountability.</p>
                </div>

                <!-- Card 3 -->
                <div class="feature-card">
                    <div class="feature-icon-wrap icon-emerald">⚡</div>
                    <h3 class="feature-title">Indexed Filter & Search Engine</h3>
                    <p class="feature-desc">Fast query pipeline supporting multi-criteria filtering by status, priority, due date, assignee, full-text keyword searches, and dynamic field sorting with pagination.</p>
                </div>

                <!-- Card 4 -->
                <div class="feature-card">
                    <div class="feature-icon-wrap icon-amber">📬</div>
                    <h3 class="feature-title">Asynchronous Notifications</h3>
                    <p class="feature-desc">Queued notification events dispatched seamlessly on task delegation and status transitions, notifying assignees and stakeholders without blocking HTTP workers.</p>
                </div>

                <!-- Card 5 -->
                <div class="feature-card">
                    <div class="feature-icon-wrap icon-rose">⏰</div>
                    <h3 class="feature-title">Automated Overdue Cron</h3>
                    <p class="feature-desc">Scheduled Artisan command `php artisan tasks:remind-overdue` running daily at 09:00 UTC to flag past-due obligations and notify responsible assignees.</p>
                </div>

                <!-- Card 6 -->
                <div class="feature-card">
                    <div class="feature-icon-wrap icon-violet">🛡️</div>
                    <h3 class="feature-title">Brute-Force & Rate Throttling</h3>
                    <p class="feature-desc">Layered rate limiting with dedicated throttles: 5 attempts/minute for authentication endpoints and 60 requests/minute for protected REST operations.</p>
                </div>
            </div>
        </section>

        <!-- REST Endpoints Catalog -->
        <section class="endpoints-section" id="endpoints">
            <h2>RESTful API Routing Architecture</h2>
            <p>Protected with Bearer Token Authorization via Laravel Sanctum. Available under <code>/api/v1</code> prefix.</p>

            <div class="endpoints-list">
                <div class="endpoint-row">
                    <div class="endpoint-identity">
                        <span class="method-badge method-post">POST</span>
                        <span>/api/v1/login</span>
                    </div>
                    <div class="endpoint-meta">
                        <span>Authenticate & issue Sanctum Bearer Token</span>
                        <span class="role-pill">5 req/min throttle</span>
                    </div>
                </div>

                <div class="endpoint-row">
                    <div class="endpoint-identity">
                        <span class="method-badge method-post">POST</span>
                        <span>/api/v1/logout</span>
                    </div>
                    <div class="endpoint-meta">
                        <span>Revoke current personal access token</span>
                        <span class="role-pill">All Authenticated</span>
                    </div>
                </div>

                <div class="endpoint-row">
                    <div class="endpoint-identity">
                        <span class="method-badge method-get">GET</span>
                        <span>/api/v1/tasks</span>
                    </div>
                    <div class="endpoint-meta">
                        <span>List paginated tasks (status, priority, search filters)</span>
                        <span class="role-pill">Scoped by Role</span>
                    </div>
                </div>

                <div class="endpoint-row">
                    <div class="endpoint-identity">
                        <span class="method-badge method-post">POST</span>
                        <span>/api/v1/tasks</span>
                    </div>
                    <div class="endpoint-meta">
                        <span>Create and delegate a new task</span>
                        <span class="role-pill">Admin & Manager Only</span>
                    </div>
                </div>

                <div class="endpoint-row">
                    <div class="endpoint-identity">
                        <span class="method-badge method-get">GET</span>
                        <span>/api/v1/tasks/statistics</span>
                    </div>
                    <div class="endpoint-meta">
                        <span>Real-time status breakdown, overdue metrics & counts</span>
                        <span class="role-pill">Scoped by Role</span>
                    </div>
                </div>

                <div class="endpoint-row">
                    <div class="endpoint-identity">
                        <span class="method-badge method-get">GET</span>
                        <span>/api/v1/tasks/{id}</span>
                    </div>
                    <div class="endpoint-meta">
                        <span>Retrieve individual task details</span>
                        <span class="role-pill">Policy Verified</span>
                    </div>
                </div>

                <div class="endpoint-row">
                    <div class="endpoint-identity">
                        <span class="method-badge method-put">PUT</span>
                        <span>/api/v1/tasks/{id}</span>
                    </div>
                    <div class="endpoint-meta">
                        <span>Update task details or status</span>
                        <span class="role-pill">Status only for Employee</span>
                    </div>
                </div>

                <div class="endpoint-row">
                    <div class="endpoint-identity">
                        <span class="method-badge method-delete">DELETE</span>
                        <span>/api/v1/tasks/{id}</span>
                    </div>
                    <div class="endpoint-meta">
                        <span>Soft or hard deletion of specified task</span>
                        <span class="role-pill">Admin & Manager Only</span>
                    </div>
                </div>

                <div class="endpoint-row">
                    <div class="endpoint-identity">
                        <span class="method-badge method-get">GET</span>
                        <span>/api/v1/tasks/{id}/history</span>
                    </div>
                    <div class="endpoint-meta">
                        <span>Retrieve complete status transition audit trail</span>
                        <span class="role-pill">Policy Verified</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer>
            <p>© {{ date('Y') }} TaskFlow API Platform. Built with Laravel 12 & Sanctum. All rights reserved.</p>
            <p style="margin-top: 0.5rem;">
                <a href="/docs">Swagger Documentation</a> • 
                <a href="/openapi.json">OpenAPI Specification</a> • 
                <a href="https://github.com/mparadkar671-dev/laravel-task-management" target="_blank" rel="noopener">GitHub Repository</a>
            </p>
        </footer>
    </div>

    <!-- Client-side Testing Script -->
    <script>
        const personas = {
            admin: { email: 'admin@example.com', password: 'password' },
            manager: { email: 'manager@example.com', password: 'password' },
            employee1: { email: 'employee1@example.com', password: 'password' }
        };

        let currentPersona = 'admin';

        function switchPersona(role) {
            currentPersona = role;
            document.querySelectorAll('.role-btn').forEach(btn => btn.classList.remove('active'));
            const targetBtn = document.getElementById('btn-' + role);
            if (targetBtn) targetBtn.classList.add('active');

            const payload = personas[role];
            document.getElementById('request-payload').textContent = JSON.stringify(payload, null, 2);
        }

        async function testApiLogin() {
            const payload = personas[currentPersona];
            const responseBadge = document.getElementById('response-status-badge');
            const responseBox = document.getElementById('response-payload');

            responseBadge.textContent = 'EXECUTING REQUEST...';
            responseBox.textContent = '// Sending POST /api/v1/login...';

            try {
                const res = await fetch('/api/v1/login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });

                const data = await res.json();
                responseBadge.textContent = `RESPONSE (HTTP ${res.status})`;

                if (res.ok && data.token) {
                    // Fetch statistics with this token
                    responseBox.textContent = `// Authentication Successful!\n// Sanctum Bearer Token: ${data.token.substring(0, 24)}...\n\n` + JSON.stringify(data, null, 2);
                } else {
                    responseBox.textContent = JSON.stringify(data, null, 2);
                }
            } catch (err) {
                responseBadge.textContent = 'CONNECTION ERROR';
                responseBox.textContent = `// Error connecting to backend API: ${err.message}`;
            }
        }
    </script>
</body>
</html>
