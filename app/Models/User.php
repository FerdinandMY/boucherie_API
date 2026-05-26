<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, HasRoles, Notifiable;

    protected $fillable = ['name', 'email', 'password', 'boucherie_id'];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
    ];

    public function boucherie(): BelongsTo
    {
        return $this->belongsTo(Boucherie::class, 'boucherie_id');
    }

    public function fournisseur(): HasOne
    {
        return $this->hasOne(Fournisseur::class, 'user_id');
    }

    public function ventes(): HasMany
    {
        return $this->hasMany(Vente::class, 'user_id');
    }

    public function abattages(): HasMany
    {
        return $this->hasMany(Abattage::class, 'user_id');
    }

    public function paiements(): HasMany
    {
        return $this->hasMany(Paiement::class, 'user_id');
    }

    public function receptions(): HasMany
    {
        return $this->hasMany(Reception::class, 'user_id');
    }

    public function mouvementsStock(): HasMany
    {
        return $this->hasMany(MouvementStock::class, 'user_id');
    }
}
