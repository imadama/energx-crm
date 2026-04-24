{{-- Tekst + Afbeelding blok --}}
<div class="blok-2kolommen" style="display:flex;align-items:flex-start;gap:0;">

  {{-- Afbeelding links --}}
  <template x-if="element.inhoud.afbeelding_links">
    <div class="kolom" :style="'width:' + (100 - (element.inhoud.tekst_breedte_pct ?? 60)) + '%;padding-right:12px;'">
      @include('document.blokken._afbeelding-upload')
    </div>
  </template>

  {{-- Tekst --}}
  <div class="kolom" :style="'width:' + (element.inhoud.tekst_breedte_pct ?? 60) + '%;'">
    <div :id="'quill-' + element.id + '-tekst'" class="quill-veld"
         x-effect="
           const el = document.getElementById('quill-' + element.id + '-tekst');
           if (el && !el._quill) {
             $nextTick(() => initQuillVeld(element.id + '-tekst', (html) => {
               element.inhoud.tekst_html = html;
               debouncedSave();
             }, element.inhoud.tekst_html || ''));
           }
         ">
    </div>
  </div>

  {{-- Kolom resize --}}
  <div class="kolom-resizer"
       @mousedown.prevent="startKolomResize($event, pagina.id, element.id)"
       title="Sleep om kolombreedtes aan te passen">
    <span style="color:rgba(45,189,110,.5);user-select:none;font-size:16px;">⋮⋮</span>
  </div>

  {{-- Afbeelding rechts --}}
  <template x-if="!element.inhoud.afbeelding_links">
    <div class="kolom" :style="'width:' + (100 - (element.inhoud.tekst_breedte_pct ?? 60)) + '%;padding-left:12px;'">
      @include('document.blokken._afbeelding-upload')
    </div>
  </template>

  {{-- Wissel positie knop --}}
  <div style="position:absolute;top:4px;right:4px;">
    <button @click="element.inhoud.afbeelding_links = !element.inhoud.afbeelding_links; save()"
            class="icon-btn" style="font-size:.65rem;width:auto;padding:2px 6px;"
            title="Wissel positie afbeelding">
      ⇄
    </button>
  </div>

</div>
