<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LigneVente extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'lignes_vente';

    protected $fillable = ['vente_id', 'produit_id', 'quantite', 'prix_unitaire', 'sous_total'];

    protected $casts = [
        'quantite'     => 'decimal:3',
        'prix_unitaire' => 'decimal:2',
        'sous_total'   => 'decimal:2',
    ];

    public function vente(): BelongsTo
    {
        return $this->belongsTo(Vente::class, 'vente_id');
    }

    public function produit(): BelongsTo
    {
        return $this->belongsTo(Produit::class, 'produit_id');
    }
}
