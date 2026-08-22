namespace ScannerAgent;

/**
 * Localhost HTTP server for the scanner agent.
 *
 * Binds to 127.0.0.1 only. Never exposed publicly.
 * Handles:
 *  - POST /scan — receive a scan job payload
 *  - POST /jobs — register a job (called by the Laravel frontend before scanning)
 *  - GET /health — health check
 *  - GET /scanners — list available scanners
 *  - OPTIONS * — CORS preflight
 */
public class AgentServer
{
    private readonly AgentService _service;
    private readonly string _host;
    private readonly int _port;
    private System.Net.HttpListener? _listener;
    private readonly object _lock = new();

    // Adjust this if your Laravel APP_URL differs. Both localhost and
    // 127.0.0.1 are listed because browsers treat them as distinct origins
    // even though they resolve to the same machine.
    private static readonly string[] AllowedOrigins =
    {
        "http://localhost:8000",
        "http://127.0.0.1:8000",
    };

    private static readonly System.Text.Json.JsonSerializerOptions JsonOptions = new()
    {
        PropertyNameCaseInsensitive = true,
    };

    public AgentServer(AgentService service, string host = "127.0.0.1", int port = 8765)
    {
        _service = service;
        _host = host;
        _port = port;
    }

    public void Start()
    {
        _listener = new System.Net.HttpListener();
        _listener.Prefixes.Add($"http://{_host}:{_port}/");
        _listener.Start();

        _ = Task.Run(() => ListenAsync());
        Console.WriteLine($"Scanner Agent listening on http://{_host}:{_port}");
    }

    public void Stop()
    {
        _listener?.Stop();
        _listener?.Close();
    }

    private async System.Threading.Tasks.Task ListenAsync()
    {
        while (_listener!.IsListening)
        {
            try
            {
                var context = await _listener.GetContextAsync();
                _ = Task.Run(() => HandleConnection(context));
            }
            catch
            {
                break;
            }
        }
    }

    /// <summary>
    /// Adds CORS headers to every response. Must be called before writing
    /// the response body, on every code path (success, error, 404, OPTIONS).
    /// Reflects back the request's Origin header if it is in the allow-list,
    /// since Access-Control-Allow-Origin only accepts a single exact value.
    /// </summary>
    private void ApplyCorsHeaders(System.Net.HttpListenerRequest request, System.Net.HttpListenerResponse response)
    {
        var origin = request.Headers["Origin"];
        var allowedOrigin = (origin != null && System.Array.IndexOf(AllowedOrigins, origin) >= 0)
            ? origin
            : AllowedOrigins[0];

        response.Headers.Add("Access-Control-Allow-Origin", allowedOrigin);
        response.Headers.Add("Access-Control-Allow-Methods", "GET, POST, OPTIONS");
        response.Headers.Add("Access-Control-Allow-Headers", "Content-Type");
    }

    private async System.Threading.Tasks.Task HandleConnection(System.Net.HttpListenerContext context)
    {
        try
        {
            var request = context.Request;
            var response = context.Response;

            // CORS headers must be present on every response, including
            // errors and the preflight OPTIONS response.
            ApplyCorsHeaders(request, response);

            var method = request.HttpMethod;
            var path = request.Url?.AbsolutePath ?? "/";

            // Handle CORS preflight before doing anything else.
            if (method == "OPTIONS")
            {
                response.StatusCode = 204;
                response.OutputStream.Close();
                return;
            }

            // Read request body
            var body = "";
            try
            {
                using var reader = new System.IO.StreamReader(request.InputStream, request.ContentEncoding ?? System.Text.Encoding.UTF8);
                body = await reader.ReadToEndAsync();
            }
            catch
            {
                body = "";
            }

            string responseBody;

            // Handle routes
            if (method == "GET" && path == "/health")
            {
                responseBody = HealthResponse();
            }
            else if (method == "GET" && path == "/scanners")
            {
                responseBody = ScannersResponse();
            }
            else if (method == "POST" && path == "/scan")
            {
                responseBody = ScanHandler(body);
            }
            else if (method == "POST" && path == "/jobs")
            {
                responseBody = RegisterJobHandler(body);
            }
            else
            {
                responseBody = "{\"error\": \"not_found\"}";
                response.StatusCode = 404;
            }

            var responseBytes = System.Text.Encoding.UTF8.GetBytes(responseBody);
            response.ContentType = "application/json";
            response.ContentLength64 = responseBytes.Length;
            response.OutputStream.Write(responseBytes, 0, responseBytes.Length);
            response.OutputStream.Close();
        }
        catch (Exception ex)
        {
            Console.Error.WriteLine($"Scanner agent request error: {ex.Message}");
            try
            {
                context.Response.StatusCode = 500;
                context.Response.Close();
            }
            catch
            {
                // Ignore errors while closing the response.
            }
        }
    }

