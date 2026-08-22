// See https://aka.ms/new-console-template for more information
using ScannerAgent;

var service = new AgentService();
service.AddProvider(new WiaProvider());
service.AddProvider(new NetworkProvider());

var server = new AgentServer(service, host: "127.0.0.1", port: 8765);
using var shutdown = new ManualResetEventSlim(false);

Console.CancelKeyPress += (_, eventArgs) =>
{
    eventArgs.Cancel = true;
    server.Stop();
    shutdown.Set();
};

server.Start();
shutdown.Wait();
