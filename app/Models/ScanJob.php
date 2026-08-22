<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScanJob extends Model
{
    use HasFactory;

    public const PENDING = 'pending';
    public const SCANNING = 'scanning';
    public const COMPLETED = 'completed';
    public const FAILED = 'failed';
    public const CANCELLED = 'cancelled';
    public const EXPIRED = 'expired';

    protected $fillable = ['public_id', 'user_id', 'courrier_id', 'token_hash', 'status', 'agent_status', 'scanner_name', 'scanner_manufacturer', 'scanner_model', 'scanner_connection', 'error_message', 'expires_at', 'used_at', 'started_at', 'completed_at', 'failed_at', 'original_filename', 'mime_type', 'file_size', 'page_count', 'qr_payload_hash'];
    protected $hidden = ['token_hash'];
    protected $casts = ['expires_at' => 'datetime', 'used_at' => 'datetime', 'started_at' => 'datetime', 'completed_at' => 'datetime', 'failed_at' => 'datetime'];

    public function courrier(): BelongsTo { return $this->belongsTo(Courrier::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function getRouteKeyName(): string { return 'public_id'; }
    public function isExpired(): bool { return $this->expires_at->isPast(); }
    public function isTerminal(): bool { return in_array($this->status, [self::COMPLETED, self::FAILED, self::CANCELLED, self::EXPIRED], true); }
}
