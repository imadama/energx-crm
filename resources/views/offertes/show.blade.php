<x-layouts.crm title="{{ $offerte->nummer }}">
  <x-slot:actions>
    <a href="{{ route('offertes.viewer', $offerte->token) }}" target="_blank" class="btn btn-secondary">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="15" height="15"><path d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
      Viewer
    </a>
    <a href="{{ route('offertes.editor', $offerte) }}" class="btn btn-secondary">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="15" height="15"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
      Editor
    </a>
    @if(in_array($offerte->status, ['concept','verstuurd']))
      <a href="{{ route('offertes.edit', $offerte) }}" class="btn btn-secondary">Bewerken</a>
    @endif
    @if($offerte->status === 'concept')
      <form method="POST" action="{{ route('offertes.update', $offerte) }}">
        @csrf @method('PUT')
        <input type="hidden" name="status" value="verstuurd">
        <button type="submit" class="btn btn-primary">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="15" height="15"><path d="M22 2L11 13"/><path d="M22 2L15 22l-4-9-9-4 20-7z"/></svg>
          Versturen
        </button>
      </form>
    @endif
  </x-slot:actions>

  <div class="page-header">
    <div>
      <h1>{{ $offerte->nummer }}</h1>
      <p>{{ $offerte->klant->naam }} · <span class="badge badge-{{ $offerte->status }}">{{ ucfirst($offerte->status) }}</span></p>
    </div>
  </div>

  <div style="display:grid;grid-template-columns:1fr 280px;gap:20px;align-items:start">

    <div style="display:flex;flex-direction:column;gap:16px">
      <!-- Regels -->
      <div class="card">
        <div class="card-header"><span class="card-title">Offerte regels</span></div>
        <div class="table-wrap">
          <table>
            <thead><tr><th>Omschrijving</th><th>Aantal</th><th>Eenheidsprijs</th><th>Totaal</th></tr></thead>
            <tbody>
              @foreach($offerte->regels as $regel)
              <tr>
                <td>
                  <div style="font-weight:500;color:#1a1a1a">{{ $regel->naam }}</div>
                  @if($regel->beschrijving)
                    <div style="font-size:.78rem;color:#aaa;margin-top:2px">{{ $regel->beschrijving }}</div>
                  @endif
                </td>
                <td>{{ $regel->aantal }}</td>
                <td>€ {{ number_format($regel->eenheidsprijs, 2, ',', '.') }}</td>
                <td style="font-weight:500">€ {{ number_format($regel->totaal, 2, ',', '.') }}</td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        <div style="padding:16px 20px;border-top:1px solid #f0f0f0;display:flex;justify-content:flex-end">
          <div style="display:flex;flex-direction:column;gap:6px;min-width:220px">
            <div style="display:flex;justify-content:space-between;font-size:.875rem;color:#666">
              <span>Subtotaal</span><span>€ {{ number_format($offerte->subtotaal, 2, ',', '.') }}</span>
            </div>
            <div style="display:flex;justify-content:space-between;font-size:.875rem;color:#666">
              <span>BTW (21%)</span><span>€ {{ number_format($offerte->btw_bedrag, 2, ',', '.') }}</span>
            </div>
            <div style="display:flex;justify-content:space-between;font-size:1rem;font-weight:700;color:var(--green-800);padding-top:8px;border-top:2px solid #e8e8e8;margin-top:4px">
              <span>Totaal incl. BTW</span><span>€ {{ number_format($offerte->totaal, 2, ',', '.') }}</span>
            </div>
          </div>
        </div>
      </div>

      @if($offerte->inleiding)
      <div class="card">
        <div class="card-header"><span class="card-title">Inleiding</span></div>
        <div class="card-body" style="color:#555;font-size:.9rem;line-height:1.7;white-space:pre-line">{{ $offerte->inleiding }}</div>
      </div>
      @endif
    </div>

    <!-- Rechts: info -->
    <div class="card">
      <div class="card-header"><span class="card-title">Details</span></div>
      <div class="card-body" style="display:flex;flex-direction:column;gap:14px">
        @foreach([
          ['Klant', $offerte->klant->naam],
          ['E-mail', $offerte->klant->email],
          ['Status', ucfirst($offerte->status)],
          ['Aangemaakt', $offerte->created_at->format('d M Y')],
          ['Geldig tot', $offerte->geldig_tot?->format('d M Y') ?? '—'],
          ['Verstuurd op', $offerte->verstuurd_op?->format('d M Y H:i') ?? '—'],
          ['Bekeken op', $offerte->bekeken_op?->format('d M Y H:i') ?? '—'],
        ] as [$label, $value])
        <div>
          <div style="font-size:.7rem;font-weight:600;text-transform:uppercase;letter-spacing:.07em;color:#aaa;margin-bottom:3px">{{ $label }}</div>
          <div style="font-size:.875rem;color:#1a1a1a">{{ $value }}</div>
        </div>
        @endforeach

        @if($offerte->status === 'geaccepteerd')
        <div style="background:var(--green-100);border-radius:8px;padding:12px;margin-top:4px">
          <div style="font-size:.75rem;font-weight:600;color:var(--green-600);margin-bottom:4px">✓ Geaccepteerd</div>
          <div style="font-size:.82rem;color:#444">{{ $offerte->geaccepteerd_door }}</div>
          <div style="font-size:.78rem;color:#aaa">{{ $offerte->geaccepteerd_op?->format('d M Y H:i') }}</div>
        </div>
        @endif

        <div style="margin-top:8px">
          <div style="font-size:.7rem;font-weight:600;text-transform:uppercase;letter-spacing:.07em;color:#aaa;margin-bottom:6px">Viewer link</div>
          <div style="font-size:.78rem;color:#aaa;word-break:break-all;background:#f9f9f9;padding:8px;border-radius:6px">
            {{ route('offertes.viewer', $offerte->token) }}
          </div>
        </div>
      </div>
    </div>
  </div>
</x-layouts.crm>
