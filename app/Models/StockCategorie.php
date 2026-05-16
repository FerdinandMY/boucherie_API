<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockCategorie extends Model
{
    use HasUuids;

    protected $fillable = ['boucherie_id', 'categorie', 'poids_kg_disponible'];

    protected $casts = [
        'poids_kg_disponible' => 'decimal:3',
    ];

    public function boucherie(): BelongsTo
    {
        return $this->belongsTo(Boucherie::class, 'boucherie_id');
    }
}
