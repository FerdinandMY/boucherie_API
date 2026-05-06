<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Abattage extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'animal_id', 'user_id', 'boucherie_id',
        'date_abattage', 'poids_carcasse_kg', 'rendement_pct', 'notes',
    ];

    protected $casts = [
        'date_abattage'   => 'date',
        'poids_carcasse_kg' => 'decimal:2',
        'rendement_pct'   => 'decimal:2',
    ];

    public function animal(): BelongsTo
    {
        return $this->belongsTo(Animal::class, 'animal_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function boucherie(): BelongsTo
    {
        return $this->belongsTo(Boucherie::class, 'boucherie_id');
    }

    public function stocks(): HasMany
    {
        return $this->hasMany(Stock::class, 'abattage_id');
    }
}
