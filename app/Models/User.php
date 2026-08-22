<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role_id', 'service_id', 'section_id', 'is_active'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * The service this user belongs to. Mainly relevant for a
     * "chef_service" user: it's what determines which routings they
     * are allowed to accept or reject.
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    public function hasRole(string ...$slugs): bool
    {
        return $this->role !== null && in_array($this->role->slug, $slugs, true);
    }

    public function hasPermission(string $slug): bool
    {
        // Admins implicitly have every permission.
        if ($this->hasRole(Role::ADMIN)) {
            return true;
        }

        return $this->role !== null && $this->role->hasPermission($slug);
    }
}
