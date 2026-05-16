<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RecetteJournaliere extends Model
{
    use HasUuids;

    protected $fillable = [
        'boucherie_id', 'fournisseur_id', 'date',
        'montant_total', 'montant_verse', 'statut_versement', 'notes',
    ];

    protected $casts = [
        'date'          => 'date',
        'montant_total' => 'decimal:2',
        'montant_verse' => 'decimal:2',
    ];

    public function boucherie(): BelongsTo
    {
        return $this->belongsTo(Boucherie::class, 'boucherie_id');
    }

    public function fournisseur(): BelongsTo
    {
        return $this->belongsTo(Fournisseur::class, 'fournisseur_id');
    }

    public function lignes(): HasMany
    {
        return $this->hasMany(RecetteLigne::class, 'recette_id');
    }
}
