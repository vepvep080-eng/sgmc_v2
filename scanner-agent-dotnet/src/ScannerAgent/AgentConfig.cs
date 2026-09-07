namespace ScannerAgent;

/**
 * Loads (and creates when missing) the agent configuration file.
 *
 * Location: next to the executable (AppContext.BaseDirectory). The current
 * working directory is also looked up (covers `dotnet run` started from the
 * project folder). When missing, the file is created next to the executable
 * with default values, so existing installations keep working without any
 * manual step.
 *
 * Supported keys:
 * {
 *   "allowedOrigins": [
 *     "http://localhost:8000",
 *     "http://127.0.0.1:8000",
 *     "http://192.168.1.10:8000"
 *   ],
 *   "laravelServerUrl": "http://localhost:8000"
 * }
 *
 * Origins and the Laravel URL are normalized before use: trimmed, trailing
 * slash removed, lowercase scheme/host, default port omitted, empty path.
 */
public sealed class AgentConfig
{
    public const string ConfigFileName = "config.json";

    public const string DefaultLaravelServerUrl = "http://localhost:8000";

    public static readonly string[] DefaultAllowedOrigins =
    {
        "http://localhost:8000",
        "http://127.0.0.1:8000",
    };

    /** Canonical CORS origins allowed to call the agent. */
    public IReadOnlyList<string> AllowedOrigins { get; init; } = DefaultAllowedOrigins;

    /** Base URL of the SGMC Laravel application (scan uploads). */
    public string LaravelServerUrl { get; init; } = DefaultLaravelServerUrl;

    /** Absolute path of the config file that was used (or created). */
    public string ConfigFilePath { get; init; } = "";

    /** True when the config file did not exist and was created with defaults. */
    public bool Created { get; init; }

    /** True when defaults are used in memory because the file is invalid. */
    public bool UsedFallbackDefaults { get; init; }

    /** Non-fatal problems found while loading the file. */
    public IReadOnlyList<string> Warnings { get; init; } = Array.Empty<string>();

    private sealed class ConfigFileModel
    {
        public List<string>? AllowedOrigins { get; set; }
        public string? LaravelServerUrl { get; set; }
    }

    private static readonly System.Text.Json.JsonSerializerOptions ReadOptions = new()
    {
        PropertyNameCaseInsensitive = true,
        AllowTrailingCommas = true,
    };

    private static readonly System.Text.Json.JsonSerializerOptions WriteOptions = new()
    {
        PropertyNamingPolicy = System.Text.Json.JsonNamingPolicy.CamelCase,
        WriteIndented = true,
    };

    /**
     * First existing config file wins: next to the executable, then the
     * current working directory. When none exists, the returned path is the
     * one next to the executable (where the file will be created).
     */
    public static string ResolveConfigFilePath()
    {
        var besideExecutable = Path.Combine(AppContext.BaseDirectory, ConfigFileName);
        if (File.Exists(besideExecutable))
        {
            return besideExecutable;
        }

        var besideWorkingDirectory = Path.Combine(Directory.GetCurrentDirectory(), ConfigFileName);
        if (File.Exists(besideWorkingDirectory))
        {
            return besideWorkingDirectory;
        }

        return besideExecutable;
    }

