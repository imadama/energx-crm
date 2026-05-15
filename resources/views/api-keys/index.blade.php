<x-layouts.crm title="API-sleutels">

  <div class="page-header">
    <div>
      <h1>API-sleutels</h1>
      <p>Beheer de sleutels waarmee websites aanvragen kunnen insturen</p>
    </div>
  </div>

  @foreach($teams as $team)
    <div class="card" style="margin-bottom:24px">
      <div class="card-header">
        <span class="card-title">{{ $team->naam }}</span>
        @if($team->is_admin)
          <span class="badge" style="background:rgba(45,189,110,.1); color:#15803d">Admin</span>
        @endif
      </div>

      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th>Naam</th>
              <th>Sleutel</th>
              <th>Status</th>
              <th>Aangemaakt</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            @forelse($team->apiKeys as $key)
              <tr x-data="{ copied: false }">
                <td style="font-weight:600">{{ $key->naam }}</td>
                <td>
                  <code style="font-size:.8rem; background:#f3f4f6; padding:4px 8px; border-radius:6px; font-family:monospace; letter-spacing:.03em">
                    {{ $key->key }}
                  </code>
                  <button
                    @click="navigator.clipboard.writeText('{{ $key->key }}'); copied = true; setTimeout(() => copied = false, 2000)"
                    class="btn btn-secondary btn-sm"
                    style="margin-left:8px"
                    x-text="copied ? '✓ Gekopieerd' : 'Kopieer'"
                  ></button>
                </td>
                <td>
                  @if($key->actief)
                    <span class="badge badge-geaccepteerd">Actief</span>
                  @else
                    <span class="badge badge-afgewezen">Inactief</span>
                  @endif
                </td>
                <td style="color:#888; font-size:.8rem">{{ $key->created_at->format('d M Y') }}</td>
                <td style="text-align:right">
                  <form method="POST" action="{{ route('api-keys.destroy', $key) }}" onsubmit="return confirm('Sleutel verwijderen?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm">Verwijder</button>
                  </form>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="5" style="text-align:center; color:#aaa; padding:20px">Nog geen sleutels aangemaakt.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <div style="padding:16px 20px; border-top:1px solid #f0f0f0">
        <form method="POST" action="{{ route('api-keys.store') }}" style="display:flex; gap:10px; align-items:flex-end">
          @csrf
          <input type="hidden" name="team_id" value="{{ $team->id }}">
          <div class="form-group" style="margin:0; flex:1">
            <label class="form-label">Nieuwe sleutel naam</label>
            <input type="text" name="naam" class="form-input" placeholder="bijv. Energx Website" required>
          </div>
          <button type="submit" class="btn btn-primary">Genereer sleutel</button>
        </form>
      </div>
    </div>
  @endforeach

  <div class="card">
    <div class="card-header"><span class="card-title">Hoe gebruik je een API-sleutel?</span></div>
    <div class="card-body">
      <p style="font-size:.875rem; color:#555; margin-bottom:12px">Stuur de sleutel mee als <code>X-Api-Key</code> header bij elk verzoek naar de API:</p>
      <pre style="background:#1a1a2e; color:#e2e8f0; padding:16px; border-radius:8px; font-size:.8rem; overflow-x:auto">fetch('https://crm.energx.nl/api/v1/offer', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    'X-Api-Key': 'jouw_sleutel_hier'
  },
  body: JSON.stringify({ ... })
});</pre>
      <p style="font-size:.8rem; color:#888; margin-top:10px">
        Volledige documentatie: <a href="{{ route('docs.swagger') }}" target="_blank" style="color:var(--green-400)">API docs →</a>
      </p>
    </div>
  </div>

</x-layouts.crm>
