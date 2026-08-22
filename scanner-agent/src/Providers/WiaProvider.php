<?php

namespace ScannerAgent\Providers;

/**
 * WIA (Windows Image Acquisition) scanner provider.
 *
 * ⚠️ CONTRACT STUB — REQUIRES WINDOWS VALIDATION
 *
 * This provider defines the contract for using WIA on Windows.
 * It is NOT a working implementation. To use it:
 *
 * 1. Install the WIA COM extension for PHP on Windows:
 *    - Enable `php_com_dotnet.dll` in php.ini
 *    - Or use a COM wrapper library
 * 2. Validate against actual scanner hardware
 * 3. Implement the `discover()` and `scan()` methods using WIA COM objects
 *
 * The interface contract is complete. Hardware execution is NOT verified.
 */
class WiaProvider implements ScannerProviderInterface
{
    public function discover(): array
    {
        // TODO: Implement WIA device discovery using COM:
        // $wia = new \COM('WIA.DeviceManager');
        // foreach ($wia->DeviceInfos as $deviceInfo) { ... }
        throw new \RuntimeException('WIA provider requires Windows validation. Not implemented.');
    }

    public function scan(string $scannerId, array $options = []): string
    {
        // TODO: Implement WIA scanning using COM:
        // $device = $wia->DeviceInfos->Item($scannerId)->Connect();
        // $item = $device->Items(1);
        // $item->Properties('CurrentIntent')->Value = 0;
        // $image = $item->Transfer($item->Properties('FormatID')->Value);
        throw new \RuntimeException('WIA provider requires Windows validation. Not implemented.');
    }

    public function isAvailable(): bool
    {
        // WIA is only available on Windows with COM support
        return PHP_OS_FAMILY === 'Windows' && class_exists('COM');
    }

    public function name(): string
    {
        return 'WIA';
    }
}
