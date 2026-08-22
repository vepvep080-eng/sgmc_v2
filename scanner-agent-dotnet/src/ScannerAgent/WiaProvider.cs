namespace ScannerAgent;

/**
 * WIA (Windows Image Acquisition) scanner provider.
 *
 * Uses the Windows WIA COM device manager for discovery. Scanning itself is
 * intentionally not implemented yet.
 */
public class WiaProvider : IScannerProvider
{
    public string Name => "WIA";

    public bool IsAvailable => OperatingSystem.IsWindows();

    public Task<IReadOnlyList<ScannerDevice>> DiscoverAsync()
    {
        if (!OperatingSystem.IsWindows())
        {
            return Task.FromResult<IReadOnlyList<ScannerDevice>>([]);
        }

        var deviceManagerType = Type.GetTypeFromProgID("WIA.DeviceManager");
        if (deviceManagerType == null)
        {
            return Task.FromResult<IReadOnlyList<ScannerDevice>>([]);
        }

        dynamic deviceManager = Activator.CreateInstance(deviceManagerType)
            ?? throw new InvalidOperationException("Unable to create the WIA device manager.");
        var scanners = new List<ScannerDevice>();

        foreach (var deviceInfoObject in deviceManager.DeviceInfos)
        {
            dynamic deviceInfo = deviceInfoObject;

            // WIA_DEVICE_TYPE_SCANNER is 1. Cameras and video devices are not scanners.
            if (Convert.ToInt32(deviceInfo.Type) != 1)
            {
                continue;
            }

            var id = ReadDeviceId(deviceInfo);
            if (string.IsNullOrWhiteSpace(id))
            {
                continue;
            }

            var name = ReadProperty(deviceInfo, "Name");
            var manufacturer = ReadProperty(deviceInfo, "Manufacturer");
            var model = ReadProperty(deviceInfo, "Description");

            scanners.Add(new ScannerDevice(
                id,
                string.IsNullOrWhiteSpace(name) ? id : name,
                manufacturer,
                model,
                Name,
                "detected"));
        }

        return Task.FromResult<IReadOnlyList<ScannerDevice>>(scanners);
    }

    private static string ReadDeviceId(dynamic deviceInfo)
    {
        try
        {
            return Convert.ToString(deviceInfo.DeviceID) ?? string.Empty;
        }
        catch
        {
            return string.Empty;
        }
    }

    private static string ReadProperty(dynamic deviceInfo, string propertyName)
    {
        try
        {
            return Convert.ToString(deviceInfo.Properties[propertyName].Value) ?? string.Empty;
        }
        catch
        {
            return string.Empty;
        }
    }

    public Task<ScanResult> ScanAsync(ScannerDevice device, ScanOptions options)
    {
        return Task.FromException<ScanResult>(new NotImplementedException(
            "WIA provider requires Windows validation. Not implemented."));
    }
}
