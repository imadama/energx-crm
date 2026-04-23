<x-layouts.crm title="Dashboard">

  <x-slot:actions>
    <a href="{{ route('offertes.create') }}" class="btn btn-primary">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
      Nieuwe offerte
    </a>
  </x-slot:actions>

  <!-- Stat cards -->
  <div class="stats-grid">
    <div class="stat-card">
      <div class="stat-icon green">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/>
          <path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/>
        </svg>
      </div>
      <div class="stat-label">Klanten</div>
      <div class="stat-value">{{ $stats['klanten'] }}</div>
      <div class="stat-sub">totaal geregistreerd</div>
    </div>
    <div class="stat-card">
      <div class="stat-icon blue">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
          <polyline points="14 2 14 8 20 8"/>
        </svg>
      </div>
      <div class="stat-label">Open offertes</div>
      <div class="stat-value">{{ $stats['open'] }}</div>
      <div class="stat-sub">concept, verstuurd of bekeken</div>
    </div>
    <div class="stat-card">
      <div class="stat-icon orange">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <polyline points="20 6 9 17 4 12"/>
        </svg>
      </div>
      <div class="stat-label">Geaccepteerd</div>
      <div class="stat-value">{{ $stats['geaccepteerd'] }}</div>
      <div class="stat-sub">ondertekende offertes</div>
    </div>
    <div class="stat-card">
      <div class="stat-icon purple">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z"/>
        </svg>
      </div>
      <div class="stat-label">Actieve producten</div>
      <div class="stat-value">{{ $stats['producten'] }}</div>
      <div class="stat-sub">beschikbaar in offerte-builder</div>
    </div>
  </div>

  <!-- Recente offertes -->
  <div class="card">
    <div class="card-header">
      <span class="card-title">Recente offertes</span>
      <a href="{{ route('offertes.index') }}" class="btn btn-secondary btn-sm">Alle offertes</a>
    </div>
    <div class="table-wrap">
      @if($recenteOffertes->isEmpty())
        <div class="empty-state">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
            <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
            <polyline points="14 2 14 8 20 8"/>
          </svg>
          <p>Nog geen offertes aangemaakt.</p>
        </div>
      @else
        <table>
          <thead>
            <tr>
              <th>Nummer</th>
              <th>Klant</th>
              <th>Status</th>
              <th>Totaal</th>
              <th>Datum</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            @foreach($recenteOffertes as $offerte)
            <tr>
              <td style="font-weight:600; color:#1a1a1a">{{ $offerte->nummer }}</td>
              <td>{{ $offerte->klant->naam }}</td>
              <td>
                <span class="badge badge-{{ $offerte->status }}">
                  {{ ucfirst($offerte->status) }}
                </span>
              </td>
              <td>€ {{ number_format($offerte->totaal, 2, ',', '.') }}</td>
              <td style="color:#aaa">{{ $offerte->created_at->format('d M Y') }}</td>
              <td>
                <a href="{{ route('offertes.show', $offerte) }}" class="btn btn-secondary btn-sm">Bekijk</a>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      @endif
    </div>
  </div>

</x-layouts.crm>
