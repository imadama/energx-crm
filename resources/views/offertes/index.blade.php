<x-layouts.crm title="Offertes">
  <x-slot:actions>
    <a href="{{ route('offertes.create') }}" class="btn btn-primary">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
      Nieuwe offerte
    </a>
  </x-slot:actions>

  <div class="page-header">
    <div>
      <h1>Offertes</h1>
      <p>Beheer en verstuur offertes naar klanten</p>
    </div>
  </div>

  <div class="card">
    <div class="table-wrap">
      @if($offertes->isEmpty())
        <div class="empty-state">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
            <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/>
          </svg>
          <p>Nog geen offertes. <a href="{{ route('offertes.create') }}" style="color:var(--green-400)">Maak de eerste aan.</a></p>
        </div>
      @else
        <table>
          <thead>
            <tr>
              <th>Nummer</th>
              <th>Klant</th>
              <th>Status</th>
              <th>Totaal</th>
              <th>Geldig tot</th>
              <th>Aangemaakt</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            @foreach($offertes as $offerte)
            <tr>
              <td style="font-weight:600;color:#1a1a1a">{{ $offerte->nummer }}</td>
              <td>{{ $offerte->klant->naam }}</td>
              <td><span class="badge badge-{{ $offerte->status }}">{{ ucfirst($offerte->status) }}</span></td>
              <td>€ {{ number_format($offerte->totaal, 2, ',', '.') }}</td>
              <td style="color:#aaa">{{ $offerte->geldig_tot?->format('d M Y') ?? '—' }}</td>
              <td style="color:#aaa">{{ $offerte->created_at->format('d M Y') }}</td>
              <td>
                <a href="{{ route('offertes.show', $offerte) }}" class="btn btn-secondary btn-sm">Bekijk</a>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
        <div style="padding:16px 20px;border-top:1px solid #f0f0f0">
          {{ $offertes->links() }}
        </div>
      @endif
    </div>
  </div>
</x-layouts.crm>