    public static AgentConfig Load()
    {
        var path = ResolveConfigFilePath();
        var warnings = new List<string>();

        if (!File.Exists(path))
        {
            if (TryWriteDefaultFile(path, warnings))
            {
                return new AgentConfig
                {
                    AllowedOrigins = NormalizeOrigins(DefaultAllowedOrigins, warnings),
                    LaravelServerUrl = NormalizeBaseUrl(DefaultLaravelServerUrl)!,
                    ConfigFilePath = path,
                    Created = true,
                    Warnings = warnings,
                };
            }

            warnings.Add("using default values.");
            return Fallback(path, warnings);
        }

        string json;
        try
        {
            json = File.ReadAllText(path);
        }
        catch (Exception exception)
        {
            warnings.Add($"config file could not be read ({exception.Message}). Using default values.");
            return Fallback(path, warnings);
        }

        ConfigFileModel? model;
        try
        {
            model = System.Text.Json.JsonSerializer.Deserialize<ConfigFileModel>(json, ReadOptions);
        }
        catch (System.Text.Json.JsonException exception)
        {
            warnings.Add($"config file is not valid JSON ({exception.Message}). Using default values (file left untouched).");
            return Fallback(path, warnings);
        }

        if (model == null)
        {
            warnings.Add("config file is empty. Using default values.");
            return Fallback(path, warnings);
        }

        var origins = NormalizeOrigins(model.AllowedOrigins, warnings);
        if (origins.Count == 0)
        {
            warnings.Add("no valid allowedOrigins entry. Using default origins.");
            origins = NormalizeOrigins(DefaultAllowedOrigins, warnings);
        }

        var laravelUrl = NormalizeBaseUrl(model.LaravelServerUrl);
        if (laravelUrl == null)
        {
            warnings.Add("invalid laravelServerUrl. Using default value.");
            laravelUrl = DefaultLaravelServerUrl;
        }

        return new AgentConfig
        {
            AllowedOrigins = origins,
            LaravelServerUrl = laravelUrl,
            ConfigFilePath = path,
            Warnings = warnings,
        };
    }

    /**
     * Canonical form of an origin or base URL: lowercase scheme and host,
     * default port omitted, no trailing slash, no path. Returns null when
     * the value is not an absolute http(s) URL with an empty path.
     */
    public static string? NormalizeOrigin(string? value)
    {
        if (string.IsNullOrWhiteSpace(value))
        {
            return null;
        }

        var trimmed = value.Trim().TrimEnd('/');
        if (!Uri.TryCreate(trimmed, UriKind.Absolute, out var uri))
        {
            return null;
        }

        if (uri.Scheme != Uri.UriSchemeHttp && uri.Scheme != Uri.UriSchemeHttps)
        {
            return null;
        }

        if (uri.PathAndQuery != "/" && uri.PathAndQuery != "")
        {
            return null;
        }

        var isDefaultPort = (uri.Scheme == Uri.UriSchemeHttp && uri.Port == 80)
            || (uri.Scheme == Uri.UriSchemeHttps && uri.Port == 443);

        return isDefaultPort
            ? $"{uri.Scheme}://{uri.Host.ToLowerInvariant()}"
            : $"{uri.Scheme}://{uri.Host.ToLowerInvariant()}:{uri.Port}";
    }

    private static List<string> NormalizeOrigins(IEnumerable<string>? raw, List<string> warnings)
    {
        var result = new List<string>();

        if (raw == null)
        {
            return result;
        }

        foreach (var entry in raw)
        {
            var normalized = NormalizeOrigin(entry);
            if (normalized == null)
            {
                warnings.Add($"invalid origin skipped: \"{entry}\" (expected http://host[:port]).");
                continue;
            }

            if (!result.Contains(normalized, StringComparer.OrdinalIgnoreCase))
            {
                result.Add(normalized);
            }
        }

        return result;
    }

    private static string? NormalizeBaseUrl(string? value) => NormalizeOrigin(value);

    private static AgentConfig Fallback(string path, List<string> warnings) => new()
    {
        AllowedOrigins = DefaultAllowedOrigins,
        LaravelServerUrl = DefaultLaravelServerUrl,
        ConfigFilePath = path,
        UsedFallbackDefaults = true,
        Warnings = warnings,
    };

    private static bool TryWriteDefaultFile(string path, List<string> warnings)
    {
        try
        {
            var model = new ConfigFileModel
            {
                AllowedOrigins = DefaultAllowedOrigins.ToList(),
                LaravelServerUrl = DefaultLaravelServerUrl,
            };

            var json = System.Text.Json.JsonSerializer.Serialize(model, WriteOptions);
            File.WriteAllText(path, json + Environment.NewLine);
            return true;
        }
        catch (Exception exception)
        {
            warnings.Add($"config file could not be created at {path} ({exception.Message}).");
            return false;
        }
    }
}
