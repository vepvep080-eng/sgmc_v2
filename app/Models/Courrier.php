<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Storage;

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
     */
    public const STATUTS = [
        'nouveau' => 'Nouveau',
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
        'numero', 'objet', 'expediteur', 'date_reception', 'priorite', 'statut', 'created_at',
    ];

    protected $fillable = [
        'numero',
        'objet',
        'expediteur',
        'date_reception',
        'reference',
        'priorite',
        'type',
        'service_id',
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

        $count = static::withTrashedIfAvailable()
            ->whereYear('created_at', $year)
            ->count();

        return sprintf('COUR-%s-%04d', $year, $count + 1);
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
    public function currentRouting(): HasOne
    {
        return $this->hasOne(Routing::class)->latestOfMany('sent_at');
    }

    /**
     * Full public URL to the attachment, or null if none was uploaded.
     */
    public function pieceJointeUrl(): ?string
    {
        return $this->piece_jointe
            ? Storage::disk('public')->url($this->piece_jointe)
            : null;
    }

    public function pieceJointeExtension(): ?string
    {
        return $this->piece_jointe
            ? strtolower(pathinfo($this->piece_jointe, PATHINFO_EXTENSION))
            : null;
    }

    /**
     * Full public URL to the processing attachment, or null if none
     * was uploaded when the courrier was treated.
     */
    public function traitementPieceJointeUrl(): ?string
    {
        return $this->traitement_piece_jointe
            ? Storage::disk('public')->url($this->traitement_piece_jointe)
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
        return self::PRIORITES[$this->priorite] ?? self::PRIORITES['normale'];
    }

    public function statusLabel(): string
    {
        return self::STATUTS[$this->statut] ?? self::STATUTS['nouveau'];
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
                ->orWhere('reference', 'like', "%{$term}%");
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
     * Data isolation between services: admins and the Bureau d'Ordre see
     * every courrier (they're the ones routing them in the first place);
     * a chef_service only ever sees courriers currently assigned to
     * their own service.
     */
    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        if ($user->hasRole(Role::ADMIN, Role::BUREAU_ORDRE, Role::READ_ONLY)) {
            return $query;
        }

        return $query->where('service_id', $user->service_id);
    }
}
