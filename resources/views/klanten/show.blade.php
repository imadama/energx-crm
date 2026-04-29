<x-layouts.crm title="{{ $klant->naam }}">
  <x-slot:actions>
    <a href="{{ route('offertes.create', ['klant_id' => $klant->id]) }}" class="btn btn-primary">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
      Nieuwe offerte
    </a>
    <a href="{{ route('klanten.edit', $klant) }}" class="btn btn-secondary">Bewerken</a>
  </x-slot:actions>

  <div class="page-header">
    <div><h1>{{ $klant->naam }}</h1><p>{{ $contactpersonen->first()->email ?? '' }}</p></div>
  </div>

  <div style="display:grid;grid-template-columns:320px 1fr;gap:20px;align-items:start">

    <!-- Klantgegevens -->
    <div class="card">
      <div class="card-header"><span class="card-title">Gegevens</span></div>
      <div class="card-body" style="display:flex;flex-direction:column;gap:14px">
        @foreach([
          ['Soort', ucfirst($klant->soort)],
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

    <!-- Tabs content -->
    <div x-data="{ tab: 'contactpersonen' }">
      <div style="display:flex;gap:16px;margin-bottom:20px;border-bottom:1px solid #e5e7eb">
        <button @click="tab = 'contactpersonen'" :class="tab === 'contactpersonen' ? 'border-b-2 border-green-600 text-green-800' : 'text-gray-500'" style="padding:10px 16px;font-weight:600;background:transparent;border-top:none;border-left:none;border-right:none;cursor:pointer">Contactpersonen ({{ count($contactpersonen) }})</button>
        <button @click="tab = 'offertes'" :class="tab === 'offertes' ? 'border-b-2 border-green-600 text-green-800' : 'text-gray-500'" style="padding:10px 16px;font-weight:600;background:transparent;border-top:none;border-left:none;border-right:none;cursor:pointer">Offertes ({{ count($offertes) }})</button>
        <button @click="tab = 'tickets'" :class="tab === 'tickets' ? 'border-b-2 border-green-600 text-green-800' : 'text-gray-500'" style="padding:10px 16px;font-weight:600;background:transparent;border-top:none;border-left:none;border-right:none;cursor:pointer">Tickets ({{ count($tickets) }})</button>
      </div>

      <!-- Contactpersonen Tab -->
      <div x-show="tab === 'contactpersonen'" class="card">
        <div class="card-header" style="display:flex;justify-content:space-between;align-items:center">
          <span class="card-title">Contactpersonen</span>
          <a href="{{ route('contactpersonen.create', $klant) }}" class="btn btn-primary btn-sm">Nieuw contact</a>
        </div>
        @if($contactpersonen->isEmpty())
          <div class="empty-state">
            <p>Geen contactpersonen gevonden.</p>
          </div>
        @else
          <div class="table-wrap">
            <table>
              <thead><tr><th>Naam</th><th>E-mailadres</th><th>Telefoon</th><th></th></tr></thead>
              <tbody>
                @foreach($contactpersonen as $contact)
                <tr>
                  <td style="font-weight:600;color:#1a1a1a">{{ $contact->voornaam }} {{ $contact->achternaam }}</td>
                  <td>{{ $contact->email }}</td>
                  <td>{{ $contact->telefoon ?? '—' }}</td>
                  <td style="text-align:right">
                    <a href="{{ route('contactpersonen.edit', $contact) }}" class="btn btn-secondary btn-sm" style="margin-right:4px">Bewerken</a>
                    <form action="{{ route('contactpersonen.destroy', $contact) }}" method="POST" style="display:inline-block" onsubmit="return confirm('Weet je zeker dat je deze contactpersoon wilt verwijderen?')">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-secondary btn-sm" style="color:#ef4444;border-color:#fca5a5" {{ count($contactpersonen) <= 1 ? 'disabled title="Laatste contactpersoon kan niet verwijderd worden"' : '' }}>Verwijderen</button>
                    </form>
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @endif
      </div>

      <!-- Offertes Tab -->
      <div x-show="tab === 'offertes'" class="card" style="display:none">
        <div class="card-header"><span class="card-title">Offertes</span></div>
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

      <!-- Tickets Tab -->
      <div x-show="tab === 'tickets'" class="card" style="display:none">
        <div class="card-header"><span class="card-title">Tickets</span></div>
        @if($tickets->isEmpty())
          <div class="empty-state">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
              <path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/>
            </svg>
            <p>Geen tickets voor deze klant.</p>
          </div>
        @else
          <div class="table-wrap">
            <table>
              <thead><tr><th>Nummer</th><th>Status</th><th>Titel</th><th>Contactpersoon</th><th></th></tr></thead>
              <tbody>
                @foreach($tickets as $ticket)
                <tr>
                  <td style="font-weight:600;color:#1a1a1a">{{ $ticket->nummer }}</td>
                  <td>
                    <span class="badge" style="
                      {{ $ticket->status === 'open' ? 'background:#fee2e2;color:#dc2626;' : '' }}
                      {{ $ticket->status === 'in afwachting' ? 'background:#fef3c7;color:#d97706;' : '' }}
                      {{ $ticket->status === 'gesloten' ? 'background:#f3f4f6;color:#6b7280;' : '' }}
                    ">{{ ucfirst($ticket->status) }}</span>
                  </td>
                  <td>{{ $ticket->titel }}</td>
                  <td>{{ $ticket->contactpersoon->voornaam }} {{ $ticket->contactpersoon->achternaam }}</td>
                  <td><a href="{{ route('tickets.show', $ticket) }}" class="btn btn-secondary btn-sm">Bekijk</a></td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @endif
      </div>
    </div>
  </div>
</x-layouts.crm>
