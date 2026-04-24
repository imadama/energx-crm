<style>
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
  :root {
    --green-800: #0F4A2A; --green-600: #1a6e3f; --green-400: #2DBD6E;
    --font-body: 'Outfit', sans-serif; --font-display: 'DM Serif Display', serif;
    --topbar: 56px; --left-panel: 220px; --right-panel: 280px;
  }
  html, body { height: 100%; font-family: var(--font-body); background: #e8eaed; }
  .editor-root { display: flex; height: 100vh; overflow: hidden; }

  /* TOPBAR */
  .topbar { position: fixed; top:0; left:0; right:0; height: var(--topbar); background:#fff;
    border-bottom:1px solid #e5e7eb; display:flex; align-items:center; justify-content:space-between;
    padding:0 20px; z-index:200; box-shadow:0 1px 4px rgba(0,0,0,.06); }
  .topbar-left, .topbar-right { display:flex; align-items:center; gap:12px; }
  .topbar-title { font-weight:600; font-size:.9rem; color:#1a1a1a; }
  .btn-sm { display:inline-flex; align-items:center; gap:5px; padding:5px 12px; font-size:.8rem;
    font-family:var(--font-body); font-weight:500; border-radius:7px; cursor:pointer;
    text-decoration:none; border:1px solid transparent; transition:background .15s; }
  .btn-secondary { background:#f3f4f6; color:#444; border-color:#e5e7eb; }
  .btn-secondary:hover { background:#e5e7eb; }
  .save-status { font-size:.78rem; font-weight:500; padding:3px 10px; border-radius:20px; }
  .save-status.idle { background:#f3f4f6; color:#888; }
  .save-status.saving { background:#fef3c7; color:#92400e; }
  .save-status.saved { background:#d1fae5; color:#065f46; }
  .save-status.error { background:#fee2e2; color:#991b1b; }

  /* LEFT PANEL */
  .left-panel { position:fixed; top:var(--topbar); left:0; bottom:0; width:var(--left-panel);
    background:#1a2e1f; overflow-y:auto; z-index:100; display:flex; flex-direction:column; gap:0; }
  .panel-section { padding:16px; border-bottom:1px solid rgba(255,255,255,.07); }
  .panel-label { font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.08em;
    color:rgba(255,255,255,.35); margin-bottom:10px; }
  .page-thumb { display:flex; align-items:center; justify-content:space-between; padding:8px 10px;
    border-radius:7px; cursor:pointer; color:rgba(255,255,255,.6); font-size:.82rem;
    transition:background .15s; margin-bottom:3px; }
  .page-thumb:hover { background:rgba(255,255,255,.06); color:rgba(255,255,255,.9); }
  .page-thumb.active { background:rgba(45,189,110,.12); color:#fff; }
  .page-thumb-actions { display:flex; gap:3px; opacity:.45; transition:opacity .15s; }
  .page-thumb:hover .page-thumb-actions { opacity:1; }
  .add-btn { width:100%; padding:8px 10px; background:rgba(255,255,255,.05); border:1px dashed rgba(255,255,255,.15);
    border-radius:7px; color:rgba(255,255,255,.5); font-family:var(--font-body); font-size:.8rem;
    cursor:pointer; transition:all .15s; margin-top:4px; }
  .add-btn:hover { background:rgba(255,255,255,.09); color:rgba(255,255,255,.85); border-color:rgba(255,255,255,.3); }
  .token-groep-naam { font-size:.7rem; font-weight:600; color:rgba(255,255,255,.35); margin:8px 0 4px; text-transform:uppercase; }
  .token-btn { display:block; width:100%; text-align:left; padding:5px 8px; background:rgba(45,189,110,.08);
    border:1px solid rgba(45,189,110,.2); border-radius:5px; color:rgba(255,255,255,.7);
    font-family:var(--font-body); font-size:.77rem; cursor:pointer; margin-bottom:3px; transition:all .15s; }
  .token-btn:hover { background:rgba(45,189,110,.18); color:#fff; }
  .token-tip { font-size:.75rem; color:#2DBD6E; margin-top:6px; font-weight:500; }

  /* CANVAS */
  .canvas { flex:1; margin-left:var(--left-panel); margin-top:var(--topbar); overflow-y:auto;
    padding:48px 40px 80px; display:flex; flex-direction:column; align-items:center; gap:40px; }

  /* A4 */
  .a4-pagina-wrapper { width:794px; }
  .pagina-label { font-size:.72rem; font-weight:600; color:#94a3b8; text-transform:uppercase;
    letter-spacing:.07em; margin-bottom:6px; }
  .a4-pagina { width:794px; min-height:1123px; background:#fff;
    box-shadow:0 4px 32px rgba(0,0,0,.12), 0 1px 6px rgba(0,0,0,.06);
    border-radius:2px; position:relative; }
  .elementen-container { min-height:40px; }

  /* ELEMENT WRAPPER */
  .document-element-wrapper { position:relative; border:2px solid transparent; transition:border-color .15s; }
  .document-element-wrapper:hover, .document-element-wrapper.is-actief { border-color:rgba(45,189,110,.35); }
  .document-element-wrapper:hover .element-toolbar,
  .document-element-wrapper.is-actief .element-toolbar { opacity:1; pointer-events:all; }
  .element-toolbar { position:absolute; top:-34px; left:0; right:0; height:32px;
    background:#fff; border:1px solid #e5e7eb; border-radius:7px 7px 0 0;
    display:flex; align-items:center; justify-content:space-between; padding:0 8px;
    opacity:0; pointer-events:none; transition:opacity .15s; z-index:50;
    box-shadow:0 -2px 8px rgba(0,0,0,.06); }
  .element-type-label { font-size:.72rem; font-weight:600; color:#94a3b8; text-transform:uppercase; letter-spacing:.05em; }
  .element-toolbar-actions { display:flex; gap:2px; }

  /* RESIZE HANDLES */
  .element-outer { position:relative; }
  .content-resizer { position:relative; display:flex; align-items:stretch; }
  .resize-handle { width:12px; flex-shrink:0; cursor:ew-resize; display:flex; align-items:center; justify-content:center; opacity:0; transition:opacity .2s; }
  .document-element-wrapper:hover .resize-handle,
  .document-element-wrapper.is-actief .resize-handle { opacity:1; }
  .resize-handle::after { content:''; width:3px; height:24px; background:rgba(45,189,110,.5); border-radius:2px; }
  .document-content { flex:1; min-width:0; }

  /* ADD ELEMENT */
  .add-element-bar { padding:16px; display:flex; justify-content:center; border-top:2px dashed #e5e7eb; margin:0 -1px; }
  .add-element-dropdown { position:relative; }
  .add-btn--element { width:auto; padding:9px 24px; border-radius:20px;
    background:#f0fdf4 !important; border:1px solid rgba(45,189,110,.5) !important;
    color:#15803d !important; font-weight:600; font-size:.85rem; }
  .add-btn--element:hover { background:#dcfce7 !important; border-color:rgba(45,189,110,.8) !important; color:#166534 !important; }
  .element-type-menu { position:absolute; top:calc(100% + 6px); left:50%; transform:translateX(-50%);
    background:#fff; border:1px solid #e5e7eb; border-radius:10px; padding:6px;
    box-shadow:0 8px 24px rgba(0,0,0,.14); z-index:500; min-width:220px; white-space:nowrap; }
  .element-type-item { display:flex; align-items:center; width:100%; padding:9px 12px;
    background:none; border:none; border-radius:7px; cursor:pointer; font-family:var(--font-body);
    font-size:.85rem; color:#333; transition:background .1s; text-align:left; }
  .element-type-item:hover { background:#f3f4f6; }

  /* RIGHT PANEL */
  .right-panel { position:fixed; top:var(--topbar); right:-280px; bottom:0;
    width:var(--right-panel); background:#fff; border-left:1px solid #e5e7eb;
    overflow-y:auto; z-index:100; transition:right .25s ease; }
  .right-panel.open { right:0; }
  .canvas.panel-open { margin-right:var(--right-panel); }
  .panel-content { padding:0; }
  .panel-header { display:flex; align-items:center; justify-content:space-between;
    padding:16px; border-bottom:1px solid #f3f4f6; position:sticky; top:0; background:#fff; z-index:1; }
  .panel-header strong { font-size:.9rem; }
  .instellingen-form { padding:16px; display:flex; flex-direction:column; gap:8px; }
  .form-label { font-size:.78rem; font-weight:600; color:#555; }
  .form-input { width:100%; padding:7px 10px; border:1px solid #e5e7eb; border-radius:7px;
    font-family:var(--font-body); font-size:.84rem; color:#333; }
  .form-color { width:100%; height:36px; border:1px solid #e5e7eb; border-radius:7px; cursor:pointer; padding:2px; }
  .form-range { width:100%; }
  .marge-grid { display:grid; grid-template-columns:1fr 1fr; gap:6px; }
  .uitlijning-knoppen { display:flex; gap:4px; }
  .uitlijning-btn { flex:1; padding:6px; background:#f3f4f6; border:1px solid #e5e7eb;
    border-radius:6px; font-size:.78rem; cursor:pointer; font-family:var(--font-body); }
  .uitlijning-btn:hover { background:#e5e7eb; }

  /* ICON BUTTONS */
  .icon-btn { display:inline-flex; align-items:center; justify-content:center; width:26px; height:26px;
    background:none; border:1px solid #e5e7eb; border-radius:5px; cursor:pointer;
    font-size:.75rem; color:#555; transition:all .15s; }
  .icon-btn:hover { background:#f3f4f6; }
  .icon-btn:disabled { opacity:.3; cursor:default; }
  .icon-btn--danger:hover { background:#fee2e2; color:#dc2626; border-color:#fca5a5; }
  /* icon-btns inside dark left panel */
  .left-panel .icon-btn { color:rgba(255,255,255,.7); border-color:rgba(255,255,255,.2); }
  .left-panel .icon-btn:hover { background:rgba(255,255,255,.12); color:#fff; border-color:rgba(255,255,255,.4); }
  .left-panel .icon-btn--danger:hover { background:rgba(220,38,38,.25); color:#fca5a5; border-color:rgba(220,38,38,.4); }

  /* DOCUMENT BLOKKEN (editor mode) */
  .blok-tekst { outline:none; min-height:40px; }
  .blok-tekst .ql-container { border:none; font-family:var(--font-body); }
  .blok-tekst .ql-toolbar { border:none; border-bottom:1px solid #f0f0f0; padding:4px; }
  .blok-tekst .ql-editor { padding:8px 0; min-height:40px; }
  .blok-2kolommen { display:flex; gap:0; }
  .blok-2kolommen .kolom { padding:0 8px; }
  .kolom-resizer { width:8px; cursor:ew-resize; background:rgba(45,189,110,.15); display:flex; align-items:center; justify-content:center; flex-shrink:0; }
  .kolom-resizer:hover { background:rgba(45,189,110,.35); }

  /* PRIJSTABEL */
  .blok-prijstabel { width:100%; overflow-x:auto; }
  .pt-tabel { width:100%; border-collapse:collapse; font-size:.85rem; }
  .pt-tabel th { background:var(--green-800); color:#fff; padding:8px 10px; text-align:left; font-weight:600; font-size:.78rem; }
  .pt-tabel td { padding:7px 10px; border-bottom:1px solid #f3f4f6; vertical-align:top; }
  .pt-tabel tbody tr:hover { background:#f9fafb; }
  .pt-tabel tfoot td { border-top:2px solid #e5e7eb; font-size:.85rem; }
  .pt-subtotaal-rij td { border-top:2px solid #e5e7eb; }
  .pt-totaal-rij td { background:#f0fdf4; }
  .pt-omschrijving { width:45%; }
  .pt-aantal, .pt-prijs, .pt-btw { width:13%; }
  .pt-totaal, .pt-bedrag { width:16%; text-align:right; }
  .pt-label { text-align:right; font-weight:500; color:#555; }
  .pt-optioneel { display:inline-block; font-size:.65rem; font-weight:700; text-transform:uppercase;
    letter-spacing:.05em; background:#fef3c7; color:#92400e; padding:1px 6px;
    border-radius:10px; margin-left:6px; vertical-align:middle; }

  /* STANDAARD TABEL */
  .blok-standaard-tabel table { width:100%; border-collapse:collapse; font-size:.85rem; }
  .blok-standaard-tabel th { background:#f8fafc; color:#374151; padding:8px 10px; text-align:left;
    font-weight:600; border:1px solid #e5e7eb; }
  .blok-standaard-tabel td { padding:7px 10px; border:1px solid #e5e7eb; }

  /* AFBEELDING */
  .blok-afbeelding img { max-width:100%; height:auto; display:block; }
  .blok-afbeelding--leeg { background:#f8fafc; border:2px dashed #e5e7eb; border-radius:8px;
    padding:40px; text-align:center; color:#94a3b8; font-size:.85rem; }
  .afbeelding-upload-area { position:relative; }
  .afbeelding-upload-area input[type=file] { display:none; }
</style>
