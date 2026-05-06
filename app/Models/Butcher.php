<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Butcher extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'address',
        'city',
        'postal_code',
        'phone',
        'email',
        'opening_hours',
        'website',
        'owner',
        'specialties',
        'average_rating',
        'review_count',
    ];

    public function stocks(): BelongsToMany
    {
        return $this->belongsToMany(Stock::class, 'butcherstocks');
    }
}
