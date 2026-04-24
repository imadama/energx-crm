<!DOCTYPE html>
<html lang="nl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ $offerteTemplate->naam }} — Template Editor | Energx</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
  <script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
  @include('document.editor-styles')
</head>
<body>

@php
  $doc         = $offerteTemplate->document ?? \App\Services\DocumentRenderer::leegDocument();
  $productenJs = $producten->map(fn($p) => [
    'id'       => $p->id,
    'naam'     => $p->naam,
    'prijs'    => (float)$p->prijs,
    'categorie'=> $p->categorie,
  ])->values()->toArray();
@endphp

<div x-data="pageEditor(
  @js($doc),
  [],
  @js($productenJs),
  '{{ route('offerte-templates.document.update', $offerteTemplate) }}',
  '#', '#',
  '{{ route('upload.afbeelding') }}',
  @js($tokens)
)" x-init="init()" class="editor-root">

  <div class="topbar">
    <div class="topbar-left">
      <a href="{{ route('offerte-templates.index') }}" class="btn-sm btn-secondary">← Terug</a>
      <span class="topbar-title">Template: {{ $offerteTemplate->naam }}</span>
    </div>
    <div class="topbar-right">
      <span class="save-status" :class="saveStatus" x-text="saveLabel"></span>
    </div>
  </div>

  <div class="left-panel">
    <div class="panel-section">
      <div class="panel-label">Pagina's</div>
      <template x-for="(pagina, pi) in document.paginas" :key="pagina.id">
        <div class="page-thumb" :class="{ active: activePaginaIndex === pi }" @click="scrollToPagina(pi)">
          <span x-text="'Pagina ' + (pi + 1)"></span>
          <div class="page-thumb-actions">
            <button @click.stop="openPaginaInstellingen(pi)" class="icon-btn">⚙</button>
            <button @click.stop="verwijderPagina(pi)" class="icon-btn icon-btn--danger" x-show="document.paginas.length > 1">✕</button>
          </div>
        </div>
      </template>
      <button @click="voegPaginaToe()" class="add-btn">+ Pagina toevoegen</button>
    </div>
    <div class="panel-section">
      <div class="panel-label">Tokens</div>
      <template x-for="(groep, naam) in tokens" :key="naam">
        <div>
          <div class="token-groep-naam" x-text="naam"></div>
          <template x-for="t in groep" :key="t.token">
            <button class="token-btn" @click="kopieerToken(t.token, t.label)"><span x-text="t.label"></span></button>
          </template>
        </div>
      </template>
      <div class="token-tip" x-show="tokenKopieerdLabel" x-text="'✓ ' + tokenKopieerdLabel + ' gekopieerd'" style="display:none"></div>
    </div>
  </div>

  <div class="canvas" id="editor-canvas">
    <template x-for="(pagina, pi) in document.paginas" :key="pagina.id">
      <div class="a4-pagina-wrapper" :id="'pagina-' + pi">
        <div class="pagina-label" x-text="'Pagina ' + (pi + 1)"></div>
        <div class="a4-pagina" :style="paginaStijl(pagina)">
          <div :id="'sortable-' + pagina.id" class="elementen-container">
            <template x-for="(element, ei) in pagina.elementen" :key="element.id">
              <div class="document-element-wrapper" :data-element-id="element.id" :class="{ 'is-actief': activeElementId === element.id }">
                <div class="element-toolbar">
                  <span class="element-type-label" x-text="elementLabel(element.type)"></span>
                  <div class="element-toolbar-actions">
                    <button @click="verplaatsElement(pi, ei, -1)" :disabled="ei === 0" class="icon-btn">↑</button>
                    <button @click="verplaatsElement(pi, ei, 1)" :disabled="ei === pagina.elementen.length - 1" class="icon-btn">↓</button>
                    <button @click="openElementInstellingen(pi, ei)" class="icon-btn">⚙</button>
                    <button @click="verwijderElement(pi, ei)" class="icon-btn icon-btn--danger">✕</button>
                  </div>
                </div>
                <div class="element-outer" :style="elementOuterStijl(element)">
                  <div class="content-resizer" :data-elem-id="element.id" :data-pagina-id="pagina.id">
                    <div class="resize-handle resize-handle--left" @mousedown.prevent="startResize($event, pagina.id, element.id, 'left')"></div>
                    <div class="document-content" :style="contentStijl(element)" @click="activeElementId = element.id">
                      @include('document.blokken.dispatcher')
                    </div>
                    <div class="resize-handle resize-handle--right" @mousedown.prevent="startResize($event, pagina.id, element.id, 'right')"></div>
                  </div>
                </div>
              </div>
            </template>
          </div>
          <div class="add-element-bar">
            <div x-data="{ open: false }" class="add-element-dropdown">
              <button @click="open = !open" class="add-btn add-btn--element">+ Element toevoegen</button>
              <div x-show="open" @click.outside="open = false" class="element-type-menu" style="display:none">
                <template x-for="type in elementTypen" :key="type.type">
                  <button @click="voegElementToe(pi, type.type); open = false" class="element-type-item">
                    <span x-html="type.icon" style="margin-right:6px;opacity:.7"></span>
                    <span x-text="type.label"></span>
                  </button>
                </template>
              </div>
            </div>
          </div>
        </div>
      </div>
    </template>
  </div>

  <div class="right-panel" :class="{ open: rightPanel !== null }">
    <template x-if="rightPanel === 'pagina' && activePaginaIndex !== null">
      <div class="panel-content">
        <div class="panel-header"><strong>Pagina instellingen</strong><button @click="rightPanel = null" class="icon-btn">✕</button></div>
        <div class="instellingen-form">
          <label class="form-label">Marges (px)</label>
          <div class="marge-grid">
            <div><small>Boven</small><input type="number" x-model.number="document.paginas[activePaginaIndex].instellingen.marge.top" @change="save()" class="form-input"></div>
            <div><small>Rechts</small><input type="number" x-model.number="document.paginas[activePaginaIndex].instellingen.marge.right" @change="save()" class="form-input"></div>
            <div><small>Onder</small><input type="number" x-model.number="document.paginas[activePaginaIndex].instellingen.marge.bottom" @change="save()" class="form-input"></div>
            <div><small>Links</small><input type="number" x-model.number="document.paginas[activePaginaIndex].instellingen.marge.left" @change="save()" class="form-input"></div>
          </div>
          <label class="form-label">Achtergrondkleur</label>
          <input type="color" x-model="document.paginas[activePaginaIndex].instellingen.achtergrond_kleur" @change="save()" class="form-color">
          <label class="form-label">Achtergrond afbeelding URL</label>
          <input type="text" x-model="document.paginas[activePaginaIndex].instellingen.achtergrond_afbeelding" @change="save()" placeholder="/storage/..." class="form-input">
        </div>
      </div>
    </template>
    <template x-if="rightPanel === 'element' && activePaginaIndex !== null && activeElementIndex !== null">
      <div class="panel-content">
        <div class="panel-header"><strong>Element instellingen</strong><button @click="rightPanel = null" class="icon-btn">✕</button></div>
        <div class="instellingen-form">
          <label class="form-label">Achtergrondkleur</label>
          <input type="color"
            :value="document.paginas[activePaginaIndex].elementen[activeElementIndex].instellingen.achtergrond_kleur || '#ffffff'"
            @change="document.paginas[activePaginaIndex].elementen[activeElementIndex].instellingen.achtergrond_kleur = $event.target.value; save()"
            class="form-color">
          <label class="form-label">Padding boven (px)</label>
          <input type="number" x-model.number="document.paginas[activePaginaIndex].elementen[activeElementIndex].instellingen.marge_top" @change="save()" class="form-input">
          <label class="form-label">Padding onder (px)</label>
          <input type="number" x-model.number="document.paginas[activePaginaIndex].elementen[activeElementIndex].instellingen.marge_bottom" @change="save()" class="form-input">
          <label class="form-label">Content breedte: <span x-text="document.paginas[activePaginaIndex].elementen[activeElementIndex].instellingen.content_breedte_pct + '%'"></span></label>
          <input type="range" min="10" max="100" x-model.number="document.paginas[activePaginaIndex].elementen[activeElementIndex].instellingen.content_breedte_pct" @change="save()" class="form-range">
          <label class="form-label">Uitlijning</label>
          <div class="uitlijning-knoppen">
            <button @click="zetUitlijning(activePaginaIndex, activeElementIndex, 'left')" class="uitlijning-btn">Links</button>
            <button @click="zetUitlijning(activePaginaIndex, activeElementIndex, 'center')" class="uitlijning-btn">Midden</button>
            <button @click="zetUitlijning(activePaginaIndex, activeElementIndex, 'right')" class="uitlijning-btn">Rechts</button>
          </div>
          <label class="form-label">Offset links: <span x-text="document.paginas[activePaginaIndex].elementen[activeElementIndex].instellingen.content_offset_pct + '%'"></span></label>
          <input type="range" min="0"
            :max="100 - document.paginas[activePaginaIndex].elementen[activeElementIndex].instellingen.content_breedte_pct"
            x-model.number="document.paginas[activePaginaIndex].elementen[activeElementIndex].instellingen.content_offset_pct"
            @change="save()" class="form-range">
        </div>
      </div>
    </template>
  </div>

</div>

@include('document.editor-script')
</body>
</html>
