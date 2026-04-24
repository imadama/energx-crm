{{-- Standaard tabel blok (specs, etc.) --}}
<div class="blok-standaard-tabel" x-data="{
  voegKolomToe() {
    element.inhoud.kolommen.push('Kolom');
    element.inhoud.rijen.forEach(r => r.push(''));
    debouncedSave();
  },
  verwijderKolom(ki) {
    element.inhoud.kolommen.splice(ki, 1);
    element.inhoud.rijen.forEach(r => r.splice(ki, 1));
    debouncedSave();
  },
  voegRijToe() {
    element.inhoud.rijen.push(element.inhoud.kolommen.map(() => ''));
    debouncedSave();
  },
  verwijderRij(ri) {
    element.inhoud.rijen.splice(ri, 1);
    debouncedSave();
  }
}">
  <table style="width:100%;border-collapse:collapse;">
    <thead>
      <tr>
        <template x-for="(kol, ki) in element.inhoud.kolommen" :key="ki">
          <th style="background:#f8fafc;border:1px solid #e5e7eb;padding:0;position:relative;">
            <input type="text" x-model="element.inhoud.kolommen[ki]" @change="debouncedSave()"
                   style="width:100%;padding:7px 24px 7px 8px;background:transparent;border:none;font-weight:600;font-size:.82rem;color:#374151;outline:none;">
            <button @click="verwijderKolom(ki)" class="icon-btn icon-btn--danger"
                    style="position:absolute;right:2px;top:50%;transform:translateY(-50%);width:18px;height:18px;font-size:.6rem;"
                    x-show="element.inhoud.kolommen.length > 1">✕</button>
          </th>
        </template>
        <th style="background:#f8fafc;border:1px solid #e5e7eb;padding:4px;width:32px;">
          <button @click="voegKolomToe()" class="icon-btn" style="width:22px;height:22px;font-size:.7rem;" title="Kolom toevoegen">+</button>
        </th>
      </tr>
    </thead>
    <tbody>
      <template x-for="(rij, ri) in element.inhoud.rijen" :key="ri">
        <tr>
          <template x-for="(cel, ci) in rij" :key="ci">
            <td style="border:1px solid #e5e7eb;padding:0;">
              <input type="text" x-model="element.inhoud.rijen[ri][ci]" @change="debouncedSave()"
                     style="width:100%;padding:7px 8px;border:none;font-size:.82rem;outline:none;">
            </td>
          </template>
          <td style="border:1px solid #e5e7eb;padding:4px;text-align:center;">
            <button @click="verwijderRij(ri)" class="icon-btn icon-btn--danger"
                    style="width:18px;height:18px;font-size:.6rem;"
                    x-show="element.inhoud.rijen.length > 1">✕</button>
          </td>
        </tr>
      </template>
    </tbody>
  </table>
  <button @click="voegRijToe()" class="add-btn" style="margin-top:4px;padding:5px 10px;font-size:.75rem;">+ Rij toevoegen</button>
</div>
