<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReceptionLigne extends Model
{
    use HasUuids;

    protected $fillable = ['reception_id', 'categorie', 'poids_kg_attendu', 'poids_kg_recu'];

    protected $casts = [
        'poids_kg_attendu' => 'decimal:2',
        'poids_kg_recu'    => 'decimal:2',
    ];

    public function reception(): BelongsTo
    {
        return $this->belongsTo(Reception::class, 'reception_id');
    }
}
