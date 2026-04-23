<x-layouts.crm title="Template bewerken">
  <x-slot:actions>
    <form method="POST" action="{{ route('offerte-templates.destroy', $offerteTemplate) }}" onsubmit="return confirm('Template verwijderen?')">
      @csrf @method('DELETE')
      <button type="submit" class="btn btn-danger btn-sm">Verwijderen</button>
    </form>
    <a href="{{ route('offerte-templates.index') }}" class="btn btn-secondary">Annuleren</a>
  </x-slot:actions>

  <div class="page-header">
    <div><h1>{{ $offerteTemplate->naam }}</h1><p>Template bewerken</p></div>
  </div>

  <form method="POST" action="{{ route('offerte-templates.update', $offerteTemplate) }}"
        x-data="templateBuilder({{ json_encode($offerteTemplate->secties) }}, {{ json_encode($offerteTemplate->regels) }})">
    @csrf @method('PUT')

    <div style="display:grid;grid-template-columns:1fr 300px;gap:20px;align-items:start">

      <div style="display:flex;flex-direction:column;gap:16px">

        <div class="card">
          <div class="card-header"><span class="card-title">Template info</span></div>
          <div class="card-body">
            <div class="form-grid-2">
              <div class="form-group">
                <label class="form-label">Naam *</label>
                <input class="form-input" type="text" name="naam" value="{{ old('naam', $offerteTemplate->naam) }}" required>
              </div>
              <div class="form-group">
                <label class="form-label">Categorie</label>
                <select class="form-select" name="categorie">
                  <option value="">Geen categorie</option>
                  @foreach(['laadpaal','warmtepomp','thuisbatterij','overig'] as $cat)
                    <option value="{{ $cat }}" {{ old('categorie', $offerteTemplate->categorie) === $cat ? 'selected' : '' }}>{{ ucfirst($cat) }}</option>
                  @endforeach
                </select>
              </div>
              <div class="form-group" style="grid-column:1/-1">
                <label class="form-label">Beschrijving</label>
                <input class="form-input" type="text" name="beschrijving" value="{{ old('beschrijving', $offerteTemplate->beschrijving) }}">
              </div>
            </div>
          </div>
        </div>

        <!-- Secties (zelfde builder als create) -->
        <div class="card">
          <div class="card-header">
            <span class="card-title">Secties</span>
            <div x-data="{ open: false }" style="position:relative">
              <button type="button" class="btn btn-primary btn-sm" @click="open = !open">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="13" height="13"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Sectie toevoegen
              </button>
              <div x-show="open" @click.outside="open=false"
                   style="position:absolute;top:100%;right:0;margin-top:4px;background:#fff;border:1px solid #e5e7eb;border-radius:10px;box-shadow:0 8px 24px rgba(0,0,0,.12);z-index:50;min-width:200px">
                @foreach([
                  ['voorblad','Voorblad'],['introductie','Introductie'],['prijzen','Prijzen'],
                  ['product','Product info'],['werkwijze','Werkwijze'],['acceptatie','Acceptatie'],['tekst','Vrije tekst']
                ] as [$type, $label])
                <div @click="addSectie('{{ $type }}', '{{ $label }}'); open=false"
                     style="padding:10px 14px;cursor:pointer;font-size:.875rem;transition:background .1s"
                     onmouseover="this.style.background='#f9f9f9'" onmouseout="this.style.background=''">{{ $label }}</div>
                @endforeach
              </div>
            </div>
          </div>

          <div style="padding:16px;display:flex;flex-direction:column;gap:10px">
            <template x-for="(sectie, i) in secties" :key="sectie.key">
              <div style="border:1.5px solid #e8e8e8;border-radius:10px;overflow:hidden">
                <div style="padding:12px 16px;background:#fafafa;display:flex;align-items:center;gap:10px;cursor:pointer" @click="sectie.open = !sectie.open">
                  <span style="font-size:.7rem;background:var(--green-100);color:var(--green-600);padding:2px 8px;border-radius:20px;font-weight:600;text-transform:uppercase" x-text="sectie.type"></span>
                  <input :name="`secties[${i}][type]`" type="hidden" :value="sectie.type">
                  <input class="form-input" style="flex:1;font-size:.875rem;padding:5px 10px;font-weight:500" :name="`secties[${i}][titel]`" x-model="sectie.titel" @click.stop>
                  <div style="display:flex;gap:4px;flex-shrink:0">
                    <button type="button" @click.stop="moveUp(i)" :disabled="i===0" style="background:none;border:none;cursor:pointer;color:#ccc;padding:3px" :style="i===0?'opacity:.3':''">
                      <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="18 15 12 9 6 15"/></svg>
                    </button>
                    <button type="button" @click.stop="moveDown(i)" :disabled="i===secties.length-1" style="background:none;border:none;cursor:pointer;color:#ccc;padding:3px" :style="i===secties.length-1?'opacity:.3':''">
                      <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
                    </button>
                    <button type="button" @click.stop="removeSectie(i)" style="background:none;border:none;cursor:pointer;color:#ccc;padding:3px;transition:color .15s" onmouseover="this.style.color='#dc2626'" onmouseout="this.style.color='#ccc'">
                      <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    </button>
                  </div>
                </div>
                <div x-show="sectie.open" style="padding:16px;border-top:1px solid #f0f0f0">
                  <template x-if="['introductie','acceptatie','tekst'].includes(sectie.type)">
                    <textarea class="form-textarea" rows="5" :name="`secties[${i}][inhoud][tekst]`" x-model="sectie.inhoud.tekst" placeholder="Tekst..."></textarea>
                  </template>
                  <template x-if="sectie.type === 'product'">
                    <div>
                      <div class="form-group">
                        <label class="form-label">Beschrijving</label>
                        <textarea class="form-textarea" rows="4" :name="`secties[${i}][inhoud][beschrijving]`" x-model="sectie.inhoud.beschrijving"></textarea>
                      </div>
                      <label class="form-label">Specificaties</label>
                      <div style="display:flex;flex-direction:column;gap:6px;margin-bottom:8px">
                        <template x-for="(spec, si) in sectie.inhoud.specs" :key="si">
                          <div style="display:flex;gap:8px">
                            <input class="form-input" style="flex:1;font-size:.85rem;padding:7px 10px" :name="`secties[${i}][inhoud][specs][${si}][label]`" x-model="spec.label" placeholder="Label">
                            <input class="form-input" style="flex:1;font-size:.85rem;padding:7px 10px" :name="`secties[${i}][inhoud][specs][${si}][waarde]`" x-model="spec.waarde" placeholder="Waarde">
                            <button type="button" @click="sectie.inhoud.specs.splice(si,1)" style="background:none;border:none;cursor:pointer;color:#ccc;transition:color .15s" onmouseover="this.style.color='#dc2626'" onmouseout="this.style.color='#ccc'">
                              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                            </button>
                          </div>
                        </template>
                      </div>
                      <button type="button" @click="sectie.inhoud.specs.push({label:'',waarde:''})" class="btn btn-secondary btn-sm">+ Spec</button>
                    </div>
                  </template>
                  <template x-if="sectie.type === 'werkwijze'">
                    <div>
                      <div style="display:flex;flex-direction:column;gap:10px;margin-bottom:8px">
                        <template x-for="(stap, si) in sectie.inhoud.stappen" :key="si">
                          <div style="border:1px solid #f0f0f0;border-radius:8px;padding:12px">
                            <div style="display:flex;gap:8px;margin-bottom:8px">
                              <div style="width:24px;height:24px;border-radius:50%;background:var(--green-400);color:#fff;font-size:.72rem;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0" x-text="si+1"></div>
                              <input class="form-input" style="flex:1;font-size:.875rem;padding:6px 10px" :name="`secties[${i}][inhoud][stappen][${si}][titel]`" x-model="stap.titel" placeholder="Staptitel">
                              <button type="button" @click="sectie.inhoud.stappen.splice(si,1)" style="background:none;border:none;cursor:pointer;color:#ccc;transition:color .15s" onmouseover="this.style.color='#dc2626'" onmouseout="this.style.color='#ccc'">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                              </button>
                            </div>
                            <textarea class="form-textarea" style="min-height:60px;font-size:.85rem" :name="`secties[${i}][inhoud][stappen][${si}][beschrijving]`" x-model="stap.beschrijving" placeholder="Beschrijving"></textarea>
                          </div>
                        </template>
                      </div>
                      <button type="button" @click="sectie.inhoud.stappen.push({titel:'',beschrijving:''})" class="btn btn-secondary btn-sm">+ Stap</button>
                    </div>
                  </template>
                  <template x-if="['voorblad','prijzen'].includes(sectie.type)">
                    <p style="font-size:.82rem;color:#aaa">
                      <span x-show="sectie.type==='voorblad'">Toont het voorblad met klantgegevens en offerte-details.</span>
                      <span x-show="sectie.type==='prijzen'">Toont automatisch de prijstabel op basis van de productregels.</span>
                    </p>
                  </template>
                </div>
              </div>
            </template>
          </div>
        </div>

        <!-- Regels -->
        <div class="card">
          <div class="card-header">
            <span class="card-title">Standaard prijsregels</span>
            <div style="display:flex;gap:8px" x-data="{ open: false }">
              <div style="position:relative">
                <button type="button" class="btn btn-secondary btn-sm" @click="open=!open">Uit catalogus</button>
                <div x-show="open" @click.outside="open=false" style="position:absolute;top:100%;right:0;margin-top:4px;background:#fff;border:1px solid #e5e7eb;border-radius:10px;box-shadow:0 8px 24px rgba(0,0,0,.12);z-index:50;min-width:260px;max-height:280px;overflow-y:auto">
                  @foreach($producten->groupBy('categorie') as $cat => $items)
                    <div style="padding:7px 12px 3px;font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#aaa">{{ ucfirst($cat) }}</div>
                    @foreach($items as $product)
                    <div @click="addRegel({{ $product->id }},'{{ addslashes($product->naam) }}','{{ addslashes($product->beschrijving ?? '') }}',{{ $product->prijs }});open=false"
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
              <thead><tr><th>Naam</th><th>Subtekst</th><th>Aantal</th><th>Prijs (€)</th><th></th></tr></thead>
              <tbody>
                <template x-for="(r, ri) in regels" :key="r.key">
                  <tr>
                    <td><input type="hidden" :name="`regels[${ri}][product_id]`" :value="r.product_id"><input class="form-input" style="font-size:.85rem;padding:6px 10px" :name="`regels[${ri}][naam]`" x-model="r.naam" placeholder="Naam"></td>
                    <td><input class="form-input" style="font-size:.82rem;padding:6px 10px;color:#888" :name="`regels[${ri}][beschrijving]`" x-model="r.beschrijving" placeholder="Subtekst"></td>
                    <td><input class="form-input" style="font-size:.85rem;padding:6px 10px;text-align:center;width:70px" type="number" :name="`regels[${ri}][aantal]`" x-model="r.aantal" min="1"></td>
                    <td><input class="form-input" style="font-size:.85rem;padding:6px 10px" type="number" :name="`regels[${ri}][eenheidsprijs]`" x-model="r.prijs" step="0.01" min="0"></td>
                    <td><button type="button" @click="regels.splice(ri,1)" style="background:none;border:none;cursor:pointer;color:#ccc;transition:color .15s" onmouseover="this.style.color='#dc2626'" onmouseout="this.style.color='#ccc'"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/></svg></button></td>
                  </tr>
                </template>
              </tbody>
            </table>
            <div x-show="regels.length===0" class="empty-state" style="padding:28px"><p>Geen standaard regels.</p></div>
          </div>
        </div>

      </div>

      <div class="card" style="position:sticky;top:76px">
        <div class="card-body">
          <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:11px">Opslaan</button>
          <a href="{{ route('offertes.create', ['template_id' => $offerteTemplate->id]) }}"
             class="btn btn-secondary" style="width:100%;justify-content:center;margin-top:8px">
            Gebruik template →
          </a>
        </div>
      </div>

    </div>
  </form>

  <script>
    function templateBuilder(bestaandeSecties, bestaandeRegels) {
      let key = 0;
      return {
        secties: (bestaandeSecties || []).map(s => ({
          key: key++, type: s.type, titel: s.titel,
          inhoud: s.inhoud ?? {}, open: false
        })),
        regels: (bestaandeRegels || []).map(r => ({
          key: key++, product_id: r.product_id, naam: r.naam,
          beschrijving: r.beschrijving ?? '', aantal: r.aantal, prijs: r.eenheidsprijs
        })),
        _key: key,

        addSectie(type, titel) {
          const d = { introductie:{tekst:''}, acceptatie:{tekst:'Ben je akkoord met deze offerte?'}, tekst:{tekst:''}, product:{beschrijving:'',specs:[]}, werkwijze:{stappen:[]}, voorblad:{}, prijzen:{} };
          this.secties.push({ key: this._key++, type, titel, inhoud: d[type]??{}, open: true });
        },
        removeSectie(i) { this.secties.splice(i,1); },
        moveUp(i) { if(i>0)[this.secties[i-1],this.secties[i]]=[this.secties[i],this.secties[i-1]]; },
        moveDown(i) { if(i<this.secties.length-1)[this.secties[i],this.secties[i+1]]=[this.secties[i+1],this.secties[i]]; },
        addRegel(id, naam, beschrijving, prijs) { this.regels.push({key:this._key++,product_id:id,naam,beschrijving,aantal:1,prijs}); },
        addLege() { this.regels.push({key:this._key++,product_id:null,naam:'',beschrijving:'',aantal:1,prijs:0}); }
      }
    }
  </script>
</x-layouts.crm>
