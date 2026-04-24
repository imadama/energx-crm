<x-layouts.crm title="API velden">
  <x-slot:actions>
    <a href="{{ route('api-fields.create') }}" class="btn btn-primary">Nieuw veld</a>
  </x-slot:actions>

  <div class="page-header">
    <div><h1>API velden</h1><p>Definieer welke keys in `details` zijn toegestaan en hun datatype</p></div>
  </div>

  <div class="card">
    <div class="table-wrap">
      @if($fields->isEmpty())
        <div class="empty-state">
          <p>Nog geen velden. <a href="{{ route('api-fields.create') }}" style="color:var(--green-400)">Voeg het eerste veld toe</a>.</p>
        </div>
      @else
        <table>
          <thead>
            <tr><th>Key</th><th>Label</th><th>Type</th><th>Lijstwaarden</th><th></th></tr>
          </thead>
          <tbody>
            @foreach($fields as $f)
              <tr>
                <td style="font-weight:600;color:#111">{{ $f->key }}</td>
                <td>{{ $f->label ?? '—' }}</td>
                <td>{{ $f->type }}</td>
                <td style="color:#666;font-size:.85rem">
                  @if($f->type === 'list')
                    {{ implode(', ', $f->allowed_values ?? []) }}
                  @else
                    —
                  @endif
                </td>
                <td style="display:flex;gap:8px;justify-content:flex-end">
                  <a class="btn btn-secondary btn-sm" href="{{ route('api-fields.edit', $f) }}">Bewerk</a>
                  <form method="POST" action="{{ route('api-fields.destroy', $f) }}" onsubmit="return confirm('Veld verwijderen?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm">Verwijder</button>
                  </form>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      @endif
    </div>
  </div>
</x-layouts.crm>

