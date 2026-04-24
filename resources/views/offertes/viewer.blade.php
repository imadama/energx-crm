<!DOCTYPE html>
<html lang="nl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $offerte->nummer }} — Offerte Energx</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    :root {
      --green-800: #0F4A2A; --green-400: #2DBD6E;
      --font-display: 'DM Serif Display', serif; --font-body: 'Outfit', sans-serif;
    }
    html, body { font-family: var(--font-body); background: #e8eaed; color: #1a1a1a; }

    /* TOPBAR */
    .viewer-topbar { position:fixed; top:0; left:0; right:0; height:56px; background:#fff;
      border-bottom:1px solid #e5e7eb; display:flex; align-items:center; justify-content:space-between;
      padding:0 24px; z-index:100; box-shadow:0 1px 4px rgba(0,0,0,.06); }
    .topbar-brand { display:flex; align-items:center; gap:8px; text-decoration:none; color:var(--green-800); }
    .topbar-brand-logo { width:32px; height:32px; background:var(--green-800); border-radius:8px; display:flex; align-items:center; justify-content:center; }
    .topbar-brand-logo svg { width:18px; height:18px; }
    .topbar-brand-name { font-weight:700; font-size:.95rem; }
    .topbar-right { display:flex; align-items:center; gap:10px; }
    .btn-accept { display:flex; align-items:center; gap:7px; padding:8px 18px; background:var(--green-400);
      color:#fff; border:none; border-radius:8px; font-family:var(--font-body); font-weight:600;
      font-size:.875rem; cursor:pointer; transition:background .15s; text-decoration:none; }
    .btn-accept:hover { background:#25a560; }
    .btn-print { display:flex; align-items:center; gap:6px; padding:7px 14px; background:#f3f4f6;
      border:1px solid #e5e7eb; border-radius:8px; font-family:var(--font-body); font-size:.82rem;
      color:#555; cursor:pointer; transition:background .15s; }
    .btn-print:hover { background:#e5e7eb; }

    /* STATUS BANNERS */
    .status-banner { text-align:center; padding:12px; font-size:.875rem; font-weight:500; }
    .status-banner.geaccepteerd { background:#d1fae5; color:#065f46; }
    .status-banner.verlopen { background:#fee2e2; color:#991b1b; }

    /* CANVAS */
    .viewer-canvas { padding: 56px 40px 80px; display:flex; flex-direction:column; align-items:center; gap:24px; }

    /* A4 PAGINA'S */
    .a4-pagina { width:794px; min-height:1123px; box-shadow:0 4px 32px rgba(0,0,0,.12), 0 1px 6px rgba(0,0,0,.06); border-radius:2px; }
    .document-element { position:relative; }
    .document-content { display:block; }

    /* DOCUMENT CONTENT STIJLEN */
    .blok-tekst { font-size:.9rem; line-height:1.7; }
    .blok-tekst h1 { font-family:var(--font-display); font-size:2rem; line-height:1.2; margin-bottom:.75rem; }
    .blok-tekst h2 { font-family:var(--font-display); font-size:1.5rem; line-height:1.25; margin-bottom:.6rem; }
    .blok-tekst h3 { font-size:1.1rem; font-weight:700; margin-bottom:.5rem; }
    .blok-tekst p { margin-bottom:.6rem; }
    .blok-tekst ul { padding-left:1.4rem; margin-bottom:.6rem; }
    .blok-tekst li { margin-bottom:.25rem; }
    .blok-2kolommen { display:flex; gap:0; }
    .blok-2kolommen .kolom { font-size:.88rem; line-height:1.65; padding:0 8px; }
    .blok-afbeelding img { max-width:100%; height:auto; display:block; }
    .blok-afbeelding--leeg { background:#f8fafc; border:2px dashed #e5e7eb; border-radius:8px; padding:40px; text-align:center; color:#94a3b8; }

    /* PRIJSTABEL */
    .blok-prijstabel { width:100%; }
    .pt-tabel { width:100%; border-collapse:collapse; font-size:.84rem; }
    .pt-tabel th { background:var(--green-800); color:#fff; padding:9px 12px; text-align:left; font-weight:600; font-size:.78rem; }
    .pt-tabel td { padding:8px 12px; border-bottom:1px solid #f3f4f6; vertical-align:top; }
    .pt-tabel tfoot td { border-top:2px solid #e5e7eb; }
    .pt-subtotaal-rij td { border-top:2px solid #e5e7eb; }
    .pt-totaal-rij td { background:#f0fdf4; }
    .pt-omschrijving { width:45%; }
    .pt-aantal, .pt-prijs, .pt-btw { width:13%; }
    .pt-totaal, .pt-bedrag { width:16%; text-align:right; }
    .pt-label { text-align:right; font-weight:500; color:#555; }
    .pt-optioneel { display:inline-block; font-size:.65rem; font-weight:700; text-transform:uppercase;
      letter-spacing:.05em; background:#fef3c7; color:#92400e; padding:1px 6px; border-radius:10px; margin-left:6px; }
    .regel-tekst td { color:#6b7280; font-style:italic; }
    .blok-standaard-tabel table { width:100%; border-collapse:collapse; font-size:.84rem; }
    .blok-standaard-tabel th { background:#f8fafc; color:#374151; padding:9px 12px; text-align:left; font-weight:600; border:1px solid #e5e7eb; }
    .blok-standaard-tabel td { padding:8px 12px; border:1px solid #e5e7eb; }

    /* ACCEPTATIE MODAL */
    .modal-overlay { position:fixed; inset:0; background:rgba(0,0,0,.5); z-index:200; display:flex; align-items:center; justify-content:center; padding:20px; }
    .modal { background:#fff; border-radius:16px; padding:32px; max-width:480px; width:100%; box-shadow:0 20px 60px rgba(0,0,0,.2); }
    .modal h2 { font-family:var(--font-display); font-size:1.5rem; margin-bottom:8px; }
    .modal p { color:#6b7280; font-size:.88rem; margin-bottom:20px; }
    .modal-form { display:flex; flex-direction:column; gap:12px; }
    .modal-form label { font-size:.82rem; font-weight:600; color:#374151; margin-bottom:2px; display:block; }
    .modal-form input { width:100%; padding:10px 12px; border:1px solid #e5e7eb; border-radius:8px; font-family:var(--font-body); font-size:.9rem; }
    .modal-actions { display:flex; gap:8px; margin-top:8px; }
    .modal-actions .btn-accept { flex:1; justify-content:center; }
    .modal-actions .btn-annuleer { flex:1; padding:10px; border:1px solid #e5e7eb; border-radius:8px; background:#f3f4f6; font-family:var(--font-body); cursor:pointer; }

    /* SUCCESS */
    .geaccepteerd-banner { background:#d1fae5; border:1px solid #6ee7b7; border-radius:12px;
      padding:24px; text-align:center; margin:32px auto; max-width:500px; }
    .geaccepteerd-banner h3 { font-size:1.2rem; color:#065f46; margin-bottom:6px; }
    .geaccepteerd-banner p { color:#047857; font-size:.88rem; }

    /* PRINT */
    @media print {
      .viewer-topbar, .modal-overlay { display:none !important; }
      body { background:#fff; }
      .viewer-canvas { padding:0; }
      .a4-pagina { box-shadow:none; page-break-after:always; }
    }
  </style>
</head>
<body>

  {{-- TOPBAR --}}
  <div class="viewer-topbar">
    <a href="#" class="topbar-brand">
      <div class="topbar-brand-logo">
        <svg viewBox="0 0 24 24" fill="white"><path d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
      </div>
      <span class="topbar-brand-name">Energx</span>
    </a>
    <div class="topbar-right">
      <button onclick="window.print()" class="btn-print">🖨 Printen / PDF</button>
      @if(in_array($offerte->status, ['verstuurd', 'bekeken']))
        <button onclick="document.getElementById('accepteer-modal').style.display='flex'" class="btn-accept">
          ✓ Offerte accepteren
        </button>
      @elseif($offerte->status === 'geaccepteerd')
        <span style="color:#065f46;font-weight:600;font-size:.875rem;">✓ Geaccepteerd op {{ $offerte->geaccepteerd_op?->format('d-m-Y') }}</span>
      @endif
    </div>
  </div>

  {{-- STATUS BANNER --}}
  @if($offerte->status === 'geaccepteerd')
    <div style="margin-top:56px;background:#d1fae5;padding:12px;text-align:center;font-weight:500;color:#065f46;font-size:.875rem;">
      ✓ Offerte geaccepteerd door {{ $offerte->geaccepteerd_door }} op {{ $offerte->geaccepteerd_op?->format('d-m-Y') }}
    </div>
  @elseif($offerte->geldig_tot && $offerte->geldig_tot->isPast() && $offerte->status !== 'geaccepteerd')
    <div style="margin-top:56px;background:#fee2e2;padding:12px;text-align:center;font-weight:500;color:#991b1b;font-size:.875rem;">
      ⚠ Deze offerte is verlopen op {{ $offerte->geldig_tot->format('d-m-Y') }}
    </div>
  @else
    <div style="margin-top:56px;"></div>
  @endif

  {{-- DOCUMENT --}}
  <div class="viewer-canvas">
    {!! $document_html !!}
  </div>

  {{-- ACCEPTEER MODAL --}}
  @if(in_array($offerte->status, ['verstuurd', 'bekeken']))
    <div id="accepteer-modal" style="display:none;" class="modal-overlay" onclick="if(event.target===this)this.style.display='none'">
      <div class="modal">
        @if(session('accepted'))
          <div class="geaccepteerd-banner">
            <h3>🎉 Geaccepteerd!</h3>
            <p>Bedankt voor uw akkoord. We nemen zo snel mogelijk contact met u op.</p>
          </div>
        @else
          <h2>Offerte accepteren</h2>
          <p>Vul uw naam en e-mailadres in om de offerte digitaal te accepteren.</p>
          <form method="POST" action="{{ route('offertes.accepteer', $offerte->token) }}" class="modal-form">
            @csrf
            @if($errors->any())
              <div style="background:#fee2e2;color:#991b1b;padding:10px;border-radius:8px;font-size:.82rem;">
                {{ $errors->first() }}
              </div>
            @endif
            <div>
              <label>Volledige naam *</label>
              <input type="text" name="naam" value="{{ old('naam') }}" required placeholder="Jan de Vries">
            </div>
            <div>
              <label>E-mailadres *</label>
              <input type="email" name="email" value="{{ old('email') }}" required placeholder="jan@bedrijf.nl">
            </div>
            <div class="modal-actions">
              <button type="button" onclick="document.getElementById('accepteer-modal').style.display='none'" class="modal-actions btn-annuleer">Annuleren</button>
              <button type="submit" class="btn-accept">✓ Akkoord geven</button>
            </div>
          </form>
        @endif
      </div>
    </div>

    @if(session('accepted') || $errors->any())
      <script>document.addEventListener('DOMContentLoaded', () => document.getElementById('accepteer-modal').style.display='flex');</script>
    @endif
  @endif

</body>
</html>
