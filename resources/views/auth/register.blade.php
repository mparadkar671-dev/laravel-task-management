<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ !$adminExists ? 'Initialize Administrator' : 'Employee Registration' }} | TaskFlow Enterprise</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-base: #0b0f19;
            --bg-surface: #111827;
            --border: rgba(255, 255, 255, 0.08);
            --primary: #6366f1;
            --primary-gradient: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #ec4899 100%);
            --text-main: #f9fafb;
            --text-muted: #9ca3af;
            --text-dim: #6b7280;
            --font-sans: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            --radius-lg: 14px;
            --radius-md: 8px;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            background-color: var(--bg-base);
            color: var(--text-main);
            font-family: var(--font-sans);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1.5rem;
            background-image: 
                radial-gradient(circle at 85% 15%, rgba(99, 102, 241, 0.15) 0%, transparent 40%),
                radial-gradient(circle at 15% 85%, rgba(16, 185, 129, 0.12) 0%, transparent 40%);
        }

        .auth-card {
            background: rgba(17, 24, 39, 0.85);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            width: 100%;
            max-width: 480px;
            padding: 2.5rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(16px);
        }

        .auth-brand {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 2rem;
            text-decoration: none;
            color: var(--text-main);
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
        }

        .brand-title {
            font-size: 1.25rem;
            font-weight: 800;
        }

        h1 {
            font-size: 1.5rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
            letter-spacing: -0.02em;
        }

        .auth-subtitle {
            color: var(--text-muted);
            font-size: 0.9rem;
            margin-bottom: 1.5rem;
            line-height: 1.5;
        }

        .role-notice-box {
            padding: 0.85rem 1.1rem;
            border-radius: var(--radius-md);
            margin-bottom: 1.5rem;
            font-size: 0.825rem;
            line-height: 1.5;
        }

        .notice-admin {
            background: rgba(168, 85, 247, 0.1);
            border: 1px solid rgba(168, 85, 247, 0.3);
            color: #d8b4fe;
        }

        .notice-employee {
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.3);
            color: #6ee7b7;
        }

        .form-group {
            margin-bottom: 1.25rem;
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
            background: #090d16;
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            padding: 0.75rem 1rem;
            color: var(--text-main);
            font-size: 0.9rem;
            outline: none;
            transition: all 0.2s ease;
        }

        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.2);
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .btn-submit {
            width: 100%;
            background: var(--primary-gradient);
            color: #ffffff;
            border: none;
            padding: 0.8rem;
            border-radius: var(--radius-md);
            font-size: 0.95rem;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 4px 14px rgba(99, 102, 241, 0.35);
            transition: all 0.2s ease;
            margin-top: 0.5rem;
        }

        .btn-submit:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(99, 102, 241, 0.5);
        }

        .auth-footer {
            margin-top: 1.5rem;
            text-align: center;
            font-size: 0.85rem;
            color: var(--text-muted);
        }

        .auth-footer a {
            color: #818cf8;
            text-decoration: none;
            font-weight: 600;
        }

        .auth-footer a:hover {
            text-decoration: underline;
        }

        .alert-error {
            background: rgba(244, 63, 94, 0.15);
            border: 1px solid rgba(244, 63, 94, 0.3);
            color: #fb7185;
            padding: 0.75rem 1rem;
            border-radius: var(--radius-md);
            font-size: 0.85rem;
            margin-bottom: 1.25rem;
        }
    </style>
</head>
<body>

    <div class="auth-card">
        <a href="/" class="auth-brand">
            <div class="brand-icon">✓</div>
            <span class="brand-title">TaskFlow Enterprise</span>
        </a>

        @if(!$adminExists)
            <h1>Platform Setup</h1>
            <p class="auth-subtitle">Initialize your workspace as the Primary Administrator.</p>
            <div class="role-notice-box notice-admin">
                👑 <strong>Single Administrator Rule:</strong> Since no Administrator exists, you will be registered with full platform governance and executive rights. You can appoint Managers and delegate tasks once initialized.
            </div>
        @else
            <h1>Employee Registration</h1>
            <p class="auth-subtitle">Create your account to view and manage assigned tasks.</p>
            <div class="role-notice-box notice-employee">
                🧑‍💻 <strong>Employee Account:</strong> You are registering as a team employee. Managers are appointed directly by the Administrator.
            </div>
        @endif

        @if($errors->any())
            <div class="alert-error">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('register.post') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label" for="name">Full Name *</label>
                <input type="text" id="name" name="name" class="form-control" value="{{ old('name') }}" required autofocus placeholder="John Doe">
            </div>

            <div class="form-group">
                <label class="form-label" for="email">Work Email *</label>
                <input type="email" id="email" name="email" class="form-control" value="{{ old('email') }}" required placeholder="name@company.com">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="password">Password *</label>
                    <input type="password" id="password" name="password" class="form-control" required placeholder="Min. 8 characters">
                </div>

                <div class="form-group">
                    <label class="form-label" for="password_confirmation">Confirm Password *</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required placeholder="Repeat password">
                </div>
            </div>

            <button type="submit" class="btn-submit">
                {{ !$adminExists ? 'Initialize Administrator & Launch' : 'Register Employee Account' }}
            </button>
        </form>

        <div class="auth-footer">
            Already have an account? <a href="{{ route('login') }}">Sign In</a>
        </div>
    </div>

</body>
</html>
