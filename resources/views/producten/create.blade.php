<x-layouts.crm title="Nieuw product">
  <x-slot:actions>
    <a href="{{ route('producten.index') }}" class="btn btn-secondary">Annuleren</a>
  </x-slot:actions>

  <div class="page-header">
    <div><h1>Nieuw product</h1><p>Voeg een product of pakket toe aan de catalogus</p></div>
  </div>

  <div class="card" style="max-width:640px">
    <div class="card-body">
      <form method="POST" action="{{ route('producten.store') }}">
        @csrf

        <div class="form-grid-2">
          <div class="form-group" style="grid-column:1/-1">
            <label class="form-label">Naam *</label>
            <input class="form-input" type="text" name="naam" value="{{ old('naam') }}" required placeholder="bijv. Zaptec Go 2 — 11 kW">
            @error('naam')<div class="form-error">{{ $message }}</div>@enderror
          </div>

          <div class="form-group">
            <label class="form-label">Categorie *</label>
            <select class="form-select" name="categorie" required>
              <option value="">Kies categorie</option>
              @foreach(['laadpaal','installatie','thuisbatterij','warmtepomp','accessoire','overig'] as $cat)
                <option value="{{ $cat }}" {{ old('categorie') === $cat ? 'selected' : '' }}>{{ ucfirst($cat) }}</option>
              @endforeach
            </select>
            @error('categorie')<div class="form-error">{{ $message }}</div>@enderror
          </div>

          <div class="form-group">
            <label class="form-label">Merk</label>
            <input class="form-input" type="text" name="merk" value="{{ old('merk') }}" placeholder="bijv. Zaptec, Ratio">
          </div>

          <div class="form-group">
            <label class="form-label">Prijs excl. BTW (€) *</label>
            <input class="form-input" type="number" name="prijs" value="{{ old('prijs') }}" step="0.01" min="0" required placeholder="0.00">
            @error('prijs')<div class="form-error">{{ $message }}</div>@enderror
          </div>

          <div class="form-group" style="display:flex;align-items:center;gap:10px;padding-top:24px">
            <input type="checkbox" name="actief" id="actief" value="1" {{ old('actief', '1') ? 'checked' : '' }}
                   style="width:16px;height:16px;accent-color:var(--green-400)">
            <label for="actief" class="form-label" style="margin:0;cursor:pointer">Actief (beschikbaar in offerte-builder)</label>
          </div>

          <div class="form-group" style="grid-column:1/-1">
            <label class="form-label">Beschrijving</label>
            <textarea class="form-textarea" name="beschrijving" placeholder="Verschijnt als subtekst op de offerte">{{ old('beschrijving') }}</textarea>
          </div>
        </div>

        <div style="display:flex;gap:10px;margin-top:8px">
          <button type="submit" class="btn btn-primary">Product opslaan</button>
          <a href="{{ route('producten.index') }}" class="btn btn-secondary">Annuleren</a>
        </div>
      </form>
    </div>
  </div>
</x-layouts.crm>
