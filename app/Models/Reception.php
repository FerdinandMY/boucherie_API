<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\HasAttachments;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Reception extends Model
{
    use HasAttachments, HasFactory, HasUuids;

    protected $fillable = [
        'distribution_id', 'boucherie_id', 'user_id',
        'quantite_recue', 'date_reception', 'notes',
    ];

    protected $casts = [
        'quantite_recue' => 'decimal:3',
        'date_reception' => 'date',
    ];

    public function distribution(): BelongsTo
    {
        return $this->belongsTo(Distribution::class, 'distribution_id');
    }

    public function boucherie(): BelongsTo
    {
        return $this->belongsTo(Boucherie::class, 'boucherie_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function lignes(): HasMany
    {
        return $this->hasMany(ReceptionLigne::class, 'reception_id');
    }
}
