<?php

namespace App\Services;

use App\Models\Courrier;
use App\Models\ScanJob;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Authoritative ScanJob lifecycle manager.
 *
 * Handles creation, agent communication, attachment, cancellation,
 * and validation of all scan jobs.
 */
class ScanJobService
{
    public function create(User $user, Courrier $courrier): array
    {
        $token = Str::random(64);
        $job = ScanJob::create(['public_id' => (string) Str::uuid(), 'user_id' => $user->id, 'courrier_id' => $courrier->id, 'token_hash' => Hash::make($token), 'status' => ScanJob::PENDING, 'expires_at' => now()->addMinutes(15)]);
        app(AuditService::class)->record('scan_started', $courrier, $courrier->service_id, 'Création d\'une opération de numérisation.', null, ['scan_job' => $job->public_id]);
        return [$job, $token];
    }

    /**
     * Mark a job as connected to the agent with scanner metadata.
     *
     * @param  array{name?: string, manufacturer?: string, model?: string, connection?: string}  $scanner
     */
    public function markAgentConnected(ScanJob $job, array $scanner = []): void
    {
        $job->update([
            'agent_status' => 'connected',
            'scanner_name' => $scanner['name'] ?? $job->scanner_name,
            'scanner_manufacturer' => $scanner['manufacturer'] ?? $job->scanner_manufacturer,
            'scanner_model' => $scanner['model'] ?? $job->scanner_model,
            'scanner_connection' => $scanner['connection'] ?? $job->scanner_connection,
        ]);
    }

    /**
     * Mark a job as disconnected from the agent.
     */
    public function markAgentDisconnected(ScanJob $job, string $errorMessage = null): void
    {
        $job->update([
            'agent_status' => 'disconnected',
            'error_message' => $errorMessage,
        ]);
    }

    /**
     * Validate that a job is usable by the agent or uploader.
     *
     * The token is a short-lived, job-scoped capability token:
     *  - bound to exactly one ScanJob
     *  - expires after 15 minutes
     *  - validated against the stored hash
     *  - rejected if the job is expired, completed, cancelled, or invalid
     *
     * @throws RuntimeException
     */
    public function assertUsable(ScanJob $job, User $user, string $token): void
    {
        if ($job->user_id !== $user->id || $job->isTerminal()) {
            throw new RuntimeException('invalid_job');
        }

        if (! Hash::check($token, $job->token_hash)) {
            throw new RuntimeException('invalid_token');
        }

        if ($job->isExpired()) {
            $job->update(['status' => ScanJob::EXPIRED]);
            throw new RuntimeException('expired_job');
        }
    }

    /**
     * Validate a capability token without necessarily consuming it.
     *
     * @return array<string, mixed>
     *
     * @throws RuntimeException
     */
    public function validateToken(ScanJob $job, string $token): array
    {
        if ($job->isTerminal()) {
            throw new RuntimeException('terminal_job');
        }

        if (! Hash::check($token, $job->token_hash)) {
            throw new RuntimeException('invalid_token');
        }

        if ($job->isExpired()) {
            $job->update(['status' => ScanJob::EXPIRED]);
            throw new RuntimeException('expired_job');
        }

        return [
            'job_id' => $job->public_id,
            'courrier_id' => $job->courrier_id,
            'status' => $job->status,
            'expires_at' => $job->expires_at->toIso8601String(),
            'scanner_name' => $job->scanner_name,
            'scanner_manufacturer' => $job->scanner_manufacturer,
            'scanner_model' => $job->scanner_model,
            'scanner_connection' => $job->scanner_connection,
        ];
    }

    public function attach(ScanJob $job, User $user, string $token, UploadedFile $file): ScanJob
    {
        $this->assertUsable($job, $user, $token);
        if ($job->courrier->piece_jointe) throw new RuntimeException('attachment_exists');

        $temporary = $file->store('scan-temp', 'local');
        $finalPath = null;
        try {
            $job->update(['status' => ScanJob::SCANNING, 'started_at' => now()]);
            $filename = Str::uuid().'.'.$file->extension();
            $finalPath = 'courriers/'.$filename;
            Storage::disk('public')->write($finalPath, Storage::disk('local')->get($temporary));

            DB::transaction(function () use ($job, $file, $finalPath) {
                $job->courrier->update(['piece_jointe' => $finalPath]);
                $job->update(['status' => ScanJob::COMPLETED, 'used_at' => now(), 'completed_at' => now(), 'original_filename' => Str::limit($file->getClientOriginalName(), 255, ''), 'mime_type' => $file->getMimeType(), 'file_size' => $file->getSize()]);
            });
            app(AuditService::class)->record('scan_completed', $job->courrier, $job->courrier->service_id, 'Numérisation terminée et document joint.', null, ['scan_job' => $job->public_id, 'mime_type' => $file->getMimeType(), 'file_size' => $file->getSize()]);
            app(AuditService::class)->record('scan_attached', $job->courrier, $job->courrier->service_id, 'Document numérisé joint au courrier.', null, ['scan_job' => $job->public_id]);
            return $job->fresh();
        } catch (\Throwable $exception) {
            if ($finalPath) Storage::disk('public')->delete($finalPath);
            $job->update(['status' => ScanJob::FAILED, 'failed_at' => now()]);
            app(AuditService::class)->record('scan_failed', $job->courrier, $job->courrier->service_id, 'Échec de la numérisation.', null, ['scan_job' => $job->public_id]);
            throw $exception;
        } finally {
            Storage::disk('local')->delete($temporary);
        }
    }

    public function cancel(ScanJob $job, User $user): void
    {
        if ($job->user_id !== $user->id || $job->isTerminal()) throw new RuntimeException('invalid_job');
        $job->update(['status' => ScanJob::CANCELLED, 'used_at' => now()]);
        app(AuditService::class)->record('scan_cancelled', $job->courrier, $job->courrier->service_id, 'Opération de numérisation annulée.', null, ['scan_job' => $job->public_id]);
    }
}
