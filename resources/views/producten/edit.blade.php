<x-layouts.crm title="Product bewerken">
  <x-slot:actions>
    <form method="POST" action="{{ route('producten.destroy', $product) }}" onsubmit="return confirm('Product verwijderen?')">
      @csrf @method('DELETE')
      <button type="submit" class="btn btn-danger btn-sm">Verwijderen</button>
    </form>
  </x-slot:actions>

  <div class="page-header">
    <div><h1>{{ $product->naam }}</h1><p>Product bewerken</p></div>
  </div>

  @php
    $initialConditions = old('generator_conditions_json', json_encode($product->generator_conditions ?? []));
    $initialValueRules = old('generator_value_rules_json', json_encode($product->generator_value_rules ?? [
      'aantal' => ['enabled' => false, 'field' => null, 'op' => '', 'delta' => 0],
      'prijs'  => ['enabled' => false, 'field' => null, 'op' => '', 'delta' => 0],
    ]));
    $apiFieldsJs = ($apiFields ?? collect())->map(fn($f) => [
      'key' => $f->key,
      'label' => $f->label ?: $f->key,
      'type' => $f->type,
      'allowed_values' => $f->allowed_values ?? [],
    ])->values();
  @endphp

  <form method="POST" action="{{ route('producten.update', $product) }}"
        x-data="productEditor(
          @js(old('tab','details')),
          @js(old('generator_mode', $product->generator_mode ?? 'manual')),
          @js($initialConditions),
          @js($initialValueRules),
          @js($apiFieldsJs)
        )"
        x-init="init()">
    @csrf @method('PUT')

    <input type="hidden" name="generator_conditions_json" :value="conditionsJson">
    <input type="hidden" name="generator_value_rules_json" :value="valueRulesJson">
    <input type="hidden" name="tab" :value="activeTab">

    <div class="card" style="max-width:920px">
      <div class="card-body">

        <style>
          .tabs-bar { display:flex; gap:0; border-bottom:1px solid #e5e7eb; margin-bottom:16px; }
          .tab-btn {
            appearance:none; border:none; background:transparent; cursor:pointer;
            padding:10px 14px; font-family:var(--font-body); font-weight:700; font-size:.9rem;
            color:#64748b; border-bottom:3px solid transparent; margin-bottom:-1px;
            border-top-left-radius:10px; border-top-right-radius:10px;
          }
          .tab-btn:hover { color:#334155; background:rgba(15, 74, 42, .03); }
          .tab-btn.is-active { color:var(--green-800); border-bottom-color:var(--green-400); background:rgba(45,189,110,.08); }
        </style>

        <div class="tabs-bar" role="tablist" aria-label="Product tabs">
          <button type="button" class="tab-btn" :class="activeTab==='details' ? 'is-active' : ''" @click="activeTab='details'" role="tab" :aria-selected="activeTab==='details'">Details</button>
          <button type="button" class="tab-btn" :class="activeTab==='generator' ? 'is-active' : ''" @click="activeTab='generator'" role="tab" :aria-selected="activeTab==='generator'">Offerte generator</button>
        </div>

        <!-- TAB: details -->
        <div x-show="activeTab==='details'">
          <div class="form-grid-2">
            <div class="form-group" style="grid-column:1/-1">
              <label class="form-label">Naam *</label>
              <input class="form-input" type="text" name="naam" value="{{ old('naam', $product->naam) }}" required>
              @error('naam')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
              <label class="form-label">Categorie *</label>
              <select class="form-select" name="categorie" required>
                @foreach(['laadpaal','installatie','thuisbatterij','warmtepomp','accessoire','overig'] as $cat)
                  <option value="{{ $cat }}" {{ old('categorie', $product->categorie) === $cat ? 'selected' : '' }}>{{ ucfirst($cat) }}</option>
                @endforeach
              </select>
            </div>

            <div class="form-group">
              <label class="form-label">Merk</label>
              <input class="form-input" type="text" name="merk" value="{{ old('merk', $product->merk) }}">
            </div>

            <div class="form-group">
              <label class="form-label">Prijs excl. BTW (€) *</label>
              <input class="form-input" type="number" name="prijs" value="{{ old('prijs', $product->prijs) }}" step="0.01" min="0" required>
            </div>

            <div class="form-group">
              <label class="form-label">Volgorde</label>
              <input class="form-input" type="number" name="order" value="{{ old('order', $product->order ?? 0) }}" min="0">
            </div>

            <div class="form-group" style="display:flex;align-items:center;gap:10px;padding-top:24px">
              <input type="checkbox" name="actief" id="actief" value="1" {{ old('actief', $product->actief) ? 'checked' : '' }}
                     style="width:16px;height:16px;accent-color:var(--green-400)">
              <label for="actief" class="form-label" style="margin:0;cursor:pointer">Actief</label>
            </div>

            <div class="form-group" style="grid-column:1/-1">
              <label class="form-label">Beschrijving</label>
              <textarea class="form-textarea" name="beschrijving">{{ old('beschrijving', $product->beschrijving) }}</textarea>
            </div>
          </div>
        </div>

        <!-- TAB: generator -->
        <div x-show="activeTab==='generator'" style="display:none">
          <div class="form-group" style="max-width:420px">
            <label class="form-label">Automatisch toevoegen</label>
            <select class="form-select" name="generator_mode" x-model="generatorMode">
              <option value="manual">Nooit</option>
              <option value="always">Altijd toevoegen</option>
              <option value="conditional">Conditioneel</option>
            </select>
            <div style="font-size:.8rem;color:#888;margin-top:6px">
              Nooit = nooit automatisch. Altijd = altijd. Conditioneel = op basis van details uit de API.
            </div>
          </div>

          <div style="height:1px;background:#e5e7eb;margin:16px 0 18px"></div>

          <div x-show="generatorMode==='conditional'" style="margin-top:18px">
            <div style="font-weight:700;margin-bottom:8px">Condities</div>
            <div style="font-size:.82rem;color:#666;margin-bottom:10px">
              Tussen blokken is het OR. Binnen een blok is het AND.
            </div>

            <template x-for="(block, bi) in conditionBlocks" :key="bi">
              <div class="card" style="margin-bottom:12px;background:#fbfcfd">
                <div class="card-body" style="padding:12px">
                  <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px">
                    <div style="font-weight:700">Blok <span x-text="bi+1"></span></div>
                    <button type="button" class="btn btn-danger btn-sm" @click="removeBlock(bi)">Verwijder blok</button>
                  </div>

                  <table style="width:100%;border-collapse:collapse">
                    <thead>
                      <tr style="text-align:left;color:#777;font-size:.78rem">
                        <th style="padding:6px 4px">Veld</th>
                        <th style="padding:6px 4px">Conditie</th>
                        <th style="padding:6px 4px">Waarde</th>
                        <th style="padding:6px 4px"></th>
                      </tr>
                    </thead>
                    <tbody>
                      <template x-for="(rule, ri) in block.and" :key="ri">
                        <tr>
                          <td style="padding:6px 4px">
                            <select class="form-select" x-model="rule.field"
                                    x-effect="
                                      if (debug) {
                                        $nextTick(() => {
                                          if ($el.value !== rule.field) {
                                            $el.value = rule.field || '';
                                            console.log('[ProductEditor] sync rule.field select', { ruleField: rule.field, elValue: $el.value });
                                          }
                                        });
                                      }
                                    ">
                              <option value="">— kies veld —</option>
                              <template x-if="rule.field && !allApiFields.some(f => f.key === rule.field)">
                                <option :value="rule.field" x-text="rule.field + ' (huidige veld)'" ></option>
                              </template>
                              <template x-for="f in allApiFields" :key="f.key">
                                <option :value="f.key" x-text="f.label"></option>
                              </template>
                            </select>
                          </td>
                          <td style="padding:6px 4px">
                            <select class="form-select" x-model="rule.op"
                                    x-effect="
                                      const t = fieldType(rule.field);
                                      if (!['integer','decimal'].includes(t) && (rule.op === 'gt' || rule.op === 'lt')) rule.op = 'eq';
                                    ">
                              <option value="present">is aanwezig</option>
                              <option value="eq">is gelijk aan</option>
                              <option value="neq">is niet gelijk aan</option>
                              <template x-if="['integer','decimal'].includes(fieldType(rule.field))">
                                <option value="gt">groter dan</option>
                              </template>
                              <template x-if="['integer','decimal'].includes(fieldType(rule.field))">
                                <option value="lt">kleiner dan</option>
                              </template>
                            </select>
                          </td>
                          <td style="padding:6px 4px">
                            <div x-show="rule.op !== 'present'">
                              <!-- text -->
                              <template x-if="fieldType(rule.field) === 'text'">
                                <input class="form-input" type="text" x-model="rule.value" placeholder="waarde">
                              </template>

                              <!-- integer -->
                              <template x-if="fieldType(rule.field) === 'integer'">
                                <input class="form-input" type="number" step="1" inputmode="numeric" x-model.number="rule.value" placeholder="0">
                              </template>

                              <!-- decimal -->
                              <template x-if="fieldType(rule.field) === 'decimal'">
                                <input class="form-input" type="number" step="0.01" inputmode="decimal" x-model.number="rule.value" placeholder="0.00">
                              </template>

                              <!-- list -->
                              <template x-if="fieldType(rule.field) === 'list'">
                                <select class="form-select" x-model="rule.value"
                                        x-effect="
                                          if (debug) {
                                            $nextTick(() => {
                                              const want = String(rule.value ?? '');
                                              if ($el.value !== want) {
                                                $el.value = want;
                                                console.log('[ProductEditor] sync rule.value list select', { want, elValue: $el.value });
                                              }
                                            });
                                          }
                                        ">
                                  <option value="">— kies waarde —</option>
                                  <template x-if="rule.value && !fieldAllowedValues(rule.field).includes(String(rule.value))">
                                    <option :value="String(rule.value)" x-text="String(rule.value) + ' (huidige waarde)'" ></option>
                                  </template>
                                  <template x-for="v in fieldAllowedValues(rule.field)" :key="v">
                                    <option :value="v" x-text="v"></option>
                                  </template>
                                </select>
                              </template>

                              <!-- unknown field -->
                              <template x-if="!fieldType(rule.field)">
                                <input class="form-input" type="text" x-model="rule.value" placeholder="waarde">
                              </template>
                            </div>
                            <div x-show="rule.op === 'present'" style="color:#94a3b8;font-size:.82rem;padding:8px 0">
                              —
                            </div>
                          </td>
                          <td style="padding:6px 4px;text-align:right">
                            <button type="button" class="btn btn-secondary btn-sm" @click="removeRule(bi, ri)">✕</button>
                          </td>
                        </tr>
                      </template>
                    </tbody>
                  </table>
                  <div style="margin-top:10px">
                    <button type="button" class="btn btn-secondary btn-sm" @click="addRule(bi)">+ Conditie</button>
                  </div>
                </div>
              </div>
            </template>

            <button type="button" class="btn btn-secondary btn-sm" @click="addBlock()">+ Blok toevoegen (OR)</button>
          </div>

          <div x-show="generatorMode==='always' || generatorMode==='conditional'" style="margin-top:20px">
            <div style="font-weight:700;margin-bottom:8px">Waarde gebruiken</div>
            <div style="font-size:.82rem;color:#666;margin-bottom:12px">
              Koppel numerieke API velden aan aantal/prijs, optioneel met +/− delta.
            </div>

            <template x-for="target in ['aantal','prijs']" :key="target">
              <div class="card" style="margin-bottom:12px">
                <div class="card-body" style="padding:12px">
                  <div style="display:flex;align-items:center;gap:10px">
                    <input type="checkbox" :id="'en-'+target" x-model="valueRules[target].enabled" style="width:16px;height:16px;accent-color:var(--green-400)">
                    <label :for="'en-'+target" class="form-label" style="margin:0;cursor:pointer" x-text="target === 'aantal' ? 'Aantal' : 'Prijs'"></label>
                  </div>

                  <div style="display:grid;grid-template-columns:1fr 140px 120px;gap:10px;margin-top:10px" x-show="valueRules[target].enabled">
                    <div>
                      <label class="form-label" style="font-size:.78rem">Veld</label>
                      <select class="form-select" x-model="valueRules[target].field">
                        <option value="">— kies veld —</option>
                        <template x-for="f in numericApiFields" :key="f.key">
                          <option :value="f.key" x-text="f.label"></option>
                        </template>
                      </select>
                    </div>
                    <div>
                      <label class="form-label" style="font-size:.78rem">Extra optie</label>
                      <select class="form-select" x-model="valueRules[target].op">
                        <option value="">niets</option>
                        <option value="+">+</option>
                        <option value="-">-</option>
                      </select>
                    </div>
                    <div>
                      <label class="form-label" style="font-size:.78rem">Delta</label>
                      <input class="form-input" type="number" step="0.01" x-model.number="valueRules[target].delta" :disabled="!valueRules[target].op">
                    </div>
                  </div>
                </div>
              </div>
            </template>
          </div>
        </div>

        <div style="display:flex;gap:10px;margin-top:8px">
          <button type="submit" class="btn btn-primary">Opslaan</button>
          <a href="{{ route('producten.index') }}" class="btn btn-secondary">Annuleren</a>
        </div>

      </div>
    </div>
  </form>

  <script>
    function productEditor(initialTab, initialMode, initialConditionsJson, initialValueRulesJson, apiFields) {
      return {
        debug: true,
        activeTab: initialTab || 'details',
        generatorMode: initialMode || 'manual',
        conditionBlocks: [],
        valueRules: { aantal: {enabled:false,field:null,op:'',delta:0}, prijs: {enabled:false,field:null,op:'',delta:0} },
        allApiFields: apiFields || [],

        get conditionsJson() { return JSON.stringify(this.conditionBlocks); },
        get valueRulesJson() { return JSON.stringify(this.valueRules); },
        get numericApiFields() { return (this.allApiFields || []).filter(f => ['integer','decimal'].includes(f.type)); },
        fieldType(key) {
          return (this.allApiFields || []).find(f => f.key === key)?.type || '';
        },
        fieldAllowedValues(key) {
          return (this.allApiFields || []).find(f => f.key === key)?.allowed_values || [];
        },

        init() {
          if (this.debug) {
            console.group('[ProductEditor] init');
            console.log('apiFields', this.allApiFields);
            console.log('initialConditionsJson (type)', typeof initialConditionsJson);
            console.log('initialConditionsJson (raw)', initialConditionsJson);
            console.log('initialValueRulesJson (raw)', initialValueRulesJson);
          }
          try { this.conditionBlocks = JSON.parse(initialConditionsJson || '[]') || []; } catch { this.conditionBlocks = []; }
          try { this.valueRules = JSON.parse(initialValueRulesJson || '{}') || this.valueRules; } catch {}
          // Normalize legacy/alternative shapes:
          // - [{field,op,value}, ...]              -> wrap into one block
          // - [[{field,op,value}], [{...}], ...]   -> map arrays to blocks
          // - [{and:[...]}, ...]                   -> already ok
          if (!Array.isArray(this.conditionBlocks)) this.conditionBlocks = [];

          if (this.conditionBlocks.length > 0) {
            const first = this.conditionBlocks[0];
            if (first && typeof first === 'object' && !Array.isArray(first) && ('field' in first)) {
              this.conditionBlocks = [{ and: this.conditionBlocks }];
            } else if (Array.isArray(first)) {
              this.conditionBlocks = this.conditionBlocks.map(rules => ({ and: Array.isArray(rules) ? rules : [] }));
            }
          }

          this.conditionBlocks = this.conditionBlocks
            .map(b => {
              const and = Array.isArray(b?.and) ? b.and : (Array.isArray(b) ? b : []);
              return { and };
            })
            .filter(b => Array.isArray(b.and));

          if (this.conditionBlocks.length === 0) this.conditionBlocks = [{ and: [ { field:'', op:'present', value:'' } ] }];

          // Normalize rule values so selects show previously saved values.
          this.conditionBlocks.forEach((block) => {
            block.and = (block.and || []).map((r) => {
              const rule = r && typeof r === 'object' ? r : {};
              rule.field = (rule.field === null || typeof rule.field === 'undefined') ? '' : String(rule.field).trim();
              rule.op = rule.op ?? 'present';
              if (rule.op === 'present') {
                rule.value = '';
                return rule;
              }

              const t = this.fieldType(rule.field);
              if (t === 'list' || t === 'text') {
                rule.value = rule.value === null || typeof rule.value === 'undefined' ? '' : String(rule.value);
              }
              // integer/decimal: keep as-is; inputs use x-model.number
              return rule;
            });
          });

          // Normalize valueRules to keep selects in sync
          ['aantal', 'prijs'].forEach((t) => {
            const vr = this.valueRules[t] || {};
            vr.enabled = !!vr.enabled;
            vr.field = (vr.field === null || typeof vr.field === 'undefined') ? '' : String(vr.field).trim();
            vr.op = vr.op ?? '';
            vr.delta = typeof vr.delta === 'number' ? vr.delta : Number(vr.delta || 0);
            this.valueRules[t] = vr;
          });

          if (this.debug) {
            console.log('conditionBlocks (normalized)', JSON.parse(JSON.stringify(this.conditionBlocks)));
            const keys = (this.allApiFields || []).map(f => f.key);
            console.log('apiField keys', keys);
            this.conditionBlocks.forEach((b, bi) => {
              (b.and || []).forEach((r, ri) => {
                const field = r.field;
                const match = keys.includes(field);
                console.log(`rule[${bi}][${ri}] field=`, field, 'len=', String(field).length, 'match=', match);
                if (!match && field) {
                  const hex = Array.from(String(field)).map(ch => ch.charCodeAt(0).toString(16).padStart(2,'0')).join(' ');
                  console.warn('field does not match any apiField key. hex=', hex);
                }
              });
            });
            console.groupEnd();
          }
        },

        addBlock() { this.conditionBlocks.push({ and: [ { field:'', op:'present', value:'' } ] }); },
        removeBlock(i) { this.conditionBlocks.splice(i, 1); if (this.conditionBlocks.length === 0) this.addBlock(); },
        addRule(bi) { this.conditionBlocks[bi].and.push({ field:'', op:'present', value:'' }); },
        removeRule(bi, ri) { this.conditionBlocks[bi].and.splice(ri, 1); if (this.conditionBlocks[bi].and.length === 0) this.addRule(bi); },
      };
    }
  </script>
</x-layouts.crm>
