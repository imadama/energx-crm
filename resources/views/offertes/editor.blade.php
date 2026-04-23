<!DOCTYPE html>
<html lang="nl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ $offerte->nummer }} — Editor</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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
    .topbar-left { display: flex; align-items: center; gap: 14px; }
    .topbar-brand { display: flex; align-items: center; gap: 10px; text-decoration: none; color: var(--green-800); }
    .topbar-brand span { font-weight: 700; font-size: 1.05rem; }
    .editor-badge { display: flex; align-items: center; gap: 6px; background: rgba(45,189,110,.12); color: var(--green-600); font-size: .75rem; font-weight: 600; padding: 4px 10px; border-radius: 20px; border: 1px solid rgba(45,189,110,.25); }
    .editor-badge svg { width: 12px; height: 12px; }
    .topbar-actions { display: flex; align-items: center; gap: 8px; }
    .btn-sm { display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; font-size: .8rem; font-family: var(--font-body); font-weight: 500; border-radius: 7px; cursor: pointer; text-decoration: none; transition: background .15s; border: 1px solid transparent; }
    .btn-secondary { background: #f3f4f6; color: #444; border-color: #e5e7eb; }
    .btn-secondary:hover { background: #e9eaec; }
    .btn-primary { background: var(--green-400); color: #fff; }
    .btn-primary:hover { background: #25a560; }
    .btn-nav { display: flex; align-items: center; justify-content: center; width: 34px; height: 34px; border: 1px solid #e5e7eb; background: #fff; border-radius: 8px; cursor: pointer; color: #555; transition: background .15s; }
    .btn-nav:hover { background: #f3f4f6; }
    .btn-nav:disabled { opacity: .35; cursor: default; }
    .btn-nav svg { width: 15px; height: 15px; }

    /* TOAST */
    .toast { position: fixed; bottom: 28px; left: 50%; transform: translateX(-50%); padding: 10px 20px; border-radius: 10px; font-size: .875rem; font-weight: 500; z-index: 300; pointer-events: none; transition: opacity .2s; }
    .toast-saving { background: #1a1a1a; color: #fff; }
    .toast-success { background: var(--green-400); color: #fff; }
    .toast-error { background: #dc2626; color: #fff; }

    /* SIDEBAR */
    .sidebar { position: fixed; top: var(--topbar-height); left: 0; bottom: 0; width: var(--sidebar-width); background: var(--sidebar-bg); overflow-y: auto; z-index: 90; display: flex; flex-direction: column; }
    .sidebar-header { padding: 20px 16px 16px; border-bottom: 1px solid rgba(255,255,255,.07); }
    .sidebar-logo { display: flex; align-items: center; gap: 9px; margin-bottom: 14px; }
    .sidebar-logo-text { font-size: .85rem; font-weight: 600; color: #fff; }
    .proposal-title { font-size: .84rem; font-weight: 600; color: #fff; line-height: 1.4; }
    .proposal-date { font-size: .73rem; color: rgba(255,255,255,.4); margin-top: 4px; }
    .sidebar-nav { padding: 6px 0; flex: 1; }
    .nav-item { display: flex; align-items: center; gap: 8px; padding: 9px 16px; cursor: pointer; color: rgba(255,255,255,.55); font-size: .84rem; text-decoration: none; transition: all .15s; border-left: 3px solid transparent; }
    .nav-item:hover { color: rgba(255,255,255,.85); background: rgba(255,255,255,.04); }
    .nav-item.active { color: #fff; background: rgba(45,189,110,.1); border-left-color: var(--green-400); }
    .nav-item.editing { color: var(--green-400); background: rgba(45,189,110,.08); border-left-color: var(--green-400); }
    .nav-item-title { flex: 1; min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .nav-item-actions { display: none; align-items: center; gap: 2px; flex-shrink: 0; }
    .nav-item:hover .nav-item-actions { display: flex; }
    .nav-icon-btn { display: flex; align-items: center; justify-content: center; width: 22px; height: 22px; border: none; background: none; cursor: pointer; color: rgba(255,255,255,.35); border-radius: 4px; transition: all .15s; padding: 0; }
    .nav-icon-btn:hover { background: rgba(255,255,255,.1); color: rgba(255,255,255,.8); }
    .nav-icon-btn.danger:hover { background: rgba(220,38,38,.2); color: #f87171; }
    .nav-icon-btn svg { width: 11px; height: 11px; }
    .sidebar-add { padding: 10px 16px 16px; border-top: 1px solid rgba(255,255,255,.07); }
    .add-sectie-btn { display: flex; align-items: center; gap: 8px; width: 100%; padding: 9px 12px; background: rgba(255,255,255,.06); border: 1px dashed rgba(255,255,255,.15); border-radius: 8px; cursor: pointer; color: rgba(255,255,255,.45); font-family: var(--font-body); font-size: .8rem; transition: all .15s; }
    .add-sectie-btn:hover { background: rgba(255,255,255,.1); color: rgba(255,255,255,.75); border-color: rgba(255,255,255,.3); }
    .sectie-type-picker { margin-top: 8px; background: #0a1a0e; border: 1px solid rgba(255,255,255,.1); border-radius: 8px; overflow: hidden; }
    .sectie-type-item { display: flex; align-items: center; gap: 10px; padding: 9px 12px; cursor: pointer; color: rgba(255,255,255,.6); font-size: .8rem; transition: background .1s; }
    .sectie-type-item:hover { background: rgba(45,189,110,.1); color: rgba(255,255,255,.9); }

    /* MAIN */
    .main { margin-left: var(--sidebar-width); margin-top: var(--topbar-height); min-height: calc(100vh - var(--topbar-height)); display: flex; align-items: flex-start; justify-content: center; padding: 48px 40px 80px; }
    .section { display: none; width: 100%; max-width: 820px; }
    .section.active { display: block; }
    .page-card { background: #fff; border-radius: 3px; box-shadow: 0 2px 24px rgba(0,0,0,.09); overflow: hidden; }

    /* SECTION WRAPPER (adds edit overlay) */
    .section-wrapper { position: relative; }
    .edit-bar { display: flex; justify-content: flex-end; padding: 10px 0 0; }
    .edit-bar-btn { display: inline-flex; align-items: center; gap: 6px; padding: 7px 14px; background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; font-family: var(--font-body); font-size: .8rem; font-weight: 500; color: #555; cursor: pointer; box-shadow: 0 1px 4px rgba(0,0,0,.08); transition: all .15s; }
    .edit-bar-btn:hover { background: var(--green-400); border-color: var(--green-400); color: #fff; }
    .edit-bar-btn svg { width: 13px; height: 13px; }

    /* EDIT CARD */
    .edit-card { background: #fff; border-radius: 3px; box-shadow: 0 2px 24px rgba(0,0,0,.09); border-top: 3px solid var(--green-400); }
    .edit-card-header { padding: 20px 28px 0; display: flex; align-items: center; justify-content: space-between; }
    .edit-card-title { font-size: .75rem; font-weight: 700; text-transform: uppercase; letter-spacing: .08em; color: var(--green-400); }
    .edit-card-body { padding: 20px 28px 28px; display: flex; flex-direction: column; gap: 18px; }
    .ef-group { display: flex; flex-direction: column; gap: 6px; }
    .ef-label { font-size: .75rem; font-weight: 600; text-transform: uppercase; letter-spacing: .06em; color: #999; }
    .ef-input { width: 100%; padding: 9px 12px; border: 1.5px solid #e5e7eb; border-radius: 8px; font-family: var(--font-body); font-size: .9rem; outline: none; transition: border-color .15s; }
    .ef-input:focus { border-color: var(--green-400); }
    .ef-textarea { width: 100%; padding: 10px 12px; border: 1.5px solid #e5e7eb; border-radius: 8px; font-family: var(--font-body); font-size: .875rem; line-height: 1.65; outline: none; resize: vertical; transition: border-color .15s; }
    .ef-textarea:focus { border-color: var(--green-400); }
    .ef-actions { display: flex; gap: 10px; padding-top: 8px; border-top: 1px solid #f0f0f0; margin-top: 4px; }
    .btn-save { display: inline-flex; align-items: center; gap: 7px; padding: 9px 18px; background: var(--green-400); color: #fff; border: none; border-radius: 8px; font-family: var(--font-body); font-weight: 600; font-size: .875rem; cursor: pointer; transition: background .15s; }
    .btn-save:hover { background: #25a560; }
    .btn-save:disabled { opacity: .55; cursor: default; }
    .btn-cancel { display: inline-flex; align-items: center; gap: 7px; padding: 9px 16px; background: #f3f4f6; color: #555; border: 1px solid #e5e7eb; border-radius: 8px; font-family: var(--font-body); font-size: .875rem; cursor: pointer; transition: background .15s; }
    .btn-cancel:hover { background: #eaebec; }

    /* SPECS / STAPPEN editor rows */
    .row-list { display: flex; flex-direction: column; gap: 8px; }
    .spec-edit-row { display: grid; grid-template-columns: 1fr 1fr auto; gap: 8px; align-items: center; }
    .stap-edit-row { display: grid; grid-template-columns: 1fr auto; gap: 8px; }
    .stap-edit-row-inner { display: flex; flex-direction: column; gap: 6px; }
    .row-remove-btn { display: flex; align-items: center; justify-content: center; width: 28px; height: 28px; background: none; border: 1px solid #e5e7eb; border-radius: 6px; cursor: pointer; color: #ccc; transition: all .15s; flex-shrink: 0; }
    .row-remove-btn:hover { background: #fee2e2; border-color: #fca5a5; color: #dc2626; }
    .row-remove-btn svg { width: 12px; height: 12px; }
    .add-row-btn { display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; background: #f9fafb; border: 1px dashed #d1d5db; border-radius: 7px; font-family: var(--font-body); font-size: .8rem; color: #666; cursor: pointer; transition: all .15s; }
    .add-row-btn:hover { background: #f0fdf4; border-color: var(--green-400); color: var(--green-600); }

    /* PRIJZEN edit table */
    .regels-table { width: 100%; border-collapse: collapse; }
    .regels-table th { text-align: left; padding: 8px 10px; font-size: .68rem; font-weight: 700; text-transform: uppercase; letter-spacing: .07em; color: #aaa; border-bottom: 2px solid #f0f0f0; }
    .regels-table td { padding: 6px 4px; vertical-align: middle; }
    .regels-table .td-naam { width: 32%; }
    .regels-table .td-sub { width: 28%; }
    .regels-table .td-num { width: 10%; }
    .regels-table .td-prijs { width: 18%; }
    .regels-table .td-del { width: 6%; }
    .totaal-live { display: flex; flex-direction: column; align-items: flex-end; gap: 5px; padding-top: 12px; border-top: 1px solid #f0f0f0; }
    .totaal-row { display: flex; gap: 32px; font-size: .85rem; color: #666; }
    .totaal-row .lbl { min-width: 100px; text-align: right; }
    .totaal-row .val { min-width: 80px; text-align: right; font-variant-numeric: tabular-nums; }
    .totaal-row.grand { font-weight: 700; color: var(--green-800); font-size: .95rem; }

    /* VIEWER CONTENT (same as viewer.blade.php) */
    .cover-hero-placeholder { width: 100%; height: 380px; background: linear-gradient(135deg, var(--green-800) 0%, #1a5c35 45%, var(--night) 100%); display: flex; flex-direction: column; align-items: center; justify-content: center; }
    .cover-hero-title { font-family: var(--font-display); font-size: 3.5rem; color: #fff; }
    .cover-hero-sub { font-size: .9rem; color: rgba(255,255,255,.55); margin-top: 6px; }
    .cover-body { padding: 36px 48px 40px; }
    .cover-meta { display: flex; gap: 40px; padding-bottom: 28px; border-bottom: 1px solid #f0f0f0; margin-bottom: 28px; flex-wrap: wrap; }
    .meta-label { font-size: .7rem; font-weight: 600; text-transform: uppercase; letter-spacing: .08em; color: #aaa; margin-bottom: 5px; }
    .meta-value { font-size: .95rem; font-weight: 600; color: #1a1a1a; }
    .meta-value small { display: block; font-weight: 400; color: #777; font-size: .85rem; margin-top: 2px; }
    .section-content { padding: 48px; }
    .section-title { font-family: var(--font-display); font-size: 2rem; color: var(--green-800); margin-bottom: 28px; padding-bottom: 16px; border-bottom: 2px solid var(--green-400); }
    .intro-text p { color: #4a4a4a; line-height: 1.85; margin-bottom: 16px; font-size: .93rem; }
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
    .product-layout { display: grid; grid-template-columns: 1fr 1fr; gap: 36px; }
    .spec-list { margin-top: 16px; }
    .spec-row { display: flex; justify-content: space-between; padding: 9px 0; border-bottom: 1px solid #f2f2f2; font-size: .85rem; }
    .spec-row:last-child { border-bottom: none; }
    .spec-key { color: #888; }
    .spec-val { font-weight: 500; color: #1a1a1a; text-align: right; }
    .product-info p { color: #555; line-height: 1.75; font-size: .9rem; margin-bottom: 14px; }
    .step { display: flex; gap: 20px; padding: 20px 0; border-bottom: 1px solid #f2f2f2; }
    .step:last-child { border-bottom: none; }
    .step-num { width: 34px; height: 34px; border-radius: 50%; background: var(--green-400); color: #fff; font-weight: 700; font-size: .82rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px; }
    .step-title { font-weight: 600; color: #1a1a1a; margin-bottom: 5px; font-size: .9rem; }
    .step-desc { font-size: .85rem; color: #666; line-height: 1.65; }
    .accept-page { padding: 64px 48px; text-align: center; }
    .accept-icon { width: 60px; height: 60px; border-radius: 50%; background: rgba(45,189,110,.1); color: var(--green-400); display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; }
    .accept-icon svg { width: 28px; height: 28px; }
    .accept-title { font-family: var(--font-display); font-size: 1.9rem; color: var(--green-800); margin-bottom: 12px; }
    .accept-desc { color: #666; font-size: .93rem; line-height: 1.75; max-width: 460px; margin: 0 auto 32px; }
    .btn-accept-preview { display: inline-flex; align-items: center; gap: 10px; padding: 13px 26px; background: #e5e7eb; color: #aaa; border: none; border-radius: 10px; font-family: var(--font-body); font-weight: 600; font-size: .95rem; cursor: default; }
  </style>
</head>
<body x-data="editor()" x-init="init()">

  <!-- TOPBAR -->
  <header class="topbar">
    <div class="topbar-left">
      <a class="topbar-brand" href="{{ route('offertes.show', $offerte) }}">
        <svg width="30" height="30" viewBox="0 0 32 32" fill="none">
          <circle cx="16" cy="16" r="16" fill="#0F4A2A"/>
          <path d="M16 5l2.5 8.5H27l-7 5 2.5 8.5L16 22l-6.5 5 2.5-8.5-7-5h8.5L16 5z" fill="#2DBD6E"/>
        </svg>
        <span>Energx</span>
      </a>
      <div class="editor-badge">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
        Bewerkmodus
      </div>
    </div>
    <div class="topbar-actions">
      <a href="{{ route('offertes.viewer', $offerte->token) }}" target="_blank" class="btn-sm btn-secondary">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="13" height="13"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
        Bekijk als klant
      </a>
      <a href="{{ route('offertes.show', $offerte) }}" class="btn-sm btn-secondary">
        ← Terug naar overzicht
      </a>
      <button class="btn-nav" :disabled="currentIndex === 0" @click="navigate(-1)">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
      </button>
      <button class="btn-nav" :disabled="currentIndex === secties.length - 1" @click="navigate(1)">
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
      <template x-for="(sectie, index) in secties" :key="sectie.id">
        <div class="nav-item"
             :class="{ active: currentIndex === index && editingId !== sectie.id, editing: editingId === sectie.id }"
             @click="goTo(index)">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:10px;height:10px;flex-shrink:0;opacity:.5"><polyline points="9 18 15 12 9 6"/></svg>
          <span class="nav-item-title" x-text="sectie.titel"></span>
          <div class="nav-item-actions" @click.stop>
            <button class="nav-icon-btn" title="Omhoog" @click="verplaats(index, -1)" :disabled="index === 0">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="18 15 12 9 6 15"/></svg>
            </button>
            <button class="nav-icon-btn" title="Omlaag" @click="verplaats(index, 1)" :disabled="index === secties.length - 1">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
            </button>
            <button class="nav-icon-btn danger" title="Verwijder sectie" @click="verwijderSectie(sectie.id, index)">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/></svg>
            </button>
          </div>
        </div>
      </template>
    </nav>
    <div class="sidebar-add" x-data="{ open: false }">
      <button class="add-sectie-btn" @click="open = !open">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:13px;height:13px"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Sectie toevoegen
      </button>
      <div class="sectie-type-picker" x-show="open" @click.outside="open = false" x-cloak>
        <template x-for="type in sectieTypes" :key="type.key">
          <div class="sectie-type-item" @click="voegSectieToee(type.key, type.label); open = false">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:12px;height:12px;flex-shrink:0"><polyline points="9 18 15 12 9 6"/></svg>
            <span x-text="type.label"></span>
          </div>
        </template>
      </div>
    </div>
  </aside>

  <!-- MAIN -->
  <main class="main">
    <template x-for="(sectie, index) in secties" :key="sectie.id">
      <div class="section" :class="{ active: currentIndex === index }" :id="'s-' + index">

        <!-- VIEW MODE -->
        <div class="section-wrapper" x-show="editingId !== sectie.id">
          <div class="page-card" x-html="renderSectie(sectie)"></div>
          <div class="edit-bar">
            <button class="edit-bar-btn" @click="startEdit(sectie, index)">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
              Sectie bewerken
            </button>
          </div>
        </div>

        <!-- EDIT MODE -->
        <template x-if="editingId === sectie.id && editData !== null">
          <div class="edit-card">
            <div class="edit-card-header">
              <span class="edit-card-title" x-text="sectieTypeLabel(editData.type) + ' bewerken'"></span>
            </div>
            <div class="edit-card-body">

              <!-- Titel (all types) -->
              <div class="ef-group">
                <label class="ef-label">Sectietitel</label>
                <input class="ef-input" type="text" x-model="editData.titel">
              </div>

              <!-- INTRODUCTIE / TEKST -->
              <template x-if="editData.type === 'introductie' || editData.type === 'tekst'">
                <div class="ef-group">
                  <label class="ef-label">Tekst <span style="font-weight:400;text-transform:none;letter-spacing:0;color:#bbb">— gebruik [naam] voor de naam van de klant, lege regel voor nieuwe alinea</span></label>
                  <textarea class="ef-textarea" rows="12" x-model="editData.inhoud.tekst"></textarea>
                </div>
              </template>

              <!-- ACCEPTATIE -->
              <template x-if="editData.type === 'acceptatie'">
                <div class="ef-group">
                  <label class="ef-label">Acceptatietekst</label>
                  <textarea class="ef-textarea" rows="4" x-model="editData.inhoud.tekst"></textarea>
                </div>
              </template>

              <!-- VOORBLAD -->
              <template x-if="editData.type === 'voorblad'">
                <div style="background:#f9fafb;border-radius:8px;padding:14px;font-size:.85rem;color:#666">
                  Het voorblad toont automatisch de naam en het adres van de klant, het offerte&shy;nummer, de datum en de geldigheidsdatum.
                </div>
              </template>

              <!-- PRODUCT -->
              <template x-if="editData.type === 'product'">
                <div style="display:flex;flex-direction:column;gap:18px">
                  <div class="ef-group">
                    <label class="ef-label">Beschrijving <span style="font-weight:400;text-transform:none;letter-spacing:0;color:#bbb">— lege regel voor nieuwe alinea</span></label>
                    <textarea class="ef-textarea" rows="7" x-model="editData.inhoud.beschrijving"></textarea>
                  </div>
                  <div class="ef-group">
                    <label class="ef-label">Specificaties</label>
                    <div class="row-list">
                      <template x-for="(spec, si) in editData.inhoud.specs" :key="si">
                        <div class="spec-edit-row">
                          <input class="ef-input" type="text" x-model="spec.label" placeholder="Eigenschap (bijv. Vermogen)">
                          <input class="ef-input" type="text" x-model="spec.waarde" placeholder="Waarde (bijv. 22 kW)">
                          <button class="row-remove-btn" @click="editData.inhoud.specs.splice(si, 1)">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                          </button>
                        </div>
                      </template>
                    </div>
                    <button class="add-row-btn" style="margin-top:8px" @click="editData.inhoud.specs.push({ label: '', waarde: '' })">
                      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:11px;height:11px"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                      Specificatie toevoegen
                    </button>
                  </div>
                </div>
              </template>

              <!-- WERKWIJZE -->
              <template x-if="editData.type === 'werkwijze'">
                <div class="ef-group">
                  <label class="ef-label">Stappen</label>
                  <div class="row-list">
                    <template x-for="(stap, si) in editData.inhoud.stappen" :key="si">
                      <div class="stap-edit-row">
                        <div class="stap-edit-row-inner">
                          <input class="ef-input" type="text" x-model="stap.titel" placeholder="Staptitel">
                          <textarea class="ef-textarea" rows="2" x-model="stap.beschrijving" placeholder="Beschrijving van de stap"></textarea>
                        </div>
                        <button class="row-remove-btn" style="align-self:flex-start;margin-top:2px" @click="editData.inhoud.stappen.splice(si, 1)">
                          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                        </button>
                      </div>
                    </template>
                  </div>
                  <button class="add-row-btn" style="margin-top:8px" @click="editData.inhoud.stappen.push({ titel: '', beschrijving: '' })">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:11px;height:11px"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    Stap toevoegen
                  </button>
                </div>
              </template>

              <!-- PRIJZEN -->
              <template x-if="editData.type === 'prijzen'">
                <div style="display:flex;flex-direction:column;gap:16px">
                  <table class="regels-table">
                    <thead><tr>
                      <th class="td-naam">Omschrijving</th>
                      <th class="td-sub">Subtekst</th>
                      <th class="td-num">Aantal</th>
                      <th class="td-prijs">Prijs (€)</th>
                      <th class="td-del"></th>
                    </tr></thead>
                    <tbody>
                      <template x-for="(regel, ri) in regelsCopy" :key="ri">
                        <tr>
                          <td class="td-naam">
                            <input class="ef-input" style="font-size:.82rem;padding:6px 8px" type="text" x-model="regel.naam" placeholder="Naam" @input="herbereken()">
                          </td>
                          <td class="td-sub">
                            <input class="ef-input" style="font-size:.8rem;padding:6px 8px;color:#888" type="text" x-model="regel.beschrijving" placeholder="Subtekst (optioneel)">
                          </td>
                          <td class="td-num">
                            <input class="ef-input" style="font-size:.82rem;padding:6px 8px;text-align:center" type="number" x-model.number="regel.aantal" min="1" @input="herbereken()">
                          </td>
                          <td class="td-prijs">
                            <input class="ef-input" style="font-size:.82rem;padding:6px 8px" type="number" x-model.number="regel.eenheidsprijs" step="0.01" min="0" @input="herbereken()">
                          </td>
                          <td class="td-del">
                            <button class="row-remove-btn" @click="regelsCopy.splice(ri, 1); herbereken()">
                              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                            </button>
                          </td>
                        </tr>
                      </template>
                    </tbody>
                  </table>
                  <button class="add-row-btn" @click="regelsCopy.push({ product_id: null, naam: '', beschrijving: '', aantal: 1, eenheidsprijs: 0 }); herbereken()">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:11px;height:11px"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    Regel toevoegen
                  </button>
                  <div class="totaal-live">
                    <div class="totaal-row"><span class="lbl">Subtotaal</span><span class="val" x-text="'€ ' + editTotalen.subtotaal.toFixed(2).replace('.',',')"></span></div>
                    <div class="totaal-row"><span class="lbl">BTW (21%)</span><span class="val" x-text="'€ ' + editTotalen.btw.toFixed(2).replace('.',',')"></span></div>
                    <div class="totaal-row grand"><span class="lbl">Totaal incl. BTW</span><span class="val" x-text="'€ ' + editTotalen.totaal.toFixed(2).replace('.',',')"></span></div>
                  </div>
                </div>
              </template>

              <!-- ACTIONS -->
              <div class="ef-actions">
                <button class="btn-save" :disabled="saving"
                        @click="editData.type === 'prijzen' ? opslaanRegels() : opslaanSectie()">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:14px;height:14px"><polyline points="20 6 9 17 4 12"/></svg>
                  <span x-text="saving ? 'Opslaan...' : 'Opslaan'"></span>
                </button>
                <button class="btn-cancel" @click="cancelEdit()">Annuleren</button>
              </div>

            </div>
          </div>
        </template>

      </div>
    </template>
  </main>

  <!-- TOAST -->
  <div class="toast toast-saving" x-show="saving" x-cloak style="opacity:.9">Opslaan...</div>
  <div class="toast toast-success" x-show="successMsg" x-cloak x-text="successMsg"></div>
  <div class="toast toast-error" x-show="errorMsg" x-cloak x-text="errorMsg"></div>

  @php
    $editorSecties = $offerte->secties->map(fn($s) => [
      'id'       => $s->id,
      'type'     => $s->type,
      'titel'    => $s->titel,
      'inhoud'   => $s->inhoud ?: new stdClass(),
      'volgorde' => $s->volgorde,
    ]);
    $editorRegels = $offerte->regels->map(fn($r) => [
      'id'            => $r->id,
      'product_id'    => $r->product_id,
      'naam'          => $r->naam,
      'beschrijving'  => $r->beschrijving ?? '',
      'aantal'        => (int) $r->aantal,
      'eenheidsprijs' => (float) $r->eenheidsprijs,
    ]);
    $editorOfferte = [
      'id'          => $offerte->id,
      'klant_naam'  => $offerte->klant->naam,
      'klant_adres' => $offerte->klant->adres,
      'nummer'      => $offerte->nummer,
      'datum'       => $offerte->created_at->format('d F Y'),
      'geldig_tot'  => $offerte->geldig_tot?->format('d F Y'),
      'inleiding'   => $offerte->inleiding,
    ];
  @endphp

  <script>
  function editor() {
    const secties = @json($editorSecties);

    const regels = @json($editorRegels);

    const offerte = @json($editorOfferte);

    return {
      secties:      JSON.parse(JSON.stringify(secties)),
      regels:       JSON.parse(JSON.stringify(regels)),
      totalen:      {
        subtotaal: {{ (float) $offerte->subtotaal }},
        btw:       {{ (float) $offerte->btw_bedrag }},
        totaal:    {{ (float) $offerte->totaal }},
      },
      offerte,
      currentIndex: 0,
      editingId:    null,
      editData:     null,
      regelsCopy:   [],
      editTotalen:  { subtotaal: 0, btw: 0, totaal: 0 },
      saving:       false,
      successMsg:   '',
      errorMsg:     '',
      csrf:         document.querySelector('meta[name="csrf-token"]').content,
      sectieTypes:  [
        { key: 'introductie', label: 'Introductie' },
        { key: 'tekst',       label: 'Tekstblok' },
        { key: 'product',     label: 'Productinfo' },
        { key: 'werkwijze',   label: 'Werkwijze' },
        { key: 'acceptatie',  label: 'Acceptatie' },
      ],

      init() {
        // Ensure inhoud has required shapes
      },

      /* ── Navigation ─────────────────────────────────── */
      goTo(index) {
        this.currentIndex = index;
        window.scrollTo({ top: 0, behavior: 'smooth' });
      },
      navigate(dir) {
        const next = this.currentIndex + dir;
        if (next >= 0 && next < this.secties.length) this.goTo(next);
      },

      /* ── Edit ───────────────────────────────────────── */
      startEdit(sectie, index) {
        this.goTo(index);
        const copy = JSON.parse(JSON.stringify(sectie));
        // Normalise inhoud shape
        copy.inhoud = copy.inhoud || {};
        if (!copy.inhoud.tekst)        copy.inhoud.tekst = '';
        if (!copy.inhoud.beschrijving) copy.inhoud.beschrijving = '';
        if (!copy.inhoud.specs)        copy.inhoud.specs = [];
        if (!copy.inhoud.stappen)      copy.inhoud.stappen = [];
        this.editData  = copy;
        this.editingId = sectie.id;
        if (sectie.type === 'prijzen') {
          this.regelsCopy = JSON.parse(JSON.stringify(this.regels));
          this.herbereken();
        }
      },

      cancelEdit() {
        this.editingId = null;
        this.editData  = null;
        this.regelsCopy = [];
      },

      /* ── Save section ───────────────────────────────── */
      async opslaanSectie() {
        this.saving = true;
        try {
          const res = await fetch(`/offertes/${this.offerte.id}/secties/${this.editData.id}`, {
            method:  'PATCH',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this.csrf },
            body:    JSON.stringify({ titel: this.editData.titel, inhoud: this.editData.inhoud }),
          });
          if (!res.ok) throw new Error();
          const updated = await res.json();
          const idx = this.secties.findIndex(s => s.id === updated.id);
          if (idx !== -1) this.secties[idx] = updated;
          this.editingId = null;
          this.editData  = null;
          this.toonSuccess('✓ Sectie opgeslagen');
        } catch {
          this.toonError('Er ging iets mis — probeer opnieuw.');
        } finally {
          this.saving = false;
        }
      },

      /* ── Save regels ────────────────────────────────── */
      async opslaanRegels() {
        this.saving = true;
        try {
          const res = await fetch(`/offertes/${this.offerte.id}/regels`, {
            method:  'PATCH',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this.csrf },
            body:    JSON.stringify({ regels: this.regelsCopy }),
          });
          if (!res.ok) throw new Error();
          const data = await res.json();
          this.regels  = data.regels;
          this.totalen = { subtotaal: data.subtotaal, btw: data.btw_bedrag, totaal: data.totaal };
          this.editingId = null;
          this.editData  = null;
          this.regelsCopy = [];
          this.toonSuccess('✓ Prijsregels opgeslagen');
        } catch {
          this.toonError('Er ging iets mis — probeer opnieuw.');
        } finally {
          this.saving = false;
        }
      },

      /* ── Delete section ─────────────────────────────── */
      async verwijderSectie(id, index) {
        if (!confirm('Sectie verwijderen?')) return;
        const res = await fetch(`/offertes/${this.offerte.id}/secties/${id}`, {
          method: 'DELETE', headers: { 'X-CSRF-TOKEN': this.csrf },
        });
        if (res.ok) {
          this.secties.splice(index, 1);
          if (this.currentIndex >= this.secties.length) this.currentIndex = Math.max(0, this.secties.length - 1);
        }
      },

      /* ── Add section ────────────────────────────────── */
      async voegSectieToee(type, titel) {
        const res = await fetch(`/offertes/${this.offerte.id}/secties`, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this.csrf },
          body: JSON.stringify({ type, titel }),
        });
        const nieuws = await res.json();
        this.secties.push(nieuws);
        this.goTo(this.secties.length - 1);
        this.$nextTick(() => this.startEdit(nieuws, this.secties.length - 1));
      },

      /* ── Reorder ────────────────────────────────────── */
      async verplaats(index, richting) {
        const nieuwIndex = index + richting;
        if (nieuwIndex < 0 || nieuwIndex >= this.secties.length) return;
        // Swap in local array
        const tmp = this.secties[index];
        this.secties.splice(index, 1);
        this.secties.splice(nieuwIndex, 0, tmp);
        if (this.currentIndex === index) this.currentIndex = nieuwIndex;
        // Persist new order
        const volgorde = this.secties.map((s, i) => ({ id: s.id, volgorde: i }));
        await fetch(`/offertes/${this.offerte.id}/secties-volgorde`, {
          method: 'PATCH',
          headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this.csrf },
          body: JSON.stringify({ volgorde }),
        });
      },

      /* ── Helpers ────────────────────────────────────── */
      herbereken() {
        const sub = this.regelsCopy.reduce((s, r) => s + (r.aantal * r.eenheidsprijs), 0);
        this.editTotalen = { subtotaal: sub, btw: sub * 0.21, totaal: sub * 1.21 };
      },

      sectieTypeLabel(type) {
        return { voorblad:'Voorblad', introductie:'Introductie', tekst:'Tekstblok', prijzen:'Prijzen', product:'Productinfo', werkwijze:'Werkwijze', acceptatie:'Acceptatie' }[type] ?? type;
      },

      toonSuccess(msg) {
        this.successMsg = msg;
        setTimeout(() => this.successMsg = '', 2500);
      },
      toonError(msg) {
        this.errorMsg = msg;
        setTimeout(() => this.errorMsg = '', 4000);
      },

      /* ── Render functions ───────────────────────────── */
      e(str) {
        return String(str ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
      },

      nl2p(str) {
        const naam = this.e(this.offerte.klant_naam);
        return (str || '').split('\n\n').filter(p => p.trim())
          .map(p => `<p>${this.e(p).replace(/\[naam\]/g, naam)}</p>`).join('');
      },

      renderSectie(s) {
        switch (s.type) {
          case 'voorblad':    return this.renderVoorblad();
          case 'introductie':
          case 'tekst':       return this.renderTekst(s);
          case 'prijzen':     return this.renderPrijzen(s);
          case 'product':     return this.renderProduct(s);
          case 'werkwijze':   return this.renderWerkwijze(s);
          case 'acceptatie':  return this.renderAcceptatie(s);
          default:            return `<div class="section-content"><p>${this.e(s.titel)}</p></div>`;
        }
      },

      renderVoorblad() {
        const o = this.offerte;
        const geldig = o.geldig_tot ? `<div><div class="meta-label">Geldig tot</div><div class="meta-value">${this.e(o.geldig_tot)}</div></div>` : '';
        const inleiding = o.inleiding ? `<div><div class="meta-label">Betreft</div><div class="meta-value" style="margin-top:6px">${this.e(o.inleiding)}</div></div>` : '';
        return `
          <div class="cover-hero-placeholder">
            <div class="cover-hero-title">Energx</div>
            <div class="cover-hero-sub">www.energx.nl</div>
          </div>
          <div class="cover-body">
            <div class="cover-meta">
              <div><div class="meta-label">Offerte voor</div><div class="meta-value">${this.e(o.klant_naam)}<small>${this.e(o.klant_adres)}</small></div></div>
              <div><div class="meta-label">Offerte nr.</div><div class="meta-value">${this.e(o.nummer)}</div></div>
              <div><div class="meta-label">Datum</div><div class="meta-value">${this.e(o.datum)}</div></div>
              ${geldig}
            </div>
            ${inleiding}
          </div>`;
      },

      renderTekst(s) {
        return `<div class="section-content">
          <h2 class="section-title">${this.e(s.titel)}</h2>
          <div class="intro-text">${this.nl2p(s.inhoud?.tekst)}</div>
        </div>`;
      },

      renderPrijzen(s) {
        const rows = this.regels.map(r => `
          <tr>
            <td><div class="item-name">${this.e(r.naam)}</div>${r.beschrijving ? `<div class="item-desc">${this.e(r.beschrijving)}</div>` : ''}</td>
            <td>${r.aantal}</td>
            <td>€ ${Number(r.eenheidsprijs).toFixed(2).replace('.',',')}</td>
            <td>€ ${(r.aantal * r.eenheidsprijs).toFixed(2).replace('.',',')}</td>
          </tr>`).join('');
        const fmt = v => Number(v).toFixed(2).replace('.',',');
        return `<div class="section-content">
          <h2 class="section-title">${this.e(s.titel)}</h2>
          <table class="price-table">
            <thead><tr><th style="width:48%">Omschrijving</th><th>Aantal</th><th>Eenheidsprijs</th><th>Totaal</th></tr></thead>
            <tbody>${rows}</tbody>
          </table>
          <div class="price-totals">
            <div class="total-row"><span class="lbl">Subtotaal</span><span class="val">€ ${fmt(this.totalen.subtotaal)}</span></div>
            <div class="total-row"><span class="lbl">BTW (21%)</span><span class="val">€ ${fmt(this.totalen.btw)}</span></div>
            <div class="total-row grand"><span class="lbl">Totaal incl. BTW</span><span class="val">€ ${fmt(this.totalen.totaal)}</span></div>
          </div>
        </div>`;
      },

      renderProduct(s) {
        const specs = (s.inhoud?.specs || []).map(sp =>
          `<div class="spec-row"><span class="spec-key">${this.e(sp.label)}</span><span class="spec-val">${this.e(sp.waarde)}</span></div>`
        ).join('');
        return `<div class="section-content">
          <h2 class="section-title">${this.e(s.titel)}</h2>
          <div class="product-layout">
            <div>
              <div style="width:100%;aspect-ratio:1;background:linear-gradient(135deg,#f0f0f0,#e0e0e0);border-radius:12px;display:flex;align-items:center;justify-content:center;color:#bbb;font-size:3rem">⚡</div>
              ${specs ? `<div class="spec-list">${specs}</div>` : ''}
            </div>
            <div class="product-info">${this.nl2p(s.inhoud?.beschrijving)}</div>
          </div>
        </div>`;
      },

      renderWerkwijze(s) {
        const stappen = (s.inhoud?.stappen || []).map((stap, i) => `
          <div class="step">
            <div class="step-num">${i + 1}</div>
            <div>
              <div class="step-title">${this.e(stap.titel)}</div>
              <div class="step-desc">${this.e(stap.beschrijving)}</div>
            </div>
          </div>`).join('');
        return `<div class="section-content">
          <h2 class="section-title">${this.e(s.titel)}</h2>
          <div>${stappen}</div>
        </div>`;
      },

      renderAcceptatie(s) {
        return `<div class="accept-page">
          <div class="accept-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg></div>
          <h2 class="accept-title">${this.e(s.titel)}</h2>
          <p class="accept-desc">${this.e(s.inhoud?.tekst || '')}</p>
          <button class="btn-accept-preview" disabled>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="15" height="15"><polyline points="20 6 9 17 4 12"/></svg>
            Offerte goedkeuren (preview)
          </button>
        </div>`;
      },
    }
  }
  </script>
</body>
</html>
