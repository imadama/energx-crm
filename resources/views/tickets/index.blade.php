<x-layouts.crm title="Tickets">
  <x-slot:actions>
    <a href="{{ route('tickets.create') }}" class="btn btn-primary">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
      Nieuw ticket
    </a>
  </x-slot:actions>

  <div class="page-header" style="margin-bottom:16px">
    <div><h1>Tickets</h1><p>Klantcommunicatie en support tickets</p></div>
  </div>

  <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;background:#fff;padding:12px 20px;border-radius:10px;border:1px solid #ebebeb">
    <form method="GET" action="{{ route('tickets.index') }}" style="display:flex;gap:12px;align-items:center;flex:1">
      <div style="position:relative;width:300px">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="position:absolute;left:10px;top:10px;width:16px;color:#aaa"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Zoek op nummer, titel of naam..." class="form-input" style="padding-left:34px;padding-top:7px;padding-bottom:7px;min-height:36px">
      </div>
      <select name="status" class="form-select" style="width:160px;padding-top:7px;padding-bottom:7px;min-height:36px" onchange="this.form.submit()">
        <option value="" {{ request('status') === null ? 'selected' : '' }}>Open & In afwachting</option>
        <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>Alleen Open</option>
        <option value="in afwachting" {{ request('status') === 'in afwachting' ? 'selected' : '' }}>In afwachting</option>
        <option value="gesloten" {{ request('status') === 'gesloten' ? 'selected' : '' }}>Gesloten</option>
      </select>
      @if(request('search') || request('status'))
        <a href="{{ route('tickets.index') }}" style="font-size:13px;color:#888;text-decoration:none">Filters wissen</a>
      @endif
    </form>
  </div>

  <div class="card">
    @if($tickets->isEmpty())
      <div class="empty-state">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
          <path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/>
        </svg>
        <p>Geen tickets gevonden.</p>
      </div>
    @else
      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th>Nummer</th>
              <th>Status</th>
              <th>Titel</th>
              <th>Klant & Contactpersoon</th>
              <th>Laatste update</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            @foreach($tickets as $ticket)
            @php $isOpen = $ticket->status === 'open'; @endphp
            <tr style="cursor:pointer; {{ $isOpen ? 'background:#f0fdf4;' : '' }}" onclick="window.location='{{ route('tickets.show', $ticket) }}'">
              <td style="font-weight:600;color:{{ $isOpen ? '#166534' : '#1a1a1a' }}">{{ $ticket->nummer }}</td>
              <td>
                <span class="badge" style="
                  {{ $ticket->status === 'open' ? 'background:#fee2e2;color:#dc2626;' : '' }}
                  {{ $ticket->status === 'in afwachting' ? 'background:#fef3c7;color:#d97706;' : '' }}
                  {{ $ticket->status === 'gesloten' ? 'background:#f3f4f6;color:#6b7280;' : '' }}
                ">{{ ucfirst($ticket->status) }}</span>
              </td>
              <td style="font-weight:{{ $isOpen ? '700' : '500' }};color:{{ $isOpen ? '#000' : '#444' }}">{{ $ticket->titel }}</td>
              <td>
                <div style="font-weight:600;color:#1a1a1a">{{ $ticket->contactpersoon->klant->naam }}</div>
                <div style="font-size:.75rem;color:#666">{{ $ticket->contactpersoon->voornaam }} {{ $ticket->contactpersoon->achternaam }}</div>
              </td>
              <td style="color:#666;font-size:.8rem">{{ $ticket->updated_at->diffForHumans() }}</td>
              <td style="text-align:right">
                <a href="{{ route('tickets.show', $ticket) }}" class="btn btn-secondary btn-sm" onclick="event.stopPropagation()">Bekijk</a>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      @if($tickets->hasPages())
        <div style="padding:16px 20px;border-top:1px solid #f0f0f0">
          {{ $tickets->links() }}
        </div>
      @endif
    @endif
  </div>
</x-layouts.crm>
