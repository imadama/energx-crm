{{-- Tekst blok — Quill editor --}}
<div class="blok-tekst">
  <div :id="'quill-' + element.id + '-tekst'" class="quill-veld"
       x-effect="
         const el = document.getElementById('quill-' + element.id + '-tekst');
         if (el && !el._quill) {
           $nextTick(() => initQuillVeld(element.id + '-tekst', (html) => {
             element.inhoud.html = html;
             debouncedSave();
           }, element.inhoud.html || ''));
         }
       ">
  </div>
</div>
