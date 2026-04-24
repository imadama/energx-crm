<x-layouts.crm title="API veld toevoegen">
  <x-slot:actions>
    <a href="{{ route('api-fields.index') }}" class="btn btn-secondary">Annuleren</a>
  </x-slot:actions>

  <div class="page-header">
    <div><h1>Nieuw API veld</h1><p>Definieer een key die via `details` kan worden aangeleverd</p></div>
  </div>

  <form method="POST" action="{{ route('api-fields.store') }}">
    @csrf
    @include('api-fields._form')
    <div style="margin-top:14px;max-width:760px;display:flex;justify-content:flex-end">
      <button class="btn btn-primary" type="submit">Opslaan</button>
    </div>
  </form>
</x-layouts.crm>

