<x-layouts.crm title="Nieuw Ticket">
  <x-slot:actions>
    <a href="{{ route('tickets.index') }}" class="btn btn-secondary">Annuleren</a>
  </x-slot:actions>

  <div class="page-header">
    <div><h1>Nieuw ticket aanmaken</h1><p>Start een nieuwe thread met een klant</p></div>
  </div>

  <div class="card" style="max-width:700px">
    <div class="card-body">
      <form method="POST" action="{{ route('tickets.store') }}" enctype="multipart/form-data" x-data="{ klant_id: '' }">
        @csrf

        <div class="form-group">
          <label class="form-label">Klant & Contactpersoon *</label>
          <select name="contactpersoon_id" class="form-select" required>
            <option value="">Selecteer contactpersoon...</option>
            @foreach($klanten as $klant)
              <optgroup label="{{ $klant->naam }}">
                @foreach($klant->contactpersonen as $contact)
                  <option value="{{ $contact->id }}" {{ old('contactpersoon_id') == $contact->id ? 'selected' : '' }}>
                    {{ $contact->voornaam }} {{ $contact->achternaam }} ({{ $contact->email }})
                  </option>
                @endforeach
              </optgroup>
            @endforeach
          </select>
          @error('contactpersoon_id')<div class="form-error">{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
          <label class="form-label">Onderwerp (Titel) *</label>
          <input type="text" name="titel" class="form-input" value="{{ old('titel') }}" required placeholder="Bijv. Vraag over factuur">
          @error('titel')<div class="form-error">{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
          <label class="form-label">Kanaal (Bron) *</label>
          <select name="bron" class="form-select" required>
            <option value="email" {{ old('bron') === 'email' ? 'selected' : '' }}>E-mail</option>
            <option value="telefoon" {{ old('bron') === 'telefoon' ? 'selected' : '' }}>Telefoon</option>
            <option value="whatsapp" {{ old('bron') === 'whatsapp' ? 'selected' : '' }}>WhatsApp</option>
            <option value="portaal" {{ old('bron') === 'portaal' ? 'selected' : '' }}>Klantportaal</option>
          </select>
          @error('bron')<div class="form-error">{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
          <label class="form-label">Eerste bericht (Klantvraag) *</label>
          <x-wysiwyg name="inhoud" value="{{ old('inhoud') }}" placeholder="Typ hier de inhoud van het bericht..." />
          @error('inhoud')<div class="form-error">{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
          <label class="form-label">Bijlagen (optioneel)</label>
          <input type="file" name="bijlagen[]" class="form-input" multiple style="padding:6px;background:#f9fafb">
          <div style="font-size:0.75rem;color:#888;margin-top:4px">Meerdere bestanden toegestaan (max 10MB per stuk)</div>
          @error('bijlagen.*')<div class="form-error">{{ $message }}</div>@enderror
        </div>

        <div style="display:flex;gap:10px;margin-top:24px">
          <button type="submit" class="btn btn-primary">Ticket opslaan & openen</button>
        </div>
      </form>
    </div>
  </div>
</x-layouts.crm>
