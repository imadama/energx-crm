<x-layouts.crm title="Nieuwe klant">
  <x-slot:actions>
    <a href="{{ route('klanten.index') }}" class="btn btn-secondary">Annuleren</a>
  </x-slot:actions>

  <div class="page-header">
    <div><h1>Nieuwe klant</h1><p>Voeg een klant toe aan het CRM</p></div>
  </div>

  <div class="card" style="max-width:640px">
    <div class="card-body">
      <form method="POST" action="{{ route('klanten.store') }}" x-data="{ soort: '{{ old('soort', 'particulier') }}' }">
        @csrf

        <div class="form-grid-2">
          <div class="form-group" style="grid-column:1/-1">
            <label class="form-label">Soort klant *</label>
            <div style="display:flex;gap:16px;margin-top:8px">
              <label style="display:flex;align-items:center;gap:6px;cursor:pointer">
                <input type="radio" name="soort" value="particulier" x-model="soort"> Particulier
              </label>
              <label style="display:flex;align-items:center;gap:6px;cursor:pointer">
                <input type="radio" name="soort" value="bedrijf" x-model="soort"> Bedrijf
              </label>
            </div>
            @error('soort')<div class="form-error">{{ $message }}</div>@enderror
          </div>

          <div class="form-group" style="grid-column:1/-1" x-show="soort === 'bedrijf'">
            <label class="form-label">Bedrijfsnaam *</label>
            <input class="form-input" type="text" name="naam" value="{{ old('naam') }}" placeholder="Bedrijf B.V." :required="soort === 'bedrijf'">
            @error('naam')<div class="form-error">{{ $message }}</div>@enderror
          </div>

          <div style="grid-column:1/-1;margin-top:10px;margin-bottom:10px;border-top:1px solid #e5e7eb;padding-top:20px;font-weight:600">
            Gegevens Contactpersoon
          </div>

          <div class="form-group">
            <label class="form-label">Voornaam *</label>
            <input class="form-input" type="text" name="voornaam" value="{{ old('voornaam') }}" required placeholder="Jan">
            @error('voornaam')<div class="form-error">{{ $message }}</div>@enderror
          </div>

          <div class="form-group">
            <label class="form-label">Achternaam *</label>
            <input class="form-input" type="text" name="achternaam" value="{{ old('achternaam') }}" required placeholder="de Vries">
            @error('achternaam')<div class="form-error">{{ $message }}</div>@enderror
          </div>

          <div class="form-group">
            <label class="form-label">E-mailadres *</label>
            <input class="form-input" type="email" name="email" value="{{ old('email') }}" required placeholder="jan@email.nl">
            @error('email')<div class="form-error">{{ $message }}</div>@enderror
          </div>

          <div class="form-group">
            <label class="form-label">Telefoonnummer</label>
            <input class="form-input" type="text" name="telefoon" value="{{ old('telefoon') }}" placeholder="06 12345678">
            @error('telefoon')<div class="form-error">{{ $message }}</div>@enderror
          </div>

          <div style="grid-column:1/-1;margin-top:10px;margin-bottom:10px;border-top:1px solid #e5e7eb;padding-top:20px;font-weight:600">
            Adres & Overig
          </div>

          <div class="form-group">
            <label class="form-label">Straat</label>
            <input class="form-input" type="text" name="straat" value="{{ old('straat') }}" placeholder="Hoofdstraat">
          </div>

          <div class="form-group">
            <label class="form-label">Huisnummer</label>
            <input class="form-input" type="text" name="huisnummer" value="{{ old('huisnummer') }}" placeholder="12A">
          </div>

          <div class="form-group">
            <label class="form-label">Postcode</label>
            <input class="form-input" type="text" name="postcode" value="{{ old('postcode') }}" placeholder="1234 AB">
          </div>

          <div class="form-group">
            <label class="form-label">Stad</label>
            <input class="form-input" type="text" name="stad" value="{{ old('stad') }}" placeholder="Amsterdam">
          </div>

          <div class="form-group" style="grid-column:1/-1">
            <label class="form-label">Bron *</label>
            <select class="form-select" name="bron" required>
              @foreach(['website','telefoon','email','doorverwijzing','anders'] as $bron)
                <option value="{{ $bron }}" {{ old('bron') === $bron ? 'selected' : '' }}>{{ ucfirst($bron) }}</option>
              @endforeach
            </select>
          </div>

          <div class="form-group" style="grid-column:1/-1">
            <label class="form-label">Notities</label>
            <textarea class="form-textarea" name="notities" placeholder="Interne notities over deze klant">{{ old('notities') }}</textarea>
          </div>
        </div>

        <div style="display:flex;gap:10px;margin-top:8px">
          <button type="submit" class="btn btn-primary">Klant opslaan</button>
          <a href="{{ route('klanten.index') }}" class="btn btn-secondary">Annuleren</a>
        </div>
      </form>
    </div>
  </div>
</x-layouts.crm>
