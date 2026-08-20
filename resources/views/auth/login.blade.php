@extends('frontend.frontend_master')

@section('content')

    <div id="app">
        <section class="login-section">
            <div class="container">
                <div class="row justify-content-center align-items-center min-vh-100">
                    <div class="col-12 col-lg-10 col-xl-9">

                        {{-- Main Login Card --}}
                        <div class="login-card row g-0">

                            {{-- Left Banner - Blue Gradient --}}
                            <div class="col-12 col-lg-5 login-banner">
                                <div>
                                    <div class="d-flex align-items-center gap-3 mb-4">
                                        <div class="brand-icon-wrapper">
                                            <img src="{{ asset('assets/img/vm/icon.jpg') }}" alt="V-Suite Logo" class="brand-logo">
                                        </div>
                                        <div>
                                            <h4 class="fw-bold mb-1 lh-sm text-white">V-Suite</h4>
                                            <span class="small text-white-50">Central Administration</span>
                                        </div>
                                    </div>

                                    <h3 class="fw-bold mb-3 text-white d-none d-lg-block">V-Suite</h3>
                                    <p class="small text-white-70 lh-lg mb-4 d-none d-lg-block">
                                        A centralized approval portal for VIMS Hospital to streamline the submission, review, verification, and approval of hospital-related requests through a structured and transparent workflow.
                                    </p>
                                </div>

                                <div class="border-top border-light border-opacity-25 pt-3 d-none d-lg-block">
                                    <div class="small text-white-50 d-flex flex-column gap-1">
                                        <div>V-Suite &ndash; &copy; {{ date('Y') }} All Rights Reserved.</div>
                                        <div class="fw-semibold text-white-70">Design &amp; Developed by VMRF(DU) IT Team</div>
                                    </div>
                                </div>
                            </div>

                            {{-- Right Form - White with Blue Accents --}}
                            <div class="col-12 col-lg-7 login-form-area bg-white">
                                <div class="mb-4">
                                    <h4 class="fw-bold text-dark mb-1">Welcome back</h4>
                                    <p class="text-muted small mb-0">Sign in to your institutional account</p>
                                </div>

                                {{-- Display session messages / errors --}}
                                <div aria-live="polite">
                                    @if(session('info'))
                                        <div class="alert alert-info d-flex align-items-start gap-2 py-2 px-3 small rounded-3 mb-3" role="status">
                                            <i class="bi bi-info-circle mt-1"></i>
                                            <span>{{ session('info') }}</span>
                                        </div>
                                    @endif
                                    @if(session('error'))
                                        <div class="alert alert-danger d-flex align-items-start gap-2 py-2 px-3 small rounded-3 mb-3" role="alert">
                                            <i class="bi bi-exclamation-triangle mt-1"></i>
                                            <span>{{ session('error') }}</span>
                                        </div>
                                    @endif
                                    @if($errors->any())
                                        <div class="alert alert-danger d-flex align-items-start gap-2 py-2 px-3 small rounded-3 mb-3" role="alert">
                                            <i class="bi bi-exclamation-triangle mt-1"></i>
                                            <ul class="mb-0 ps-3">
                                                @foreach($errors->all() as $err)
                                                    <li>{{ $err }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                </div>

                                {{-- Login Form --}}
                                <form method="POST" action="{{ route('auth_login') }}" class="mb-3" id="loginForm" novalidate>
                                    @csrf

                                    <div class="mb-3">
                                        <label for="loginEmail" style="color:#4882a7" class="form-label small fw-semibold ">Email Address</label>
                                        <div class="input-group has-validation">
                                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope text-primary"></i></span>
                                            <input
                                                type="email"
                                                name="email"
                                                id="loginEmail"
                                                class="form-control border-start-0 @error('email') is-invalid @enderror"
                                                placeholder="name@vmrfdu.edu.in"
                                                required
                                                value="{{ old('email') }}"
                                                autocomplete="username"
                                                autofocus
                                                aria-describedby="loginEmailFeedback">
                                        </div>
                                        <div id="loginEmailFeedback" class="field-feedback small text-danger mt-1" role="alert"></div>
                                    </div>

                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <label for="loginPassword" style="color:#4882a7" class="form-label small fw-semibold ">Password</label>
                                            <a href="{{ route('forget.password.get') }}" class="small text-decoration-none text-primary fw-semibold">Forgot Password?</a>
                                        </div>
                                        <div class="input-group has-validation">
                                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-key text-primary"></i></span>
                                            <input
                                                type="password"
                                                name="password"
                                                id="loginPassword"
                                                class="form-control border-start-0 border-end-0 @error('password') is-invalid @enderror"
                                                placeholder="Enter Password"
                                                required
                                                autocomplete="current-password"
                                                aria-describedby="loginPasswordFeedback">
                                            <button class="btn border border-start-0 bg-light" type="button" id="togglePassword" aria-label="Show password" aria-pressed="false">
                                                <i class="bi bi-eye text-muted" id="togglePasswordIcon" aria-hidden="true"></i>
                                            </button>
                                        </div>
                                        <div id="loginPasswordFeedback" class="field-feedback small text-danger mt-1" role="alert"></div>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center mb-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="remember" id="rememberMe" checked>
                                            <label class="form-check-label small text-muted" for="rememberMe">Remember this device</label>
                                        </div>
                                    </div>

                                    <button type="submit" class="btn btn-primary w-100 shadow-sm login-btn" id="loginBtn">
                                        <span class="btn-text">Sign In <i class="bi bi-arrow-right ms-1"></i></span>
                                        <span class="btn-spinner d-none">
                                            <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                                            Logging in&hellip;
                                        </span>
                                    </button>
                                </form>

                                {{-- Optional support links --}}
                                <div class="d-flex justify-content-center gap-3 mt-3 pt-2 border-top border-light">
                                    <a href="#" class="small text-decoration-none text-muted support-link"><i class="bi bi-question-circle me-1"></i>Help</a>
                                    <a href="#" class="small text-decoration-none text-muted support-link"><i class="bi bi-envelope me-1"></i>Support</a>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </section>
    </div>

    {{-- ============= STYLES ============= --}}
    <style>
        /* ── Google Fonts ── */
        @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap');

        :root {
            --primary-blue: #1a56db;
            --primary-dark: #0f2b6b;
            --primary-light: #e8f0fe;
            --focus-ring: 0 0 0 4px rgba(26, 86, 219, 0.22);
        }

        .login-section {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #f0f5ff;
            background-image: radial-gradient(circle at 10% 20%, rgba(26, 86, 219, 0.08) 0%, transparent 50%),
                              radial-gradient(circle at 90% 80%, rgba(26, 86, 219, 0.06) 0%, transparent 50%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 2rem 1rem;
            -webkit-font-smoothing: antialiased;
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: 'Outfit', sans-serif;
            letter-spacing: -0.025em;
        }

        .login-card {
            background: #ffffff;
            border-radius: 32px;
            box-shadow: 0 30px 60px -20px rgba(0, 20, 60, 0.25), 0 0 0 1px rgba(26, 86, 219, 0.06);
            overflow: hidden;
            width: 100%;
            max-width: 1060px;
            animation: fadeInUp 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Respect reduced-motion preference: kill decorative motion */
        @media (prefers-reduced-motion: reduce) {
            .login-card { animation: none; }
            .login-btn, .form-control, .btn { transition: none !important; }
            .login-btn:hover { transform: none; }
        }

        .login-banner {
            background: linear-gradient(145deg, #0a2a6a 0%, #1a56db 70%, #3b82f6 100%);
            color: #ffffff;
            padding: 3.25rem 2.75rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
        }

        .login-banner::before {
            content: '';
            position: absolute;
            top: -80px;
            right: -80px;
            width: 280px;
            height: 280px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.08);
            pointer-events: none;
        }

        .login-banner::after {
            content: '';
            position: absolute;
            bottom: -60px;
            left: -60px;
            width: 200px;
            height: 200px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.05);
            pointer-events: none;
        }

        .brand-icon-wrapper {
            width: 76px;
            height: 76px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            padding: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
            flex-shrink: 0;
            backdrop-filter: blur(4px);
        }

        .brand-logo {
            width: 100%;
            height: 100%;
            object-fit: contain;
            border-radius: 50%;
            background: #fff;
            padding: 4px;
        }

        .text-white-70 { color: rgba(255, 255, 255, 0.75); }
        .text-white-50 { color: rgba(255, 255, 255, 0.55); }

        .login-form-area {
            padding: 3.25rem 3rem;
        }

        .form-label {
            color: #334155;
        }

        .form-control {
            border-radius: 14px;
            padding: 0.85rem 1.15rem;
            border: 1.5px solid #e2e8f0;
            font-size: 0.95rem;
            font-family: 'Plus Jakarta Sans', sans-serif;
            transition: box-shadow 0.2s ease, border-color 0.2s ease, background 0.2s ease;
            background: #fafcff;
            color: #0f172a;
        }

        .form-control::placeholder {
            color: #94a3b8;
        }

        .form-control:focus {
            box-shadow: var(--focus-ring);
            border-color: #1a56db;
            background: #ffffff;
        }

        .form-control.is-invalid {
            border-color: #dc2626;
            background-image: none;
        }
        .form-control.is-invalid:focus {
            box-shadow: 0 0 0 4px rgba(220, 38, 38, 0.15);
        }

        .input-group .form-control {
            border-radius: 0 14px 14px 0;
        }

        .input-group .input-group-text {
            border-radius: 14px 0 0 14px;
            border: 1.5px solid #e2e8f0;
            border-right: none;
            background: #fafcff;
            color: #1a56db;
        }

        .input-group .form-control.border-end-0 {
            border-right: none;
        }

        .input-group .btn.border {
            border-radius: 0 14px 14px 0;
            border: 1.5px solid #e2e8f0 !important;
            border-left: none !important;
            background: #fafcff;
            padding: 0 1rem;
            color: #1a56db;
            transition: background 0.2s ease;
        }

        .input-group .btn.border:hover {
            background: #e8f0fe;
        }

        .input-group .btn.border:focus-visible {
            outline: none;
            box-shadow: var(--focus-ring);
            z-index: 3;
        }

        .field-feedback:empty { display: none; }

        .login-btn {
            background: linear-gradient(135deg, #1a56db 0%, #2563eb 100%);
            color: #fff;
            border: none;
            border-radius: 14px;
            padding: 1rem;
            font-weight: 600;
            font-size: 0.96rem;
            letter-spacing: 0.02em;
            box-shadow: 0 8px 20px rgba(26, 86, 219, 0.35);
            transition: transform 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
        }

        .login-btn:hover {
            background: linear-gradient(135deg, #2563eb 0%, #3b82f6 100%);
            transform: translateY(-2px);
            box-shadow: 0 12px 28px rgba(26, 86, 219, 0.45);
        }

        .login-btn:active {
            transform: scale(0.98);
        }

        .login-btn:focus-visible {
            outline: none;
            box-shadow: 0 0 0 4px rgba(26, 86, 219, 0.3), 0 8px 20px rgba(26, 86, 219, 0.35);
        }

        .login-btn:disabled {
            opacity: 0.85;
            transform: none;
            box-shadow: 0 6px 16px rgba(26, 86, 219, 0.25);
            cursor: progress;
        }

        .login-btn.loading .btn-text { display: none; }
        .login-btn.loading .btn-spinner { display: inline-flex; }
        .btn-spinner { align-items: center; justify-content: center; }

        .form-check-input:checked {
            background-color: #1a56db;
            border-color: #1a56db;
        }

        .form-check-input:focus-visible,
        a:focus-visible,
        .support-link:focus-visible {
            outline: 2px solid #1a56db;
            outline-offset: 2px;
        }

        /* ── smooth error styling ── */
        .alert {
            border-radius: 12px;
            border: 1px solid transparent;
        }
        .alert-danger {
            background: #fee2e2;
            border-color: #fecaca;
            color: #991b1b;
        }
        .alert-info {
            background: #dbeafe;
            border-color: #bfdbfe;
            color: #1e3a8a;
        }

        /* ── Responsive ── */
        @media (max-width: 992px) {
            .login-banner {
                padding: 1.75rem 2rem;
            }
            .login-form-area {
                padding: 2.25rem 2rem 2.5rem;
            }
            .brand-icon-wrapper {
                width: 56px;
                height: 56px;
            }
        }

        @media (max-width: 576px) {
            .login-section {
                padding: 1.25rem 0.75rem;
            }
            .login-card {
                border-radius: 22px;
            }
            .login-banner {
                padding: 1.5rem 1.25rem;
            }
            .login-form-area {
                padding: 1.75rem 1.25rem 2rem;
            }
            .brand-icon-wrapper {
                width: 48px;
                height: 48px;
            }
            .login-btn {
                font-size: 0.9rem;
            }
        }
    </style>

    {{-- ============= SCRIPTS ============= --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            // ── Toggle password visibility (UI-only; does not touch auth logic) ──
            var toggleBtn = document.getElementById('togglePassword');
            var passwordInput = document.getElementById('loginPassword');
            var icon = document.getElementById('togglePasswordIcon');

            if (toggleBtn && passwordInput && icon) {
                toggleBtn.addEventListener('click', function (e) {
                    e.preventDefault();
                    var isHidden = passwordInput.type === 'password';
                    passwordInput.type = isHidden ? 'text' : 'password';
                    icon.classList.toggle('bi-eye', !isHidden);
                    icon.classList.toggle('bi-eye-slash', isHidden);
                    toggleBtn.setAttribute('aria-label', isHidden ? 'Hide password' : 'Show password');
                    toggleBtn.setAttribute('aria-pressed', String(isHidden));
                });
            }

            // ── Lightweight client-side field feedback (cosmetic only; server-side
            //     validation/auth logic in the controller remains the source of truth) ──
            var emailInput = document.getElementById('loginEmail');
            var emailFeedback = document.getElementById('loginEmailFeedback');
            var passwordFeedback = document.getElementById('loginPasswordFeedback');

            function validateEmailField() {
                if (!emailInput) return true;
                if (emailInput.validity.valueMissing) {
                    emailFeedback.textContent = 'Email address is required.';
                    emailInput.classList.add('is-invalid');
                    return false;
                }
                if (emailInput.validity.typeMismatch) {
                    emailFeedback.textContent = 'Enter a valid email address.';
                    emailInput.classList.add('is-invalid');
                    return false;
                }
                emailFeedback.textContent = '';
                emailInput.classList.remove('is-invalid');
                return true;
            }

            function validatePasswordField() {
                if (!passwordInput) return true;
                if (passwordInput.validity.valueMissing) {
                    passwordFeedback.textContent = 'Password is required.';
                    passwordInput.classList.add('is-invalid');
                    return false;
                }
                passwordFeedback.textContent = '';
                passwordInput.classList.remove('is-invalid');
                return true;
            }

            if (emailInput) {
                emailInput.addEventListener('blur', validateEmailField);
                emailInput.addEventListener('input', function () {
                    if (emailInput.classList.contains('is-invalid')) validateEmailField();
                });
            }
            if (passwordInput) {
                passwordInput.addEventListener('blur', validatePasswordField);
                passwordInput.addEventListener('input', function () {
                    if (passwordInput.classList.contains('is-invalid')) validatePasswordField();
                });
            }

            // ── Submit handling: validate, show loading state, then submit as before ──
            var form = document.getElementById('loginForm');
            var loginBtn = document.getElementById('loginBtn');

            if (form && loginBtn) {
                form.addEventListener('submit', function (e) {
                    var emailOk = validateEmailField();
                    var passwordOk = validatePasswordField();

                    if (!emailOk || !passwordOk) {
                        e.preventDefault();
                        var firstInvalid = form.querySelector('.is-invalid');
                        if (firstInvalid) firstInvalid.focus();
                        return;
                    }

                    // Same submission path as before; only the button's visual state changes.
                    loginBtn.classList.add('loading');
                    loginBtn.disabled = true;
                });
            }
        });
    </script>

@endsection