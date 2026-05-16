<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AbattageLigne extends Model
{
    use HasUuids;

    protected $fillable = ['abattage_id', 'categorie', 'poids_kg'];

    protected $casts = [
        'poids_kg' => 'decimal:2',
    ];

    public function abattage(): BelongsTo
    {
        return $this->belongsTo(Abattage::class, 'abattage_id');
    }
}
