{{-- Dispatcher: geeft het juiste blok terug op basis van element.type --}}
{{-- Wordt altijd gebruikt binnen een x-for op 'element' --}}
<template x-if="element.type === 'tekst'">
  @include('document.blokken.tekst')
</template>
<template x-if="element.type === 'tekst_2kolommen'">
  @include('document.blokken.tekst-2kolommen')
</template>
<template x-if="element.type === 'tekst_afbeelding'">
  @include('document.blokken.tekst-afbeelding')
</template>
<template x-if="element.type === 'afbeelding'">
  @include('document.blokken.afbeelding')
</template>
<template x-if="element.type === 'prijstabel'">
  @include('document.blokken.prijstabel')
</template>
<template x-if="element.type === 'standaard_tabel'">
  @include('document.blokken.standaard-tabel')
</template>
