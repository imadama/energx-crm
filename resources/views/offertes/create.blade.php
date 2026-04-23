<x-layouts.crm title="Nieuwe offerte">
  <x-slot:actions>
    <a href="{{ route('offertes.index') }}" class="btn btn-secondary">Annuleren</a>
  </x-slot:actions>

  <div class="page-header">
    <div><h1>Nieuwe offerte</h1><p>Stel een offerte samen en verstuur hem naar de klant</p></div>
  </div>

  <form method="POST" action="{{ route('offertes.store') }}" x-data="offerteBuilder()">
    @csrf
    @if($template)
      <input type="hidden" name="template_id" value="{{ $template->id }}">
    @endif

    <div style="display:grid;grid-template-columns:1fr 340px;gap:20px;align-items:start">

      <!-- Links: regels -->
      <div style="display:flex;flex-direction:column;gap:16px">

        @if($template)
        <div class="card" style="border-left:3px solid var(--green-400)">
          <div class="card-body" style="padding:12px 16px;display:flex;align-items:center;gap:10px">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--green-400)" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="21" x2="9" y2="9"/></svg>
            <span style="font-size:.85rem;color:#444">Template: <strong>{{ $template->naam }}</strong> — secties en prijsregels zijn voorgeladen.</span>
          </div>
        </div>
        @endif

        <!-- Klant + inleiding -->
        <div class="card">
          <div class="card-header"><span class="card-title">Klant & inleiding</span></div>
          <div class="card-body">
            <div class="form-grid-2">
              <div class="form-group" style="grid-column:1/-1">
                <label class="form-label">Klant *</label>
                <select class="form-select" name="klant_id" required>
                  <option value="">Selecteer klant</option>
                  @foreach($klanten as $klant)
                    <option value="{{ $klant->id }}" {{ (old('klant_id') == $klant->id || request('klant_id') == $klant->id) ? 'selected' : '' }}>
                      {{ $klant->naam }} — {{ $klant->email }}
                    </option>
                  @endforeach
                </select>
                @error('klant_id')<div class="form-error">{{ $message }}</div>@enderror
              </div>
              <div class="form-group" style="grid-column:1/-1">
                <label class="form-label">Inleiding / begeleidende tekst</label>
                <textarea class="form-textarea" name="inleiding" rows="4" placeholder="Beste [naam], Hartelijk dank voor uw interesse...">{{ old('inleiding') }}</textarea>
              </div>
            </div>
          </div>
        </div>

        <!-- Offerte regels -->
        <div class="card">
          <div class="card-header">
            <span class="card-title">Producten & diensten</span>
            <div style="display:flex;gap:8px">
              <!-- Product toevoegen vanuit catalogus -->
              <div style="position:relative" x-data="{ open: false }">
                <button type="button" class="btn btn-secondary btn-sm" @click="open = !open">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z"/>
                  </svg>
                  Uit catalogus
                </button>
                <div x-show="open" @click.outside="open = false"
                     style="position:absolute;top:100%;right:0;margin-top:4px;background:#fff;border:1px solid #e5e7eb;border-radius:10px;box-shadow:0 8px 24px rgba(0,0,0,.12);z-index:50;min-width:280px;max-height:320px;overflow-y:auto">
                  @foreach($producten->groupBy('categorie') as $cat => $items)
                    <div style="padding:8px 12px 4px;font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#aaa">{{ ucfirst($cat) }}</div>
                    @foreach($items as $product)
                      <div @click="addProduct({{ $product->id }}, '{{ addslashes($product->naam) }}', '{{ addslashes($product->beschrijving ?? '') }}', {{ $product->prijs }}); open = false"
                           style="padding:9px 12px;cursor:pointer;font-size:.875rem;display:flex;justify-content:space-between;align-items:center;transition:background .1s"
                           onmouseover="this.style.background='#f9f9f9'" onmouseout="this.style.background=''">
                        <span>{{ $product->naam }}</span>
                        <span style="color:#aaa;font-size:.8rem">€ {{ number_format($product->prijs, 2, ',', '.') }}</span>
                      </div>
                    @endforeach
                  @endforeach
                </div>
              </div>
              <button type="button" class="btn btn-primary btn-sm" @click="addLege()">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Lege regel
              </button>
            </div>
          </div>

          <!-- Tabel met regels -->
          <div class="table-wrap">
            <table>
              <thead>
                <tr>
                  <th style="width:38%">Omschrijving</th>
                  <th style="width:22%">Subtekst</th>
                  <th style="width:8%">Aantal</th>
                  <th style="width:14%">Prijs (€)</th>
                  <th style="width:12%">Totaal</th>
                  <th style="width:6%"></th>
                </tr>
              </thead>
              <tbody>
                <template x-for="(regel, i) in regels" :key="regel.key">
                  <tr>
                    <td>
                      <input type="hidden" :name="`regels[${i}][product_id]`" :value="regel.product_id">
                      <input class="form-input" style="font-size:.85rem;padding:7px 10px" type="text"
                             :name="`regels[${i}][naam]`" x-model="regel.naam" required placeholder="Naam">
                    </td>
                    <td>
                      <input class="form-input" style="font-size:.82rem;padding:7px 10px;color:#888" type="text"
                             :name="`regels[${i}][beschrijving]`" x-model="regel.beschrijving" placeholder="Optionele subtekst">
                    </td>
                    <td>
                      <input class="form-input" style="font-size:.85rem;padding:7px 10px;text-align:center" type="number"
                             :name="`regels[${i}][aantal]`" x-model.number="regel.aantal" min="1" required @input="bereken()">
                    </td>
                    <td>
                      <input class="form-input" style="font-size:.85rem;padding:7px 10px" type="number"
                             :name="`regels[${i}][eenheidsprijs]`" x-model.number="regel.eenheidsprijs" step="0.01" min="0" required @input="bereken()">
                    </td>
                    <td style="font-weight:500;color:#1a1a1a;font-size:.875rem" x-text="'€ ' + (regel.aantal * regel.eenheidsprijs).toFixed(2).replace('.',',')"></td>
                    <td>
                      <button type="button" @click="remove(i)"
                              style="background:none;border:none;cursor:pointer;color:#ccc;padding:4px;transition:color .15s"
                              onmouseover="this.style.color='#dc2626'" onmouseout="this.style.color='#ccc'">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                          <polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4h6v2"/>
                        </svg>
                      </button>
                    </td>
                  </tr>
                </template>
              </tbody>
            </table>

            <div x-show="regels.length === 0" class="empty-state" style="padding:32px">
              <p>Nog geen regels. Voeg een product toe uit de catalogus of voeg een lege regel toe.</p>
            </div>
          </div>

          <!-- Totalen -->
          <div style="padding:16px 20px;border-top:1px solid #f0f0f0;display:flex;justify-content:flex-end">
            <div style="display:flex;flex-direction:column;gap:6px;min-width:220px">
              <div style="display:flex;justify-content:space-between;font-size:.875rem;color:#666">
                <span>Subtotaal</span>
                <span x-text="'€ ' + subtotaal.toFixed(2).replace('.',',')" ></span>
              </div>
              <div style="display:flex;justify-content:space-between;font-size:.875rem;color:#666">
                <span>BTW (21%)</span>
                <span x-text="'€ ' + btw.toFixed(2).replace('.',',')" ></span>
              </div>
              <div style="display:flex;justify-content:space-between;font-size:1rem;font-weight:700;color:var(--green-800);padding-top:8px;border-top:2px solid #e8e8e8;margin-top:4px">
                <span>Totaal incl. BTW</span>
                <span x-text="'€ ' + totaal.toFixed(2).replace('.',',')" ></span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Rechts: instellingen + verzenden -->
      <div style="display:flex;flex-direction:column;gap:16px">
        <div class="card">
          <div class="card-header"><span class="card-title">Instellingen</span></div>
          <div class="card-body">
            <div class="form-group">
              <label class="form-label">Geldig tot</label>
              <input class="form-input" type="date" name="geldig_tot" value="{{ old('geldig_tot', now()->addDays(30)->format('Y-m-d')) }}">
            </div>
          </div>
        </div>

        <div class="card">
          <div class="card-body">
            <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:11px">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 2L11 13"/><path d="M22 2L15 22l-4-9-9-4 20-7z"/></svg>
              Offerte aanmaken
            </button>
            <p style="font-size:.75rem;color:#aaa;text-align:center;margin-top:8px">Je kunt de offerte daarna vanuit het overzicht versturen.</p>
          </div>
        </div>
      </div>
    </div>
  </form>

  <script>
    function offerteBuilder() {
      const initial = @json($initialRegels);
      return {
        regels: initial,
        subtotaal: 0,
        btw: 0,
        totaal: 0,
        _key: initial.length,

        init() { this.bereken(); },

        addProduct(id, naam, beschrijving, prijs) {
          this.regels.push({ key: this._key++, product_id: id, naam, beschrijving, aantal: 1, eenheidsprijs: prijs });
          this.bereken();
        },

        addLege() {
          this.regels.push({ key: this._key++, product_id: null, naam: '', beschrijving: '', aantal: 1, eenheidsprijs: 0 });
        },

        remove(i) {
          this.regels.splice(i, 1);
          this.bereken();
        },

        bereken() {
          this.subtotaal = this.regels.reduce((s, r) => s + (r.aantal * r.eenheidsprijs), 0);
          this.btw = this.subtotaal * 0.21;
          this.totaal = this.subtotaal * 1.21;
        }
      }
    }
  </script>
</x-layouts.crm>
