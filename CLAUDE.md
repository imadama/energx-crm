# CLAUDE.md — Energx CRM

Instructies voor Claude Code bij het werken in dit project.

## Wat is dit?

Laravel 13 CRM voor Energx.nl. Beheert klanten, producten, offertes en offerte templates. Offertes worden via een unieke link naar klanten gestuurd die ze digitaal kunnen accepteren.

## Stack

- **Backend:** Laravel 13, PHP 8.5, MySQL 8.4
- **Frontend:** Blade templates + Alpine.js (CDN), geen Vite/npm build voor de CRM
- **Docker:** Laravel Sail (`./vendor/bin/sail`)
- **Mail (dev):** Mailpit op `http://localhost:8025`

## Commando's

```bash
./vendor/bin/sail up -d          # containers starten
./vendor/bin/sail down           # containers stoppen
./vendor/bin/sail artisan ...    # artisan commando's
./vendor/bin/sail mysql          # MySQL shell

# Database resetten + demodata
./vendor/bin/sail artisan migrate:fresh --seed
```

## Architectuurpatronen

### Nederlandse tabelnamen
Alle Eloquent models hebben `protected $table = '...'` omdat Laravel Engelse enkelvoudsvormen genereert die niet kloppen voor Nederlands:
- `Klant` → `$table = 'klanten'`
- `Product` → `$table = 'producten'`
- `Offerte` → `$table = 'offertes'`
- `OfferteRegel` → `$table = 'offerte_regels'`
- `OfferteSectie` → `$table = 'offerte_secties'`
- `OfferteTemplate` → `$table = 'offerte_templates'`

### Resource routes met parameters
`Route::resource` gebruikt standaard de Engelse enkelvoudsvorm als parameternaam. Voor Nederlandse resource-namen expliciet instellen:
```php
Route::resource('klanten', KlantController::class)->parameters(['klanten' => 'klant']);
Route::resource('producten', ProductController::class)->parameters(['producten' => 'product']);
```

### Layout component
De hoofd-layout is een anonieme Blade component op:
`resources/views/components/layouts/crm.blade.php`

Gebruik in views: `<x-layouts.crm title="...">`. **Niet** `resources/views/layouts/`.

### JSON sectie-inhoud
`offerte_secties.inhoud` en `offerte_template_secties.inhoud` zijn JSON-kolommen, gecast als `array` in de models. De structuur verschilt per sectie-type:

| Type | Inhoud structuur |
|---|---|
| `voorblad` | `[]` (leeg, data komt uit offerte) |
| `introductie` / `tekst` | `{ tekst: "..." }` |
| `prijzen` | `[]` (toont offerte_regels) |
| `product` | `{ beschrijving: "...", specs: [{label, waarde}] }` |
| `werkwijze` | `{ stappen: [{titel, beschrijving}] }` |
| `acceptatie` | `{ tekst: "..." }` |

### Alpine.js in Blade
CDN-versie, geen npm. Gebruik `x-data`, `x-model`, `x-show`, `x-for`, `x-if`.

**Let op bij `@json()` in Blade:** Blade's haakjesteller raakt in de war bij complexe PHP-expressies met geneste closures en casts. Bereid data voor in een `@php` blok:
```blade
@php
  $data = $collection->map(fn($item) => [...]);
@endphp
<script>
  const data = @json($data);
</script>
```

### Offerte token & nummering
Auto-gegenereerd in `Offerte::booted()`:
- `token` → `Str::random(32)` — voor publieke viewer URL
- `nummer` → `ENX-{jaar}-{volgnummer}` (bijv. ENX-2026-0001)

### Totalen berekening
`Offerte::berekenTotalen()` herberekent subtotaal/btw/totaal op basis van alle regels. Wordt automatisch aangeroepen via model events in `OfferteRegel`.

## Bestandsstructuur (belangrijk)

```
app/Http/Controllers/
  OfferteController.php          # CRUD + editor() + updateRegels() + viewer() + accepteer()
  OfferteSectieController.php    # AJAX API: store/update/destroy/reorder secties
  OfferteTemplateController.php  # CRUD templates + parseSectieInhoud()
  KlantController.php
  ProductController.php
  DashboardController.php

resources/views/
  components/layouts/crm.blade.php   # hoofd-layout CRM
  auth/login.blade.php               # custom login (geen Vite)
  offertes/
    viewer.blade.php                 # publieke klantviewer (geen auth)
    editor.blade.php                 # inline editor (Alpine.js + AJAX)
  offerte-templates/
    create.blade.php / edit.blade.php  # Alpine.js template builder

database/seeders/
  DemoSeeder.php          # gebruiker + producten + klant + offerte
  ZaptecTemplatesSeeder.php  # Zaptec Go templates met volledige secties
```

## Routes structuur

```
/dashboard                          auth
/klanten                            resource (param: klant)
/producten                          resource (param: product)
/offertes                           resource
/offertes/{offerte}/editor          GET  — inline editor
/offertes/{offerte}/regels          PATCH — AJAX regels update
/offertes/{offerte}/secties         POST  — AJAX sectie toevoegen
/offertes/{offerte}/secties/{id}    PATCH/DELETE — AJAX sectie update/delete
/offertes/{offerte}/secties-volgorde PATCH — AJAX volgorde opslaan
/offerte-templates                  resource (except show)
/offerte/{token}                    publiek — viewer
/offerte/{token}/accepteer          publiek POST — digitale acceptatie
```

## Design systeem

CSS-variabelen gedefinieerd in `crm.blade.php`:
- `--green-800: #0F4A2A` — primair (sidebar, knoppen)
- `--green-400: #2DBD6E` — accent (highlights, badges)
- `--green-100` / `--green-600` — lichte/donkere varianten
- Fonts: `DM Serif Display` (koppen) + `Outfit` (body) via Google Fonts

## Inloggegevens (development)

```
URL:       http://localhost
E-mail:    admin@energx.nl
Wachtwoord: password
Mailpit:   http://localhost:8025
```
