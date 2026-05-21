<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\Abattage;
use App\Models\AchatFournisseur;
use App\Models\Animal;
use App\Models\Attachment;
use App\Models\Client;
use App\Models\Fournisseur;
use App\Models\Produit;
use App\Models\Stock;
use App\Models\Vente;
use App\Policies\AbattagePolicy;
use App\Policies\AttachmentPolicy;
use App\Policies\AchatFournisseurPolicy;
use App\Policies\AnimalPolicy;
use App\Policies\ClientPolicy;
use App\Policies\FournisseurPolicy;
use App\Policies\ProduitPolicy;
use App\Policies\StockPolicy;
use App\Policies\VentePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Attachment::class      => AttachmentPolicy::class,
        Vente::class           => VentePolicy::class,
        Fournisseur::class     => FournisseurPolicy::class,
        Client::class          => ClientPolicy::class,
        Produit::class         => ProduitPolicy::class,
        AchatFournisseur::class => AchatFournisseurPolicy::class,
        Animal::class          => AnimalPolicy::class,
        Abattage::class        => AbattagePolicy::class,
        Stock::class           => StockPolicy::class,
    ];

    public function boot(): void
    {
        //
    }
}
