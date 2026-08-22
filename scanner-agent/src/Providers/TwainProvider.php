<?php

namespace ScannerAgent\Providers;

/**
 * TWAIN scanner provider.
 *
 * ⚠️ CONTRACT STUB — REQUIRES WINDOWS VALIDATION
 *
 * This provider defines the contract for using TWAIN on Windows.
 * It is NOT a working implementation. To use it:
 *
 * 1. Install a TWAIN PHP extension or wrapper library
 * 2. Validate against actual scanner hardware
 * 3. Implement the `discover()` and `scan()` methods using TWAIN API
 *
 * The interface contract is complete. Hardware execution is NOT verified.
 */
class TwainProvider implements ScannerProviderInterface
{
    public function discover(): array
    {
        // TODO: Implement TWAIN device discovery
        throw new \RuntimeException('TWAIN provider requires Windows validation. Not implemented.');
    }

    public function scan(string $scannerId, array $options = []): string
    {
        // TODO: Implement TWAIN scanning
        throw new \RuntimeException('TWAIN provider requires Windows validation. Not implemented.');
    }

    public function isAvailable(): bool
    {
        // TWAIN is only available on Windows with a TWAIN extension
        return PHP_OS_FAMILY === 'Windows' && extension_loaded('twain');
    }

    public function name(): string
    {
        return 'TWAIN';
    }
}
