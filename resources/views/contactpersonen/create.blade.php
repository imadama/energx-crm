<x-layouts.crm title="Nieuwe contactpersoon">
  <x-slot:actions>
    <a href="{{ route('klanten.show', $klant) }}" class="btn btn-secondary">Annuleren</a>
  </x-slot:actions>

  <div class="page-header">
    <div><h1>Nieuwe contactpersoon</h1><p>Voor klant: {{ $klant->naam }}</p></div>
  </div>

  <div class="card" style="max-width:640px">
    <div class="card-body">
      <form method="POST" action="{{ route('contactpersonen.store', $klant) }}">
        @csrf

        <div class="form-grid-2">
          <div class="form-group">
            <label class="form-label">Voornaam *</label>
            <input class="form-input" type="text" name="voornaam" value="{{ old('voornaam') }}" required>
            @error('voornaam')<div class="form-error">{{ $message }}</div>@enderror
          </div>

          <div class="form-group">
            <label class="form-label">Achternaam *</label>
            <input class="form-input" type="text" name="achternaam" value="{{ old('achternaam') }}" required>
            @error('achternaam')<div class="form-error">{{ $message }}</div>@enderror
          </div>

          <div class="form-group">
            <label class="form-label">E-mailadres *</label>
            <input class="form-input" type="email" name="email" value="{{ old('email') }}" required>
            @error('email')<div class="form-error">{{ $message }}</div>@enderror
          </div>

          <div class="form-group">
            <label class="form-label">Telefoonnummer</label>
            <input class="form-input" type="text" name="telefoon" value="{{ old('telefoon') }}">
            @error('telefoon')<div class="form-error">{{ $message }}</div>@enderror
          </div>
        </div>

        <div style="display:flex;gap:10px;margin-top:20px">
          <button type="submit" class="btn btn-primary">Contactpersoon opslaan</button>
          <a href="{{ route('klanten.show', $klant) }}" class="btn btn-secondary">Annuleren</a>
        </div>
      </form>
    </div>
  </div>
</x-layouts.crm>
