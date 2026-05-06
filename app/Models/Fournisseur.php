<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Fournisseur extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['boucherie_id', 'nom', 'contact', 'telephone', 'email', 'adresse'];

    public function animaux(): HasMany
    {
        return $this->hasMany(Animal::class, 'fournisseur_id');
    }

    public function achatsFournisseurs(): HasMany
    {
        return $this->hasMany(AchatFournisseur::class, 'fournisseur_id');
    }
}
