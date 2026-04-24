<x-layouts.crm title="Offerte bewerken — {{ $offerte->nummer }}">
  <x-slot:actions>
    <a href="{{ route('offertes.editor', $offerte) }}" class="btn btn-primary">Editor openen</a>
    <a href="{{ route('offertes.show', $offerte) }}" class="btn btn-secondary">Annuleren</a>
  </x-slot:actions>

  <div class="page-header">
    <div>
      <h1>{{ $offerte->nummer }} — Instellingen</h1>
      <p>{{ $offerte->klant->naam }} · <span class="badge badge-{{ $offerte->status }}">{{ ucfirst($offerte->status) }}</span></p>
    </div>
  </div>

  <form method="POST" action="{{ route('offertes.update', $offerte) }}" style="max-width:560px">
    @csrf @method('PUT')

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
            @foreach($klanten as $klant)
              <option value="{{ $klant->id }}" {{ $offerte->klant_id == $klant->id ? 'selected' : '' }}>
                {{ $klant->naam }} — {{ $klant->stad }}
              </option>
            @endforeach
          </select>
        </div>

        <div class="form-group">
          <label class="form-label">Geldig tot</label>
          <input class="form-input" type="date" name="geldig_tot" value="{{ old('geldig_tot', $offerte->geldig_tot?->format('Y-m-d')) }}">
        </div>

        <button type="submit" class="btn btn-primary">Opslaan</button>
      </div>
    </div>
  </form>
</x-layouts.crm>
