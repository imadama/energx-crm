<!DOCTYPE html>
<html lang="nl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $offerte->nummer }} — Energx offerte</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Momo+Signature&display=swap" rel="stylesheet">
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    :root {
      --green-800: #0F4A2A; --green-600: #1a6e3f; --green-400: #2DBD6E;
      --night: #090F0C; --sidebar-bg: #0d1f14; --sidebar-width: 260px;
      --topbar-height: 60px;
      --font-display: 'DM Serif Display', serif; --font-body: 'Outfit', sans-serif;
    }
    html, body { height: 100%; font-family: var(--font-body); background: #eef0f3; color: #1a1a1a; }

    /* TOPBAR */
    .topbar { position: fixed; top: 0; left: 0; right: 0; height: var(--topbar-height); background: #fff; border-bottom: 1px solid #e5e7eb; display: flex; align-items: center; justify-content: space-between; padding: 0 24px; z-index: 100; box-shadow: 0 1px 6px rgba(0,0,0,.07); }
    .topbar-brand { display: flex; align-items: center; gap: 10px; text-decoration: none; color: var(--green-800); }
    .topbar-brand span { font-weight: 700; font-size: 1.05rem; }
    .topbar-actions { display: flex; align-items: center; gap: 6px; }
    .btn-icon { display: flex; flex-direction: column; align-items: center; gap: 2px; padding: 6px 10px; border: none; background: none; cursor: pointer; color: #666; font-size: .68rem; font-family: var(--font-body); border-radius: 8px; transition: background .15s; }
    .btn-icon:hover { background: #f3f4f6; }
    .btn-icon svg { width: 18px; height: 18px; }
    .btn-accept { display: flex; align-items: center; gap: 8px; padding: 8px 16px; background: var(--green-400); color: #fff; border: none; border-radius: 8px; font-family: var(--font-body); font-weight: 600; font-size: .875rem; cursor: pointer; transition: background .15s; }
    .btn-accept:hover { background: #25a560; }
    .btn-accept.accepted { background: #1a6e3f; cursor: default; }
    .btn-nav { display: flex; align-items: center; justify-content: center; width: 34px; height: 34px; border: 1px solid #e5e7eb; background: #fff; border-radius: 8px; cursor: pointer; color: #555; transition: background .15s; }
    .btn-nav:hover { background: #f3f4f6; }
    .btn-nav:disabled { opacity: .35; cursor: default; }
    .btn-nav svg { width: 15px; height: 15px; }

    /* SIDEBAR */
    .sidebar { position: fixed; top: var(--topbar-height); left: 0; bottom: 0; width: var(--sidebar-width); background: var(--sidebar-bg); overflow-y: auto; z-index: 90; display: flex; flex-direction: column; }
    .sidebar-header { padding: 20px 16px 16px; border-bottom: 1px solid rgba(255,255,255,.07); }
    .sidebar-logo { display: flex; align-items: center; gap: 9px; margin-bottom: 14px; }
    .sidebar-logo-text { font-size: .85rem; font-weight: 600; color: #fff; }
    .proposal-title { font-size: .84rem; font-weight: 600; color: #fff; line-height: 1.4; }
    .proposal-date { font-size: .73rem; color: rgba(255,255,255,.4); margin-top: 4px; }
    .sidebar-nav { padding: 6px 0; flex: 1; }
    .nav-item { display: flex; align-items: center; gap: 10px; padding: 10px 16px; cursor: pointer; color: rgba(255,255,255,.55); font-size: .84rem; text-decoration: none; transition: all .15s; border-left: 3px solid transparent; }
    .nav-item:hover { color: rgba(255,255,255,.85); background: rgba(255,255,255,.04); }
    .nav-item.active { color: #fff; background: rgba(45,189,110,.1); border-left-color: var(--green-400); }
    .nav-item svg { width: 13px; height: 13px; flex-shrink: 0; opacity: .6; }
    .sidebar-bijlagen { padding: 14px 16px; border-top: 1px solid rgba(255,255,255,.07); }
    .bijlagen-label { font-size: .67rem; font-weight: 600; text-transform: uppercase; letter-spacing: .09em; color: rgba(255,255,255,.3); margin-bottom: 8px; }
    .bijlage-item { display: flex; align-items: center; gap: 8px; padding: 6px 0; color: rgba(255,255,255,.5); font-size: .8rem; cursor: pointer; transition: color .15s; }
    .bijlage-item:hover { color: rgba(255,255,255,.85); }

    /* MAIN */
    .main {
      margin-left: var(--sidebar-width);
      margin-top: var(--topbar-height);
      min-height: calc(100vh - var(--topbar-height));
      padding: 48px 40px 80px;
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 36px;
    }
    .section {
      width: 794px; /* A4 @ 96dpi */
      scroll-margin-top: calc(var(--topbar-height) + 20px);
    }
    .page-card {
      width: 794px;
      min-height: 1123px; /* A4 @ 96dpi */
      background: #fff;
      border-radius: 3px;
      box-shadow: 0 4px 32px rgba(0,0,0,.12), 0 1px 6px rgba(0,0,0,.06);
      overflow: hidden;
    }

    /* VOORBLAD */
    .cover-hero { width: 100%; height: 380px; object-fit: cover; display: block; }
    .cover-hero-placeholder { width: 100%; height: 380px; background: linear-gradient(135deg, var(--green-800) 0%, #1a5c35 45%, var(--night) 100%); display: flex; flex-direction: column; align-items: center; justify-content: center; }
    .cover-hero-title { font-family: var(--font-display); font-size: 3.5rem; color: #fff; }
    .cover-hero-sub { font-size: .9rem; color: rgba(255,255,255,.55); margin-top: 6px; }
    .cover-body { padding: 36px 48px 40px; }
    .cover-meta { display: flex; gap: 40px; padding-bottom: 28px; border-bottom: 1px solid #f0f0f0; margin-bottom: 28px; flex-wrap: wrap; }
    .meta-label { font-size: .7rem; font-weight: 600; text-transform: uppercase; letter-spacing: .08em; color: #aaa; margin-bottom: 5px; }
    .meta-value { font-size: .95rem; font-weight: 600; color: #1a1a1a; }
    .meta-value small { display: block; font-weight: 400; color: #777; font-size: .85rem; margin-top: 2px; }

    /* CONTENT */
    .section-content { padding: 48px; }
    .section-title { font-family: var(--font-display); font-size: 2rem; color: var(--green-800); margin-bottom: 28px; padding-bottom: 16px; border-bottom: 2px solid var(--green-400); }
    .intro-text p { color: #4a4a4a; line-height: 1.85; margin-bottom: 16px; font-size: .93rem; }

    /* PRIJZEN */
    .price-table { width: 100%; border-collapse: collapse; }
    .price-table th { text-align: left; padding: 10px 14px; font-size: .7rem; font-weight: 600; text-transform: uppercase; letter-spacing: .07em; color: #999; border-bottom: 2px solid #e8e8e8; }
    .price-table th:last-child, .price-table td:last-child { text-align: right; }
    .price-table td { padding: 14px 14px; font-size: .88rem; color: #333; border-bottom: 1px solid #f2f2f2; vertical-align: top; }
    .price-table tbody tr:hover td { background: #fafafa; }
    .item-name { font-weight: 500; color: #1a1a1a; }
    .item-desc { font-size: .78rem; color: #999; margin-top: 3px; }
    .price-totals { display: flex; flex-direction: column; align-items: flex-end; gap: 7px; padding: 20px 14px 4px; }
    .total-row { display: flex; gap: 48px; font-size: .88rem; color: #666; }
    .total-row .lbl { min-width: 110px; text-align: right; }
    .total-row .val { min-width: 90px; text-align: right; }
    .total-row.grand { font-size: 1.05rem; font-weight: 700; color: var(--green-800); padding-top: 12px; border-top: 2px solid #e8e8e8; margin-top: 4px; }

    /* PRODUCT */
    .product-layout { display: grid; grid-template-columns: 1fr 1fr; gap: 36px; }
    .spec-list { margin-top: 16px; }
    .spec-row { display: flex; justify-content: space-between; padding: 9px 0; border-bottom: 1px solid #f2f2f2; font-size: .85rem; }
    .spec-row:last-child { border-bottom: none; }
    .spec-key { color: #888; }
    .spec-val { font-weight: 500; color: #1a1a1a; text-align: right; }
    .product-info p { color: #555; line-height: 1.75; font-size: .9rem; margin-bottom: 14px; }

    /* WERKWIJZE */
    .step { display: flex; gap: 20px; padding: 20px 0; border-bottom: 1px solid #f2f2f2; }
    .step:last-child { border-bottom: none; }
    .step-num { width: 34px; height: 34px; border-radius: 50%; background: var(--green-400); color: #fff; font-weight: 700; font-size: .82rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px; }
    .step-title { font-weight: 600; color: #1a1a1a; margin-bottom: 5px; font-size: .9rem; }
    .step-desc { font-size: .85rem; color: #666; line-height: 1.65; }

    /* ACCEPTATIE */
    .accept-page { padding: 64px 48px; text-align: center; }
    .accept-icon { width: 60px; height: 60px; border-radius: 50%; background: rgba(45,189,110,.1); color: var(--green-400); display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; }
    .accept-icon svg { width: 28px; height: 28px; }
    .accept-title { font-family: var(--font-display); font-size: 1.9rem; color: var(--green-800); margin-bottom: 12px; }
    .accept-desc { color: #666; font-size: .93rem; line-height: 1.75; max-width: 460px; margin: 0 auto 32px; }
    .btn-accept-big { display: inline-flex; align-items: center; gap: 10px; padding: 13px 26px; background: var(--green-400); color: #fff; border: none; border-radius: 10px; font-family: var(--font-body); font-weight: 600; font-size: .95rem; cursor: pointer; transition: background .15s; }
    .btn-accept-big:hover { background: #25a560; }
    .sign-placeholder { margin: 32px auto 0; max-width: 400px; padding: 24px; border: 2px dashed #e0e0e0; border-radius: 12px; font-size: .85rem; color: #bbb; }

    /* MODAL */
    .modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,.45); z-index: 200; display: flex; align-items: center; justify-content: center; opacity: 0; pointer-events: none; transition: opacity .2s; }
    .modal-overlay.open { opacity: 1; pointer-events: all; }
    .modal { background: #fff; border-radius: 18px; padding: 34px; max-width: 920px; width: min(920px, calc(100vw - 36px)); text-align: left; transform: scale(.98); transition: transform .2s; box-shadow: 0 20px 60px rgba(0,0,0,.25); }
    .modal-overlay.open .modal { transform: scale(1); }
    .modal-header { display:flex; align-items:flex-start; justify-content:space-between; gap: 16px; margin-bottom: 18px; }
    .modal-title { font-family: var(--font-display); font-size: 1.85rem; color: var(--green-800); line-height: 1.1; }
    .modal-subtitle { color:#4b5563; font-size: .98rem; line-height: 1.5; margin-top: 6px; }
    .modal-close-x { width: 40px; height: 40px; border-radius: 12px; border: 1px solid #e5e7eb; background: #fff; cursor: pointer; color:#555; display:flex; align-items:center; justify-content:center; }
    .modal-close-x:hover { background:#f3f4f6; }

    .modal-meta { display:grid; grid-template-columns: 1fr; gap: 8px; padding: 14px 16px; border: 1px solid #eef0f3; background: #f9fafb; border-radius: 14px; margin-bottom: 18px; }
    @media (min-width: 900px) { .modal-meta { grid-template-columns: 1.4fr 1fr 1fr; } }
    .meta-item .lbl { font-size:.72rem; font-weight: 700; text-transform: uppercase; letter-spacing:.08em; color:#9ca3af; }
    .meta-item .val { margin-top: 2px; font-size:.95rem; font-weight: 600; color:#111827; }

    .modal-input { width: 100%; padding: 10px 14px; border: 1.5px solid #e5e7eb; border-radius: 8px; font-family: var(--font-body); font-size: .9rem; margin-bottom: 10px; outline: none; transition: border-color .15s; }
    .modal-input:focus { border-color: var(--green-400); }

    .momo-signature-regular {
      font-family: "Momo Signature", cursive;
      font-weight: 400;
      font-style: normal;
    }

    .sig-label { font-size:.78rem; font-weight: 700; color:#374151; margin: 14px 0 6px; }
    .sig-tabs {
      display: flex;
      gap: 0;
      border-bottom: 1px solid #e5e7eb;
      margin-bottom: 12px;
    }
    .sig-tab {
      padding: 10px 14px;
      border: none;
      background: transparent;
      cursor: pointer;
      font-family: var(--font-body);
      font-size: .9rem;
      font-weight: 700;
      color: #64748b;
      border-bottom: 3px solid transparent;
      margin-bottom: -1px;
    }
    .sig-tab:hover { color: #334155; background: rgba(15, 74, 42, .03); }
    .sig-tab.active {
      color: var(--green-800);
      border-bottom-color: var(--green-400);
      background: rgba(45,189,110,.08);
    }

    .sig-type-input { width: 100%; padding: 10px 12px; border: 1.5px solid #e5e7eb; border-radius: 10px; font-family: var(--font-body); font-size: .95rem; outline:none; }
    .sig-type-input:focus { border-color: var(--green-400); }

    .sig-topbar { height: 44px; display:flex; align-items:center; }
    .sig-topbar .sig-type-input { margin: 0; }

    .sig-pane {
      height: 320px; /* fixed to prevent layout shift between tabs */
      display: flex;
      flex-direction: column;
      gap: 10px;
    }
    .sig-pane .modal-error {
      min-height: 20px; /* reserve space even when hidden */
      display: block;
      visibility: hidden;
      margin-top: 0;
    }
    .sig-pane .modal-error.show { visibility: visible; }

    .sig-preview {
      border: 1.5px dashed #dbe3ea;
      border-radius: 12px;
      background: #fff;
      height: 180px; /* match drawing pad height */
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 12px 16px;
      overflow: hidden;
    }
    .sig-preview-text { font-size: 44px; line-height: 1; color: #111827; white-space: nowrap; transform-origin: center; }
    .sig-preview-placeholder { color:#9ca3af; font-size:.9rem; }

    .sig-canvas-wrap { border: 1.5px dashed #dbe3ea; border-radius: 12px; background:#fff; height: 180px; overflow:hidden; position: relative; }
    .sig-canvas { width: 100%; height: 180px; display:block; touch-action: none; }
    .sig-canvas-actions { display:flex; justify-content:flex-end; gap:8px; margin-top: 10px; }
    .sig-clear { padding: 8px 10px; border-radius: 10px; border: 1px solid #e5e7eb; background:#fff; cursor:pointer; font-family: var(--font-body); font-weight: 600; font-size:.85rem; color:#555; }
    .sig-clear:hover { background:#f3f4f6; }

    .modal-footer { display:flex; align-items:center; justify-content:space-between; gap: 12px; margin-top: 18px; padding-top: 14px; border-top: 1px solid #f1f5f9; }
    .modal-link { color:#64748b; font-size:.9rem; text-decoration:none; cursor:pointer; }
    .modal-link:hover { text-decoration: underline; }
    .modal-save { padding: 12px 18px; background: var(--green-400); color:#fff; border:none; border-radius: 12px; font-family: var(--font-body); font-weight: 700; font-size:.95rem; cursor:pointer; }
    .modal-save:hover { background:#25a560; }

    .modal-check { display:flex; gap: 10px; align-items:flex-start; margin-top: 14px; }
    .modal-check input { margin-top: 3px; }
    .modal-check label { font-size: .88rem; color:#374151; line-height: 1.45; }
    .modal-error {
      margin-top: 6px;
      font-size: .86rem;
      color: #b91c1c;
      line-height: 1.35;
      display: none;
    }
    .modal-error.show { display: block; }
    .modal-input.invalid, .sig-type-input.invalid, .sig-canvas-wrap.invalid {
      border-color: #ef4444 !important;
      background: #fff7f7;
    }
    .sig-actions-spacer { height: 40px; display:flex; align-items:center; justify-content:flex-end; }
    .sig-actions-spacer.is-hidden { visibility: hidden; }
  </style>
</head>
<body>
  @php
    $secties = $offerte->secties;
    $isGeaccepteerd = $offerte->status === 'geaccepteerd';
  @endphp

  <!-- TOP BAR -->
  <header class="topbar">
    <a class="topbar-brand" href="/">
      <svg width="30" height="30" viewBox="0 0 32 32" fill="none">
        <circle cx="16" cy="16" r="16" fill="#0F4A2A"/>
        <path d="M16 5l2.5 8.5H27l-7 5 2.5 8.5L16 22l-6.5 5 2.5-8.5-7-5h8.5L16 5z" fill="#2DBD6E"/>
      </svg>
      <span>Energx</span>
    </a>
    <div class="topbar-actions">
      <a class="btn-icon" href="{{ route('offertes.pdf', $offerte->token) }}">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
        pdf
      </a>
      @if(!$isGeaccepteerd)
        <button class="btn-accept" id="topbar-accept" onclick="openModal()">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="15" height="15"><polyline points="20 6 9 17 4 12"/></svg>
          offerte goedkeuren
        </button>
      @else
        <span class="btn-accept accepted">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="15" height="15"><polyline points="20 6 9 17 4 12"/></svg>
          Geaccepteerd
        </span>
      @endif
      <button class="btn-nav" id="btn-prev" onclick="navigate(-1)" disabled>
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
      </button>
      <button class="btn-nav" id="btn-next" onclick="navigate(1)">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
      </button>
    </div>
  </header>

  <!-- SIDEBAR -->
  <aside class="sidebar">
    <div class="sidebar-header">
      <div class="sidebar-logo">
        <svg width="22" height="22" viewBox="0 0 32 32" fill="none">
          <circle cx="16" cy="16" r="16" fill="rgba(255,255,255,.12)"/>
          <path d="M16 5l2.5 8.5H27l-7 5 2.5 8.5L16 22l-6.5 5 2.5-8.5-7-5h8.5L16 5z" fill="#2DBD6E"/>
        </svg>
        <span class="sidebar-logo-text">Energx.nl</span>
      </div>
      <div class="proposal-title">{{ $offerte->nummer }} — {{ $offerte->klant->naam }}</div>
      <div class="proposal-date">{{ $offerte->created_at->format('d M Y') }}</div>
    </div>
    <nav class="sidebar-nav">
      @foreach($secties as $index => $sectie)
      <div class="nav-item {{ $index === 0 ? 'active' : '' }}" data-index="{{ $index }}" onclick="goTo({{ $index }})">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
        {{ $sectie->titel }}
      </div>
      @endforeach
    </nav>
    <div class="sidebar-bijlagen">
      <div class="bijlagen-label">Bijlagen</div>
      <div class="bijlage-item">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21.44 11.05l-9.19 9.19a6 6 0 01-8.49-8.49l9.19-9.19a4 4 0 015.66 5.66l-9.2 9.19a2 2 0 01-2.83-2.83l8.49-8.48"/></svg>
        Algemene voorwaarden
      </div>
    </div>
  </aside>

  <!-- MAIN -->
  <main class="main">
    @foreach($secties as $index => $sectie)
    @php $inhoud = $sectie->inhoud ?? []; @endphp
    <section class="section" id="s-{{ $index }}" data-index="{{ $index }}">
      <div class="page-card">

        {{-- VOORBLAD --}}
        @if($sectie->type === 'voorblad')
          <div class="cover-hero-placeholder">
            <div class="cover-hero-title">Energx</div>
            <div class="cover-hero-sub">www.energx.nl</div>
          </div>
          <div class="cover-body">
            <div class="cover-meta">
              <div><div class="meta-label">Offerte voor</div><div class="meta-value">{{ $offerte->klant->naam }}<small>{{ $offerte->klant->adres }}</small></div></div>
              <div><div class="meta-label">Offerte nr.</div><div class="meta-value">{{ $offerte->nummer }}</div></div>
              <div><div class="meta-label">Datum</div><div class="meta-value">{{ $offerte->created_at->format('d F Y') }}</div></div>
              @if($offerte->geldig_tot)<div><div class="meta-label">Geldig tot</div><div class="meta-value">{{ $offerte->geldig_tot->format('d F Y') }}</div></div>@endif
            </div>
            @if($offerte->inleiding)
              <div><div class="meta-label">Betreft</div><div class="meta-value" style="margin-top:6px">{{ $offerte->inleiding }}</div></div>
            @endif
          </div>

        {{-- INTRODUCTIE / TEKST --}}
        @elseif(in_array($sectie->type, ['introductie', 'tekst']))
          <div class="section-content">
            <h2 class="section-title">{{ $sectie->titel }}</h2>
            <div class="intro-text">
              @foreach(explode("\n\n", $inhoud['tekst'] ?? '') as $alinea)
                <p>{{ str_replace('[naam]', $offerte->klant->naam, $alinea) }}</p>
              @endforeach
            </div>
          </div>

        {{-- PRIJZEN --}}
        @elseif($sectie->type === 'prijzen')
          <div class="section-content">
            <h2 class="section-title">{{ $sectie->titel }}</h2>
            <table class="price-table">
              <thead><tr><th style="width:48%">Omschrijving</th><th>Aantal</th><th>Eenheidsprijs</th><th>Totaal</th></tr></thead>
              <tbody>
                @foreach($offerte->regels as $regel)
                <tr>
                  <td><div class="item-name">{{ $regel->naam }}</div>@if($regel->beschrijving)<div class="item-desc">{{ $regel->beschrijving }}</div>@endif</td>
                  <td>{{ $regel->aantal }}</td>
                  <td>€ {{ number_format($regel->eenheidsprijs, 2, ',', '.') }}</td>
                  <td>€ {{ number_format($regel->totaal, 2, ',', '.') }}</td>
                </tr>
                @endforeach
              </tbody>
            </table>
            <div class="price-totals">
              <div class="total-row"><span class="lbl">Subtotaal</span><span class="val">€ {{ number_format($offerte->subtotaal, 2, ',', '.') }}</span></div>
              <div class="total-row"><span class="lbl">BTW (21%)</span><span class="val">€ {{ number_format($offerte->btw_bedrag, 2, ',', '.') }}</span></div>
              <div class="total-row grand"><span class="lbl">Totaal incl. BTW</span><span class="val">€ {{ number_format($offerte->totaal, 2, ',', '.') }}</span></div>
            </div>
          </div>

        {{-- PRODUCT INFO --}}
        @elseif($sectie->type === 'product')
          <div class="section-content">
            <h2 class="section-title">{{ $sectie->titel }}</h2>
            <div class="product-layout">
              <div>
                <div style="width:100%;aspect-ratio:1;background:linear-gradient(135deg,#f0f0f0,#e0e0e0);border-radius:12px;display:flex;align-items:center;justify-content:center;color:#bbb;font-size:3rem">⚡</div>
                @if(!empty($inhoud['specs']))
                <div class="spec-list">
                  @foreach($inhoud['specs'] as $spec)
                    <div class="spec-row"><span class="spec-key">{{ $spec['label'] }}</span><span class="spec-val">{{ $spec['waarde'] }}</span></div>
                  @endforeach
                </div>
                @endif
              </div>
              <div class="product-info">
                @foreach(explode("\n\n", $inhoud['beschrijving'] ?? '') as $alinea)
                  @if(trim($alinea))<p>{{ $alinea }}</p>@endif
                @endforeach
              </div>
            </div>
          </div>

        {{-- WERKWIJZE --}}
        @elseif($sectie->type === 'werkwijze')
          <div class="section-content">
            <h2 class="section-title">{{ $sectie->titel }}</h2>
            <div>
              @foreach($inhoud['stappen'] ?? [] as $i => $stap)
              <div class="step">
                <div class="step-num">{{ $i + 1 }}</div>
                <div>
                  <div class="step-title">{{ $stap['titel'] }}</div>
                  <div class="step-desc">{{ $stap['beschrijving'] }}</div>
                </div>
              </div>
              @endforeach
            </div>
          </div>

        {{-- ACCEPTATIE --}}
        @elseif($sectie->type === 'acceptatie')
          <div class="accept-page">
            @if($isGeaccepteerd)
              <div class="accept-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg></div>
              <h2 class="accept-title">Offerte geaccepteerd</h2>
              <p class="accept-desc">Deze offerte is geaccepteerd door <strong>{{ $offerte->geaccepteerd_door }}</strong> op {{ $offerte->geaccepteerd_op->format('d F Y \o\m H:i') }}.</p>
            @else
              <div class="accept-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg></div>
              <h2 class="accept-title">{{ $sectie->titel }}</h2>
              <p class="accept-desc">{{ $inhoud['tekst'] ?? 'Ben je akkoord met deze offerte? Klik op de knop hieronder om digitaal te ondertekenen.' }}</p>
              <button class="btn-accept-big" onclick="openModal()">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                Offerte goedkeuren
              </button>
              <div class="sign-placeholder" id="sign-placeholder">Digitale handtekening verschijnt hier na goedkeuring</div>
            @endif
          </div>
        @endif

      </div>
    </section>
    @endforeach
  </main>

  @if(!$isGeaccepteerd)
  <div class="modal-overlay" id="modal" onclick="if(event.target===this)closeModal()">
    <div class="modal">
      <div class="modal-header">
        <div>
          <div class="modal-title">Offerte goedkeuren</div>
          <div class="modal-subtitle">Fijn dat u voor ons wilt kiezen voor de installatie van uw laadpaal!</div>
        </div>
        <button type="button" class="modal-close-x" onclick="closeModal()" aria-label="Sluiten">✕</button>
      </div>

      <div class="modal-meta">
        <div class="meta-item">
          <div class="lbl">Offerte</div>
          <div class="val">{{ $offerte->inleiding ?: $offerte->nummer }}</div>
        </div>
        <div class="meta-item">
          <div class="lbl">Contact</div>
          <div class="val">{{ $offerte->klant->naam }}</div>
        </div>
        <div class="meta-item">
          <div class="lbl">Datum</div>
          <div class="val">{{ now()->format('d-m-Y') }}</div>
        </div>
      </div>

      <form method="POST" action="{{ route('offertes.accepteer', $offerte->token) }}" id="accept-form" novalidate>
        @csrf
        <div>
          <div class="sig-label">Naam:</div>
          <input class="modal-input" type="text" name="naam" id="accept-naam" placeholder="Uw volledige naam" required>
          <div class="modal-error" id="err-naam"></div>
        </div>

        <div class="sig-label">Handtekening:</div>

        <div class="sig-tabs" role="tablist" aria-label="Handtekening methode">
          <button type="button" class="sig-tab active" id="tab-typed" onclick="setSigMode('typed')">Typen</button>
          <button type="button" class="sig-tab" id="tab-drawn" onclick="setSigMode('drawn')">Tekenen</button>
        </div>

        <input type="hidden" name="signature_type" id="signature_type" value="typed">
        <input type="hidden" name="signature_data" id="signature_data" value="">

        <div id="sig-typed" class="sig-pane">
          <div class="sig-topbar">
            <input type="text" class="sig-type-input" id="sig-typed-input" placeholder="type hier je handtekening" autocomplete="off">
          </div>
          <div class="sig-preview" id="sig-preview">
            <div class="sig-preview-placeholder" id="sig-preview-placeholder">Uw handtekening verschijnt hier</div>
            <div class="sig-preview-text momo-signature-regular" id="sig-preview-text" style="display:none"></div>
          </div>
          <div class="modal-error" id="err-signature"></div>
          <div class="sig-actions-spacer is-hidden">
            <button type="button" class="sig-clear" tabindex="-1">Wis</button>
          </div>
        </div>

        <div id="sig-drawn" class="sig-pane" style="display:none">
          <div class="sig-topbar"></div>
          <div class="sig-canvas-wrap" id="sig-canvas-wrap">
            <canvas class="sig-canvas" id="sig-canvas" width="1200" height="360"></canvas>
          </div>
          <div class="modal-error" id="err-signature-drawn"></div>
          <div class="sig-actions-spacer" id="sig-drawn-actions">
            <button type="button" class="sig-clear" onclick="clearSignature()">Wis</button>
          </div>
        </div>

        <div class="modal-check">
          <input type="checkbox" id="akkoord" name="akkoord" value="1" required>
          <label for="akkoord">Ja, ik ga akkoord met dit voorstel en de van toepassing zijnde algemene voorwaarden.</label>
        </div>
        <div class="modal-error" id="err-akkoord"></div>

        <div class="modal-footer">
          <a class="modal-link" onclick="closeModal()">sluiten</a>
          <button type="submit" class="modal-save">Opslaan</button>
        </div>
      </form>
    </div>
  </div>
  @endif

  <script>
    const sections = [...document.querySelectorAll('.section')];
    const navItems = [...document.querySelectorAll('.nav-item')];
    let current = 0;

    function setActive(index) {
      current = index;
      navItems.forEach((el, i) => el.classList.toggle('active', i === index));
      const prev = document.getElementById('btn-prev');
      const next = document.getElementById('btn-next');
      if (prev) prev.disabled = index === 0;
      if (next) next.disabled = index === sections.length - 1;
    }

    function goTo(index) {
      const target = sections[index];
      if (!target) return;
      target.scrollIntoView({ behavior: 'smooth', block: 'start' });
      setActive(index);
    }

    function navigate(dir) {
      const nextIdx = current + dir;
      if (nextIdx >= 0 && nextIdx < sections.length) goTo(nextIdx);
    }

    // Highlight active sidebar item while scrolling (pdf-reader feel)
    const io = new IntersectionObserver((entries) => {
      const visible = entries
        .filter(e => e.isIntersecting)
        .sort((a, b) => b.intersectionRatio - a.intersectionRatio)[0];
      if (!visible) return;
      const idx = Number(visible.target.getAttribute('data-index') || '0');
      if (!Number.isNaN(idx)) setActive(idx);
    }, {
      root: null,
      threshold: [0.2, 0.35, 0.5, 0.65],
    });
    sections.forEach(s => io.observe(s));
    setActive(0);

    function openModal() { document.getElementById('modal').classList.add('open'); }
    function closeModal() { document.getElementById('modal').classList.remove('open'); }

    // ── Acceptatie modal logic (signature) ───────────────────────────────────
    let sigMode = 'typed';
    let isDrawing = false;
    let hasDrawn = false;
    let last = null;
    const canvas = document.getElementById('sig-canvas');
    const ctx = canvas ? canvas.getContext('2d') : null;

    function setSigMode(mode) {
      sigMode = mode;
      document.getElementById('signature_type').value = mode;
      document.getElementById('tab-typed').classList.toggle('active', mode === 'typed');
      document.getElementById('tab-drawn').classList.toggle('active', mode === 'drawn');
      document.getElementById('sig-typed').style.display = mode === 'typed' ? 'block' : 'none';
      document.getElementById('sig-drawn').style.display = mode === 'drawn' ? 'block' : 'none';
      syncSignatureData();
    }

    function fitSignatureText() {
      const box = document.getElementById('sig-preview');
      const txt = document.getElementById('sig-preview-text');
      if (!box || !txt) return;

      txt.style.fontSize = '44px';
      txt.style.transform = 'scale(1)';

      const maxW = box.clientWidth - 28;
      const w = txt.scrollWidth;
      if (w <= 0) return;
      if (w > maxW) {
        const scale = Math.max(0.6, maxW / w);
        txt.style.transform = `scale(${scale})`;
      }
    }

    function syncSignatureData() {
      const hidden = document.getElementById('signature_data');
      if (!hidden) return;

      if (sigMode === 'typed') {
        const input = document.getElementById('sig-typed-input');
        hidden.value = (input?.value || '').trim();
      } else {
        hidden.value = hasDrawn && canvas ? canvas.toDataURL('image/png') : '';
      }
    }

    function clearSignature() {
      if (!ctx || !canvas) return;
      ctx.clearRect(0, 0, canvas.width, canvas.height);
      hasDrawn = false;
      last = null;
      syncSignatureData();
    }

    function getCanvasPoint(e) {
      const rect = canvas.getBoundingClientRect();
      const clientX = e.touches ? e.touches[0].clientX : e.clientX;
      const clientY = e.touches ? e.touches[0].clientY : e.clientY;
      const x = (clientX - rect.left) * (canvas.width / rect.width);
      const y = (clientY - rect.top) * (canvas.height / rect.height);
      return { x, y };
    }

    function startDraw(e) {
      if (!ctx || sigMode !== 'drawn') return;
      isDrawing = true;
      last = getCanvasPoint(e);
      e.preventDefault?.();
    }
    function moveDraw(e) {
      if (!ctx || !isDrawing || sigMode !== 'drawn') return;
      const p = getCanvasPoint(e);
      ctx.lineCap = 'round';
      ctx.lineJoin = 'round';
      ctx.strokeStyle = '#111827';
      ctx.lineWidth = 5;
      ctx.beginPath();
      ctx.moveTo(last.x, last.y);
      ctx.lineTo(p.x, p.y);
      ctx.stroke();
      last = p;
      hasDrawn = true;
      syncSignatureData();
      e.preventDefault?.();
    }
    function endDraw() {
      isDrawing = false;
      last = null;
      syncSignatureData();
    }

    // typed input → preview
    const typedInput = document.getElementById('sig-typed-input');
    if (typedInput) {
      typedInput.addEventListener('input', () => {
        const v = typedInput.value.trim();
        const placeholder = document.getElementById('sig-preview-placeholder');
        const txt = document.getElementById('sig-preview-text');
        if (!txt || !placeholder) return;
        if (!v) {
          placeholder.style.display = 'block';
          txt.style.display = 'none';
          txt.textContent = '';
        } else {
          placeholder.style.display = 'none';
          txt.style.display = 'block';
          txt.textContent = v;
          fitSignatureText();
        }
        syncSignatureData();
      });
    }

    // canvas draw listeners
    if (canvas) {
      canvas.addEventListener('mousedown', startDraw);
      canvas.addEventListener('mousemove', moveDraw);
      window.addEventListener('mouseup', endDraw);
      canvas.addEventListener('touchstart', startDraw, { passive: false });
      canvas.addEventListener('touchmove', moveDraw, { passive: false });
      window.addEventListener('touchend', endDraw);
      window.addEventListener('resize', () => { if (sigMode === 'typed') fitSignatureText(); });
    }

    function clearInlineErrors() {
      const ids = ['err-naam','err-akkoord','err-signature','err-signature-drawn'];
      ids.forEach((id) => {
        const el = document.getElementById(id);
        if (el) { el.textContent = ''; el.classList.remove('show'); }
      });
      document.getElementById('accept-naam')?.classList.remove('invalid');
      document.getElementById('sig-typed-input')?.classList.remove('invalid');
      document.getElementById('sig-canvas-wrap')?.classList.remove('invalid');
      document.getElementById('akkoord')?.classList.remove('invalid');
    }

    function showError(id, msg) {
      const el = document.getElementById(id);
      if (!el) return;
      el.textContent = msg;
      el.classList.add('show');
    }

    // enforce required fields before submit (no browser popups)
    const form = document.getElementById('accept-form');
    if (form) {
      form.addEventListener('submit', (e) => {
        clearInlineErrors();
        syncSignatureData();
        const naam = (document.getElementById('accept-naam')?.value || '').trim();
        const akkoord = document.getElementById('akkoord')?.checked;
        const sigVal = (document.getElementById('signature_data')?.value || '').trim();

        let ok = true;

        if (!naam) {
          ok = false;
          document.getElementById('accept-naam')?.classList.add('invalid');
          showError('err-naam', 'Vul uw naam in.');
        }

        if (!akkoord) {
          ok = false;
          showError('err-akkoord', 'U moet akkoord gaan met dit voorstel en de van toepassing zijnde algemene voorwaarden.');
        }

        if (sigMode === 'typed') {
          if (!sigVal) {
            ok = false;
            document.getElementById('sig-typed-input')?.classList.add('invalid');
            showError('err-signature', 'Vul uw handtekening in.');
          }
        } else {
          if (!sigVal) {
            ok = false;
            document.getElementById('sig-canvas-wrap')?.classList.add('invalid');
            showError('err-signature-drawn', 'Zet alstublieft uw handtekening in het tekenvak.');
          }
        }

        if (!ok) e.preventDefault();
      });
    }
  </script>
</body>
</html>
