<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Vente extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'boucherie_id', 'client_id', 'user_id',
        'type_vente', 'statut', 'montant_total', 'notes',
    ];

    protected $casts = [
        'montant_total' => 'decimal:2',
    ];

    public function boucherie(): BelongsTo
    {
        return $this->belongsTo(Boucherie::class, 'boucherie_id');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function lignes(): HasMany
    {
        return $this->hasMany(LigneVente::class, 'vente_id');
    }

    public function paiements(): HasMany
    {
        return $this->hasMany(Paiement::class, 'vente_id');
    }

    public function livraison(): HasOne
    {
        return $this->hasOne(Livraison::class, 'vente_id');
    }
}
