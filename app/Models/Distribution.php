<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Distribution extends Model
{
    use HasUuids;

    protected $fillable = [
        'abattage_id', 'fournisseur_user_id', 'boucherie_id',
        'produit_id', 'quantite', 'statut', 'notes',
    ];

    protected $casts = [
        'quantite' => 'decimal:3',
    ];

    public function abattage(): BelongsTo
    {
        return $this->belongsTo(Abattage::class, 'abattage_id');
    }

    public function fournisseurUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'fournisseur_user_id');
    }

    public function boucherie(): BelongsTo
    {
        return $this->belongsTo(Boucherie::class, 'boucherie_id');
    }

    public function produit(): BelongsTo
    {
        return $this->belongsTo(Produit::class, 'produit_id');
    }

    public function reception(): HasOne
    {
        return $this->hasOne(Reception::class, 'distribution_id');
    }
}
