<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Department extends Model
{
    protected $fillable = ['name', 'code', 'manager_id', 'description', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    /**
     * Chef de département = user pointé par departments.manager_id
     * (cf. DepartmentSeeder / écran Départements).
     */
    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    /**
     * Tous les chefs de service rattachés à ce département :
     * users.role = chef_service ET users.service_id IN (services du département).
     * C'est la relation demandée "quel Chef de Service appartient à quel
     * Chef de Département" (via Service.department_id).
     *
     * @return HasManyThrough<User, Service, $this>
     */
    public function serviceChiefs(): HasManyThrough
    {
        $chefServiceRoleId = Role::query()->where('slug', Role::CHEF_SERVICE)->value('id');

        return $this->hasManyThrough(
            User::class,
            Service::class,
            'department_id', // FK sur services
            'service_id',    // FK sur users
            'id',
            'id'
        )->when($chefServiceRoleId, fn (Builder $q, $roleId) => $q->where('users.role_id', $roleId));
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}

