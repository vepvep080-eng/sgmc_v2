<?php

namespace ScannerAgent;

use ScannerAgent\Providers\ScannerProviderInterface;
use ScannerAgent\Security\TokenManager;
use ScannerAgent\Transport\SgmcClient;

/**
 * Core orchestration for the scanner agent.
 *
 * Handles the scan job lifecycle:
 *  1. Receive job payload (job_id, token, courrier_id)
 *  2. Validate token (one-time, not expired)
 *  3. Discover scanners via providers
 *  4. Scan the physical document
 *  5. Upload result to SGMC
 *  6. Clean up temporary files
 */
class AgentService
{
    /** @var array<int, ScannerProviderInterface> */
    private array $providers = [];

    public function __construct(
        private readonly TokenManager $tokens,
        private readonly SgmcClient $client,
        private readonly string $tempDir,
    ) {
        if (! is_dir($this->tempDir)) {
            mkdir($this->tempDir, 0755, true);
        }
    }

    /**
     * Register a scanner provider.
     */
    public function addProvider(ScannerProviderInterface $provider): void
    {
        $this->providers[] = $provider;
    }

    /**
     * Get all registered providers.
     *
     * @return array<int, ScannerProviderInterface>
     */
    public function providers(): array
    {
        return $this->providers;
    }

    /**
     * Discover available scanners across all providers.
     *
     * @return array<int, array{provider: string, id: string, name: string}>
     */
    public function discoverScanners(): array
    {
        $scanners = [];

        foreach ($this->providers as $provider) {
            if (! $provider->isAvailable()) {
                continue;
            }

            foreach ($provider->discover() as $scanner) {
                $scanners[] = [
                    'provider' => $provider->name(),
                    'id' => $scanner['id'],
                    'name' => $scanner['name'],
                ];
            }
        }

        return $scanners;
    }

    /**
     * Process a scan job.
     *
     * @param  array{job_id: string, token: string, courrier_id: int, expires_at?: string}  $payload
     * @return array{status: string, filename?: string, size?: int}
     *
     * @throws \RuntimeException If scanning or upload fails.
     */
    public function processJob(array $payload): array
    {
        $jobId = $payload['job_id'] ?? null;
        $token = $payload['token'] ?? null;
        $courrierId = $payload['courrier_id'] ?? null;

        if ($jobId === null || $token === null || $courrierId === null) {
            throw new \RuntimeException('invalid_payload');
        }

        // Register the token from the job payload
        $expiresAt = isset($payload['expires_at'])
            ? strtotime($payload['expires_at'])
            : null;

        $this->tokens->register($jobId, $token, $expiresAt);

        // Validate the token
        $this->tokens->consume($jobId, $token);

        // Report connected status
        $this->client->reportStatus($jobId, $token, 'connected', null, null, $courrierId);

        // Discover scanners
        $scanners = $this->discoverScanners();

        if ($scanners === []) {
            $this->client->reportStatus($jobId, $token, 'disconnected', null, 'no_scanner_found', $courrierId);
            throw new \RuntimeException('no_scanner_found');
        }

        // Use the first available scanner
        $scanner = $scanners[0];

        // Find the provider for this scanner
        $provider = $this->providerFor($scanner['provider']);

        if ($provider === null) {
            throw new \RuntimeException('provider_not_found');
        }

        // Report connected with scanner name
        $this->client->reportStatus($jobId, $token, 'connected', $scanner['name'], null, $courrierId);

        // Scan the document
        $filePath = $provider->scan($scanner['id'], [
            'resolution' => 300,
            'color' => 'gray',
            'format' => 'pdf',
        ]);

        // Upload to SGMC
        $result = $this->client->uploadDocument($jobId, $token, $filePath);

        // Clean up temporary file
        $this->cleanup($filePath);

        return $result;
    }

    /**
     * Find a provider by name.
     */
    private function providerFor(string $name): ?ScannerProviderInterface
    {
        foreach ($this->providers as $provider) {
            if ($provider->name() === $name) {
                return $provider;
            }
        }

        return null;
    }

    /**
     * Clean up a temporary file.
     */
    private function cleanup(string $filePath): void
    {
        if (is_file($filePath) && str_starts_with(realpath($filePath), realpath($this->tempDir))) {
            unlink($filePath);
        }
    }
}
