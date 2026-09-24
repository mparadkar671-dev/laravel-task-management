<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ !$adminExists ? 'Initialize Administrator' : 'Employee Registration' }} | TaskFlow Enterprise</title>
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
            --radius-sm: 6px;
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
            padding: 2.5rem 1.5rem;
            background-image: 
                radial-gradient(circle at 85% 15%, rgba(99, 102, 241, 0.15) 0%, transparent 40%),
                radial-gradient(circle at 15% 85%, rgba(16, 185, 129, 0.12) 0%, transparent 40%);
        }

        .auth-card {
            background: rgba(17, 24, 39, 0.88);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            width: 100%;
            max-width: 520px;
            padding: 2.5rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.65);
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

        .input-password-wrap {
            position: relative;
            display: flex;
            align-items: center;
            width: 100%;
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

        .input-password-wrap .form-control {
            padding-right: 2.75rem;
        }

        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.2);
        }

        .form-control.is-invalid {
            border-color: #f43f5e;
            box-shadow: 0 0 0 2px rgba(244, 63, 94, 0.2);
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

        @media (max-width: 540px) {
            .form-row {
                grid-template-columns: 1fr;
                gap: 0;
            }
        }

        /* Password Rules & Strength Meter UI */
        .password-rules-box {
            background: rgba(11, 15, 25, 0.7);
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            padding: 0.9rem 1rem;
            margin-bottom: 1.5rem;
            margin-top: 0.25rem;
        }

        .strength-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.75rem;
            margin-bottom: 0.4rem;
        }

        .strength-title {
            color: var(--text-muted);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .strength-badge {
            font-weight: 700;
            font-size: 0.75rem;
            color: var(--text-dim);
            transition: color 0.2s ease;
        }

        .strength-meter-track {
            height: 4px;
            background: rgba(255, 255, 255, 0.08);
            border-radius: 9999px;
            overflow: hidden;
            margin-bottom: 0.75rem;
        }

        .strength-meter-bar {
            height: 100%;
            width: 0%;
            background: #ef4444;
            transition: width 0.25s ease, background-color 0.25s ease;
        }

        .rules-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.35rem 0.75rem;
        }

        .rule-item {
            display: flex;
            align-items: center;
            gap: 0.45rem;
            font-size: 0.75rem;
            color: var(--text-dim);
            transition: color 0.2s ease;
        }

        .rule-icon {
            font-size: 0.8rem;
            font-weight: 800;
            width: 14px;
            display: inline-block;
            text-align: center;
            color: var(--text-dim);
            transition: color 0.2s ease;
        }

        .rule-item.valid {
            color: #34d399;
        }

        .rule-item.valid .rule-icon {
            color: #34d399;
        }

        .field-error {
            color: #fb7185;
            font-size: 0.775rem;
            margin-top: 0.35rem;
        }

        .btn-submit {
            width: 100%;
            background: var(--primary-gradient);
            color: #ffffff;
            border: none;
            padding: 0.85rem;
            border-radius: var(--radius-md);
            font-size: 0.95rem;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 4px 14px rgba(99, 102, 241, 0.35);
            transition: all 0.2s ease;
            margin-top: 0.25rem;
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
                <strong>Authentication Notice:</strong> {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('register.post') }}" method="POST" id="register-form" novalidate>
            @csrf
            <div class="form-group">
                <label class="form-label" for="name">Full Name *</label>
                <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required autofocus placeholder="John Doe">
                @error('name')
                    <div class="field-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="email">Work Email *</label>
                <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required placeholder="name@company.com">
                @error('email')
                    <div class="field-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="password">Password *</label>
                    <div class="input-password-wrap">
                        <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" required placeholder="Create secure password" autocomplete="new-password">
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
                    @error('password')
                        <div class="field-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="password_confirmation">Confirm Password *</label>
                    <div class="input-password-wrap">
                        <input type="password" id="password_confirmation" name="password_confirmation" class="form-control @error('password_confirmation') is-invalid @enderror" required placeholder="Repeat password" autocomplete="new-password">
                        <button type="button" class="btn-eye-toggle" onclick="togglePassword('password_confirmation', this)" aria-label="Toggle password confirmation visibility" title="Show/Hide Password">
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
                    @error('password_confirmation')
                        <div class="field-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Password Security Verification Checklist & Strength Meter -->
            <div class="password-rules-box">
                <div class="strength-header">
                    <span class="strength-title">Password Security Policy</span>
                    <span class="strength-badge" id="strength-label">Enter password</span>
                </div>
                <div class="strength-meter-track">
                    <div class="strength-meter-bar" id="strength-bar"></div>
                </div>
                <div class="rules-grid">
                    <div class="rule-item" id="rule-length">
                        <span class="rule-icon">○</span>
                        <span>8+ characters</span>
                    </div>
                    <div class="rule-item" id="rule-upper">
                        <span class="rule-icon">○</span>
                        <span>1 uppercase letter (A-Z)</span>
                    </div>
                    <div class="rule-item" id="rule-lower">
                        <span class="rule-icon">○</span>
                        <span>1 lowercase letter (a-z)</span>
                    </div>
                    <div class="rule-item" id="rule-number">
                        <span class="rule-icon">○</span>
                        <span>1 number (0-9)</span>
                    </div>
                    <div class="rule-item" id="rule-symbol">
                        <span class="rule-icon">○</span>
                        <span>1 symbol (@$!%*#?&)</span>
                    </div>
                    <div class="rule-item" id="rule-match">
                        <span class="rule-icon">○</span>
                        <span>Passwords match</span>
                    </div>
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

        const pwdInput = document.getElementById('password');
        const confirmInput = document.getElementById('password_confirmation');
        const strengthBar = document.getElementById('strength-bar');
        const strengthLabel = document.getElementById('strength-label');

        const rules = {
            length: document.getElementById('rule-length'),
            upper: document.getElementById('rule-upper'),
            lower: document.getElementById('rule-lower'),
            number: document.getElementById('rule-number'),
            symbol: document.getElementById('rule-symbol'),
            match: document.getElementById('rule-match')
        };

        function setRuleStatus(el, isValid) {
            const icon = el.querySelector('.rule-icon');
            if (isValid) {
                el.classList.add('valid');
                icon.textContent = '✓';
            } else {
                el.classList.remove('valid');
                icon.textContent = '○';
            }
        }

        function validatePassword() {
            const val = pwdInput.value;
            const confirmVal = confirmInput.value;

            const isLength = val.length >= 8;
            const isUpper = /[A-Z]/.test(val);
            const isLower = /[a-z]/.test(val);
            const isNumber = /[0-9]/.test(val);
            const isSymbol = /[^A-Za-z0-9]/.test(val);
            const isMatch = val.length > 0 && val === confirmVal;

            setRuleStatus(rules.length, isLength);
            setRuleStatus(rules.upper, isUpper);
            setRuleStatus(rules.lower, isLower);
            setRuleStatus(rules.number, isNumber);
            setRuleStatus(rules.symbol, isSymbol);
            setRuleStatus(rules.match, isMatch);

            // Calculate strength score
            let score = 0;
            if (isLength) score++;
            if (isUpper && isLower) score++;
            if (isNumber) score++;
            if (isSymbol) score++;

            if (val.length === 0) {
                strengthBar.style.width = '0%';
                strengthLabel.textContent = 'Enter password';
                strengthLabel.style.color = 'var(--text-dim)';
            } else if (score <= 1) {
                strengthBar.style.width = '25%';
                strengthBar.style.backgroundColor = '#ef4444'; // Red
                strengthLabel.textContent = 'Weak';
                strengthLabel.style.color = '#ef4444';
            } else if (score === 2) {
                strengthBar.style.width = '50%';
                strengthBar.style.backgroundColor = '#f59e0b'; // Amber
                strengthLabel.textContent = 'Fair';
                strengthLabel.style.color = '#f59e0b';
            } else if (score === 3) {
                strengthBar.style.width = '75%';
                strengthBar.style.backgroundColor = '#3b82f6'; // Blue
                strengthLabel.textContent = 'Good';
                strengthLabel.style.color = '#3b82f6';
            } else {
                strengthBar.style.width = '100%';
                strengthBar.style.backgroundColor = '#10b981'; // Green
                strengthLabel.textContent = 'Strong & Secure';
                strengthLabel.style.color = '#10b981';
            }
        }

        pwdInput.addEventListener('input', validatePassword);
        confirmInput.addEventListener('input', validatePassword);
    </script>
</body>
</html>
