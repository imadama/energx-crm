<!DOCTYPE html>
<html lang="nl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Inloggen — Energx CRM</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display&family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    :root {
      --green-800: #0F4A2A;
      --green-400: #2DBD6E;
      --night: #090F0C;
      --font-display: 'DM Serif Display', serif;
      --font-body: 'Outfit', sans-serif;
    }
    html, body {
      height: 100%; font-family: var(--font-body);
      background: #0d1f14;
    }
    .login-wrap {
      min-height: 100vh;
      display: flex; align-items: center; justify-content: center;
      padding: 24px;
      background: radial-gradient(ellipse at 30% 40%, rgba(45,189,110,.08) 0%, transparent 60%),
                  radial-gradient(ellipse at 70% 80%, rgba(15,74,42,.4) 0%, transparent 60%),
                  #0d1f14;
    }
    .login-card {
      background: #fff;
      border-radius: 16px;
      padding: 40px;
      width: 100%;
      max-width: 400px;
      box-shadow: 0 24px 80px rgba(0,0,0,.4);
    }
    .login-logo {
      display: flex; align-items: center; gap: 10px;
      margin-bottom: 28px;
    }
    .login-logo-text { font-size: 1.1rem; font-weight: 700; color: var(--green-800); }
    .login-logo-sub { font-size: .72rem; color: #aaa; margin-top: 1px; }
    .login-title {
      font-family: var(--font-display);
      font-size: 1.7rem; color: var(--green-800);
      margin-bottom: 6px;
    }
    .login-sub { font-size: .875rem; color: #888; margin-bottom: 28px; }

    .form-group { margin-bottom: 16px; }
    .form-label { display: block; font-size: .8rem; font-weight: 600; color: #444; margin-bottom: 6px; }
    .form-input {
      width: 100%; padding: 10px 14px;
      border: 1.5px solid #e5e7eb; border-radius: 8px;
      font-family: var(--font-body); font-size: .9rem; color: #1a1a1a;
      outline: none; transition: border-color .15s;
    }
    .form-input:focus { border-color: var(--green-400); }
    .form-error { font-size: .78rem; color: #dc2626; margin-top: 4px; }

    .btn-login {
      width: 100%; padding: 11px;
      background: var(--green-400); color: #fff;
      border: none; border-radius: 8px;
      font-family: var(--font-body); font-size: .95rem; font-weight: 600;
      cursor: pointer; transition: background .15s; margin-top: 8px;
    }
    .btn-login:hover { background: #25a560; }

    .alert-error {
      background: rgba(220,38,38,.08); color: #dc2626;
      border: 1px solid rgba(220,38,38,.15);
      border-radius: 8px; padding: 10px 14px;
      font-size: .85rem; margin-bottom: 20px;
    }
  </style>
</head>
<body>
  <div class="login-wrap">
    <div class="login-card">
      <div class="login-logo">
        <svg width="32" height="32" viewBox="0 0 32 32" fill="none">
          <circle cx="16" cy="16" r="16" fill="#0F4A2A"/>
          <path d="M16 5l2.5 8.5H27l-7 5 2.5 8.5L16 22l-6.5 5 2.5-8.5-7-5h8.5L16 5z" fill="#2DBD6E"/>
        </svg>
        <div>
          <div class="login-logo-text">Energx</div>
          <div class="login-logo-sub">CRM & Offerte systeem</div>
        </div>
      </div>

      <h1 class="login-title">Inloggen</h1>
      <p class="login-sub">Welkom terug. Log in om verder te gaan.</p>

      @if($errors->any())
        <div class="alert-error">E-mailadres of wachtwoord is onjuist.</div>
      @endif

      @if(session('status'))
        <div style="background:rgba(45,189,110,.1);color:#1a6e3f;border:1px solid rgba(45,189,110,.2);border-radius:8px;padding:10px 14px;font-size:.85rem;margin-bottom:20px">
          {{ session('status') }}
        </div>
      @endif

      <form method="POST" action="{{ route('login') }}">
        @csrf
        <div class="form-group">
          <label class="form-label" for="email">E-mailadres</label>
          <input class="form-input" type="email" id="email" name="email"
                 value="{{ old('email') }}" required autofocus autocomplete="username"
                 placeholder="jouw@email.nl">
        </div>
        <div class="form-group">
          <label class="form-label" for="password">Wachtwoord</label>
          <input class="form-input" type="password" id="password" name="password"
                 required autocomplete="current-password" placeholder="••••••••">
        </div>
        <button type="submit" class="btn-login">Inloggen</button>
      </form>
    </div>
  </div>
</body>
</html>
