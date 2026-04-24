{{-- Tekst + Afbeelding blok --}}
<div class="blok-2kolommen" style="display:flex;align-items:flex-start;gap:0;">

  {{-- Afbeelding links --}}
  <template x-if="element.inhoud.afbeelding_links">
    <div class="kolom" :style="'width:' + (100 - (element.inhoud.tekst_breedte_pct ?? 60)) + '%;padding-right:12px;'">
      <template x-if="activeElementId === element.id">
        @include('document.blokken._afbeelding-upload')
      </template>
      <template x-if="activeElementId !== element.id && element.inhoud.afbeelding_url">
        <img :src="element.inhoud.afbeelding_url" style="max-width:100%;height:auto;display:block;border-radius:3px;">
      </template>
    </div>
  </template>

  {{-- Tekst --}}
  <div class="kolom" :style="'width:' + (element.inhoud.tekst_breedte_pct ?? 60) + '%;'">
    {{-- View mode (inactive) --}}
    <div x-show="activeElementId !== element.id" style="min-height:40px;">
      <div x-html="element.inhoud.tekst_html || ''"></div>
    </div>

    {{-- Edit mode (active) --}}
    <div x-show="activeElementId === element.id" style="display:none">
      <div :id="'quill-' + element.id + '-tekst'" class="quill-veld"
           x-effect="
             const el = window.document.getElementById('quill-' + element.id + '-tekst');
             if (el && !el._quill) {
               $nextTick(() => initQuillVeld(element.id + '-tekst', (html) => {
                 element.inhoud.tekst_html = html;
                 debouncedSave();
               }, element.inhoud.tekst_html || ''));
             }
           ">
      </div>
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
      <template x-if="activeElementId === element.id">
        @include('document.blokken._afbeelding-upload')
      </template>
      <template x-if="activeElementId !== element.id && element.inhoud.afbeelding_url">
        <img :src="element.inhoud.afbeelding_url" style="max-width:100%;height:auto;display:block;border-radius:3px;">
      </template>
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
