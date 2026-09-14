<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Courrier extends Model
{
    use HasFactory;

    /**
     * Human-readable labels for the `priorite` column.
     */
    public const PRIORITES = [
        'normale' => 'Normale',
        'urgente' => 'Urgente',
        'tres_urgente' => 'Très urgente',
    ];

    /**
     * Human-readable labels for the `statut` column.
     * 'en_attente_de_dispatch' = envoyé aux départements, en attente du
     * dispatch du chef de département vers ses services (traçabilité).
     */
    public const STATUTS = [
        'nouveau' => 'Nouveau',
        'en_attente_de_dispatch' => 'En attente de dispatch',
        'en_cours' => 'En cours',
        'traite' => 'Traité',
        'archive' => 'Archivé',
    ];

    /**
     * Columns a user is allowed to sort the index table by.
     * Whitelisted here and re-checked in the controller to avoid
     * arbitrary column sorting from user input.
     */
    public const SORTABLE_COLUMNS = [
        'numero', 'objet', 'expediteur', 'date_reception', 'priorite', 'statut', 'service', 'created_at',
    ];

    protected $fillable = [
        'numero',
        'objet',
        'expediteur',
        'date_reception',
        'reference',
        'priorite',
        'type',
        'department_id',
        'service_id',
        'section_id',
        'assigned_user_id',
        'description',
        'piece_jointe',
        'statut',
        'user_id',
        'archived_by',
        'archived_at',
        'traitement_commentaire',
        'traitement_piece_jointe',
        'traite_par',
        'traite_le',
    ];

    protected $casts = [
        'date_reception' => 'date',
        'archived_at' => 'datetime',
        'traite_le' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (Courrier $courrier) {
            if (empty($courrier->numero)) {
                $courrier->numero = static::generateNumero();
            }
        });
    }

    /**
     * Generates a sequential, year-scoped reference: COUR-2026-0001.
     */
    public static function generateNumero(): string
    {
        $year = now()->format('Y');

        $prefix = "COUR-{$year}-";
        $lastNumero = static::withTrashedIfAvailable()
            ->where('numero', 'like', $prefix.'%')
            ->orderByDesc('numero')
            ->value('numero');

        $sequence = $lastNumero === null ? 1 : ((int) substr($lastNumero, strlen($prefix))) + 1;

        return sprintf('COUR-%s-%04d', $year, $sequence);
    }

    /**
     * Tiny guard so this still works if SoftDeletes is added later
     * without having to remember to update this method.
     */
    protected static function withTrashedIfAvailable(): Builder
    {
        return method_exists(static::class, 'withTrashed')
            ? static::withTrashed()
            : static::query();
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Département courant du courrier (Étape 1 : renseigné dès l'envoi BO/Admin
     * vers les départements ; Étape 2 : le dispatch ne change pas forcément
     * ce champ si multi-départements — voir routings pour le détail).
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function section(): BelongsTo { return $this->belongsTo(Section::class); }
    public function assignee(): BelongsTo { return $this->belongsTo(User::class, 'assigned_user_id'); }

    /**
     * The user who archived this courrier, if it has been archived.
     */
    public function archivedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'archived_by');
    }

    /**
     * The user (chef_service) who processed this courrier, if it has
     * been treated.
     */
    public function treatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'traite_par');
    }

    /**
     * Full movement history, most recent first.
     */
    public function routings(): HasMany
    {
        return $this->hasMany(Routing::class)->orderByDesc('sent_at');
    }

    /**
     * The single most recent routing — the one currently "in play"
     * (pending, or the last accepted/refused move).
     */
    public function auditLogs(): MorphMany
    {
        return $this->morphMany(AuditLog::class, 'auditable')->latest();
    }
    public function currentRouting(): HasOne
    {
        return $this->hasOne(Routing::class)->latestOfMany('sent_at');
    }

    /**
     * Authorized download URL for the attachment, or null if none was uploaded.
     */
    public function pieceJointeUrl(): ?string
    {
        return $this->piece_jointe
            ? route('courriers.attachments.show', ['courrier' => $this, 'attachment' => 'piece_jointe'])
            : null;
    }

    public function pieceJointeExtension(): ?string
    {
        return $this->piece_jointe
            ? strtolower(pathinfo($this->piece_jointe, PATHINFO_EXTENSION))
            : null;
    }

    /**
     * Authorized download URL for the processing attachment, or null if none
     * was uploaded when the courrier was treated.
     */
    public function traitementPieceJointeUrl(): ?string
    {
        return $this->traitement_piece_jointe
            ? route('courriers.attachments.show', ['courrier' => $this, 'attachment' => 'traitement_piece_jointe'])
            : null;
    }

    public function traitementPieceJointeExtension(): ?string
    {
        return $this->traitement_piece_jointe
            ? strtolower(pathinfo($this->traitement_piece_jointe, PATHINFO_EXTENSION))
            : null;
    }

    public function priorityLabel(): string
    {
        return __('app.priorities.'.($this->priorite ?? 'normale'));
    }

    public function statusLabel(): string
    {
        return __('app.statuses.'.($this->statut ?? 'nouveau'));
    }

    /**
     * Maps to a <x-badge variant="..."> value — kept as a short key
     * (not a raw class string) so Tailwind can still statically detect
     * every class used across the badge component.
     */
    public function priorityVariant(): string
    {
        return match ($this->priorite) {
            'tres_urgente' => 'red',
            'urgente' => 'amber',
            default => 'gray',
        };
    }

    public function statusVariant(): string
    {
        return match ($this->statut) {
            'nouveau' => 'gold',
            'en_cours' => 'default',
            'traite' => 'green',
            'archive' => 'gray',
            default => 'gray',
        };
    }

    /**
     * Search across the fields a clerk would realistically type into
     * the search box: reference number, subject, sender, free reference.
     */
    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (blank($term)) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($term) {
            $q->where('numero', 'like', "%{$term}%")
                ->orWhere('objet', 'like', "%{$term}%")
                ->orWhere('expediteur', 'like', "%{$term}%")
                ->orWhere('reference', 'like', "%{$term}%")
                ->orWhere('description', 'like', "%{$term}%");
        });
    }

    public function scopeStatut(Builder $query, ?string $statut): Builder
    {
        return blank($statut) ? $query : $query->where('statut', $statut);
    }

    public function scopePriorite(Builder $query, ?string $priorite): Builder
    {
        return blank($priorite) ? $query : $query->where('priorite', $priorite);
    }

    /**
     * Data isolation hiérarchique :
     * - admin / BO / lecture seule : tout ;
     * - chef_departement : courriers routés vers SON département
     *   (routings.department_id) + déjà rattachés à ses services ;
     * - chef_service : courriers de SON service (+ routés vers lui, même
     *   si courrier.service_id pas encore aligné — via routings) ;
     * - autres : vide (sécurité par défaut).
     */
    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        if ($user->hasRole(Role::ADMIN, Role::BUREAU_ORDRE, Role::READ_ONLY)) {
            return $query;
        }

        if ($user->hasRole(Role::CHEF_SERVICE) && $user->service_id !== null) {
            $serviceId = $user->service_id;

            return $query->where(function ($q) use ($serviceId) {
                $q->where('courriers.service_id', $serviceId)
                    ->orWhereHas('routings', function ($r) use ($serviceId) {
                        $r->where('service_destination_id', $serviceId);
                    });
            });
        }

        if ($user->hasRole(Role::CHEF_SECTION) && $user->section_id !== null) {
            return $query->where('section_id', $user->section_id);
        }

        if ($user->hasRole(Role::EMPLOYEE)) {
            return $query->where('assigned_user_id', $user->id);
        }

        if ($user->hasRole(Role::CHEF_DEPARTEMENT)) {
            $department = Department::where('manager_id', $user->id)->first();

            if ($department === null) {
                return $query->whereNull('courriers.id');
            }

            $serviceIds = $department->services()->pluck('services.id');

            return $query->where(function ($q) use ($serviceIds, $department) {
                $q->whereIn('courriers.service_id', $serviceIds->all())
                    ->orWhereHas('routings', function ($r) use ($department) {
                        $r->where('department_id', $department->id);
                    });
            });
        }

        return $query->whereNull('courriers.id');
    }
}
