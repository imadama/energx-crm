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
        <table>
          <thead>
            <tr><th>Naam</th><th>Categorie</th><th>Merk</th><th>Prijs (excl. BTW)</th><th>Status</th><th></th></tr>
          </thead>
          <tbody>
            @foreach($producten as $product)
            <tr>
              <td style="font-weight:600;color:#1a1a1a">{{ $product->naam }}</td>
              <td>{{ ucfirst($product->categorie) }}</td>
              <td>{{ $product->merk ?? '—' }}</td>
              <td>€ {{ number_format($product->prijs, 2, ',', '.') }}</td>
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
        <div style="padding:16px 20px;border-top:1px solid #f0f0f0">{{ $producten->links() }}</div>
      @endif
    </div>
  </div>
</x-layouts.crm>
