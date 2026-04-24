{{-- Tekst 2-kolommen blok --}}
<div class="blok-2kolommen" style="display:flex;align-items:stretch;">
  <div class="kolom" :style="'width:' + (element.inhoud.kolom1_breedte_pct ?? 50) + '%;padding-right:8px;'">
    <div :id="'quill-' + element.id + '-k1'" class="quill-veld"
         x-effect="
           const el = document.getElementById('quill-' + element.id + '-k1');
           if (el && !el._quill) {
             $nextTick(() => initQuillVeld(element.id + '-k1', (html) => {
               element.inhoud.kolom1_html = html;
               debouncedSave();
             }, element.inhoud.kolom1_html || ''));
           }
         ">
    </div>
  </div>
  <div class="kolom-resizer"
       @mousedown.prevent="startKolomResize($event, pagina.id, element.id)"
       title="Sleep om kolombreedtes aan te passen">
    <span style="color:rgba(45,189,110,.5);user-select:none;font-size:16px;">⋮⋮</span>
  </div>
  <div class="kolom" :style="'width:' + (100 - (element.inhoud.kolom1_breedte_pct ?? 50)) + '%;padding-left:8px;'">
    <div :id="'quill-' + element.id + '-k2'" class="quill-veld"
         x-effect="
           const el = document.getElementById('quill-' + element.id + '-k2');
           if (el && !el._quill) {
             $nextTick(() => initQuillVeld(element.id + '-k2', (html) => {
               element.inhoud.kolom2_html = html;
               debouncedSave();
             }, element.inhoud.kolom2_html || ''));
           }
         ">
    </div>
  </div>
</div>
