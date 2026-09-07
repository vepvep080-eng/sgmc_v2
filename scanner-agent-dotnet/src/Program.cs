// See https://aka.ms/new-console-template for more information
using ScannerAgent;

var service = new AgentService();
service.AddProvider(new WiaProvider());
service.AddProvider(new NetworkProvider());

// Load (or create) config.json: CORS origins + Laravel upload URL.
var config = AgentConfig.Load();

Console.WriteLine($"Scanner Agent config file: {config.ConfigFilePath}");
if (config.Created)
{
    Console.WriteLine("  (created automatically with default values)");
}
if (config.UsedFallbackDefaults)
{
    Console.WriteLine("  (file unreadable or invalid: default values in use)");
}
foreach (var warning in config.Warnings)
{
    Console.WriteLine($"  warning: {warning}");
}

Console.WriteLine($"CORS allowed origins ({config.AllowedOrigins.Count}):");
foreach (var origin in config.AllowedOrigins)
{
    Console.WriteLine($"  - {origin}");
}
Console.WriteLine($"Laravel server URL (scan upload): {config.LaravelServerUrl}");

var server = new AgentServer(service, config, host: "127.0.0.1", port: 8765);
using var shutdown = new ManualResetEventSlim(false);

Console.CancelKeyPress += (_, eventArgs) =>
{
    eventArgs.Cancel = true;
    server.Stop();
    shutdown.Set();
};

server.Start();
shutdown.Wait();
