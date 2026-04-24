<script>
function pageEditor(docData, regels, producten, saveUrl, regelStoreUrl, regelBaseUrl, uploadUrl, tokens) {
  return {
    document: docData,
    regels,
    producten,
    tokens,
    saveUrl,
    regelStoreUrl,
    regelBaseUrl,
    uploadUrl,

    saveStatus: 'idle',
    saveLabel: 'Opgeslagen',
    saveTimer: null,

    activePaginaIndex: null,
    activeElementId: null,
    activeElementIndex: null,
    rightPanel: null,

    tokenKopieerdLabel: '',
    tokenTimer: null,

    resizing: null,

    elementTypen: [
      { type: 'tekst',            label: 'Tekst',              icon: '¶' },
      { type: 'tekst_2kolommen',  label: 'Tekst — 2 kolommen', icon: '⫼' },
      { type: 'tekst_afbeelding', label: 'Tekst + Afbeelding', icon: '▤' },
      { type: 'afbeelding',       label: 'Afbeelding',         icon: '🖼' },
      { type: 'prijstabel',       label: 'Prijstabel',         icon: '€' },
      { type: 'standaard_tabel',  label: 'Tabel',              icon: '⊞' },
    ],

    init() {
      this.$nextTick(() => {
        this.initSortables();
        this.initQuillInstances();
      });

      window.addEventListener('mousemove', (e) => this.onResize(e));
      window.addEventListener('mouseup', () => this.stopResize());
    },

    // ── Pagina's ─────────────────────────────────────────────────────────────

    voegPaginaToe() {
      this.document.paginas.push({
        id: this.uuid(),
        instellingen: {
          marge: { top: 40, right: 50, bottom: 40, left: 50 },
          achtergrond_kleur: '#ffffff',
          achtergrond_afbeelding: null,
        },
        elementen: [],
      });
      this.save();
      this.$nextTick(() => this.initSortables());
    },

    verwijderPagina(pi) {
      if (this.document.paginas.length <= 1) return;
      if (!confirm('Pagina verwijderen? Alle elementen op deze pagina worden verwijderd.')) return;
      this.document.paginas.splice(pi, 1);
      this.save();
    },

    openPaginaInstellingen(pi) {
      this.activePaginaIndex = pi;
      this.rightPanel = 'pagina';
    },

    scrollToPagina(pi) {
      this.activePaginaIndex = pi;
      const el = document.getElementById('pagina-' + pi);
      if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' });
    },

    paginaStijl(pagina) {
      const i = pagina.instellingen || {};
      const m = i.marge || {};
      let s = `padding:${m.top||40}px ${m.right||50}px ${m.bottom||40}px ${m.left||50}px;`;
      s += `background-color:${i.achtergrond_kleur || '#ffffff'};`;
      if (i.achtergrond_afbeelding) {
        s += `background-image:url('${i.achtergrond_afbeelding}');background-size:cover;background-position:center;`;
      }
      return s;
    },

    // ── Elementen ─────────────────────────────────────────────────────────────

    voegElementToe(pi, type) {
      const el = {
        id: this.uuid(),
        type,
        instellingen: {
          achtergrond_kleur: null,
          marge_top: 0,
          marge_bottom: 16,
          content_breedte_pct: 100,
          content_offset_pct: 0,
        },
        inhoud: this.leegInhoud(type),
      };
      this.document.paginas[pi].elementen.push(el);
      this.save();
      this.$nextTick(() => this.initQuillInstances());
    },

    leegInhoud(type) {
      switch (type) {
        case 'tekst':            return { html: '' };
        case 'tekst_2kolommen':  return { kolom1_breedte_pct: 50, kolom1_html: '', kolom2_html: '' };
        case 'tekst_afbeelding': return { tekst_html: '', afbeelding_url: '', afbeelding_links: false, tekst_breedte_pct: 60 };
        case 'afbeelding':       return { afbeelding_url: '', uitlijning: 'center', breedte_pct: 100 };
        case 'prijstabel':       return {};
        case 'standaard_tabel':  return { kolommen: ['Kolom 1', 'Kolom 2'], rijen: [['', '']] };
        default:                 return {};
      }
    },

    verwijderElement(pi, ei) {
      if (!confirm('Element verwijderen?')) return;
      this.document.paginas[pi].elementen.splice(ei, 1);
      this.activeElementId = null;
      this.save();
    },

    verplaatsElement(pi, ei, richting) {
      const els = this.document.paginas[pi].elementen;
      const nieuw = ei + richting;
      if (nieuw < 0 || nieuw >= els.length) return;
      const tmp = els[ei];
      els[ei] = els[nieuw];
      els[nieuw] = tmp;
      this.save();
    },

    openElementInstellingen(pi, ei) {
      this.activePaginaIndex  = pi;
      this.activeElementIndex = ei;
      this.rightPanel = 'element';
    },

    elementLabel(type) {
      return this.elementTypen.find(t => t.type === type)?.label || type;
    },

    elementOuterStijl(element) {
      const i = element.instellingen || {};
      let s = `padding-top:${i.marge_top||0}px;padding-bottom:${i.marge_bottom||16}px;`;
      if (i.achtergrond_kleur) s += `background-color:${i.achtergrond_kleur};`;
      return s;
    },

    contentStijl(element) {
      const i = element.instellingen || {};
      const breedte = i.content_breedte_pct ?? 100;
      const offset  = i.content_offset_pct ?? 0;
      return `width:${breedte}%;margin-left:${offset}%;`;
    },

    zetUitlijning(pi, ei, positie) {
      const inst = this.document.paginas[pi].elementen[ei].instellingen;
      const breedte = inst.content_breedte_pct ?? 100;
      if (positie === 'left')   inst.content_offset_pct = 0;
      if (positie === 'center') inst.content_offset_pct = Math.round((100 - breedte) / 2);
      if (positie === 'right')  inst.content_offset_pct = 100 - breedte;
      this.save();
    },

    // ── Resize content blok ───────────────────────────────────────────────────

    startResize(event, paginaId, elemId, zijde) {
      const pagina = this.document.paginas.find(p => p.id === paginaId);
      const element = pagina?.elementen.find(e => e.id === elemId);
      if (!element) return;

      const container = event.target.closest('.content-resizer');
      const rect = container.getBoundingClientRect();

      this.resizing = {
        type: 'content',
        paginaId, elemId, zijde,
        startX: event.clientX,
        containerWidth: rect.width,
        startBreedte: element.instellingen.content_breedte_pct ?? 100,
        startOffset: element.instellingen.content_offset_pct ?? 0,
        pagina, element,
      };
    },

    onResize(event) {
      if (!this.resizing) return;
      if (this.resizing.type === 'kolom') {
        this.onKolomResize(event);
        return;
      }
      const { startX, containerWidth, startBreedte, startOffset, element, zijde } = this.resizing;
      const delta = event.clientX - startX;
      const deltaPct = (delta / containerWidth) * 100;

      if (zijde === 'right') {
        const nieuw = Math.max(10, Math.min(100 - startOffset, startBreedte + deltaPct));
        element.instellingen.content_breedte_pct = Math.round(nieuw);
      } else {
        const nieuweOffset = Math.max(0, Math.min(100 - 10, startOffset + deltaPct));
        const nieuweBreedte = Math.max(10, startBreedte - deltaPct);
        if (nieuweOffset + nieuweBreedte <= 100) {
          element.instellingen.content_offset_pct = Math.round(nieuweOffset);
          element.instellingen.content_breedte_pct = Math.round(nieuweBreedte);
        }
      }
    },

    stopResize() {
      if (this.resizing) {
        this.save();
        this.resizing = null;
      }
    },

    // ── Sortable ──────────────────────────────────────────────────────────────

    initSortables() {
      this.document.paginas.forEach((pagina, pi) => {
        const container = document.getElementById('sortable-' + pagina.id);
        if (!container || container._sortable) return;
        container._sortable = Sortable.create(container, {
          handle: '.element-type-label',
          animation: 150,
          onEnd: (evt) => {
            const els = this.document.paginas[pi].elementen;
            const item = els.splice(evt.oldIndex, 1)[0];
            els.splice(evt.newIndex, 0, item);
            this.save();
          },
        });
      });
    },

    // ── Quill ─────────────────────────────────────────────────────────────────

    initQuillInstances() {
      const toolbarOptions = [
        ['bold', 'italic', 'underline', 'strike'],
        [{ 'header': [1, 2, 3, false] }],
        [{ 'align': [] }],
        [{ 'list': 'bullet' }],
        ['clean'],
      ];

      this.document.paginas.forEach((pagina) => {
        pagina.elementen.forEach((element) => {
          if (element.type === 'tekst') {
            this.initQuillVeld(element.id + '-tekst', (html) => {
              element.inhoud.html = html;
              this.debouncedSave();
            }, element.inhoud.html || '');
          }
          if (element.type === 'tekst_2kolommen') {
            this.initQuillVeld(element.id + '-k1', (html) => {
              element.inhoud.kolom1_html = html;
              this.debouncedSave();
            }, element.inhoud.kolom1_html || '');
            this.initQuillVeld(element.id + '-k2', (html) => {
              element.inhoud.kolom2_html = html;
              this.debouncedSave();
            }, element.inhoud.kolom2_html || '');
          }
          if (element.type === 'tekst_afbeelding') {
            this.initQuillVeld(element.id + '-tekst', (html) => {
              element.inhoud.tekst_html = html;
              this.debouncedSave();
            }, element.inhoud.tekst_html || '');
          }
        });
      });
    },

    initQuillVeld(id, onChange, initialHtml) {
      const el = document.getElementById('quill-' + id);
      if (!el || el._quill) return;

      const q = new Quill(el, {
        theme: 'snow',
        modules: {
          toolbar: [
            ['bold', 'italic', 'underline', 'strike'],
            [{ 'header': [1, 2, 3, false] }],
            [{ 'align': [] }],
            [{ 'list': 'bullet' }],
            ['clean'],
          ],
        },
      });
      el._quill = q;

      if (initialHtml) q.root.innerHTML = initialHtml;

      q.on('text-change', () => {
        onChange(q.root.innerHTML);
      });
    },

    // ── Tokens ────────────────────────────────────────────────────────────────

    kopieerToken(token, label) {
      const tekst = '{{ ' + token + ' }}';
      navigator.clipboard.writeText(tekst).then(() => {
        this.tokenKopieerdLabel = label;
        clearTimeout(this.tokenTimer);
        this.tokenTimer = setTimeout(() => { this.tokenKopieerdLabel = ''; }, 2000);
      });
    },

    // ── Upload ────────────────────────────────────────────────────────────────

    async uploadAfbeelding(file, callback) {
      const fd = new FormData();
      fd.append('afbeelding', file);
      fd.append('_token', document.querySelector('meta[name=csrf-token]').content);
      const res = await fetch(this.uploadUrl, { method: 'POST', body: fd });
      const data = await res.json();
      if (data.url) callback(data.url);
    },

    // ── Kolom resize ──────────────────────────────────────────────────────────

    startKolomResize(event, paginaId, elemId) {
      const pagina  = this.document.paginas.find(p => p.id === paginaId);
      const element = pagina?.elementen.find(e => e.id === elemId);
      if (!element) return;
      const container = event.target.closest('.blok-2kolommen');
      if (!container) return;
      this.resizing = {
        type: 'kolom',
        paginaId, elemId,
        startX: event.clientX,
        containerWidth: container.getBoundingClientRect().width,
        startBreedte: element.inhoud.kolom1_breedte_pct ?? 50,
        element,
      };
    },

    onKolomResize(event) {
      if (!this.resizing || this.resizing.type !== 'kolom') return;
      const { startX, containerWidth, startBreedte, element } = this.resizing;
      const delta = event.clientX - startX;
      const deltaPct = (delta / containerWidth) * 100;
      const nieuw = Math.max(15, Math.min(85, startBreedte + deltaPct));
      element.inhoud.kolom1_breedte_pct = Math.round(nieuw);
    },

    // ── Save ──────────────────────────────────────────────────────────────────

    debouncedSave() {
      clearTimeout(this.saveTimer);
      this.saveTimer = setTimeout(() => this.save(), 1200);
    },

    async save() {
      this.saveStatus = 'saving';
      this.saveLabel  = 'Opslaan...';
      try {
        const res = await fetch(this.saveUrl, {
          method: 'PATCH',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
          },
          body: JSON.stringify({ document: this.document }),
        });
        if (!res.ok) throw new Error();
        this.saveStatus = 'saved';
        this.saveLabel  = 'Opgeslagen';
        setTimeout(() => { this.saveStatus = 'idle'; this.saveLabel = 'Opgeslagen'; }, 2000);
      } catch {
        this.saveStatus = 'error';
        this.saveLabel  = 'Fout bij opslaan';
      }
    },

    // ── Helpers ───────────────────────────────────────────────────────────────

    uuid() {
      return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, c => {
        const r = Math.random() * 16 | 0;
        return (c === 'x' ? r : (r & 0x3 | 0x8)).toString(16);
      });
    },
  };
}
</script>
