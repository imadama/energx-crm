<x-layouts.crm title="Template instellingen — {{ $offerteTemplate->naam }}">
  <x-slot:actions>
    <a href="{{ route('offerte-templates.editor', $offerteTemplate) }}" class="btn btn-primary">Editor openen</a>
    <a href="{{ route('offerte-templates.index') }}" class="btn btn-secondary">Annuleren</a>
  </x-slot:actions>

  <div class="page-header">
    <div>
      <h1>{{ $offerteTemplate->naam }} — Instellingen</h1>
      <p>Bewerk de template-metadata. Inhoud bewerk je in de editor.</p>
    </div>
  </div>

  <form method="POST" action="{{ route('offerte-templates.update', $offerteTemplate) }}" style="max-width:560px">
    @csrf @method('PUT')

    <div class="card">
      <div class="card-body" style="display:flex;flex-direction:column;gap:16px">

        @if($errors->any())
          <div style="background:#fee2e2;color:#991b1b;padding:12px;border-radius:8px;font-size:.85rem;">
            {{ $errors->first() }}
          </div>
        @endif

        <div class="form-group">
          <label class="form-label">Naam *</label>
          <input class="form-input" type="text" name="naam" value="{{ old('naam', $offerteTemplate->naam) }}" required>
        </div>

        <div class="form-group">
          <label class="form-label">Categorie</label>
          <select class="form-select" name="categorie">
            <option value="">Geen categorie</option>
            @foreach(['laadpaal','warmtepomp','thuisbatterij','overig'] as $cat)
              <option value="{{ $cat }}" {{ old('categorie', $offerteTemplate->categorie) === $cat ? 'selected' : '' }}>
                {{ ucfirst($cat) }}
              </option>
            @endforeach
          </select>
        </div>

        <div class="form-group">
          <label class="form-label">Beschrijving</label>
          <input class="form-input" type="text" name="beschrijving" value="{{ old('beschrijving', $offerteTemplate->beschrijving) }}" placeholder="Korte omschrijving">
        </div>

        <button type="submit" class="btn btn-primary">Opslaan</button>
      </div>
    </div>
  </form>
</x-layouts.crm>
