<x-layouts.crm title="{{ $klant->naam }}">
  <x-slot:actions>
    <a href="{{ route('offertes.create', ['klant_id' => $klant->id]) }}" class="btn btn-primary">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
      Nieuwe offerte
    </a>
    <a href="{{ route('klanten.edit', $klant) }}" class="btn btn-secondary">Bewerken</a>
  </x-slot:actions>

  <div class="page-header">
    <div><h1>{{ $klant->naam }}</h1><p>{{ $klant->email }}</p></div>
  </div>

  <div style="display:grid;grid-template-columns:320px 1fr;gap:20px;align-items:start">

    <!-- Klantgegevens -->
    <div class="card">
      <div class="card-header"><span class="card-title">Gegevens</span></div>
      <div class="card-body" style="display:flex;flex-direction:column;gap:14px">
        @foreach([
          ['E-mail', $klant->email],
          ['Telefoon', $klant->telefoon ?? '—'],
          ['Adres', $klant->straat ? "{$klant->straat} {$klant->huisnummer}" : '—'],
          ['Postcode', $klant->postcode ?? '—'],
          ['Stad', $klant->stad ?? '—'],
          ['Bron', ucfirst($klant->bron)],
          ['Klant sinds', $klant->created_at->format('d M Y')],
        ] as [$label, $value])
        <div>
          <div style="font-size:.72rem;font-weight:600;text-transform:uppercase;letter-spacing:.07em;color:#aaa;margin-bottom:3px">{{ $label }}</div>
          <div style="font-size:.9rem;color:#1a1a1a">{{ $value }}</div>
        </div>
        @endforeach

        @if($klant->notities)
        <div>
          <div style="font-size:.72rem;font-weight:600;text-transform:uppercase;letter-spacing:.07em;color:#aaa;margin-bottom:3px">Notities</div>
          <div style="font-size:.875rem;color:#555;line-height:1.6">{{ $klant->notities }}</div>
        </div>
        @endif
      </div>
    </div>

    <!-- Offertes -->
    <div class="card">
      <div class="card-header"><span class="card-title">Offertes ({{ count($offertes) }})</span></div>
      @if($offertes->isEmpty())
        <div class="empty-state">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
            <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/>
          </svg>
          <p>Nog geen offertes voor deze klant.</p>
        </div>
      @else
        <div class="table-wrap">
          <table>
            <thead><tr><th>Nummer</th><th>Status</th><th>Totaal</th><th>Datum</th><th></th></tr></thead>
            <tbody>
              @foreach($offertes as $offerte)
              <tr>
                <td style="font-weight:600;color:#1a1a1a">{{ $offerte->nummer }}</td>
                <td><span class="badge badge-{{ $offerte->status }}">{{ ucfirst($offerte->status) }}</span></td>
                <td>€ {{ number_format($offerte->totaal, 2, ',', '.') }}</td>
                <td style="color:#aaa">{{ $offerte->created_at->format('d M Y') }}</td>
                <td><a href="{{ route('offertes.show', $offerte) }}" class="btn btn-secondary btn-sm">Bekijk</a></td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      @endif
    </div>
  </div>
</x-layouts.crm>
