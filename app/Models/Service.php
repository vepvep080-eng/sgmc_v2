<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Service extends Model
{
    protected $fillable = [
        'name',
        'code',
        'department_id',
        'manager_id',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function sections(): HasMany
    {
        return $this->hasMany(Section::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    /**
     * Chef(s) de ce service : users rattachés (service_id) avec le rôle chef_service.
     * Sert le "Envoyer à" du chef de département (filtré par département).
     */
    public function chefs(): HasMany
    {
        $chefServiceRoleId = Role::query()->where('slug', Role::CHEF_SERVICE)->value('id');

        return $this->hasMany(User::class)->when(
            $chefServiceRoleId,
            fn (Builder $q, $roleId) => $q->where('role_id', $roleId)
        );
    }

    /**
     * Le chef du service (convention : premier chef_service rattaché,
     * sinon le manager désigné via manager_id).
     */
    public function chef(): ?User
    {
        if ($this->relationLoaded('chefs') && $this->getRelation('chefs')->isNotEmpty()) {
            return $this->getRelation('chefs')->first();
        }

        return $this->chefs()->first() ?? $this->manager;
    }

    public function courriers(): HasMany
    {
        return $this->hasMany(Courrier::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
