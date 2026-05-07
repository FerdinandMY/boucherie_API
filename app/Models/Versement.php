<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Versement extends Model
{
    use HasUuids;

    protected $fillable = [
        'boucherie_id', 'fournisseur_user_id', 'montant',
        'mode_paiement', 'date_versement', 'reference',
        'statut', 'motif_rejet', 'notes', 'valide_par', 'valide_le',
    ];

    protected $casts = [
        'montant'       => 'decimal:2',
        'date_versement' => 'date',
        'valide_le'     => 'datetime',
    ];

    public function boucherie(): BelongsTo
    {
        return $this->belongsTo(Boucherie::class, 'boucherie_id');
    }

    public function fournisseurUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'fournisseur_user_id');
    }

    public function validePar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'valide_par');
    }
}
