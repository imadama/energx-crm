<x-layouts.crm title="Nieuwe template">
  <x-slot:actions>
    <a href="{{ route('offerte-templates.index') }}" class="btn btn-secondary">Annuleren</a>
  </x-slot:actions>

  <div class="page-header">
    <div><h1>Nieuwe template</h1><p>Definieer secties, teksten en standaard prijsregels</p></div>
  </div>

  <form method="POST" action="{{ route('offerte-templates.store') }}" x-data="templateBuilder()">
    @csrf

    <div style="display:grid;grid-template-columns:1fr 300px;gap:20px;align-items:start">

      <!-- Links: secties -->
      <div style="display:flex;flex-direction:column;gap:16px">

        <!-- Basis info -->
        <div class="card">
          <div class="card-header"><span class="card-title">Template info</span></div>
          <div class="card-body">
            <div class="form-grid-2">
              <div class="form-group">
                <label class="form-label">Naam *</label>
                <input class="form-input" type="text" name="naam" required placeholder="bijv. Zaptec Go 2 standaard">
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
              <div class="form-group" style="grid-column:1/-1">
                <label class="form-label">Beschrijving</label>
                <input class="form-input" type="text" name="beschrijving" placeholder="Korte omschrijving van deze template">
              </div>
              <div class="form-group" style="grid-column:1/-1">
                <label class="form-label">Identifier (API)</label>
                <input class="form-input" type="text" name="identifier" value="{{ old('identifier') }}" placeholder="bijv. zaptec_go_standaard">
                @error('identifier')<div class="form-error">{{ $message }}</div>@enderror
                <div style="font-size:.78rem;color:#888;margin-top:6px">Gebruik letters/cijfers/underscore/dash. Moet uniek zijn.</div>
              </div>
            </div>
          </div>
        </div>

        <!-- Secties -->
        <div class="card">
          <div class="card-header">
            <span class="card-title">Secties</span>
            <div style="display:flex;gap:8px;flex-wrap:wrap" x-data="{ open: false }">
              <div style="position:relative">
                <button type="button" class="btn btn-primary btn-sm" @click="open = !open">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="13" height="13"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                  Sectie toevoegen
                </button>
                <div x-show="open" @click.outside="open=false"
                     style="position:absolute;top:100%;right:0;margin-top:4px;background:#fff;border:1px solid #e5e7eb;border-radius:10px;box-shadow:0 8px 24px rgba(0,0,0,.12);z-index:50;min-width:200px">
                  @foreach([
                    ['voorblad',    'Voorblad',    'M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z'],
                    ['introductie', 'Introductie', 'M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z'],
                    ['prijzen',     'Prijzen',     'M12 2v20M17 5H9.5a3.5 3.5 0 100 7h5a3.5 3.5 0 110 7H6'],
                    ['product',     'Product info','M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z'],
                    ['werkwijze',   'Werkwijze',   'M9 11l3 3L22 4 M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11'],
                    ['acceptatie',  'Acceptatie',  'M20 6L9 17l-5-5'],
                    ['tekst',       'Vrije tekst', 'M4 6h16M4 12h16M4 18h7'],
                  ] as [$type, $label, $icon])
                  <div @click="addSectie('{{ $type }}', '{{ $label }}'); open=false"
                       style="padding:10px 14px;cursor:pointer;font-size:.875rem;display:flex;align-items:center;gap:10px;transition:background .1s"
                       onmouseover="this.style.background='#f9f9f9'" onmouseout="this.style.background=''">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="{{ $icon }}"/></svg>
                    {{ $label }}
                  </div>
                  @endforeach
                </div>
              </div>
            </div>
          </div>

          <div style="padding:16px;display:flex;flex-direction:column;gap:10px">
            <template x-for="(sectie, i) in secties" :key="sectie.key">
              <div style="border:1.5px solid #e8e8e8;border-radius:10px;overflow:hidden">
                <!-- Sectie header -->
                <div style="padding:12px 16px;background:#fafafa;display:flex;align-items:center;gap:10px;cursor:pointer"
                     @click="sectie.open = !sectie.open">
                  <span style="font-size:.7rem;background:var(--green-100);color:var(--green-600);padding:2px 8px;border-radius:20px;font-weight:600;text-transform:uppercase"
                        x-text="sectie.type"></span>
                  <input :name="`secties[${i}][type]`" type="hidden" :value="sectie.type">
                  <input class="form-input" style="flex:1;font-size:.875rem;padding:5px 10px;font-weight:500"
                         :name="`secties[${i}][titel]`" x-model="sectie.titel" placeholder="Sectietitel"
                         @click.stop>
                  <div style="display:flex;gap:4px;flex-shrink:0">
                    <button type="button" @click.stop="moveUp(i)" :disabled="i===0"
                            style="background:none;border:none;cursor:pointer;color:#ccc;padding:3px" :style="i===0?'opacity:.3':''">
                      <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="18 15 12 9 6 15"/></svg>
                    </button>
                    <button type="button" @click.stop="moveDown(i)" :disabled="i===secties.length-1"
                            style="background:none;border:none;cursor:pointer;color:#ccc;padding:3px" :style="i===secties.length-1?'opacity:.3':''">
                      <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
                    </button>
                    <button type="button" @click.stop="removeSectie(i)"
                            style="background:none;border:none;cursor:pointer;color:#ccc;padding:3px;transition:color .15s"
                            onmouseover="this.style.color='#dc2626'" onmouseout="this.style.color='#ccc'">
                      <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    </button>
                  </div>
                </div>

                <!-- Sectie inhoud -->
                <div x-show="sectie.open" style="padding:16px;border-top:1px solid #f0f0f0">

                  <!-- INTRODUCTIE / ACCEPTATIE / TEKST -->
                  <template x-if="['introductie','acceptatie','tekst'].includes(sectie.type)">
                    <div>
                      <label class="form-label">Tekst</label>
                      <textarea class="form-textarea" rows="5"
                                :name="`secties[${i}][inhoud][tekst]`"
                                x-model="sectie.inhoud.tekst"
                                placeholder="Voer de tekst in. Gebruik [naam] voor de klantnaam."></textarea>
                    </div>
                  </template>

                  <!-- PRODUCT INFO -->
                  <template x-if="sectie.type === 'product'">
                    <div>
                      <div class="form-group">
                        <label class="form-label">Productbeschrijving</label>
                        <textarea class="form-textarea" rows="4"
                                  :name="`secties[${i}][inhoud][beschrijving]`"
                                  x-model="sectie.inhoud.beschrijving"
                                  placeholder="Beschrijving die naast de specs verschijnt in de offerte"></textarea>
                      </div>
                      <div class="form-group">
                        <label class="form-label" style="margin-bottom:10px">Specificaties</label>
                        <div style="display:flex;flex-direction:column;gap:6px">
                          <template x-for="(spec, si) in sectie.inhoud.specs" :key="si">
                            <div style="display:flex;gap:8px;align-items:center">
                              <input class="form-input" style="flex:1;font-size:.85rem;padding:7px 10px"
                                     :name="`secties[${i}][inhoud][specs][${si}][label]`"
                                     x-model="spec.label" placeholder="Label (bijv. Vermogen)">
                              <input class="form-input" style="flex:1;font-size:.85rem;padding:7px 10px"
                                     :name="`secties[${i}][inhoud][specs][${si}][waarde]`"
                                     x-model="spec.waarde" placeholder="Waarde (bijv. 11 kW)">
                              <button type="button" @click="sectie.inhoud.specs.splice(si,1)"
                                      style="background:none;border:none;cursor:pointer;color:#ccc;transition:color .15s;padding:4px"
                                      onmouseover="this.style.color='#dc2626'" onmouseout="this.style.color='#ccc'">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                              </button>
                            </div>
                          </template>
                        </div>
                        <button type="button" @click="sectie.inhoud.specs.push({label:'',waarde:''})"
                                class="btn btn-secondary btn-sm" style="margin-top:8px">
                          + Spec toevoegen
                        </button>
                      </div>
                    </div>
                  </template>

                  <!-- WERKWIJZE -->
                  <template x-if="sectie.type === 'werkwijze'">
                    <div>
                      <label class="form-label" style="margin-bottom:10px">Stappen</label>
                      <div style="display:flex;flex-direction:column;gap:10px">
                        <template x-for="(stap, si) in sectie.inhoud.stappen" :key="si">
                          <div style="border:1px solid #f0f0f0;border-radius:8px;padding:12px">
                            <div style="display:flex;align-items:center;gap:8px;margin-bottom:8px">
                              <div style="width:24px;height:24px;border-radius:50%;background:var(--green-400);color:#fff;font-size:.72rem;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0"
                                   x-text="si+1"></div>
                              <input class="form-input" style="flex:1;font-size:.875rem;padding:6px 10px"
                                     :name="`secties[${i}][inhoud][stappen][${si}][titel]`"
                                     x-model="stap.titel" placeholder="Staptitel">
                              <button type="button" @click="sectie.inhoud.stappen.splice(si,1)"
                                      style="background:none;border:none;cursor:pointer;color:#ccc;transition:color .15s;padding:4px"
                                      onmouseover="this.style.color='#dc2626'" onmouseout="this.style.color='#ccc'">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                              </button>
                            </div>
                            <textarea class="form-textarea" style="min-height:60px;font-size:.85rem"
                                      :name="`secties[${i}][inhoud][stappen][${si}][beschrijving]`"
                                      x-model="stap.beschrijving" placeholder="Beschrijving"></textarea>
                          </div>
                        </template>
                      </div>
                      <button type="button" @click="sectie.inhoud.stappen.push({titel:'',beschrijving:''})"
                              class="btn btn-secondary btn-sm" style="margin-top:8px">
                        + Stap toevoegen
                      </button>
                    </div>
                  </template>

                  <!-- VOORBLAD / PRIJZEN: geen extra input -->
                  <template x-if="['voorblad','prijzen'].includes(sectie.type)">
                    <div style="font-size:.82rem;color:#aaa;padding:4px 0">
                      <span x-show="sectie.type==='voorblad'">Toont het voorblad met klantgegevens, offerte-nummer en datum.</span>
                      <span x-show="sectie.type==='prijzen'">Toont automatisch de prijstabel op basis van de producten hieronder.</span>
                    </div>
                  </template>

                </div>
              </div>
            </template>

            <div x-show="secties.length===0" class="empty-state" style="padding:32px">
              <p>Voeg secties toe via de knop hierboven. Begin met een Voorblad.</p>
            </div>
          </div>
        </div>

        <!-- Standaard prijsregels -->
        <div class="card">
          <div class="card-header">
            <span class="card-title">Standaard prijsregels</span>
            <div style="display:flex;gap:8px" x-data="{ open: false }">
              <div style="position:relative">
                <button type="button" class="btn btn-secondary btn-sm" @click="open=!open">Uit catalogus</button>
                <div x-show="open" @click.outside="open=false"
                     style="position:absolute;top:100%;right:0;margin-top:4px;background:#fff;border:1px solid #e5e7eb;border-radius:10px;box-shadow:0 8px 24px rgba(0,0,0,.12);z-index:50;min-width:260px;max-height:280px;overflow-y:auto">
                  @foreach($producten->groupBy('categorie') as $cat => $items)
                    <div style="padding:7px 12px 3px;font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#aaa">{{ ucfirst($cat) }}</div>
                    @foreach($items as $product)
                    <div @click="addRegel({{ $product->id }}, '{{ addslashes($product->naam) }}', '{{ addslashes($product->beschrijving ?? '') }}', {{ $product->prijs }}); open=false"
                         style="padding:8px 12px;cursor:pointer;font-size:.875rem;display:flex;justify-content:space-between;transition:background .1s"
                         onmouseover="this.style.background='#f9f9f9'" onmouseout="this.style.background=''">
                      <span>{{ $product->naam }}</span>
                      <span style="color:#aaa;font-size:.8rem">€ {{ number_format($product->prijs, 2, ',', '.') }}</span>
                    </div>
                    @endforeach
                  @endforeach
                </div>
              </div>
              <button type="button" class="btn btn-primary btn-sm" @click="addLege()">+ Lege regel</button>
            </div>
          </div>
          <div class="table-wrap">
            <table>
              <thead><tr><th style="width:36%">Naam</th><th style="width:24%">Subtekst</th><th>Aantal</th><th>Prijs (€)</th><th></th></tr></thead>
              <tbody>
                <template x-for="(r, ri) in regels" :key="r.key">
                  <tr>
                    <td>
                      <input type="hidden" :name="`regels[${ri}][product_id]`" :value="r.product_id">
                      <input class="form-input" style="font-size:.85rem;padding:6px 10px" type="text" :name="`regels[${ri}][naam]`" x-model="r.naam" placeholder="Naam" required>
                    </td>
                    <td><input class="form-input" style="font-size:.82rem;padding:6px 10px;color:#888" type="text" :name="`regels[${ri}][beschrijving]`" x-model="r.beschrijving" placeholder="Subtekst"></td>
                    <td><input class="form-input" style="font-size:.85rem;padding:6px 10px;text-align:center;width:70px" type="number" :name="`regels[${ri}][aantal]`" x-model="r.aantal" min="1"></td>
                    <td><input class="form-input" style="font-size:.85rem;padding:6px 10px" type="number" :name="`regels[${ri}][eenheidsprijs]`" x-model="r.prijs" step="0.01" min="0"></td>
                    <td>
                      <button type="button" @click="regels.splice(ri,1)"
                              style="background:none;border:none;cursor:pointer;color:#ccc;transition:color .15s"
                              onmouseover="this.style.color='#dc2626'" onmouseout="this.style.color='#ccc'">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/></svg>
                      </button>
                    </td>
                  </tr>
                </template>
              </tbody>
            </table>
            <div x-show="regels.length===0" class="empty-state" style="padding:28px"><p>Nog geen standaard prijsregels.</p></div>
          </div>
        </div>
      </div>

      <!-- Rechts: opslaan -->
      <div class="card" style="position:sticky;top:76px">
        <div class="card-body">
          <p style="font-size:.82rem;color:#888;margin-bottom:16px;line-height:1.6">
            Na opslaan kun je deze template gebruiken bij het aanmaken van een nieuwe offerte. Alle secties en prijsregels worden automatisch overgenomen.
          </p>
          <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:11px">
            Template opslaan
          </button>
        </div>
      </div>

    </div>
  </form>

  <script>
    function templateBuilder() {
      return {
        secties: [],
        regels: [],
        _key: 0,

        addSectie(type, titel) {
          const defaults = {
            introductie: { tekst: '' },
            acceptatie:  { tekst: 'Ben je akkoord met deze offerte? Klik op de knop hieronder om digitaal te ondertekenen.' },
            tekst:       { tekst: '' },
            product:     { beschrijving: '', specs: [] },
            werkwijze:   { stappen: [] },
            voorblad:    {},
            prijzen:     {},
          };
          this.secties.push({ key: this._key++, type, titel, inhoud: defaults[type] ?? {}, open: true });
        },

        removeSectie(i) { this.secties.splice(i, 1); },

        moveUp(i) {
          if (i > 0) [this.secties[i-1], this.secties[i]] = [this.secties[i], this.secties[i-1]];
        },

        moveDown(i) {
          if (i < this.secties.length-1) [this.secties[i], this.secties[i+1]] = [this.secties[i+1], this.secties[i]];
        },

        addRegel(id, naam, beschrijving, prijs) {
          this.regels.push({ key: this._key++, product_id: id, naam, beschrijving, aantal: 1, prijs });
        },

        addLege() {
          this.regels.push({ key: this._key++, product_id: null, naam: '', beschrijving: '', aantal: 1, prijs: 0 });
        }
      }
    }
  </script>
</x-layouts.crm>
