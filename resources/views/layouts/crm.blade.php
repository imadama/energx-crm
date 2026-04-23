<!DOCTYPE html>
<html lang="nl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ $title ?? 'Energx CRM' }}</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    :root {
      --green-800: #0F4A2A;
      --green-600: #1a6e3f;
      --green-400: #2DBD6E;
      --green-100: rgba(45,189,110,.1);
      --night: #090F0C;
      --sidebar-bg: #0d1f14;
      --sidebar-width: 240px;
      --topbar-height: 56px;
      --font-display: 'DM Serif Display', serif;
      --font-body: 'Outfit', sans-serif;
      --radius: 10px;
      --radius-sm: 6px;
    }

    html, body { height: 100%; font-family: var(--font-body); background: #f0f2f5; color: #1a1a1a; }

    /* ── SIDEBAR ── */
    .sidebar {
      position: fixed; top: 0; left: 0; bottom: 0;
      width: var(--sidebar-width);
      background: var(--sidebar-bg);
      display: flex; flex-direction: column;
      z-index: 100;
    }

    .sidebar-logo {
      display: flex; align-items: center; gap: 10px;
      padding: 18px 16px 16px;
      border-bottom: 1px solid rgba(255,255,255,.06);
      text-decoration: none;
    }
    .sidebar-logo-text { font-size: .95rem; font-weight: 700; color: #fff; }
    .sidebar-logo-sub { font-size: .68rem; color: rgba(255,255,255,.35); margin-top: 1px; }

    .sidebar-nav { flex: 1; padding: 8px 0; overflow-y: auto; }

    .nav-section-label {
      font-size: .63rem; font-weight: 600;
      text-transform: uppercase; letter-spacing: .1em;
      color: rgba(255,255,255,.25);
      padding: 14px 16px 6px;
    }

    .nav-item {
      display: flex; align-items: center; gap: 10px;
      padding: 9px 16px; cursor: pointer;
      color: rgba(255,255,255,.55); font-size: .84rem;
      text-decoration: none; transition: all .15s;
      border-left: 3px solid transparent;
    }
    .nav-item:hover { color: rgba(255,255,255,.9); background: rgba(255,255,255,.04); }
    .nav-item.active { color: #fff; background: rgba(45,189,110,.1); border-left-color: var(--green-400); }
    .nav-item svg { width: 16px; height: 16px; flex-shrink: 0; }

    .sidebar-user {
      padding: 12px 16px;
      border-top: 1px solid rgba(255,255,255,.06);
      display: flex; align-items: center; gap: 10px;
    }
    .user-avatar {
      width: 30px; height: 30px; border-radius: 50%;
      background: var(--green-400);
      display: flex; align-items: center; justify-content: center;
      font-size: .75rem; font-weight: 700; color: #fff; flex-shrink: 0;
    }
    .user-name { font-size: .8rem; font-weight: 600; color: #fff; }
    .user-role { font-size: .7rem; color: rgba(255,255,255,.35); }
    .user-logout {
      margin-left: auto; color: rgba(255,255,255,.3);
      cursor: pointer; transition: color .15s;
      background: none; border: none; padding: 4px;
    }
    .user-logout:hover { color: rgba(255,255,255,.7); }
    .user-logout svg { width: 15px; height: 15px; }

    /* ── TOPBAR ── */
    .topbar {
      position: fixed; top: 0; left: var(--sidebar-width); right: 0;
      height: var(--topbar-height);
      background: #fff; border-bottom: 1px solid #e8e8e8;
      display: flex; align-items: center; justify-content: space-between;
      padding: 0 28px; z-index: 90;
    }
    .topbar-title { font-size: 1rem; font-weight: 600; color: #1a1a1a; }
    .topbar-actions { display: flex; align-items: center; gap: 10px; }

    /* ── MAIN ── */
    .main {
      margin-left: var(--sidebar-width);
      margin-top: var(--topbar-height);
      min-height: calc(100vh - var(--topbar-height));
      padding: 32px 28px 60px;
    }

    /* ── BUTTONS ── */
    .btn {
      display: inline-flex; align-items: center; gap: 7px;
      padding: 8px 16px; border-radius: var(--radius-sm);
      font-family: var(--font-body); font-size: .875rem; font-weight: 500;
      cursor: pointer; transition: all .15s; border: none; text-decoration: none;
    }
    .btn svg { width: 15px; height: 15px; }
    .btn-primary { background: var(--green-400); color: #fff; }
    .btn-primary:hover { background: #25a560; }
    .btn-secondary { background: #fff; color: #444; border: 1.5px solid #e5e7eb; }
    .btn-secondary:hover { background: #f9f9f9; }
    .btn-danger { background: #fee2e2; color: #dc2626; }
    .btn-danger:hover { background: #fecaca; }
    .btn-sm { padding: 6px 12px; font-size: .8rem; }

    /* ── CARDS ── */
    .card { background: #fff; border-radius: var(--radius); border: 1px solid #ebebeb; overflow: hidden; }
    .card-header {
      padding: 16px 20px; border-bottom: 1px solid #f0f0f0;
      display: flex; align-items: center; justify-content: space-between;
    }
    .card-title { font-size: .9rem; font-weight: 600; color: #1a1a1a; }
    .card-body { padding: 20px; }

    /* ── STATS ── */
    .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 28px; }
    .stat-card { background: #fff; border-radius: var(--radius); border: 1px solid #ebebeb; padding: 20px; }
    .stat-label { font-size: .75rem; color: #999; font-weight: 500; margin-bottom: 8px; }
    .stat-value { font-size: 1.8rem; font-weight: 700; color: #1a1a1a; line-height: 1; }
    .stat-sub { font-size: .75rem; color: #aaa; margin-top: 6px; }
    .stat-icon { width: 36px; height: 36px; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-bottom: 12px; }
    .stat-icon svg { width: 18px; height: 18px; }
    .stat-icon.green { background: var(--green-100); color: var(--green-400); }
    .stat-icon.blue  { background: rgba(59,130,246,.1); color: #3b82f6; }
    .stat-icon.orange { background: rgba(249,115,22,.1); color: #f97316; }
    .stat-icon.purple { background: rgba(139,92,246,.1); color: #8b5cf6; }

    /* ── TABLES ── */
    .table-wrap { overflow-x: auto; }
    table { width: 100%; border-collapse: collapse; }
    thead th {
      text-align: left; padding: 10px 16px;
      font-size: .72rem; font-weight: 600;
      text-transform: uppercase; letter-spacing: .07em;
      color: #aaa; border-bottom: 1px solid #f0f0f0; white-space: nowrap;
    }
    tbody td { padding: 13px 16px; font-size: .875rem; color: #333; border-bottom: 1px solid #f7f7f7; vertical-align: middle; }
    tbody tr:last-child td { border-bottom: none; }
    tbody tr:hover td { background: #fafafa; }

    /* ── BADGES ── */
    .badge { display: inline-flex; align-items: center; gap: 4px; padding: 3px 9px; border-radius: 20px; font-size: .72rem; font-weight: 600; }
    .badge-concept      { background: #f3f4f6; color: #6b7280; }
    .badge-verstuurd    { background: rgba(59,130,246,.1); color: #3b82f6; }
    .badge-bekeken      { background: rgba(249,115,22,.1); color: #f97316; }
    .badge-geaccepteerd { background: var(--green-100); color: var(--green-600); }
    .badge-afgewezen    { background: rgba(220,38,38,.1); color: #dc2626; }
    .badge-verlopen     { background: #f3f4f6; color: #9ca3af; }

    /* ── FORMS ── */
    .form-group { margin-bottom: 18px; }
    .form-label { display: block; font-size: .8rem; font-weight: 600; color: #444; margin-bottom: 6px; }
    .form-input, .form-select, .form-textarea {
      width: 100%; padding: 9px 12px;
      border: 1.5px solid #e5e7eb; border-radius: var(--radius-sm);
      font-family: var(--font-body); font-size: .9rem; color: #1a1a1a;
      outline: none; transition: border-color .15s; background: #fff;
    }
    .form-input:focus, .form-select:focus, .form-textarea:focus { border-color: var(--green-400); }
    .form-textarea { resize: vertical; min-height: 90px; }
    .form-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
    .form-grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px; }
    .form-error { font-size: .78rem; color: #dc2626; margin-top: 4px; }

    /* ── MODAL ── */
    .modal-backdrop { position: fixed; inset: 0; background: rgba(0,0,0,.4); z-index: 200; display: flex; align-items: center; justify-content: center; }
    .modal { background: #fff; border-radius: 14px; width: 90%; max-height: 90vh; overflow-y: auto; box-shadow: 0 20px 60px rgba(0,0,0,.2); }
    .modal-sm { max-width: 420px; }
    .modal-md { max-width: 580px; }
    .modal-lg { max-width: 760px; }
    .modal-header { padding: 20px 24px 16px; border-bottom: 1px solid #f0f0f0; display: flex; align-items: center; justify-content: space-between; }
    .modal-title { font-size: 1rem; font-weight: 600; color: #1a1a1a; }
    .modal-close { background: none; border: none; cursor: pointer; color: #aaa; transition: color .15s; padding: 4px; }
    .modal-close:hover { color: #555; }
    .modal-close svg { width: 18px; height: 18px; }
    .modal-body { padding: 24px; }
    .modal-footer { padding: 16px 24px; border-top: 1px solid #f0f0f0; display: flex; justify-content: flex-end; gap: 10px; }

    /* ── MISC ── */
    .empty-state { text-align: center; padding: 60px 20px; color: #aaa; }
    .empty-state svg { width: 40px; height: 40px; margin: 0 auto 12px; display: block; opacity: .4; }
    .empty-state p { font-size: .9rem; }

    .page-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px; }
    .page-header h1 { font-family: var(--font-display); font-size: 1.6rem; color: var(--green-800); }
    .page-header p { font-size: .85rem; color: #888; margin-top: 3px; }

    .alert { padding: 12px 16px; border-radius: var(--radius-sm); font-size: .875rem; margin-bottom: 20px; }
    .alert-success { background: var(--green-100); color: var(--green-600); border: 1px solid rgba(45,189,110,.2); }
    .alert-error { background: rgba(220,38,38,.08); color: #dc2626; border: 1px solid rgba(220,38,38,.15); }
  </style>
</head>
<body>

  <aside class="sidebar">
    <a href="{{ route('dashboard') }}" class="sidebar-logo">
      <svg width="28" height="28" viewBox="0 0 32 32" fill="none">
        <circle cx="16" cy="16" r="16" fill="rgba(255,255,255,.1)"/>
        <path d="M16 5l2.5 8.5H27l-7 5 2.5 8.5L16 22l-6.5 5 2.5-8.5-7-5h8.5L16 5z" fill="#2DBD6E"/>
      </svg>
      <div>
        <div class="sidebar-logo-text">Energx</div>
        <div class="sidebar-logo-sub">CRM & Offerte systeem</div>
      </div>
    </a>

    <nav class="sidebar-nav">
      <div class="nav-section-label">Overzicht</div>
      <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/>
          <rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/>
        </svg>
        Dashboard
      </a>

      <div class="nav-section-label">Beheer</div>
      <a href="{{ route('klanten.index') }}" class="nav-item {{ request()->routeIs('klanten.*') ? 'active' : '' }}">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/>
          <path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/>
        </svg>
        Klanten
      </a>
      <a href="{{ route('producten.index') }}" class="nav-item {{ request()->routeIs('producten.*') ? 'active' : '' }}">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z"/>
        </svg>
        Producten
      </a>
      <a href="{{ route('offertes.index') }}" class="nav-item {{ request()->routeIs('offertes.*') ? 'active' : '' }}">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
          <polyline points="14 2 14 8 20 8"/>
          <line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>
        </svg>
        Offertes
      </a>
    </nav>

    <div class="sidebar-user">
      <div class="user-avatar">{{ substr(auth()->user()->name ?? 'A', 0, 1) }}</div>
      <div>
        <div class="user-name">{{ auth()->user()->name ?? 'Admin' }}</div>
        <div class="user-role">Energx team</div>
      </div>
      <form method="POST" action="{{ route('logout') }}" style="margin-left:auto">
        @csrf
        <button type="submit" class="user-logout" title="Uitloggen">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/>
            <polyline points="16 17 21 12 16 7"/>
            <line x1="21" y1="12" x2="9" y2="12"/>
          </svg>
        </button>
      </form>
    </div>
  </aside>

  <header class="topbar">
    <div class="topbar-title">{{ $title ?? 'Dashboard' }}</div>
    <div class="topbar-actions">{{ $actions ?? '' }}</div>
  </header>

  <main class="main">
    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
      <div class="alert alert-error">{{ session('error') }}</div>
    @endif
    {{ $slot }}
  </main>

</body>
</html>
