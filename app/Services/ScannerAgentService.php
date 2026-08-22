<?php

namespace App\Services;

use App\Models\Courrier;
use App\Models\ScanJob;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Manages the communication contract between SGMC and the local
 * Windows Scanner Agent.
 *
 * The agent is a separate localhost component that:
 *  - receives short-lived scan jobs
 *  - discovers and scans physical documents
 *  - uploads results back to SGMC
 *  - never stores persistent SGMC credentials
 *
 * This service handles the SGMC-side of that contract.
 */
class ScannerAgentService
{
    /**
     * Create a scan job that can be picked up by the local agent.
     *
     * @return array{job: ScanJob, token: string}
     */
    public function createJob(User $user, Courrier $courrier): array
    {
        $token = Str::random(64);

        $job = ScanJob::create([
            'public_id' => (string) Str::uuid(),
            'user_id' => $user->id,
            'courrier_id' => $courrier->id,
            'token_hash' => Hash::make($token),
            'status' => ScanJob::PENDING,
            'expires_at' => now()->addMinutes(15),
        ]);

        app(AuditService::class)->record(
            'scan_started',
            $courrier,
            $courrier->service_id,
            'Création d\'une opération de numérisation.',
            null,
            ['scan_job' => $job->public_id],
        );

        return [$job, $token];
    }

    /**
     * Mark a job as connected to the agent.
     */
    public function markAgentConnected(ScanJob $job, string $scannerName = null): void
    {
        $job->update([
            'agent_status' => 'connected',
            'scanner_name' => $scannerName,
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
     * Validate that a job is usable by the agent.
     *
     * @throws \RuntimeException
     */
    public function assertUsable(ScanJob $job, User $user, string $token): void
    {
        if ($job->user_id !== $user->id || $job->isTerminal() || $job->used_at) {
            throw new \RuntimeException('invalid_job');
        }

        if (! Hash::check($token, $job->token_hash)) {
            throw new \RuntimeException('invalid_token');
        }

        if ($job->isExpired()) {
            $job->update(['status' => ScanJob::EXPIRED]);
            throw new \RuntimeException('expired_job');
        }
    }

    /**
     * Get the agent communication payload for a job.
     *
     * @return array<string, mixed>
     */
    public function agentPayload(ScanJob $job, string $token): array
    {
        return [
            'job_id' => $job->public_id,
            'token' => $token,
            'courrier_id' => $job->courrier_id,
            'expires_at' => $job->expires_at->toIso8601String(),
        ];
    }
}
