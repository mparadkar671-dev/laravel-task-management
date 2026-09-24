<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In | TaskFlow Enterprise</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
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
                radial-gradient(circle at 15% 15%, rgba(99, 102, 241, 0.15) 0%, transparent 40%),
                radial-gradient(circle at 85% 85%, rgba(236, 72, 153, 0.12) 0%, transparent 40%);
        }

        .auth-card {
            background: rgba(17, 24, 39, 0.85);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            width: 100%;
            max-width: 440px;
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
            border-radius: var(--radius-md);
            transition: color 0.15s ease, background-color 0.15s ease;
        }

        .btn-eye-toggle:hover {
            color: #ffffff;
            background: rgba(255, 255, 255, 0.08);
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.85rem;
            color: var(--text-muted);
            margin-bottom: 1.5rem;
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

        .setup-notice {
            background: rgba(168, 85, 247, 0.12);
            border: 1px solid rgba(168, 85, 247, 0.3);
            color: #d8b4fe;
            padding: 0.85rem 1.1rem;
            border-radius: var(--radius-md);
            font-size: 0.85rem;
            margin-bottom: 1.5rem;
            line-height: 1.5;
        }
    </style>
</head>
<body>

    <div class="auth-card">
        <a href="/" class="auth-brand">
            <div class="brand-icon">✓</div>
            <span class="brand-title">TaskFlow Enterprise</span>
        </a>

        <h1>Sign In</h1>
        <p class="auth-subtitle">Access your role-specific task workspace</p>

        @if(!$adminExists)
            <div class="setup-notice">
                👑 <strong>Platform Not Initialized:</strong> No Administrator account exists yet. 
                <a href="{{ route('register') }}" style="color: #fff; font-weight: 700; text-decoration: underline; display: block; margin-top: 0.35rem;">
                    Click here to register the single Administrator ➔
                </a>
            </div>
        @endif

        @if($errors->any())
            <div class="alert-error">
                {{ $errors->first() }}
            </div>
        @endif

        @if(session('status'))
            <div style="background: rgba(16, 185, 129, 0.12); border: 1px solid rgba(16, 185, 129, 0.3); color: #34d399; padding: 0.75rem 1rem; border-radius: var(--radius-md); font-size: 0.85rem; margin-bottom: 1.25rem;">
                {{ session('status') }}
            </div>
        @endif

        <form action="{{ route('login.post') }}" method="POST" id="login-form">
            @csrf
            <div class="form-group">
                <label class="form-label" for="email">Work Email</label>
                <input type="email" id="email" name="email" class="form-control" value="{{ old('email') }}" required autofocus placeholder="name@company.com">
            </div>

            <div class="form-group">
                <label class="form-label" for="password">Password</label>
                <div class="input-password-wrap">
                    <input type="password" id="password" name="password" class="form-control" required placeholder="••••••••" autocomplete="current-password">
                    <button type="button" class="btn-eye-toggle" onclick="togglePassword('password', this)" aria-label="Toggle password visibility" title="Show/Hide Password">
                        <!-- Eye Open -->
                        <svg class="eye-open" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                        <!-- Eye Closed -->
                        <svg class="eye-closed" style="display:none;" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                            <line x1="1" y1="1" x2="23" y2="23"></line>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="checkbox-group">
                <input type="checkbox" id="remember" name="remember">
                <label for="remember">Keep me signed in on this device</label>
            </div>

            <button type="submit" class="btn-submit">Sign In to Dashboard</button>
        </form>

        <div class="auth-footer">
            Need an account? <a href="{{ route('register') }}">{{ !$adminExists ? 'Initialize Admin Account' : 'Register as Employee' }}</a>
        </div>
    </div>

    <script>
        function togglePassword(inputId, btn) {
            const input = document.getElementById(inputId);
            const eyeOpen = btn.querySelector('.eye-open');
            const eyeClosed = btn.querySelector('.eye-closed');

            if (input.type === 'password') {
                input.type = 'text';
                eyeOpen.style.display = 'none';
                eyeClosed.style.display = 'block';
                btn.setAttribute('aria-label', 'Hide password');
            } else {
                input.type = 'password';
                eyeOpen.style.display = 'block';
                eyeClosed.style.display = 'none';
                btn.setAttribute('aria-label', 'Show password');
            }
        }
    </script>
</body>
</html>
