<?php

namespace ScannerAgent\Security;

/**
 * Manages one-time scan job tokens.
 *
 * Tokens are:
 *  - short-lived (15 minutes, matching SGMC scan_jobs.expires_at)
 *  - one-time use (cannot be reused)
 *  - never stored in plaintext
 *  - never logged
 */
class TokenManager
{
    /** @var array<string, array{token: string, expires_at: int, used: bool}> */
    private array $tokens = [];

    public function __construct(private readonly int $timeoutMinutes = 15)
    {
    }

    /**
     * Register a token for a job.
     */
    public function register(string $jobId, string $token, ?int $expiresAt = null): void
    {
        $this->tokens[$jobId] = [
            'token' => $token,
            'expires_at' => $expiresAt ?? (time() + ($this->timeoutMinutes * 60)),
            'used' => false,
        ];
    }

    /**
     * Validate and consume a token.
     *
     * @throws \RuntimeException If token is invalid, expired, or already used.
     */
    public function consume(string $jobId, string $token): void
    {
        if (! isset($this->tokens[$jobId])) {
            throw new \RuntimeException('unknown_job');
        }

        $job = $this->tokens[$jobId];

        if ($job['used']) {
            throw new \RuntimeException('token_already_used');
        }

        if (time() > $job['expires_at']) {
            throw new \RuntimeException('token_expired');
        }

        if (! hash_equals($job['token'], $token)) {
            throw new \RuntimeException('invalid_token');
        }

        $this->tokens[$jobId]['used'] = true;
    }

    /**
     * Check if a token is valid without consuming it.
     */
    public function isValid(string $jobId, string $token): bool
    {
        if (! isset($this->tokens[$jobId])) {
            return false;
        }

        $job = $this->tokens[$jobId];

        return ! $job['used']
            && time() <= $job['expires_at']
            && hash_equals($job['token'], $token);
    }

    /**
     * Clean up expired tokens.
     */
    public function cleanup(): void
    {
        $now = time();

        $this->tokens = array_filter(
            $this->tokens,
            fn (array $job) => $now <= $job['expires_at'],
        );
    }
}
