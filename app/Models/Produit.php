<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Produit extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['boucherie_id', 'nom', 'description', 'categorie', 'unite', 'prix_unitaire'];

    protected $casts = [
        'prix_unitaire' => 'decimal:2',
    ];

    public function boucherie(): BelongsTo
    {
        return $this->belongsTo(Boucherie::class, 'boucherie_id');
    }

    public function stocks(): HasMany
    {
        return $this->hasMany(Stock::class, 'produit_id');
    }
}
