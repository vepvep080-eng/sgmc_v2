<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Routing extends Model
{
    public const STATUT_EN_ATTENTE = 'en_attente';
    public const STATUT_ACCEPTE = 'accepte';
    public const STATUT_REFUSE = 'refuse';

    /**
     * Human-readable labels for the `statut` column.
     */
    public const STATUTS = [
        self::STATUT_EN_ATTENTE => 'En attente',
        self::STATUT_ACCEPTE => 'Accepté',
        self::STATUT_REFUSE => 'Refusé',
    ];

    protected $fillable = [
        'courrier_id',
        'service_source_id',
        'service_destination_id',
        'routed_by',
        'statut',
        'sent_at',
        'received_at',
        'commentaire',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'received_at' => 'datetime',
    ];

    public function courrier(): BelongsTo
    {
        return $this->belongsTo(Courrier::class);
    }

    /**
     * Null when the courrier was routed for the first time directly
     * from the Bureau d'Ordre (no prior service).
     */
    public function serviceSource(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'service_source_id');
    }

    public function serviceDestination(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'service_destination_id');
    }

    public function routedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'routed_by');
    }

    public function statusLabel(): string
    {
        return self::STATUTS[$this->statut] ?? $this->statut;
    }

    /**
     * Maps to a <x-badge variant="..."> value, consistent with the
     * short-key convention used on Courrier (priorityVariant/statusVariant).
     */
    public function statusVariant(): string
    {
        return match ($this->statut) {
            self::STATUT_EN_ATTENTE => 'gold',
            self::STATUT_ACCEPTE => 'green',
            self::STATUT_REFUSE => 'red',
            default => 'gray',
        };
    }

    public function isPending(): bool
    {
        return $this->statut === self::STATUT_EN_ATTENTE;
    }
}
