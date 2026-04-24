{{-- Prijstabel regel acties --}}
<div style="display:flex;gap:2px;justify-content:center;">
  <button @click="verplaatsRegel(ri, -1)" :disabled="ri === 0" class="icon-btn" title="Omhoog" style="width:20px;height:20px;font-size:.65rem;">↑</button>
  <button @click="verplaatsRegel(ri, 1)" :disabled="ri === regelsLijst.length - 1" class="icon-btn" title="Omlaag" style="width:20px;height:20px;font-size:.65rem;">↓</button>
  <button @click="verwijderRegel(ri)" class="icon-btn icon-btn--danger" title="Verwijder" style="width:20px;height:20px;font-size:.65rem;">✕</button>
</div>
