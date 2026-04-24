{{-- Tekst blok — Quill editor --}}
<div class="blok-tekst">
  {{-- View mode (inactive) --}}
  <div x-show="activeElementId !== element.id" style="min-height:40px;">
    <div x-html="element.inhoud.html || ''"></div>
  </div>

  {{-- Edit mode (active) --}}
  <div x-show="activeElementId === element.id" style="display:none">
    <div :id="'quill-' + element.id + '-tekst'" class="quill-veld"
         x-effect="
           const el = window.document.getElementById('quill-' + element.id + '-tekst');
           if (el && !el._quill) {
             $nextTick(() => initQuillVeld(element.id + '-tekst', (html) => {
               element.inhoud.html = html;
               debouncedSave();
             }, element.inhoud.html || ''));
           }
         ">
    </div>
  </div>
</div>
