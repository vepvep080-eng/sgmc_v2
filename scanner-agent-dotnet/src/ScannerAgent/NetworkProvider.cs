namespace ScannerAgent;

/**
 * Network scanner provider.
 *
 * ⚠️ CONTRACT STUB — REQUIRES WINDOWS VALIDATION
 *
 * This provider defines the contract for using network-connected scanners
 * (e.g., scanners with built-in network interfaces, eSCL/AirScan).
 * It is NOT a working implementation. To use it:
 *
 * 1. Implement network protocol (e.g., eSCL, AirScan, vendor-specific)
 * 2. Validate against actual network scanner hardware
 * 3. Implement the `DiscoverAsync` and `ScanAsync` methods
 *
 * The interface contract is complete. Hardware execution is NOT verified.
 */
public class NetworkProvider : IScannerProvider
{
    public string Name => "Network";

    public bool IsAvailable => false; // Network scanning requires additional setup

    public Task<IReadOnlyList<ScannerDevice>> DiscoverAsync()
    {
        return Task.FromException<IReadOnlyList<ScannerDevice>>(new NotImplementedException(
            "Network provider requires Windows validation. Not implemented."));
    }

    public Task<ScanResult> ScanAsync(ScannerDevice device, ScanOptions options)
    {
        return Task.FromException<ScanResult>(new NotImplementedException(
            "Network provider requires Windows validation. Not implemented."));
    }
}
