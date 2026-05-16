<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecetteLigne extends Model
{
    use HasUuids;

    protected $fillable = ['recette_id', 'categorie', 'poids_kg_vendu', 'prix_par_kg', 'montant'];

    protected $casts = [
        'poids_kg_vendu' => 'decimal:2',
        'prix_par_kg'    => 'decimal:2',
        'montant'        => 'decimal:2',
    ];

    public function recette(): BelongsTo
    {
        return $this->belongsTo(RecetteJournaliere::class, 'recette_id');
    }
}
