# Energx CRM

Offerte- en klantbeheersysteem voor Energx.nl. Gebouwd met Laravel 13, MySQL en Docker (Laravel Sail).

---

## Wat zit erin?

| Module | Beschrijving |
|---|---|
| **Klanten** | Contactbeheer met adres, bron en notities |
| **Producten** | Productcatalogus met categorie en prijs |
| **Offerte templates** | Herbruikbare sjablonen met secties en standaard prijsregels |
| **Offertes** | Offerte aanmaken vanuit template, versturen en digitaal laten accepteren |
| **Offerte editor** | Inline bewerkingsscherm — zelfde layout als de klantviewer |
| **Offerte viewer** | Publieke pagina voor de klant met digitale acceptatie |

---

## Vereisten

| Tool | Versie | Download |
|---|---|---|
| **Docker Desktop** | 4.x of nieuwer | [docker.com/products/docker-desktop](https://www.docker.com/products/docker-desktop/) |
| **Git** | Niet vereist, maar handig | — |
| **PHP** (optioneel) | 8.3+ | Alleen nodig als je Composer lokaal draait |

> Docker Desktop is de enige harde vereiste. Alles draait in containers.

---

## Installatie (nieuwe laptop)

### Stap 1 — Project klonen

```bash
git clone https://github.com/jouw-org/energx-crm.git
cd energx-crm
```

Of kopieer de projectmap handmatig naar de nieuwe laptop.

---

### Stap 2 — `.env` aanmaken

```bash
cp .env.example .env
```

Het `.env.example` bestand is al ingesteld voor Docker/Sail. Je hoeft niets te wijzigen.

---

### Stap 3 — Composer dependencies installeren

**Optie A — met lokale PHP:**
```bash
composer install
```

**Optie B — zonder lokale PHP (via Docker):**
```bash
docker run --rm \
  -u "$(id -u):$(id -g)" \
  -v "$(pwd):/var/www/html" \
  -w /var/www/html \
  laravelsail/php85-composer:latest \
  composer install --ignore-platform-reqs
```

---

### Stap 4 — Docker containers opstarten

```bash
./vendor/bin/sail up -d
```

De eerste keer duurt dit 2–5 minuten omdat Docker de image bouwt.

> **Let op:** Zorg dat Docker Desktop open en actief is voordat je dit commando uitvoert.

---

### Stap 5 — App key genereren

```bash
./vendor/bin/sail artisan key:generate
```

---

### Stap 6 — Database aanmaken en migrations uitvoeren

```bash
./vendor/bin/sail artisan migrate
```

---

### Stap 7 — Demodata inladen

```bash
./vendor/bin/sail artisan db:seed
```

Dit maakt aan:
- Admin gebruiker (`admin@energx.nl` / `password`)
- 3 producten (Zaptec Go, 2 installatiepakketten)
- 2 offerte templates (Zaptec Go — Standaard installatie / Kruipruimte)
- 1 demo klant (Imad Amazyan)
- 1 demo offerte (ENX-2026-0001, concept)

---

### Stap 8 — Klaar!

Open de browser en ga naar:

| URL | Omschrijving |
|---|---|
| `http://localhost` | Login pagina CRM |
| `http://localhost:8025` | Mailpit (e-mails bekijken) |

**Inloggegevens:**
```
E-mail:    admin@energx.nl
Wachtwoord: password
```

---

## Dagelijks gebruik

### Containers starten

```bash
./vendor/bin/sail up -d
```

### Containers stoppen

```bash
./vendor/bin/sail down
```

### In de container werken (artisan, composer, etc.)

```bash
# Artisan commando
./vendor/bin/sail artisan <commando>

# Composer
./vendor/bin/sail composer <commando>

# MySQL shell
./vendor/bin/sail mysql
```

---

## Korte alias instellen (optioneel maar handig)

Voeg dit toe aan `~/.zshrc` of `~/.bashrc`:

```bash
alias sail='[ -f sail ] && sh sail || sh vendor/bin/sail'
```

Daarna kun je gewoon `sail up -d` gebruiken in plaats van `./vendor/bin/sail up -d`.

---

## Projectstructuur

```
energx-crm/
├── app/
│   ├── Http/Controllers/
│   │   ├── DashboardController.php
│   │   ├── KlantController.php
│   │   ├── OfferteController.php        ← CRUD + editor + viewer + accepteer
│   │   ├── OfferteSectieController.php  ← AJAX API voor editor (secties)
│   │   ├── OfferteTemplateController.php
│   │   └── ProductController.php
│   └── Models/
│       ├── Klant.php
│       ├── Offerte.php                  ← auto nummer + token, berekenTotalen()
│       ├── OfferteSectie.php
│       ├── OfferteRegel.php
│       ├── OfferteTemplate.php
│       ├── OfferteTemplateSectie.php
│       ├── OfferteTemplateRegel.php
│       └── Product.php
├── database/
│   ├── migrations/                      ← alle tabellen
│   └── seeders/
│       ├── DatabaseSeeder.php           ← roept DemoSeeder aan
│       ├── DemoSeeder.php               ← gebruiker, producten, klant, offerte
│       └── ZaptecTemplatesSeeder.php    ← Zaptec Go templates met secties
├── resources/views/
│   ├── auth/login.blade.php
│   ├── components/layouts/crm.blade.php ← hoofd-layout (sidebar, topbar, CSS)
│   ├── offertes/
│   │   ├── index.blade.php
│   │   ├── create.blade.php
│   │   ├── edit.blade.php
│   │   ├── show.blade.php
│   │   ├── editor.blade.php             ← inline editor (Alpine.js + AJAX)
│   │   └── viewer.blade.php             ← publieke viewer voor klant
│   ├── offerte-templates/
│   │   ├── index.blade.php
│   │   ├── create.blade.php
│   │   └── edit.blade.php
│   ├── klanten/
│   └── producten/
├── routes/web.php
├── compose.yaml                         ← Docker Sail configuratie
└── .env.example                         ← kopieer naar .env
```

---

## Routes overzicht

### CRM (vereist login)

| Methode | URL | Beschrijving |
|---|---|---|
| GET | `/dashboard` | Dashboard |
| GET/POST | `/klanten` | Klantenoverzicht en aanmaken |
| GET/POST | `/producten` | Productcatalogus |
| GET/POST | `/offertes` | Offerteoverzicht en aanmaken |
| GET | `/offertes/{id}/editor` | Inline editor |
| GET/POST | `/offerte-templates` | Template beheer |

### Publiek (geen login)

| Methode | URL | Beschrijving |
|---|---|---|
| GET | `/offerte/{token}` | Offerte viewer voor klant |
| POST | `/offerte/{token}/accepteer` | Digitale acceptatie |

---

## Database tabellen

| Tabel | Omschrijving |
|---|---|
| `users` | Admin gebruikers |
| `klanten` | Klantgegevens |
| `producten` | Productcatalogus |
| `offertes` | Offertes (auto nummer ENX-YYYY-NNNN, uniek token) |
| `offerte_regels` | Prijsregels per offerte |
| `offerte_secties` | Secties per offerte (inhoud als JSON) |
| `offerte_templates` | Herbruikbare templates |
| `offerte_template_secties` | Secties per template |
| `offerte_template_regels` | Standaard prijsregels per template |

---

## Problemen oplossen

### "Docker daemon not running"
Open Docker Desktop en wacht tot het groene icoontje verschijnt.

### "Port 80 already in use"
Een andere app gebruikt poort 80. Pas in `.env` aan:
```
APP_PORT=8080
```
En open `http://localhost:8080`.

### "Permission denied" bij `./vendor/bin/sail`
```bash
chmod +x vendor/bin/sail
```

### Containers resetten (alles opnieuw)
```bash
./vendor/bin/sail down -v        # verwijdert ook de database volumes
./vendor/bin/sail up -d
./vendor/bin/sail artisan migrate
./vendor/bin/sail artisan db:seed
```

### Seed opnieuw draaien zonder alles te wissen
De seeder gebruikt `updateOrCreate` — je kunt hem veilig meerdere keren draaien:
```bash
./vendor/bin/sail artisan db:seed
```

---

## Technische details

| Onderdeel | Keuze | Reden |
|---|---|---|
| Framework | Laravel 13 | Volwassen PHP framework, goede ORM |
| PHP versie | 8.5 | Nieuwste stabiele versie in Sail |
| Database | MySQL 8.4 | Via Docker, geen lokale installatie nodig |
| Frontend | Blade + Alpine.js | Geen build-stap, simpel en snel |
| Mail (dev) | Mailpit | Vangt alle e-mails lokaal op, nooit per ongeluk verzonden |
| Auth | Laravel Breeze (custom) | Simpele sessie-auth, eigen login design |
| Offerte token | `Str::random(32)` | Unieke publieke link zonder login |
| Sectie inhoud | JSON kolom | Flexibel per sectie-type (specs, stappen, tekst) |
