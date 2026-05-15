<x-layouts.crm title="Aanvragen">

  <x-slot name="actions">
    <a href="{{ route('docs.swagger') }}" target="_blank" class="btn btn-secondary btn-sm">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/>
        <line x1="12" y1="16" x2="12.01" y2="16"/>
      </svg>
      API docs
    </a>
  </x-slot>

  <div class="page-header">
    <div>
      <h1>Aanvragen</h1>
      <p>Alle binnengekomen offerteaanvragen via de website</p>
    </div>
  </div>

  @php
    $templateLabels = [
      'thuisbatterij' => 'Thuisbatterij',
      'laadpaal'      => 'Laadpaal',
      'warmtepomp'    => 'Warmtepomp',
    ];

    $detailLabels = [
      'zonnepanelen'   => 'Zonnepanelen aanwezig',
      'doelen'         => 'Doelen',
      'verbruik'       => 'Jaarlijks verbruik',
      'capaciteit'     => 'Gewenste capaciteit',
      'situatie'       => 'Installatie situatie',
      'model'          => 'Laadpaal model',
      'meters_kabel'   => 'Kabellengte (m)',
      'graawerk_meters'=> 'Graafwerk (m)',
    ];
  @endphp

  <div class="card">
    <div class="card-header">
      <span class="card-title">{{ $aanvragen->total() }} aanvragen</span>
    </div>

    @if($aanvragen->isEmpty())
      <div class="empty-state">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
          <path d="M9 12h6m-3-3v6M3 12a9 9 0 1118 0 9 9 0 01-18 0z"/>
        </svg>
        <p>Nog geen aanvragen ontvangen.</p>
      </div>
    @else
      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th>Datum</th>
              <th>Klant</th>
              <th>Product</th>
              <th>Voorkeur contact</th>
              <th>Status</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            @foreach($aanvragen as $aanvraag)
              @php
                $customer  = $aanvraag->payload['customer'] ?? [];
                $naam      = $customer['name']  ?? '—';
                $email     = $customer['email'] ?? '—';
                $telefoon  = $customer['phone'] ?? null;
                $product   = $templateLabels[$aanvraag->template_identifier] ?? ucfirst($aanvraag->template_identifier);
                $commPref  = $aanvraag->communication_preference;
                $heeftOfferte = $aanvraag->offerte !== null;
              @endphp
              <tr x-data="{ open: false }">
                <td style="white-space:nowrap; color:#888; font-size:.8rem">
                  {{ $aanvraag->created_at->format('d M Y') }}<br>
                  <span style="color:#bbb">{{ $aanvraag->created_at->format('H:i') }}</span>
                </td>
                <td>
                  <div style="font-weight:600">{{ $naam }}</div>
                  <div style="font-size:.8rem; color:#888">{{ $email }}</div>
                  @if($telefoon)
                    <div style="font-size:.8rem; color:#888">{{ $telefoon }}</div>
                  @endif
                </td>
                <td>
                  <span class="badge badge-concept" style="background:#f0fdf4; color:#15803d">{{ $product }}</span>
                </td>
                <td style="font-size:.85rem; color:#666">
                  @if($commPref === 'bellen')     Bellen
                  @elseif($commPref === 'email')  E-mail
                  @elseif($commPref === 'whatsapp') WhatsApp
                  @else —
                  @endif
                </td>
                <td>
                  @if($heeftOfferte)
                    <a href="{{ route('offertes.show', $aanvraag->offerte_id) }}" class="badge badge-geaccepteerd" style="text-decoration:none">
                      Offerte aangemaakt
                    </a>
                  @else
                    <span class="badge" style="background:#fef9c3; color:#854d0e">Nieuw</span>
                  @endif
                </td>
                <td style="text-align:right">
                  @if(!empty($aanvraag->details))
                    <button @click="open = !open" class="btn btn-secondary btn-sm">
                      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:13px;height:13px" :style="open && 'transform:rotate(180deg)'">
                        <polyline points="6 9 12 15 18 9"/>
                      </svg>
                      Details
                    </button>
                  @endif
                </td>
              </tr>
              @if(!empty($aanvraag->details))
                <tr x-show="open" x-cloak>
                  <td colspan="6" style="background:#f9fafb; padding:16px 20px">
                    <div style="display:flex; flex-wrap:wrap; gap:12px">
                      @foreach($aanvraag->details as $key => $value)
                        <div style="background:#fff; border:1px solid #e5e7eb; border-radius:8px; padding:10px 14px; min-width:160px">
                          <div style="font-size:.7rem; font-weight:600; text-transform:uppercase; letter-spacing:.06em; color:#aaa; margin-bottom:4px">
                            {{ $detailLabels[$key] ?? $key }}
                          </div>
                          <div style="font-size:.875rem; color:#1a1a1a; font-weight:500">
                            @if(is_array($value))
                              {{ implode(', ', $value) }}
                            @else
                              {{ $value }}
                            @endif
                          </div>
                        </div>
                      @endforeach
                    </div>
                  </td>
                </tr>
              @endif
            @endforeach
          </tbody>
        </table>
      </div>

      @if($aanvragen->hasPages())
        <div style="padding:16px 20px; border-top:1px solid #f0f0f0">
          {{ $aanvragen->links() }}
        </div>
      @endif
    @endif
  </div>

</x-layouts.crm>
