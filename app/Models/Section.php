<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Section extends Model
{
    protected $fillable = ['service_id', 'name', 'code', 'manager_id', 'description', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];
    public function service(): BelongsTo { return $this->belongsTo(Service::class); }
    public function manager(): BelongsTo { return $this->belongsTo(User::class, 'manager_id'); }
    public function users(): HasMany { return $this->hasMany(User::class); }
    public function scopeActive(Builder $query): Builder { return $query->where('is_active', true); }
}
