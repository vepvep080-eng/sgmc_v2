<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permission extends Model
{
    protected $fillable = ['name', 'slug'];

    /**
     * Predefined permission slugs. Single source of truth,
     * used by the seeder and by @can / Gate checks in the app.
     */
    public const CREATE = 'create';
    public const READ = 'read';
    public const UPDATE = 'update';
    public const DELETE = 'delete';
    public const EXPORT = 'export';
    public const MANAGE_USERS = 'manage_users';

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }
}
