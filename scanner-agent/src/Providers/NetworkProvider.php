<?php

namespace ScannerAgent\Providers;

/**
 * Network scanner provider.
 *
 * ⚠️ CONTRACT STUB — REQUIRES WINDOWS VALIDATION
 *
 * This provider defines the contract for using network-connected
 * scanners (e.g., scanners with built-in network interfaces).
 * It is NOT a working implementation. To use it:
 *
 * 1. Implement network protocol (e.g., eSCL, AirScan, vendor-specific)
 * 2. Validate against actual network scanner hardware
 * 3. Implement the `discover()` and `scan()` methods
 *
 * The interface contract is complete. Hardware execution is NOT verified.
 */
class NetworkProvider implements ScannerProviderInterface
{
    public function discover(): array
    {
        // TODO: Implement network scanner discovery (e.g., mDNS/SSDP)
        throw new \RuntimeException('Network provider requires Windows validation. Not implemented.');
    }

    public function scan(string $scannerId, array $options = []): string
    {
        // TODO: Implement network scanning (e.g., eSCL protocol)
        throw new \RuntimeException('Network provider requires Windows validation. Not implemented.');
    }

    public function isAvailable(): bool
    {
        // Network scanning requires network access and protocol support
        return false;
    }

    public function name(): string
    {
        return 'Network';
    }
}
