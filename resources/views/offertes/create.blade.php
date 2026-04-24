<x-layouts.crm title="Nieuwe offerte">
  <x-slot:actions>
    <a href="{{ route('offertes.index') }}" class="btn btn-secondary">Annuleren</a>
  </x-slot:actions>

  <div class="page-header">
    <div><h1>Nieuwe offerte</h1><p>Kies een klant en eventueel een template. Daarna open je de editor.</p></div>
  </div>

  <form method="POST" action="{{ route('offertes.store') }}" style="max-width:560px">
    @csrf
    <div class="card">
      <div class="card-body" style="display:flex;flex-direction:column;gap:16px">

        @if($errors->any())
          <div style="background:#fee2e2;color:#991b1b;padding:12px;border-radius:8px;font-size:.85rem;">
            {{ $errors->first() }}
          </div>
        @endif

        <div class="form-group">
          <label class="form-label">Klant *</label>
          <select class="form-select" name="klant_id" required>
            <option value="">Selecteer klant...</option>
            @foreach($klanten as $klant)
              <option value="{{ $klant->id }}" {{ old('klant_id') == $klant->id ? 'selected' : '' }}>
                {{ $klant->naam }} — {{ $klant->stad }}
              </option>
            @endforeach
          </select>
        </div>

        <div class="form-group">
          <label class="form-label">Template (optioneel)</label>
          <select class="form-select" name="template_id">
            <option value="">Leeg document</option>
            @foreach($templates as $template)
              <option value="{{ $template->id }}" {{ (old('template_id') == $template->id || request('template_id') == $template->id) ? 'selected' : '' }}>
                {{ $template->naam }}{{ $template->categorie ? ' (' . $template->categorie . ')' : '' }}
              </option>
            @endforeach
          </select>
        </div>

        <div class="form-group">
          <label class="form-label">Geldig tot</label>
          <input class="form-input" type="date" name="geldig_tot" value="{{ old('geldig_tot', now()->addDays(30)->format('Y-m-d')) }}">
        </div>

        <button type="submit" class="btn btn-primary">Offerte aanmaken →</button>
      </div>
    </div>
  </form>
</x-layouts.crm>
