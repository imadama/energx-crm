{{-- Prijstabel blok — editor mode --}}
<div class="blok-prijstabel"
     x-data="prijstabelEditor(regels, producten, regelStoreUrl, regelBaseUrl)"
     x-init="init()">

  {{-- Tabel header --}}
  <table class="pt-tabel">
    <thead>
      <tr>
        <th style="width:35%">Omschrijving</th>
        <th style="width:12%">Aantal</th>
        <th style="width:10%">Eenheid</th>
        <th style="width:13%">Prijs</th>
        <th style="width:8%">BTW</th>
        <th style="width:12%">Totaal</th>
        <th style="width:10%">Acties</th>
      </tr>
    </thead>
    <tbody :id="'pt-sortable-' + element.id">
      <template x-for="(regel, ri) in regelsLijst" :key="regel.id || ri">

        {{-- Tekstregel --}}
        <template x-if="regel.type === 'tekst'">
          <tr class="regel-tekst" :class="{ 'regel-optioneel': regel.optioneel }">
            <td colspan="5">
              <input type="text" x-model="regel.naam" @change="updateRegel(regel)"
                     placeholder="Informatieve tekst..." class="pt-input pt-input--full">
            </td>
            <td></td>
            <td class="pt-acties">@include('document.blokken._pt-acties')</td>
          </tr>
        </template>

        {{-- Subtotaal --}}
        <template x-if="regel.type === 'subtotaal'">
          <tr class="regel-subtotaal">
            <td colspan="5" class="pt-label">
              <input type="text" x-model="regel.naam" @change="updateRegel(regel)"
                     placeholder="Subtotaal label..." class="pt-input">
            </td>
            <td class="pt-bedrag" x-text="formatBedrag(berekenSubtotaalTot(ri))"></td>
            <td class="pt-acties">@include('document.blokken._pt-acties')</td>
          </tr>
        </template>

        {{-- Korting --}}
        <template x-if="regel.type === 'korting'">
          <tr class="regel-korting">
            <td colspan="2">
              <input type="text" x-model="regel.naam" @change="updateRegel(regel)"
                     placeholder="Kortingsomschrijving..." class="pt-input pt-input--full">
            </td>
            <td colspan="2" style="display:flex;align-items:center;gap:4px;">
              <input type="number" x-model.number="regel.eenheidsprijs" @change="updateRegel(regel)"
                     step="0.01" class="pt-input" style="width:80px;">
              <span style="font-size:.75rem;color:#888;">€ korting</span>
            </td>
            <td></td>
            <td class="pt-bedrag" style="color:#dc2626;">
              − <span x-text="formatBedrag(Math.abs(regel.eenheidsprijs))"></span>
            </td>
            <td class="pt-acties">@include('document.blokken._pt-acties')</td>
          </tr>
        </template>

        {{-- Product / vrije_regel / optioneel --}}
        <template x-if="['product','vrije_regel','optioneel'].includes(regel.type)">
          <tr :class="'regel-' + regel.type + (regel.optioneel ? ' regel-optioneel' : '')">
            <td>
              <template x-if="regel.type === 'product' && !regel._productGekozen">
                <div>
                  <select @change="selecteerProduct(regel, $event.target.value)" class="pt-select">
                    <option value="">— Kies product —</option>
                    <template x-for="cat in productCategorieen" :key="cat">
                      <optgroup :label="cat">
                        <template x-for="p in productenPerCategorie[cat]" :key="p.id">
                          <option :value="p.id" x-text="p.naam"></option>
                        </template>
                      </optgroup>
                    </template>
                  </select>
                  <button @click="regel._productGekozen = true" class="pt-link-btn">Of vrije invoer</button>
                </div>
              </template>
              <template x-if="regel.type !== 'product' || regel._productGekozen">
                <div>
                  <input type="text" x-model="regel.naam" @change="updateRegel(regel)"
                         placeholder="Omschrijving..." class="pt-input pt-input--full">
                  <input type="text" x-model="regel.beschrijving" @change="updateRegel(regel)"
                         placeholder="Sub-omschrijving (optioneel)..." class="pt-input pt-input--full pt-input--sub" style="font-size:.75rem;color:#666;">
                  <template x-if="regel.type === 'optioneel'">
                    <span class="pt-optioneel">optioneel</span>
                  </template>
                  <template x-if="regel.type === 'product'">
                    <button @click="regel._productGekozen = false; regel.product_id = null" class="pt-link-btn">← Kies product</button>
                  </template>
                </div>
              </template>
            </td>
            <td>
              <input type="number" x-model.number="regel.aantal" @change="updateRegel(regel)"
                     min="0" step="0.01" class="pt-input" style="width:60px;">
            </td>
            <td>
              <input type="text" x-model="regel.eenheid" @change="updateRegel(regel)"
                     placeholder="st." class="pt-input" style="width:50px;">
            </td>
            <td>
              <input type="number" x-model.number="regel.eenheidsprijs" @change="updateRegel(regel)"
                     step="0.01" class="pt-input" style="width:80px;">
            </td>
            <td>
              <select x-model.number="regel.btw_tarief" @change="updateRegel(regel)" class="pt-select" style="width:60px;">
                <option value="0">0%</option>
                <option value="9">9%</option>
                <option value="21">21%</option>
              </select>
            </td>
            <td class="pt-totaal" x-text="formatBedrag(regel.aantal * regel.eenheidsprijs)"></td>
            <td class="pt-acties">@include('document.blokken._pt-acties')</td>
          </tr>
        </template>

      </template>
    </tbody>

    {{-- Totalen footer --}}
    <tfoot>
      <tr class="pt-subtotaal-rij">
        <td colspan="5" class="pt-label">Subtotaal (excl. BTW)</td>
        <td class="pt-bedrag" x-text="formatBedrag(subtotaal)"></td>
        <td></td>
      </tr>
      <template x-for="(bedrag, tarief) in btwGroepen" :key="tarief">
        <tr>
          <td colspan="5" class="pt-label" x-text="'BTW ' + tarief + '%'"></td>
          <td class="pt-bedrag" x-text="formatBedrag(bedrag)"></td>
          <td></td>
        </tr>
      </template>
      <tr class="pt-totaal-rij">
        <td colspan="5" class="pt-label"><strong>Totaal incl. BTW</strong></td>
        <td class="pt-bedrag"><strong x-text="formatBedrag(totaalInclBtw)"></strong></td>
        <td></td>
      </tr>
    </tfoot>
  </table>

  {{-- Voeg regel toe --}}
  <div style="margin-top:8px;display:flex;gap:6px;flex-wrap:wrap;">
    <button @click="voegRegelToe('product')"    class="add-btn" style="width:auto;padding:5px 12px;font-size:.75rem;">+ Product</button>
    <button @click="voegRegelToe('vrije_regel')" class="add-btn" style="width:auto;padding:5px 12px;font-size:.75rem;">+ Vrije regel</button>
    <button @click="voegRegelToe('optioneel')"  class="add-btn" style="width:auto;padding:5px 12px;font-size:.75rem;">+ Optionele regel</button>
    <button @click="voegRegelToe('korting')"    class="add-btn" style="width:auto;padding:5px 12px;font-size:.75rem;">+ Korting</button>
    <button @click="voegRegelToe('tekst')"      class="add-btn" style="width:auto;padding:5px 12px;font-size:.75rem;">+ Tekstregel</button>
    <button @click="voegRegelToe('subtotaal')"  class="add-btn" style="width:auto;padding:5px 12px;font-size:.75rem;">+ Subtotaal</button>
  </div>

</div>
