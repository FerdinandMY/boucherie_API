<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AchatFournisseur extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'achats_fournisseurs';

    protected $fillable = ['boucherie_id', 'fournisseur_id', 'user_id', 'reference', 'montant_total', 'date_achat', 'notes'];

    protected $casts = [
        'montant_total' => 'decimal:2',
        'date_achat'    => 'date',
    ];

    public function boucherie(): BelongsTo
    {
        return $this->belongsTo(Boucherie::class, 'boucherie_id');
    }

    public function fournisseur(): BelongsTo
    {
        return $this->belongsTo(Fournisseur::class, 'fournisseur_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function animaux(): HasMany
    {
        return $this->hasMany(Animal::class, 'achat_fournisseur_id');
    }
}
