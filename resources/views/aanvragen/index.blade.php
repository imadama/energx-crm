<x-layouts.crm title="Aanvragen">

  <x-slot name="actions">
    <a href="{{ route('docs.swagger') }}" target="_blank" class="btn btn-secondary btn-sm">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
      API docs
    </a>
  </x-slot>

  <div class="page-header">
    <div>
      <h1>Aanvragen</h1>
      <p>{{ $aanvragen->flatten()->count() }} aanvragen totaal</p>
    </div>
  </div>

  @php
    $templateLabels = ['thuisbatterij' => 'Thuisbatterij', 'laadpaal' => 'Laadpaal', 'warmtepomp' => 'Warmtepomp'];
    $statusNext = [
      'nieuw'          => 'in_behandeling',
      'in_behandeling' => 'offerte_gemaakt',
      'offerte_gemaakt'=> 'afgerond',
      'afgerond'       => null,
      'afgewezen'      => null,
    ];
  @endphp

  <style>
    .board { display: flex; gap: 16px; overflow-x: auto; padding-bottom: 16px; align-items: flex-start; }
    .board-col { flex: 0 0 280px; min-width: 280px; }
    .board-col-header {
      display: flex; align-items: center; gap: 8px;
      padding: 10px 14px; border-radius: 10px 10px 0 0;
      font-size: .8rem; font-weight: 700; letter-spacing: .04em; text-transform: uppercase;
    }
    .board-col-count {
      margin-left: auto; background: rgba(0,0,0,.1);
      border-radius: 20px; padding: 2px 8px; font-size: .72rem;
    }
    .board-cards { display: flex; flex-direction: column; gap: 10px; padding: 10px 0; min-height: 80px; }
    .board-card {
      background: #fff; border-radius: 10px; border: 1px solid #e5e7eb;
      padding: 14px; box-shadow: 0 1px 3px rgba(0,0,0,.05);
      transition: box-shadow .15s;
    }
    .board-card:hover { box-shadow: 0 4px 12px rgba(0,0,0,.08); }
    .card-product { font-size: .7rem; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; margin-bottom: 6px; }
    .card-naam { font-size: .9rem; font-weight: 600; color: #1a1a1a; }
    .card-email { font-size: .75rem; color: #888; margin-top: 2px; }
    .card-meta { display: flex; align-items: center; justify-content: space-between; margin-top: 10px; font-size: .72rem; color: #aaa; }
    .card-actions { display: flex; gap: 6px; margin-top: 10px; flex-wrap: wrap; }
    .card-notitie { font-size: .78rem; color: #666; background: #f9fafb; border-radius: 6px; padding: 6px 8px; margin-top: 8px; border-left: 3px solid #e5e7eb; }
    .empty-col { text-align: center; padding: 24px 12px; color: #ccc; font-size: .8rem; border: 2px dashed #f0f0f0; border-radius: 8px; }
  </style>

  <div class="board">
    @foreach($kolommen as $statusKey => $kolom)
      @php $cards = $aanvragen->get($statusKey, collect()); @endphp
      <div class="board-col">
        <div class="board-col-header" style="background:{{ $kolom['bg'] }}; color:{{ $kolom['color'] }}">
          {{ $kolom['label'] }}
          <span class="board-col-count">{{ $cards->count() }}</span>
        </div>

        <div class="board-cards">
          @forelse($cards as $aanvraag)
            @php
              $customer = $aanvraag->payload['customer'] ?? [];
              $naam     = $customer['name']  ?? '—';
              $email    = $customer['email'] ?? '—';
              $telefoon = $customer['phone'] ?? null;
              $product  = $templateLabels[$aanvraag->template_identifier] ?? ucfirst($aanvraag->template_identifier);
              $nextStatus = $statusNext[$statusKey];
              $nextLabel  = $nextStatus ? $kolommen[$nextStatus]['label'] : null;
            @endphp
            <div class="board-card" x-data="{ open: false }">
              <div class="card-product" style="color:{{ $kolom['color'] }}">{{ $product }}</div>
              <div class="card-naam">{{ $naam }}</div>
              <div class="card-email">{{ $email }}</div>
              @if($telefoon)
                <div class="card-email">{{ $telefoon }}</div>
              @endif

              @if($aanvraag->notitie)
                <div class="card-notitie">{{ $aanvraag->notitie }}</div>
              @endif

              <div class="card-meta">
                <span>{{ $aanvraag->created_at->format('d M, H:i') }}</span>
                @if($aanvraag->offerte_id)
                  <a href="{{ route('offertes.show', $aanvraag->offerte_id) }}" style="color:var(--green-400); font-weight:600">Offerte →</a>
                @endif
              </div>

              <div class="card-actions">
                @if($nextStatus)
                  <form method="POST" action="{{ route('aanvragen.status', $aanvraag) }}">
                    @csrf @method('PATCH')
                    <input type="hidden" name="status" value="{{ $nextStatus }}">
                    <button type="submit" class="btn btn-primary btn-sm" style="font-size:.75rem; padding:5px 10px">
                      → {{ $nextLabel }}
                    </button>
                  </form>
                @endif
                @if($statusKey !== 'afgewezen')
                  <form method="POST" action="{{ route('aanvragen.status', $aanvraag) }}">
                    @csrf @method('PATCH')
                    <input type="hidden" name="status" value="afgewezen">
                    <button type="submit" class="btn btn-danger btn-sm" style="font-size:.75rem; padding:5px 10px">✕</button>
                  </form>
                @endif
                <button @click="open = !open" class="btn btn-secondary btn-sm" style="font-size:.75rem; padding:5px 10px">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:12px;height:12px"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                </button>
              </div>

              <div x-show="open" x-cloak style="margin-top:10px">
                <form method="POST" action="{{ route('aanvragen.status', $aanvraag) }}" style="display:flex; flex-direction:column; gap:6px">
                  @csrf @method('PATCH')
                  <input type="hidden" name="status" value="{{ $statusKey }}">
                  <select name="status" class="form-select" style="font-size:.8rem; padding:6px 8px">
                    @foreach($kolommen as $k => $kol)
                      <option value="{{ $k }}" {{ $statusKey === $k ? 'selected' : '' }}>{{ $kol['label'] }}</option>
                    @endforeach
                  </select>
                  <textarea name="notitie" class="form-textarea" placeholder="Notitie toevoegen..." style="font-size:.8rem; min-height:60px">{{ $aanvraag->notitie }}</textarea>
                  <button type="submit" class="btn btn-primary btn-sm" style="font-size:.78rem">Opslaan</button>
                </form>

                @if(!empty($aanvraag->details))
                  <div style="margin-top:8px; display:flex; flex-wrap:wrap; gap:6px">
                    @foreach($aanvraag->details as $key => $value)
                      <div style="background:#f3f4f6; border-radius:6px; padding:4px 8px; font-size:.72rem">
                        <span style="color:#888">{{ $key }}:</span>
                        <strong>{{ is_array($value) ? implode(', ', $value) : $value }}</strong>
                      </div>
                    @endforeach
                  </div>
                @endif
              </div>
            </div>
          @empty
            <div class="empty-col">Geen aanvragen</div>
          @endforelse
        </div>
      </div>
    @endforeach
  </div>

</x-layouts.crm>
