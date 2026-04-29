<x-layouts.crm title="Contactpersoon bewerken">
  <x-slot:actions>
    <a href="{{ route('klanten.show', $contactpersoon->klant_id) }}" class="btn btn-secondary">Annuleren</a>
  </x-slot:actions>

  <div class="page-header">
    <div><h1>Contactpersoon bewerken</h1><p>Voor klant: {{ $contactpersoon->klant->naam }}</p></div>
  </div>

  <div class="card" style="max-width:640px">
    <div class="card-body">
      <form method="POST" action="{{ route('contactpersonen.update', $contactpersoon) }}">
        @csrf @method('PUT')

        <div class="form-grid-2">
          <div class="form-group">
            <label class="form-label">Voornaam *</label>
            <input class="form-input" type="text" name="voornaam" value="{{ old('voornaam', $contactpersoon->voornaam) }}" required>
            @error('voornaam')<div class="form-error">{{ $message }}</div>@enderror
          </div>

          <div class="form-group">
            <label class="form-label">Achternaam *</label>
            <input class="form-input" type="text" name="achternaam" value="{{ old('achternaam', $contactpersoon->achternaam) }}" required>
            @error('achternaam')<div class="form-error">{{ $message }}</div>@enderror
          </div>

          <div class="form-group">
            <label class="form-label">E-mailadres *</label>
            <input class="form-input" type="email" name="email" value="{{ old('email', $contactpersoon->email) }}" required>
            @error('email')<div class="form-error">{{ $message }}</div>@enderror
          </div>

          <div class="form-group">
            <label class="form-label">Telefoonnummer</label>
            <input class="form-input" type="text" name="telefoon" value="{{ old('telefoon', $contactpersoon->telefoon) }}">
            @error('telefoon')<div class="form-error">{{ $message }}</div>@enderror
          </div>
        </div>

        <div style="display:flex;gap:10px;margin-top:20px">
          <button type="submit" class="btn btn-primary">Wijzigingen opslaan</button>
          <a href="{{ route('klanten.show', $contactpersoon->klant_id) }}" class="btn btn-secondary">Annuleren</a>
        </div>
      </form>
    </div>
  </div>
</x-layouts.crm>
