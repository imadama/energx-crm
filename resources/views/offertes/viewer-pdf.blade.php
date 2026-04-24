<!DOCTYPE html>
<html lang="nl">
<head>
  <meta charset="UTF-8">
  <title>{{ $offerte->nummer }} — Energx offerte</title>
  <base href="{{ config('app.url') }}/">
  <style>
    @page { size: A4 portrait; margin: 0; }
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    :root {
      --green-800: #0F4A2A; --green-600: #1a6e3f; --green-400: #2DBD6E;
      --night: #090F0C;
      --font-display: 'DM Serif Display', DejaVu Serif, serif;
      --font-body: 'Outfit', DejaVu Sans, sans-serif;
    }
    body { margin: 0; font-family: var(--font-body); background: #fff; color: #1a1a1a; }

    /* Embed viewer fonts as TTF (dompdf does not support woff2) */
    @font-face {
      font-family: 'Outfit';
      font-style: normal;
      font-weight: 300;
      src: url('{{ public_path('fonts/outfit/Outfit.ttf') }}') format('truetype');
    }
    @font-face {
      font-family: 'DM Serif Display';
      font-style: normal;
      font-weight: 400;
      src: url('{{ public_path('fonts/dm-serif-display/DMSerifDisplay-Regular.ttf') }}') format('truetype');
    }
    @font-face {
      font-family: 'DM Serif Display';
      font-style: italic;
      font-weight: 400;
      src: url('{{ public_path('fonts/dm-serif-display/DMSerifDisplay-Italic.ttf') }}') format('truetype');
    }

    /* Print pages: match viewer page-card look */
    .pdf-page { page-break-after: always; }
    .pdf-page:last-child { page-break-after: auto; }
    .page-card {
      width: 794px;           /* match viewer */
      min-height: 1123px;     /* match viewer */
      background: #fff;
      overflow: hidden;
    }
    img { max-width: 100%; height: auto; }

    /* VOORBLAD */
    .cover-hero-placeholder {
      width: 100%;
      height: 380px;
      background: url('file://{{ public_path('pdf-assets/cover-hero.png') }}') no-repeat center center;
      background-size: cover;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
    }
    .cover-hero-title { font-family: var(--font-display); font-size: 56px; color: #fff; }
    .cover-hero-sub { font-size: 14px; color: rgba(255,255,255,.55); margin-top: 6px; }
    .cover-body { padding: 36px 48px 40px; }
    .cover-meta { padding-bottom: 28px; border-bottom: 1px solid #f0f0f0; margin-bottom: 28px; }
    .meta-block { display: inline-block; vertical-align: top; width: 31%; margin-right: 2%; }
    .meta-block:last-child { margin-right: 0; }
    .meta-label { font-size: .7rem; font-weight: 600; text-transform: uppercase; letter-spacing: .08em; color: #aaa; margin-bottom: 5px; }
    .meta-value { font-size: .95rem; font-weight: 600; color: #1a1a1a; }
    .meta-value small { display: block; font-weight: 400; color: #777; font-size: .85rem; margin-top: 2px; }

    /* CONTENT */
    .section-content { padding: 48px; }
    .section-title {
      font-family: var(--font-display);
      font-size: 2rem;
      color: var(--green-800);
      margin-bottom: 28px;
      padding-bottom: 16px;
      border-bottom: 2px solid var(--green-400);
    }
    .intro-text p { color: #4a4a4a; line-height: 1.85; margin-bottom: 16px; font-size: .93rem; }

    /* PRIJZEN */
    .price-table { width: 100%; border-collapse: collapse; }
    .price-table th { text-align: left; padding: 10px 14px; font-size: .7rem; font-weight: 600; text-transform: uppercase; letter-spacing: .07em; color: #999; border-bottom: 2px solid #e8e8e8; }
    .price-table th:last-child, .price-table td:last-child { text-align: right; }
    .price-table td { padding: 14px 14px; font-size: .88rem; color: #333; border-bottom: 1px solid #f2f2f2; vertical-align: top; }
    .item-name { font-weight: 700; color: #1a1a1a; }
    .item-desc { font-size: .78rem; color: #999; margin-top: 3px; }
    .price-totals { margin-top: 12px; text-align: right; }
    .total-row { font-size: .88rem; color: #666; margin-top: 7px; }
    .total-row.grand { font-size: 1.05rem; font-weight: 700; color: var(--green-800); padding-top: 12px; border-top: 2px solid #e8e8e8; margin-top: 8px; }

    /* PRODUCT */
    .product-layout { width: 100%; }
    .product-col { display: inline-block; vertical-align: top; width: 48%; }
    .product-col + .product-col { margin-left: 4%; }
    .spec-list { margin-top: 16px; }
    .spec-row { display: block; padding: 9px 0; border-bottom: 1px solid #f2f2f2; font-size: .85rem; }
    .spec-key { color: #888; display: inline-block; width: 55%; }
    .spec-val { font-weight: 700; color: #1a1a1a; display: inline-block; width: 43%; text-align: right; }
    .product-info p { color: #555; line-height: 1.75; font-size: .9rem; margin-bottom: 14px; }

    /* WERKWIJZE */
    .step { padding: 20px 0; border-bottom: 1px solid #f2f2f2; }
    .step:last-child { border-bottom: none; }
    .step-num { display:inline-block; width: 34px; height: 34px; border-radius: 50%; background: var(--green-400); color:#fff; font-weight: 700; font-size: .82rem; text-align:center; line-height: 34px; margin-right: 12px; }
    .step-title { font-weight: 600; color: #1a1a1a; margin-bottom: 5px; font-size: .9rem; display:inline-block; }
    .step-desc { font-size: .85rem; color: #666; line-height: 1.65; margin-left: 46px; }

    /* ACCEPTATIE */
    .accept-page { padding: 64px 48px; text-align: center; }
    .accept-title { font-family: var(--font-display); font-size: 1.9rem; color: var(--green-800); margin-bottom: 12px; }
    .accept-desc { color: #666; font-size: .93rem; line-height: 1.75; margin: 0 auto; max-width: 460px; }
  </style>
</head>
<body>
@php
  $secties = $offerte->secties;
  $isGeaccepteerd = $offerte->status === 'geaccepteerd';
@endphp

@foreach($secties as $sectie)
  @php $inhoud = $sectie->inhoud ?? []; @endphp
  <div class="pdf-page">
    <div class="page-card">
      {{-- VOORBLAD --}}
      @if($sectie->type === 'voorblad')
        <div class="cover-hero-placeholder">
          <div class="cover-hero-title">Energx</div>
          <div class="cover-hero-sub">www.energx.nl</div>
        </div>
        <div class="cover-body">
          <div class="cover-meta">
            <div class="meta-block">
              <div class="meta-label">Offerte voor</div>
              <div class="meta-value">{{ $offerte->klant->naam }}<small>{{ $offerte->klant->adres }}</small></div>
            </div>
            <div class="meta-block">
              <div class="meta-label">Offerte nr.</div>
              <div class="meta-value">{{ $offerte->nummer }}</div>
            </div>
            <div class="meta-block">
              <div class="meta-label">Datum</div>
              <div class="meta-value">{{ $offerte->created_at->format('d F Y') }}</div>
              @if($offerte->geldig_tot)
                <div style="margin-top:4mm">
                  <div class="meta-label">Geldig tot</div>
                  <div class="meta-value">{{ $offerte->geldig_tot->format('d F Y') }}</div>
                </div>
              @endif
            </div>
          </div>
          @if($offerte->inleiding)
            <div>
              <div class="meta-label">Betreft</div>
              <div class="meta-value" style="margin-top:2mm">{{ $offerte->inleiding }}</div>
            </div>
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
            <thead>
              <tr>
                <th style="width:48%">Omschrijving</th>
                <th>Aantal</th>
                <th>Eenheidsprijs</th>
                <th>Totaal</th>
              </tr>
            </thead>
            <tbody>
              @foreach($offerte->regels as $regel)
              <tr>
                <td>
                  <div class="item-name">{{ $regel->naam }}</div>
                  @if($regel->beschrijving)<div class="item-desc">{{ $regel->beschrijving }}</div>@endif
                </td>
                <td>{{ $regel->aantal }}</td>
                <td>€ {{ number_format($regel->eenheidsprijs, 2, ',', '.') }}</td>
                <td>€ {{ number_format($regel->totaal, 2, ',', '.') }}</td>
              </tr>
              @endforeach
            </tbody>
          </table>
          <div class="price-totals">
            <div class="total-row">Subtotaal: <strong>€ {{ number_format($offerte->subtotaal, 2, ',', '.') }}</strong></div>
            <div class="total-row">BTW (21%): <strong>€ {{ number_format($offerte->btw_bedrag, 2, ',', '.') }}</strong></div>
            <div class="total-row grand">Totaal incl. BTW: <strong>€ {{ number_format($offerte->totaal, 2, ',', '.') }}</strong></div>
          </div>
        </div>

      {{-- PRODUCT INFO --}}
      @elseif($sectie->type === 'product')
        <div class="section-content">
          <h2 class="section-title">{{ $sectie->titel }}</h2>
          <div class="product-layout">
            <div class="product-col">
              <div style="width:100%;height:344px;border-radius:12px;overflow:hidden;">
                <img src="file://{{ public_path('pdf-assets/product-placeholder.png') }}" style="width:100%;height:344px;display:block;" alt="">
              </div>
              @if(!empty($inhoud['specs']))
              <div class="spec-list">
                @foreach($inhoud['specs'] as $spec)
                  <div class="spec-row"><span class="spec-key">{{ $spec['label'] }}</span><span class="spec-val">{{ $spec['waarde'] }}</span></div>
                @endforeach
              </div>
              @endif
            </div>
            <div class="product-col product-info">
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
              <div>
                <span class="step-num">{{ $i + 1 }}</span>
                <span class="step-title">{{ $stap['titel'] }}</span>
              </div>
              <div class="step-desc">{{ $stap['beschrijving'] }}</div>
            </div>
            @endforeach
          </div>
        </div>

      {{-- ACCEPTATIE --}}
      @elseif($sectie->type === 'acceptatie')
        <div class="accept-page">
          @if($isGeaccepteerd)
            <h2 class="accept-title">Offerte geaccepteerd</h2>
            <p class="accept-desc">Deze offerte is geaccepteerd door <strong>{{ $offerte->geaccepteerd_door }}</strong> op {{ $offerte->geaccepteerd_op->format('d F Y \o\m H:i') }}.</p>
          @else
            <h2 class="accept-title">{{ $sectie->titel }}</h2>
            <p class="accept-desc">{{ $inhoud['tekst'] ?? 'Ben je akkoord met deze offerte? Klik op de knop hieronder om digitaal te ondertekenen.' }}</p>
          @endif
        </div>
      @endif
    </div>
  </div>
@endforeach

</body>
</html>

