<script>
function prijstabelEditor(regels, producten, regelStoreUrl, regelBaseUrl) {
  return {
    regelsLijst: regels,
    producten,
    regelStoreUrl,
    regelBaseUrl,

    get productCategorieen() {
      return [...new Set(this.producten.map(p => p.categorie || 'Overig'))];
    },

    get productenPerCategorie() {
      const map = {};
      this.producten.forEach(p => {
        const cat = p.categorie || 'Overig';
        if (!map[cat]) map[cat] = [];
        map[cat].push(p);
      });
      return map;
    },

    get subtotaal() {
      return this.regelsLijst.reduce((som, r) => {
        if (['product','vrije_regel','optioneel'].includes(r.type)) {
          return som + (r.aantal * r.eenheidsprijs);
        }
        if (r.type === 'korting') return som - Math.abs(r.eenheidsprijs);
        return som;
      }, 0);
    },

    get btwGroepen() {
      const groepen = {};
      this.regelsLijst.forEach(r => {
        if (!['product','vrije_regel','optioneel'].includes(r.type)) return;
        const tarief = r.btw_tarief ?? 21;
        const bedrag = round2(r.aantal * r.eenheidsprijs * tarief / 100);
        groepen[tarief] = (groepen[tarief] || 0) + bedrag;
      });
      return groepen;
    },

    get totaalInclBtw() {
      const btw = Object.values(this.btwGroepen).reduce((s, b) => s + b, 0);
      return this.subtotaal + btw;
    },

    berekenSubtotaalTot(index) {
      return this.regelsLijst.slice(0, index).reduce((som, r) => {
        if (['product','vrije_regel','optioneel'].includes(r.type)) return som + (r.aantal * r.eenheidsprijs);
        if (r.type === 'korting') return som - Math.abs(r.eenheidsprijs);
        return som;
      }, 0);
    },

    init() {
      // regels zijn al geïnitialiseerd via parent scope
    },

    async voegRegelToe(type) {
      const nieuw = {
        naam:          type === 'korting' ? 'Korting' : type === 'tekst' ? 'Opmerking' : type === 'subtotaal' ? 'Subtotaal' : '',
        type,
        aantal:        1,
        eenheid:       'st.',
        eenheidsprijs: 0,
        btw_tarief:    21,
        optioneel:     type === 'optioneel',
        beschrijving:  '',
        product_id:    null,
        _productGekozen: type !== 'product',
        _temp: true,
      };

      try {
        const res = await fetch(this.regelStoreUrl, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
          },
          body: JSON.stringify(nieuw),
        });
        const data = await res.json();
        data._productGekozen = type !== 'product';
        this.regelsLijst.push(data);
      } catch (e) {
        console.error('Fout bij aanmaken regel', e);
      }
    },

    async updateRegel(regel) {
      if (!regel.id) return;
      try {
        const res = await fetch(`${this.regelBaseUrl}/${regel.id}`, {
          method: 'PATCH',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
          },
          body: JSON.stringify(regel),
        });
        const data = await res.json();
        Object.assign(regel, data);
      } catch (e) {
        console.error('Fout bij updaten regel', e);
      }
    },

    async verwijderRegel(index) {
      const regel = this.regelsLijst[index];
      if (!regel.id) {
        this.regelsLijst.splice(index, 1);
        return;
      }
      try {
        await fetch(`${this.regelBaseUrl}/${regel.id}`, {
          method: 'DELETE',
          headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content },
        });
        this.regelsLijst.splice(index, 1);
      } catch (e) {
        console.error('Fout bij verwijderen regel', e);
      }
    },

    async verplaatsRegel(index, richting) {
      const nieuw = index + richting;
      if (nieuw < 0 || nieuw >= this.regelsLijst.length) return;
      const tmp = this.regelsLijst[index];
      this.regelsLijst[index] = this.regelsLijst[nieuw];
      this.regelsLijst[nieuw] = tmp;

      // volgorde opslaan
      const volgorde = this.regelsLijst.map((r, i) => ({ id: r.id, volgorde: i }));
      const url = this.regelBaseUrl.replace('/regels', '/regels-volgorde');
      await fetch(url, {
        method: 'PATCH',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
        },
        body: JSON.stringify({ volgorde }),
      });
    },

    selecteerProduct(regel, productId) {
      const product = this.producten.find(p => p.id == productId);
      if (!product) return;
      regel.product_id    = product.id;
      regel.naam          = product.naam;
      regel.eenheidsprijs = product.prijs;
      regel._productGekozen = true;
      this.updateRegel(regel);
    },

    formatBedrag(bedrag) {
      return '€ ' + round2(bedrag).toFixed(2).replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    },
  };
}

function round2(n) { return Math.round((n + Number.EPSILON) * 100) / 100; }
</script>
