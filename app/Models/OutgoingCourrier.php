<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class OutgoingCourrier extends Model
{
    use HasFactory;

    public const STATUTS = [
        'brouillon' => 'Brouillon',
        'pret' => 'Prêt',
        'envoye' => 'Envoyé',
        'archive' => 'Archivé',
    ];

    public const MODES_ENVOI = [
        'Ordinaire',
        'Recommandé',
        'Voie légale',
        'Email',
        'Remise en main propre',
        'Autre',
    ];

    public const PRIORITES = Courrier::PRIORITES;

    public const SORTABLE_COLUMNS = [
        'numero', 'objet', 'destinataire', 'organisme_destinataire', 'date_envoi', 'mode_envoi', 'priorite', 'statut', 'service', 'created_at',
    ];

    protected $fillable = [
        'numero',
        'objet',
        'destinataire',
        'organisme_destinataire',
        'adresse_destinataire',
        'date_envoi',
        'mode_envoi',
        'reference_postale',
        'statut',
        'priorite',
        'description',
        'attachment',
        'service_id',
        'section_id',
        'assigned_user_id',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'date_envoi' => 'date',
    ];

    protected static function booted(): void
    {
        static::creating(function (OutgoingCourrier $courrier): void {
            if (blank($courrier->numero)) {
                $courrier->numero = static::generateNumero();
            }
        });
    }

    public static function generateNumero(): string
    {
        $year = now()->format('Y');
        $prefix = "DEP-{$year}-";
        $lastNumero = static::query()
            ->where('numero', 'like', $prefix.'%')
            ->orderByDesc('numero')
            ->value('numero');

        $sequence = $lastNumero === null ? 1 : ((int) substr($lastNumero, strlen($prefix))) + 1;

        return sprintf('DEP-%s-%04d', $year, $sequence);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
    public function section(): BelongsTo { return $this->belongsTo(Section::class); }
    public function assignee(): BelongsTo { return $this->belongsTo(User::class, 'assigned_user_id'); }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function auditLogs(): MorphMany
    {
        return $this->morphMany(AuditLog::class, 'auditable')->latest();
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (blank($term)) {
            return $query;
        }

        return $query->where(function (Builder $query) use ($term): void {
            $query->where('numero', 'like', "%{$term}%")
                ->orWhere('objet', 'like', "%{$term}%")
                ->orWhere('destinataire', 'like', "%{$term}%")
                ->orWhere('organisme_destinataire', 'like', "%{$term}%");
        });
    }

    public function statusLabel(): string
    {
        return __('app.statuses.'.$this->statut);
    }

    public function statusVariant(): string
    {
        return match ($this->statut) {
            'brouillon' => 'gray',
            'pret' => 'gold',
            'envoye' => 'green',
            'archive' => 'gray',
            default => 'gray',
        };
    }

    public function priorityLabel(): string
    {
        return __('app.priorities.'.($this->priorite ?? 'normale'));
    }

    public function modeEnvoiLabel(): string
    {
        return __('app.modes.'.$this->mode_envoi);
    }

    public function priorityVariant(): string
    {
        return match ($this->priorite) {
            'tres_urgente' => 'red',
            'urgente' => 'amber',
            default => 'gray',
        };
    }

    public function attachmentUrl(): ?string
    {
        return $this->attachment
            ? route('courriers-depart.attachments.show', ['outgoing_courrier' => $this])
            : null;
    }

    public function attachmentExtension(): ?string
    {
        return $this->attachment
            ? strtolower(pathinfo($this->attachment, PATHINFO_EXTENSION))
            : null;
    }

    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        if ($user->hasRole(Role::ADMIN, Role::BUREAU_ORDRE, Role::READ_ONLY)) {
            return $query;
        }

        if ($user->hasRole(Role::CHEF_SERVICE) && $user->service_id !== null) {
            return $query->where('service_id', $user->service_id);
        }

        if ($user->hasRole(Role::CHEF_SECTION) && $user->section_id !== null) {
            return $query->where('section_id', $user->section_id);
        }

        if ($user->hasRole(Role::EMPLOYEE)) {
            return $query->where('assigned_user_id', $user->id);
        }

        return $query->whereNull('outgoing_courriers.id');
    }
}
