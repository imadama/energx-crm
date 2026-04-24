{{-- Herbruikbaar afbeelding upload component --}}
<div class="afbeelding-upload-area">
  <template x-if="element.inhoud.afbeelding_url">
    <div style="position:relative;">
      <img :src="element.inhoud.afbeelding_url" style="max-width:100%;height:auto;display:block;border-radius:3px;">
      <button @click="element.inhoud.afbeelding_url = ''; save()"
              class="icon-btn icon-btn--danger"
              style="position:absolute;top:4px;right:4px;background:rgba(255,255,255,.9);">✕</button>
    </div>
  </template>
  <template x-if="!element.inhoud.afbeelding_url">
    <label class="blok-afbeelding--leeg" style="cursor:pointer;display:block;">
      <input type="file" accept="image/*" style="display:none;"
             @change="uploadAfbeelding($event.target.files[0], url => { element.inhoud.afbeelding_url = url; save(); })">
      <div>📁 Klik om afbeelding te uploaden</div>
      <small style="color:#94a3b8;margin-top:4px;display:block;">JPG, PNG, WebP — max 5 MB</small>
    </label>
  </template>
</div>
