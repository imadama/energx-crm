<x-layouts.crm title="Producten">
  <x-slot:actions>
    <a href="{{ route('producten.create') }}" class="btn btn-primary">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
      Nieuw product
    </a>
  </x-slot:actions>

  <div class="page-header">
    <div><h1>Producten</h1><p>Producten en pakketten beschikbaar in de offerte-builder</p></div>
  </div>

  <div class="card">
    <div class="table-wrap">
      @if($producten->isEmpty())
        <div class="empty-state">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
            <path d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z"/>
          </svg>
          <p>Nog geen producten. <a href="{{ route('producten.create') }}" style="color:var(--green-400)">Voeg het eerste product toe.</a></p>
        </div>
      @else
        <form method="POST" action="{{ route('producten.order.update') }}" id="order-form">
          @csrf @method('PATCH')
          <table>
            <thead>
              <tr>
                <th style="width:56px"></th>
                <th style="width:72px">Volgorde</th>
                <th>Naam</th><th>Categorie</th><th>Merk</th><th>Prijs (excl. BTW)</th><th>Generator</th><th>Status</th><th></th>
              </tr>
            </thead>
            <tbody id="products-sortable">
              @foreach($producten as $i => $product)
              <tr data-product-id="{{ $product->id }}">
                <td style="padding:10px 8px">
                  <span class="drag-handle" title="Sleep om te herordenen" aria-label="Sleep om te herordenen">⋮⋮</span>
                </td>
                <td>
                  <span class="order-badge" data-order>{{ $product->order ?? 0 }}</span>
                  <input type="hidden" name="items[{{ $i }}][order]" value="{{ $product->order ?? 0 }}" data-order-input>
                  <input type="hidden" name="items[{{ $i }}][id]" value="{{ $product->id }}">
                </td>
                <td style="font-weight:600;color:#1a1a1a">{{ $product->naam }}</td>
                <td>{{ ucfirst($product->categorie) }}</td>
                <td>{{ $product->merk ?? '—' }}</td>
                <td>€ {{ number_format($product->prijs, 2, ',', '.') }}</td>
                <td style="font-size:.85rem;color:#666">{{ $product->generator_mode ?? 'manual' }}</td>
                <td>
                  <span class="badge {{ $product->actief ? 'badge-geaccepteerd' : 'badge-verlopen' }}">
                    {{ $product->actief ? 'Actief' : 'Inactief' }}
                  </span>
                </td>
                <td>
                  <a href="{{ route('producten.edit', $product) }}" class="btn btn-secondary btn-sm">Bewerk</a>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
          <div style="padding:12px 20px;border-top:1px solid #f0f0f0;display:flex;justify-content:space-between;align-items:center;gap:12px">
            <div>{{ $producten->links() }}</div>
            <button type="submit" class="btn btn-primary btn-sm" id="save-order-btn" disabled style="opacity:.6;cursor:not-allowed">Volgorde opslaan</button>
          </div>
        </form>
      @endif
    </div>
  </div>
</x-layouts.crm>

@if(!$producten->isEmpty())
  <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
  <script>
    (function () {
      const tbody = document.getElementById('products-sortable');
      const saveBtn = document.getElementById('save-order-btn');
      const form = document.getElementById('order-form');
      if (!tbody || !saveBtn || !form || !window.Sortable) return;

      function markDirty() {
        saveBtn.disabled = false;
        saveBtn.style.opacity = '1';
        saveBtn.style.cursor = 'pointer';
      }

      function recalcOrders() {
        const rows = [...tbody.querySelectorAll('tr[data-product-id]')];
        rows.forEach((row, idx) => {
          const order = idx + 1;
          const badge = row.querySelector('[data-order]');
          const input = row.querySelector('[data-order-input]');
          if (badge) badge.textContent = String(order);
          if (input) input.value = String(order);
        });
      }

      recalcOrders();

      Sortable.create(tbody, {
        handle: '.drag-handle',
        animation: 150,
        onEnd: () => {
          recalcOrders();
          markDirty();
        },
      });
    })();
  </script>

  <style>
    .drag-handle {
      display:inline-flex;
      align-items:center;
      justify-content:center;
      width:34px;
      height:34px;
      border:1px solid #e5e7eb;
      border-radius:10px;
      background:#fff;
      color:#94a3b8;
      cursor:grab;
      user-select:none;
      font-weight:700;
      letter-spacing:-2px;
    }
    tr:active .drag-handle { cursor:grabbing; }
    .order-badge {
      display:inline-flex;
      min-width:40px;
      height:28px;
      padding:0 10px;
      align-items:center;
      justify-content:center;
      border-radius:999px;
      background:rgba(45,189,110,.10);
      color:var(--green-800);
      font-weight:700;
      font-size:.82rem;
      border:1px solid rgba(45,189,110,.25);
    }
  </style>
@endif
