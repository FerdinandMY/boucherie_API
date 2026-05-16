<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DistributionLigne extends Model
{
    use HasUuids;

    protected $fillable = ['distribution_id', 'categorie', 'poids_kg', 'prix_par_kg'];

    protected $casts = [
        'poids_kg'    => 'decimal:2',
        'prix_par_kg' => 'decimal:2',
    ];

    public function distribution(): BelongsTo
    {
        return $this->belongsTo(Distribution::class, 'distribution_id');
    }
}
