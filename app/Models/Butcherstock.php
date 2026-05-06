<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Butcherstock extends Model
{
    use HasFactory;

    protected $fillable = [
        'butcher_id',
        'stock_id',
        'quantity',
        'price',
    ];
}
