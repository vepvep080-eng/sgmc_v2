<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    protected $fillable = ['name', 'slug', 'description'];

    /**
     * Predefined role slugs used across the app.
     * Keep these as the single source of truth for role checks.
     */
    public const ADMIN = 'admin';
    public const BUREAU_ORDRE = 'bureau_ordre';
    public const CHEF_SERVICE = 'chef_service';
    public const CHEF_SECTION = 'chef_section';
    public const CHEF_DEPARTEMENT = 'chef_departement';
    public const EMPLOYEE = 'employe';
    public const READ_ONLY = 'read_only';

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class);
    }

    public function hasPermission(string $slug): bool
    {
        return $this->permissions()->where('slug', $slug)->exists();
    }
}
