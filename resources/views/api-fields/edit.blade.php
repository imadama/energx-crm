<x-layouts.crm title="API veld bewerken">
  <x-slot:actions>
    <a href="{{ route('api-fields.index') }}" class="btn btn-secondary">Annuleren</a>
  </x-slot:actions>

  <div class="page-header">
    <div><h1>API veld bewerken</h1><p>{{ $apiField->key }}</p></div>
  </div>

  <form method="POST" action="{{ route('api-fields.update', $apiField) }}">
    @csrf @method('PUT')
    @include('api-fields._form', ['apiField' => $apiField])
    <div style="margin-top:14px;max-width:760px;display:flex;justify-content:flex-end">
      <button class="btn btn-primary" type="submit">Opslaan</button>
    </div>
  </form>
</x-layouts.crm>

