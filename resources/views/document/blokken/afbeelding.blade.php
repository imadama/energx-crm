{{-- Afbeelding blok --}}
<div class="blok-afbeelding">
  <template x-if="element.inhoud.afbeelding_url">
    <div :style="'display:flex;justify-content:' + { left:'flex-start', center:'center', right:'flex-end' }[element.inhoud.uitlijning || 'center'] + ';'">
      <div style="position:relative;display:inline-block;">
        <img :src="element.inhoud.afbeelding_url"
             :style="'width:' + (element.inhoud.breedte_pct ?? 100) + '%;height:auto;display:block;border-radius:3px;'">
        <div style="position:absolute;top:4px;right:4px;display:flex;gap:4px;">
          <button @click="element.inhoud.uitlijning = 'left'; save()"   class="icon-btn" style="font-size:.65rem;width:auto;padding:2px 5px;" :class="{ 'active': element.inhoud.uitlijning === 'left' }">◀</button>
          <button @click="element.inhoud.uitlijning = 'center'; save()" class="icon-btn" style="font-size:.65rem;width:auto;padding:2px 5px;" :class="{ 'active': element.inhoud.uitlijning === 'center' }">■</button>
          <button @click="element.inhoud.uitlijning = 'right'; save()"  class="icon-btn" style="font-size:.65rem;width:auto;padding:2px 5px;" :class="{ 'active': element.inhoud.uitlijning === 'right' }">▶</button>
          <button @click="element.inhoud.afbeelding_url = ''; save()" class="icon-btn icon-btn--danger" style="background:rgba(255,255,255,.9);">✕</button>
        </div>
      </div>
    </div>
  </template>
  <template x-if="!element.inhoud.afbeelding_url">
    <label class="blok-afbeelding--leeg" style="cursor:pointer;display:block;text-align:center;">
      <input type="file" accept="image/*" style="display:none;"
             @change="uploadAfbeelding($event.target.files[0], url => { element.inhoud.afbeelding_url = url; save(); })">
      <div>📁 Klik om afbeelding te uploaden</div>
      <small style="color:#94a3b8;margin-top:4px;display:block;">JPG, PNG, WebP — max 5 MB</small>
    </label>
  </template>
  <template x-if="element.inhoud.afbeelding_url">
    <div style="margin-top:6px;display:flex;align-items:center;gap:8px;">
      <label style="font-size:.75rem;color:#666;">Breedte: <span x-text="(element.inhoud.breedte_pct ?? 100) + '%'"></span></label>
      <input type="range" min="10" max="100"
             x-model.number="element.inhoud.breedte_pct"
             @change="save()" style="flex:1;">
    </div>
  </template>
</div>
