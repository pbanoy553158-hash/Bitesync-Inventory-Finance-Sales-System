<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BiteSync | Waiting for Approval</title>
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
            background: #f7f3ed;
        }

        .card {
            width: min(100%, 520px);
            padding: 42px;
            border: 1px solid #e7ded4;
            border-radius: 22px;
            background: #fcfaf7;
            box-shadow: 0 24px 70px rgba(54, 37, 24, 0.12);
            text-align: center;
        }

        .status-icon {
            width: 62px;
            height: 62px;
            display: grid;
            place-items: center;
            margin: 0 auto 24px;
            border-radius: 50%;
            background: #fbf0df;
            color: #a85f28;
            font-size: 28px;
        }

        .eyebrow {
            margin-bottom: 9px;
            color: #c47a3a;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 1.3px;
            text-transform: uppercase;
        }

        h1 {
            margin: 0 0 12px;
            color: #241a14;
            font-size: clamp(28px, 7vw, 36px);
            letter-spacing: -1px;
        }

        p {
            margin: 0;
            color: #81776f;
            font-size: 14px;
            line-height: 1.7;
        }

        .login-button {
            min-height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: 28px;
            border-radius: 12px;
            background: linear-gradient(135deg, #c47a3a, #a9612b);
            color: white;
            font-size: 14px;
            font-weight: 700;
            text-decoration: none;
        }

        .footer {
            margin-top: 22px;
            color: #9b9189;
            font-size: 11px;
        }
    </style>
</head>
<body>
    <main class="card">
        <div class="status-icon" aria-hidden="true">…</div>
        <div class="eyebrow">Account created</div>
        <h1>Wait for admin approval</h1>
        <p>
            Your account is waiting for an administrator to review and approve
            it. You’ll be able to sign in once your account has been approved.
        </p>
        <a class="login-button" href="{{ route('login') }}">Back to Sign In</a>
        <div class="footer">BiteSync &nbsp;•&nbsp; The Crazy Bite Co.</div>
    </main>
</body>
</html>
