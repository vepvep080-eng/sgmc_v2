namespace ScannerAgent;

public class AgentService
{
    private readonly Dictionary<string, IScannerProvider> _providers = new();
    private readonly Dictionary<string, (string Token, DateTime ExpiresAt, string JobId)> _jobs = new();

    public IReadOnlyList<IScannerProvider> Providers => _providers.Values.ToList();

    public void AddProvider(IScannerProvider provider)
    {
        _providers[provider.Name] = provider;
    }

    public IReadOnlyList<ScannerDevice> DiscoverScanners()
    {
        var result = new List<ScannerDevice>();

        foreach (var provider in _providers.Values.Where(p => p.IsAvailable))
        {
            try
            {
                var devices = provider.DiscoverAsync().GetAwaiter().GetResult();
                result.AddRange(devices);
            }
            catch (Exception exception)
            {
                Console.Error.WriteLine($"Scanner provider '{provider.Name}' is unavailable: {exception.Message}");
            }
        }

        return result;
    }

    public void RegisterJob(
        string jobId,
        string token,
        string courrierId,
        int expiresMinutes = 15)
    {
        _jobs[jobId] = (
            token,
            DateTime.UtcNow.AddMinutes(expiresMinutes),
            jobId
        );
    }

    public (string Token, DateTime ExpiresAt, string JobId)? ValidateJob(
        string jobId,
        string token)
    {
        if (_jobs.TryGetValue(jobId, out var jobInfo))
        {
            if (jobInfo.Token == token &&
                DateTime.UtcNow < jobInfo.ExpiresAt)
            {
                return jobInfo;
            }
        }

        return null;
    }

    public IScannerProvider? GetProvider(string name)
    {
        _providers.TryGetValue(name, out var provider);
        return provider;
    }

    public void CleanupTempFile(string filePath)
    {
        if (File.Exists(filePath))
        {
            File.Delete(filePath);
        }
    }
}