    private string HealthResponse()
    {
        var providers = _service.Providers.Select(p => p.Name).ToList();
        return "{\"status\": \"ok\", \"agent\": \"sgmc-scanner-agent\", \"providers\": " + System.Text.Json.JsonSerializer.Serialize(providers) + "}";
    }

    private string ScannersResponse()
    {
        var scanners = _service.DiscoverScanners();
        return System.Text.Json.JsonSerializer.Serialize(scanners);
    }

    private string ScanHandler(string body)
    {
        // Parse the scan job payload
        ScanJobPayload? payload;
        try
        {
            payload = System.Text.Json.JsonSerializer.Deserialize<ScanJobPayload>(body, JsonOptions);
        }
        catch
        {
            return "{\"error\": \"invalid_json\"}";
        }

        if (payload == null) return "{\"error\": \"empty_body\"}";

        // Validate token against SGMC
        var job = _service.ValidateJob(payload.JobId, payload.Token);
        if (job == null) return "{\"error\": \"invalid_token\"}";

        // Discover scanners
        var scanners = _service.DiscoverScanners();
        if (scanners.Count == 0) return "{\"error\": \"no_scanner_found\"}";

        // Use first available scanner
        var scanner = scanners[0];

        // Scan the document
        var provider = _service.GetProvider(scanner.ConnectionType);
        if (provider == null) return "{\"error\": \"provider_not_found\"}";

        var scanOptions = new ScanOptions
        {
            Dpi = payload.Options?.Dpi ?? 300,
            ColorMode = payload.Options?.ColorMode ?? "Gray",
            Duplex = payload.Options?.Duplex ?? false,
            PaperSize = payload.Options?.PaperSize ?? "A4",
            OutputFormat = payload.Options?.OutputFormat ?? "PDF",
            MultiPage = payload.Options?.MultiPage ?? false
        };

        var scanResult = provider.ScanAsync(scanner, scanOptions).Result;

        // Upload to SGMC
        var client = new SgmcApiClient("http://localhost:8000");
        var uploadResult = client.UploadDocument(payload.JobId, payload.Token, scanResult.FilePath);

        // Clean up temp file
        _service.CleanupTempFile(scanResult.FilePath);

        return "{" +
                "\"status\": \"" + uploadResult.Status + "\"," +
                "\"attachment_url\": \"" + uploadResult.AttachmentUrl + "\"," +
                "\"pages\": " + scanResult.PageCount +
            "}";
    }

    private string RegisterJobHandler(string body)
    {
        RegisterJobPayload? payload;
        try
        {
            payload = System.Text.Json.JsonSerializer.Deserialize<RegisterJobPayload>(body, JsonOptions);
        }
        catch
        {
            return "{\"error\": \"invalid_json\"}";
        }

        if (payload == null) return "{\"error\": \"empty_body\"}";

        if (string.IsNullOrEmpty(payload.JobId) || string.IsNullOrEmpty(payload.Token))
        {
            return "{\"error\": \"missing_fields\"}";
        }

        // Register the job so ValidateJob() can find it later.
        _service.RegisterJob(payload.JobId, payload.Token, payload.CourrierId, payload.ExpiresMinutes);

        return "{\"status\": \"registered\"}";
    }

    public class RegisterJobPayload
    {
        public string JobId { get; set; } = "";
        public string Token { get; set; } = "";
        public string CourrierId { get; set; } = "";
        public int ExpiresMinutes { get; set; } = 15;
    }

    public class ScanJobPayload
    {
        public string JobId { get; set; } = "";
        public string Token { get; set; } = "";
        public string CourrierId { get; set; } = "";
        public string ExpiresAt { get; set; } = "";
        public ScanOptions? Options { get; set; }
    }

    public class ScannerInfo
    {
        public string Id { get; set; } = "";
        public string Name { get; set; } = "";
        public string ConnectionType { get; set; } = "";
    }
}
