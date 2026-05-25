<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\HasAttachments;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Boucherie extends Model
{
    use HasAttachments, HasFactory, HasUuids;

    protected $fillable = ['nom', 'adresse', 'ville', 'telephone', 'actif'];

    protected $casts = ['actif' => 'boolean'];

    /** Fournisseur unique desservant cette boucherie (contrainte pivot). */
    public function fournisseurAssigne(): BelongsToMany
    {
        return $this->belongsToMany(
            Fournisseur::class,
            'fournisseur_boucherie',
            'boucherie_id',
            'fournisseur_id',
        )->withTimestamps();
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'boucherie_id');
    }

    public function produits(): HasMany
    {
        return $this->hasMany(Produit::class, 'boucherie_id');
    }

    public function stocks(): HasMany
    {
        return $this->hasMany(Stock::class, 'boucherie_id');
    }

    public function clients(): HasMany
    {
        return $this->hasMany(Client::class, 'boucherie_id');
    }

    public function ventes(): HasMany
    {
        return $this->hasMany(Vente::class, 'boucherie_id');
    }

    public function animaux(): HasMany
    {
        return $this->hasMany(Animal::class, 'boucherie_id');
    }

    public function abattages(): HasMany
    {
        return $this->hasMany(Abattage::class, 'boucherie_id');
    }

    public function achatsFournisseurs(): HasMany
    {
        return $this->hasMany(AchatFournisseur::class, 'boucherie_id');
    }
}
