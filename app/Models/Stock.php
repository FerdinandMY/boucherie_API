<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Stock extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['boucherie_id', 'produit_id', 'abattage_id', 'quantite', 'seuil_alerte'];

    protected $casts = [
        'quantite'     => 'decimal:3',
        'seuil_alerte' => 'decimal:3',
    ];

    public function boucherie(): BelongsTo
    {
        return $this->belongsTo(Boucherie::class, 'boucherie_id');
    }

    public function produit(): BelongsTo
    {
        return $this->belongsTo(Produit::class, 'produit_id');
    }

    public function abattage(): BelongsTo
    {
        return $this->belongsTo(Abattage::class, 'abattage_id');
    }

    public function mouvements(): HasMany
    {
        return $this->hasMany(MouvementStock::class, 'stock_id');
    }

    public function isEnAlerte(): bool
    {
        return (float) $this->quantite <= (float) $this->seuil_alerte;
    }
}
