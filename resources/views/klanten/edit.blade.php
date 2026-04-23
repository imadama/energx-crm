<x-layouts.crm title="Klant bewerken">
  <x-slot:actions>
    <form method="POST" action="{{ route('klanten.destroy', $klant) }}" onsubmit="return confirm('Klant en alle bijbehorende offertes verwijderen?')">
      @csrf @method('DELETE')
      <button type="submit" class="btn btn-danger btn-sm">Verwijderen</button>
    </form>
  </x-slot:actions>

  <div class="page-header">
    <div><h1>{{ $klant->naam }}</h1><p>Klantgegevens bewerken</p></div>
  </div>

  <div class="card" style="max-width:640px">
    <div class="card-body">
      <form method="POST" action="{{ route('klanten.update', $klant) }}">
        @csrf @method('PUT')

        <div class="form-grid-2">
          <div class="form-group" style="grid-column:1/-1">
            <label class="form-label">Volledige naam *</label>
            <input class="form-input" type="text" name="naam" value="{{ old('naam', $klant->naam) }}" required>
          </div>
          <div class="form-group">
            <label class="form-label">E-mailadres *</label>
            <input class="form-input" type="email" name="email" value="{{ old('email', $klant->email) }}" required>
          </div>
          <div class="form-group">
            <label class="form-label">Telefoonnummer</label>
            <input class="form-input" type="text" name="telefoon" value="{{ old('telefoon', $klant->telefoon) }}">
          </div>
          <div class="form-group">
            <label class="form-label">Straat</label>
            <input class="form-input" type="text" name="straat" value="{{ old('straat', $klant->straat) }}">
          </div>
          <div class="form-group">
            <label class="form-label">Huisnummer</label>
            <input class="form-input" type="text" name="huisnummer" value="{{ old('huisnummer', $klant->huisnummer) }}">
          </div>
          <div class="form-group">
            <label class="form-label">Postcode</label>
            <input class="form-input" type="text" name="postcode" value="{{ old('postcode', $klant->postcode) }}">
          </div>
          <div class="form-group">
            <label class="form-label">Stad</label>
            <input class="form-input" type="text" name="stad" value="{{ old('stad', $klant->stad) }}">
          </div>
          <div class="form-group" style="grid-column:1/-1">
            <label class="form-label">Bron *</label>
            <select class="form-select" name="bron" required>
              @foreach(['website','telefoon','email','doorverwijzing','anders'] as $bron)
                <option value="{{ $bron }}" {{ old('bron', $klant->bron) === $bron ? 'selected' : '' }}>{{ ucfirst($bron) }}</option>
              @endforeach
            </select>
          </div>
          <div class="form-group" style="grid-column:1/-1">
            <label class="form-label">Notities</label>
            <textarea class="form-textarea" name="notities">{{ old('notities', $klant->notities) }}</textarea>
          </div>
        </div>

        <div style="display:flex;gap:10px;margin-top:8px">
          <button type="submit" class="btn btn-primary">Opslaan</button>
          <a href="{{ route('klanten.show', $klant) }}" class="btn btn-secondary">Annuleren</a>
        </div>
      </form>
    </div>
  </div>
</x-layouts.crm>
