<x-layouts.crm title="Product bewerken">
  <x-slot:actions>
    <form method="POST" action="{{ route('producten.destroy', $product) }}" onsubmit="return confirm('Product verwijderen?')">
      @csrf @method('DELETE')
      <button type="submit" class="btn btn-danger btn-sm">Verwijderen</button>
    </form>
  </x-slot:actions>

  <div class="page-header">
    <div><h1>{{ $product->naam }}</h1><p>Product bewerken</p></div>
  </div>

  <div class="card" style="max-width:640px">
    <div class="card-body">
      <form method="POST" action="{{ route('producten.update', $product) }}">
        @csrf @method('PUT')

        <div class="form-grid-2">
          <div class="form-group" style="grid-column:1/-1">
            <label class="form-label">Naam *</label>
            <input class="form-input" type="text" name="naam" value="{{ old('naam', $product->naam) }}" required>
            @error('naam')<div class="form-error">{{ $message }}</div>@enderror
          </div>

          <div class="form-group">
            <label class="form-label">Categorie *</label>
            <select class="form-select" name="categorie" required>
              @foreach(['laadpaal','installatie','thuisbatterij','warmtepomp','accessoire','overig'] as $cat)
                <option value="{{ $cat }}" {{ old('categorie', $product->categorie) === $cat ? 'selected' : '' }}>{{ ucfirst($cat) }}</option>
              @endforeach
            </select>
          </div>

          <div class="form-group">
            <label class="form-label">Merk</label>
            <input class="form-input" type="text" name="merk" value="{{ old('merk', $product->merk) }}">
          </div>

          <div class="form-group">
            <label class="form-label">Prijs excl. BTW (€) *</label>
            <input class="form-input" type="number" name="prijs" value="{{ old('prijs', $product->prijs) }}" step="0.01" min="0" required>
          </div>

          <div class="form-group" style="display:flex;align-items:center;gap:10px;padding-top:24px">
            <input type="checkbox" name="actief" id="actief" value="1" {{ old('actief', $product->actief) ? 'checked' : '' }}
                   style="width:16px;height:16px;accent-color:var(--green-400)">
            <label for="actief" class="form-label" style="margin:0;cursor:pointer">Actief</label>
          </div>

          <div class="form-group" style="grid-column:1/-1">
            <label class="form-label">Beschrijving</label>
            <textarea class="form-textarea" name="beschrijving">{{ old('beschrijving', $product->beschrijving) }}</textarea>
          </div>
        </div>

        <div style="display:flex;gap:10px;margin-top:8px">
          <button type="submit" class="btn btn-primary">Opslaan</button>
          <a href="{{ route('producten.index') }}" class="btn btn-secondary">Annuleren</a>
        </div>
      </form>
    </div>
  </div>
</x-layouts.crm>
