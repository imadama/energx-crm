<!DOCTYPE html>
<html lang="nl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $offerte->nummer }} — Energx offerte</title>
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
    .main { margin-left: var(--sidebar-width); margin-top: var(--topbar-height); min-height: calc(100vh - var(--topbar-height)); display: flex; align-items: flex-start; justify-content: center; padding: 48px 40px 80px; }
    .section { display: none; width: 100%; max-width: 820px; }
    .section.active { display: block; }
    .page-card { background: #fff; border-radius: 3px; box-shadow: 0 2px 24px rgba(0,0,0,.09); overflow: hidden; }

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
    .modal { background: #fff; border-radius: 16px; padding: 40px; max-width: 420px; width: 90%; text-align: center; transform: scale(.96); transition: transform .2s; }
    .modal-overlay.open .modal { transform: scale(1); }
    .modal-icon { width: 52px; height: 52px; border-radius: 50%; background: rgba(45,189,110,.1); color: var(--green-400); display: flex; align-items: center; justify-content: center; margin: 0 auto 14px; }
    .modal-icon svg { width: 26px; height: 26px; }
    .modal h2 { font-family: var(--font-display); font-size: 1.45rem; color: var(--green-800); margin-bottom: 8px; }
    .modal p { color: #666; font-size: .88rem; line-height: 1.6; margin-bottom: 20px; }
    .modal-input { width: 100%; padding: 10px 14px; border: 1.5px solid #e5e7eb; border-radius: 8px; font-family: var(--font-body); font-size: .9rem; margin-bottom: 10px; outline: none; transition: border-color .15s; }
    .modal-input:focus { border-color: var(--green-400); }
    .modal-btns { display: flex; gap: 10px; margin-top: 6px; }
    .modal-cancel { flex: 1; padding: 10px; border: 1.5px solid #e5e7eb; background: #fff; border-radius: 8px; font-family: var(--font-body); font-size: .875rem; cursor: pointer; color: #666; }
    .modal-confirm { flex: 2; padding: 10px; background: var(--green-400); color: #fff; border: none; border-radius: 8px; font-family: var(--font-body); font-weight: 600; font-size: .875rem; cursor: pointer; transition: background .15s; }
    .modal-confirm:hover { background: #25a560; }
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
      <button class="btn-icon" onclick="window.print()">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
        print
      </button>
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
    <section class="section {{ $index === 0 ? 'active' : '' }}" id="s-{{ $index }}">
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
      <div class="modal-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg></div>
      <h2>Offerte goedkeuren</h2>
      <p>Vul je naam en e-mailadres in om de offerte digitaal te accepteren.</p>
      <form method="POST" action="{{ route('offertes.accepteer', $offerte->token) }}">
        @csrf
        <input class="modal-input" type="text" name="naam" placeholder="Je volledige naam" required>
        <input class="modal-input" type="email" name="email" placeholder="Je e-mailadres" required>
        <div class="modal-btns">
          <button type="button" class="modal-cancel" onclick="closeModal()">Annuleren</button>
          <button type="submit" class="modal-confirm">Bevestigen & ondertekenen</button>
        </div>
      </form>
    </div>
  </div>
  @endif

  <script>
    const sections = document.querySelectorAll('.section');
    const navItems = document.querySelectorAll('.nav-item');
    let current = 0;

    function goTo(index) {
      sections[current].classList.remove('active');
      navItems[current].classList.remove('active');
      current = index;
      sections[current].classList.add('active');
      navItems[current].classList.add('active');
      document.getElementById('btn-prev').disabled = current === 0;
      document.getElementById('btn-next').disabled = current === sections.length - 1;
      window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function navigate(dir) {
      const next = current + dir;
      if (next >= 0 && next < sections.length) goTo(next);
    }

    function openModal() { document.getElementById('modal').classList.add('open'); }
    function closeModal() { document.getElementById('modal').classList.remove('open'); }
  </script>
</body>
</html>
