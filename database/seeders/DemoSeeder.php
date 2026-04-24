<?php

namespace Database\Seeders;

use App\Models\Klant;
use App\Models\Offerte;
use App\Models\Product;
use App\Services\DocumentRenderer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->updateOrInsert(
            ['email' => 'admin@energx.nl'],
            ['name' => 'Admin', 'email' => 'admin@energx.nl', 'password' => Hash::make('password'), 'email_verified_at' => now(), 'created_at' => now(), 'updated_at' => now()]
        );
        $this->command->info('Gebruiker aangemaakt: admin@energx.nl / password');

        $zaptec = Product::updateOrCreate(['naam' => 'Zaptec Go - 22 kW'], ['prijs' => 599.00, 'categorie' => 'laadpaal', 'merk' => 'Zaptec', 'actief' => true]);
        $installatieStandaard = Product::updateOrCreate(['naam' => 'Standaard installatie (direct achter de meterkast)'], ['beschrijving' => "- Wandmontage\n- Aardlekautomaat\n- Tot 10m kabel", 'prijs' => 399.00, 'categorie' => 'installatie', 'actief' => true]);
        Product::updateOrCreate(['naam' => 'Standaard installatie (kruipruimte)'], ['beschrijving' => "- Via kruipruimte\n- Aardlekautomaat\n- Tot 10m kabel", 'prijs' => 489.00, 'categorie' => 'installatie', 'actief' => true]);
        $this->command->info('Producten aangemaakt: ' . Product::count() . ' stuks');

        $this->call(ZaptecTemplatesSeeder::class);

        $klant = Klant::updateOrCreate(['email' => 'imadamazyan@gmail.com'], ['naam' => 'Imad Amazyan', 'telefoon' => '0685023112', 'straat' => 'Karel de Stoutestraat', 'huisnummer' => '4', 'postcode' => '4205 HM', 'stad' => 'Gorinchem', 'bron' => 'website']);
        $this->command->info('Klant aangemaakt: ' . $klant->naam);

        if (Offerte::where('nummer', 'ENX-2026-0001')->exists()) {
            $this->command->info('Demo offerte bestaat al, overgeslagen.');
            return;
        }

        $template = \App\Models\OfferteTemplate::where('naam', 'like', '%Standaard installatie%')->first();
        $offerte = Offerte::create(['klant_id' => $klant->id, 'template_id' => $template?->id, 'status' => 'concept', 'geldig_tot' => now()->addDays(30), 'document' => $template?->document ?? DocumentRenderer::leegDocument()]);

        $offerte->regels()->create(['product_id' => $zaptec->id, 'naam' => $zaptec->naam, 'beschrijving' => 'Inclusief montagebeugel en 5 jaar garantie', 'type' => 'product', 'aantal' => 1, 'eenheid' => 'st.', 'eenheidsprijs' => $zaptec->prijs, 'btw_tarief' => 21, 'volgorde' => 0]);
        $offerte->regels()->create(['product_id' => $installatieStandaard->id, 'naam' => $installatieStandaard->naam, 'beschrijving' => 'Inclusief aardlekautomaat en bekabeling', 'type' => 'product', 'aantal' => 1, 'eenheid' => 'st.', 'eenheidsprijs' => $installatieStandaard->prijs, 'btw_tarief' => 21, 'volgorde' => 1]);

        $this->command->info('Demo offerte aangemaakt: ' . $offerte->nummer);
    }
}
