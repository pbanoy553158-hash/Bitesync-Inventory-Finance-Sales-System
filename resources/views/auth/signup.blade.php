<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BiteSync | Create Account</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            margin: 0;
            display: grid;
            place-items: center;
            padding: 24px;
            font-family: "Segoe UI", Arial, Helvetica, sans-serif;
            color: #2c241f;
            background:
                radial-gradient(circle at top left, rgba(196, 122, 58, 0.12), transparent 34%),
                #f7f3ed;
        }

        .card {
            width: min(100%, 520px);
            padding: 42px;
            border: 1px solid #e7ded4;
            border-radius: 22px;
            background: #fcfaf7;
            box-shadow: 0 24px 70px rgba(54, 37, 24, 0.12);
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 13px;
            margin-bottom: 34px;
        }

        .brand-icon {
            width: 48px;
            height: 48px;
            display: grid;
            place-items: center;
            border-radius: 14px;
            background: linear-gradient(135deg, #d79555, #a85f28);
            color: white;
            font-size: 22px;
            font-weight: 800;
        }

        .brand-name {
            color: #241a14;
            font-size: 21px;
            font-weight: 800;
        }

        .brand-tagline {
            margin-top: 3px;
            color: #81776f;
            font-size: 11px;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .eyebrow {
            margin-bottom: 8px;
            color: #c47a3a;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 1.3px;
            text-transform: uppercase;
        }

        h1 {
            margin: 0 0 8px;
            color: #241a14;
            font-size: 34px;
            letter-spacing: -1.2px;
        }

        .intro {
            margin: 0 0 25px;
            color: #81776f;
            font-size: 14px;
            line-height: 1.6;
        }

        .error-box {
            margin-bottom: 20px;
            padding: 13px 16px;
            border: 1px solid #f0cccc;
            border-radius: 12px;
            background: #fff2f1;
            color: #b94b4b;
            font-size: 13px;
        }

        .error-box ul {
            margin: 0;
            padding-left: 18px;
        }

        .form-group {
            margin-bottom: 17px;
        }

        label {
            display: block;
            margin-bottom: 7px;
            color: #34251d;
            font-size: 13px;
            font-weight: 700;
        }

        input {
            width: 100%;
            height: 49px;
            padding: 0 14px;
            border: 1px solid #ddd4ca;
            border-radius: 11px;
            outline: none;
            background: #fff;
            color: #2c241f;
            font: inherit;
            font-size: 14px;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        input:focus {
            border-color: #c47a3a;
            box-shadow: 0 0 0 4px rgba(196, 122, 58, 0.1);
        }

        input::placeholder {
            color: #aaa19a;
        }

        .password-wrapper {
            position: relative;
        }

        .password-wrapper input {
            padding-right: 68px;
        }

        .password-toggle {
            position: absolute;
            top: 50%;
            right: 13px;
            transform: translateY(-50%);
            padding: 6px;
            border: 0;
            background: transparent;
            color: #81776f;
            cursor: pointer;
            font: inherit;
            font-size: 13px;
        }

        .password-toggle:hover {
            color: #a85f28;
        }

        .password-toggle:focus-visible {
            outline: 2px solid #c47a3a;
            outline-offset: 2px;
            border-radius: 4px;
        }

        .submit-button {
            width: 100%;
            min-height: 52px;
            margin-top: 5px;
            border: 0;
            border-radius: 12px;
            background: linear-gradient(135deg, #c47a3a, #a9612b);
            box-shadow: 0 9px 22px rgba(168, 95, 40, 0.2);
            color: white;
            cursor: pointer;
            font: inherit;
            font-size: 14px;
            font-weight: 700;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .submit-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 13px 27px rgba(168, 95, 40, 0.27);
        }

        .login-link {
            margin-top: 22px;
            color: #81776f;
            font-size: 13px;
            text-align: center;
        }

        .login-link a {
            color: #a85f28;
            font-weight: 700;
            text-decoration: none;
        }

        .login-link a:hover {
            text-decoration: underline;
        }

        .footer {
            margin-top: 23px;
            color: #9b9189;
            font-size: 11px;
            text-align: center;
        }

        @media (max-width: 480px) {
            .card {
                padding: 30px 23px;
            }
        }
    </style>
</head>
<body>
    <main class="card">
        <div class="brand">
            <div class="brand-icon" aria-hidden="true">B</div>
            <div>
                <div class="brand-name">BiteSync</div>
                <div class="brand-tagline">Inventory Management System</div>
            </div>
        </div>

        <div class="eyebrow">Get started</div>
        <h1>Create account</h1>
        <p class="intro">Create your BiteSync account and start managing your workspace.</p>

        @if ($errors->any())
            <div class="error-box" role="alert">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('signup.store') }}">
            @csrf

            <div class="form-group">
                <label for="name">Full name</label>
                <input
                    id="name"
                    name="name"
                    type="text"
                    value="{{ old('name') }}"
                    placeholder="Enter your full name"
                    autocomplete="name"
                    maxlength="255"
                    required
                    autofocus
                >
            </div>

            <div class="form-group">
                <label for="email">Email address</label>
                <input
                    id="email"
                    name="email"
                    type="email"
                    value="{{ old('email') }}"
                    placeholder="Enter your email address"
                    autocomplete="email"
                    maxlength="255"
                    required
                >
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <div class="password-wrapper">
                    <input
                        id="password"
                        name="password"
                        type="password"
                        placeholder="At least 8 characters"
                        autocomplete="new-password"
                        minlength="8"
                        required
                    >
                    <button
                        class="password-toggle"
                        type="button"
                        aria-controls="password"
                        aria-pressed="false"
                    >Show</button>
                </div>
            </div>

            <div class="form-group">
                <label for="password_confirmation">Confirm password</label>
                <div class="password-wrapper">
                    <input
                        id="password_confirmation"
                        name="password_confirmation"
                        type="password"
                        placeholder="Enter your password again"
                        autocomplete="new-password"
                        minlength="8"
                        required
                    >
                    <button
                        class="password-toggle"
                        type="button"
                        aria-controls="password_confirmation"
                        aria-pressed="false"
                    >Show</button>
                </div>
            </div>

            <button class="submit-button" type="submit">Create Account</button>
        </form>

        <div class="login-link">
            Already have an account?
            <a href="{{ route('login') }}">Sign in</a>
        </div>
        <div class="footer">BiteSync &nbsp;•&nbsp; The Crazy Bite Co.</div>
    </main>
    <script>
        document.querySelectorAll('.password-toggle').forEach((toggle) => {
            toggle.addEventListener('click', () => {
                const input = document.getElementById(toggle.getAttribute('aria-controls'));
                const isVisible = input.type === 'password';

                input.type = isVisible ? 'text' : 'password';
                toggle.textContent = isVisible ? 'Hide' : 'Show';
                toggle.setAttribute('aria-pressed', String(isVisible));
            });
        });
    </script>
</body>
</html>
