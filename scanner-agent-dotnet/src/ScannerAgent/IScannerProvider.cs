namespace ScannerAgent;

public interface IScannerProvider
{
    string Name { get; }
    bool IsAvailable { get; }
    Task<IReadOnlyList<ScannerDevice>> DiscoverAsync();
    Task<ScanResult> ScanAsync(ScannerDevice device, ScanOptions options);
}

public record ScannerDevice(
    string Id,
    string Name,
    string Manufacturer,
    string Model,
    string ConnectionType,
    string Status);

public record ScanOptions(
    int Dpi = 300,
    string ColorMode = "Gray",
    bool Duplex = false,
    string PaperSize = "A4",
    string OutputFormat = "PDF",
    bool MultiPage = false);

public record ScanResult(
    string FilePath,
    int PageCount,
    long FileSize,
    string MimeType);
