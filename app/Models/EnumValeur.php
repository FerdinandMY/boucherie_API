<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EnumValeur extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'enum_valeurs';

    protected $fillable = ['type', 'valeur', 'libelle', 'systeme', 'boucherie_id', 'ordre'];

    protected $casts = ['systeme' => 'boolean', 'ordre' => 'integer'];

    public function boucherie(): BelongsTo
    {
        return $this->belongsTo(Boucherie::class, 'boucherie_id');
    }
}
