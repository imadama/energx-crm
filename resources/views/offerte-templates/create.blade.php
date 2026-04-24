<x-layouts.crm title="Nieuwe template">
  <x-slot:actions>
    <a href="{{ route('offerte-templates.index') }}" class="btn btn-secondary">Annuleren</a>
  </x-slot:actions>

  <div class="page-header">
    <div><h1>Nieuwe template</h1><p>Na opslaan open je direct de editor om het document te bouwen</p></div>
  </div>

  <form method="POST" action="{{ route('offerte-templates.store') }}" style="max-width:560px">
    @csrf
    <div class="card">
      <div class="card-body" style="display:flex;flex-direction:column;gap:16px">
        <div class="form-group">
          <label class="form-label">Naam *</label>
          <input class="form-input" type="text" name="naam" required placeholder="bijv. Zaptec Go standaard installatie">
        </div>
        <div class="form-group">
          <label class="form-label">Categorie</label>
          <select class="form-select" name="categorie">
            <option value="">Geen categorie</option>
            @foreach(['laadpaal','warmtepomp','thuisbatterij','overig'] as $cat)
              <option value="{{ $cat }}">{{ ucfirst($cat) }}</option>
            @endforeach
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Beschrijving</label>
          <input class="form-input" type="text" name="beschrijving" placeholder="Korte omschrijving">
        </div>
        <button type="submit" class="btn btn-primary">Template aanmaken →</button>
      </div>
    </div>
  </form>
</x-layouts.crm>
