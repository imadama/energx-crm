<x-layouts.crm title="Offerte templates">
  <x-slot:actions>
    <a href="{{ route('offerte-templates.create') }}" class="btn btn-primary">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
      Nieuwe template
    </a>
  </x-slot:actions>

  <div class="page-header">
    <div><h1>Offerte templates</h1><p>Maak per product een standaard template met secties, teksten en prijsregels</p></div>
  </div>

  @if($templates->isEmpty())
    <div class="card">
      <div class="empty-state" style="padding:60px">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
          <rect x="3" y="3" width="18" height="18" rx="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="21" x2="9" y2="9"/>
        </svg>
        <p>Nog geen templates. <a href="{{ route('offerte-templates.create') }}" style="color:var(--green-400)">Maak de eerste aan.</a></p>
      </div>
    </div>
  @else
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:16px">
      @foreach($templates as $template)
      <div class="card" style="transition:box-shadow .15s" onmouseover="this.style.boxShadow='0 4px 20px rgba(0,0,0,.1)'" onmouseout="this.style.boxShadow=''">
        <div style="padding:20px">
          <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:10px">
            <div style="width:36px;height:36px;border-radius:8px;background:var(--green-100);color:var(--green-400);display:flex;align-items:center;justify-content:center">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="3" width="18" height="18" rx="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="21" x2="9" y2="9"/>
              </svg>
            </div>
            @if($template->categorie)
              <span class="badge badge-verstuurd">{{ ucfirst($template->categorie) }}</span>
            @endif
          </div>
          <div style="font-weight:600;color:#1a1a1a;margin-bottom:4px">{{ $template->naam }}</div>
          @if($template->beschrijving)
            <div style="font-size:.82rem;color:#aaa;margin-bottom:12px;line-height:1.5">{{ Str::limit($template->beschrijving, 80) }}</div>
          @endif
          <div style="font-size:.75rem;color:#bbb;margin-bottom:16px">{{ $template->secties_count }} secties</div>
          <div style="display:flex;gap:8px">
            <a href="{{ route('offerte-templates.edit', $template) }}" class="btn btn-secondary btn-sm" style="flex:1;justify-content:center">Bewerk</a>
            <a href="{{ route('offertes.create', ['template_id' => $template->id]) }}" class="btn btn-primary btn-sm" style="flex:1;justify-content:center">Gebruik</a>
          </div>
        </div>
      </div>
      @endforeach
    </div>
  @endif
</x-layouts.crm>
