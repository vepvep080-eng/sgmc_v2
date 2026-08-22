<?php

namespace App\Contracts;

/**
 * Contract for scanner providers.
 *
 * This interface defines the contract that any scanner provider must
 * implement. Providers are responsible for discovering and scanning
 * physical documents using a specific scanning technology (WIA, TWAIN,
 * network, etc.).
 *
 * IMPORTANT: Hardware-specific providers (WIA, TWAIN, network) require
 * Windows-side validation. This interface defines the contract only.
 */
interface ScannerProviderInterface
{
    /**
     * Discover available scanners.
     *
     * @return array<int, array<string, string>>
     */
    public function discover(): array;

    /**
     * Scan a document using the specified scanner.
     *
     * @param  string  $scannerId  The scanner identifier from discover().
     * @param  array<string, mixed>  $options  Scan options (resolution, color, format).
     * @return string  Absolute path to the scanned document.
     *
     * @throws \RuntimeException If scanning fails.
     */
    public function scan(string $scannerId, array $options = []): string;

    /**
     * Check if this provider is available on the current system.
     */
    public function isAvailable(): bool;

    /**
     * Get the provider name (e.g., 'WIA', 'TWAIN', 'Network').
     */
    public function name(): string;
}
