<x-layouts.crm title="Klanten">
  <x-slot:actions>
    <a href="{{ route('klanten.create') }}" class="btn btn-primary">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
      Nieuwe klant
    </a>
  </x-slot:actions>

  <div class="page-header">
    <div><h1>Klanten</h1><p>Alle geregistreerde klanten</p></div>
  </div>

  <div class="card">
    <div class="table-wrap">
      @if($klanten->isEmpty())
        <div class="empty-state">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
            <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/>
          </svg>
          <p>Nog geen klanten. <a href="{{ route('klanten.create') }}" style="color:var(--green-400)">Voeg de eerste toe.</a></p>
        </div>
      @else
        <table>
          <thead>
            <tr><th>Naam</th><th>E-mail</th><th>Telefoon</th><th>Stad</th><th>Offertes</th><th>Bron</th><th></th></tr>
          </thead>
          <tbody>
            @foreach($klanten as $klant)
            <tr>
              <td style="font-weight:600;color:#1a1a1a">{{ $klant->naam }}</td>
              <td>{{ $klant->email }}</td>
              <td>{{ $klant->telefoon ?? '—' }}</td>
              <td>{{ $klant->stad ?? '—' }}</td>
              <td>{{ $klant->offertes_count }}</td>
              <td><span class="badge badge-concept">{{ ucfirst($klant->bron) }}</span></td>
              <td><a href="{{ route('klanten.show', $klant) }}" class="btn btn-secondary btn-sm">Bekijk</a></td>
            </tr>
            @endforeach
          </tbody>
        </table>
        <div style="padding:16px 20px;border-top:1px solid #f0f0f0">{{ $klanten->links() }}</div>
      @endif
    </div>
  </div>
</x-layouts.crm>
