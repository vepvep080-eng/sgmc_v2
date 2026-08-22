namespace ScannerAgent;

public class SgmcApiClient
{
    private readonly string _baseUrl;
    private readonly System.Net.Http.HttpClient _client;

    public SgmcApiClient(string baseUrl)
    {
        _baseUrl = baseUrl.TrimEnd('/');
        _client = new System.Net.Http.HttpClient
        {
            BaseAddress = new System.Uri(_baseUrl)
        };
    }

    public (string Status, string? AttachmentUrl) UploadDocument(string jobId, string token, string filePath)
    {
        if (!File.Exists(filePath))
        {
            return ("file_not_found", null);
        }

        using var formData = new System.Net.Http.MultipartFormDataContent();
        formData.Add(new System.Net.Http.StringContent(token), "token");
        formData.Add(new System.Net.Http.StreamContent(File.OpenRead(filePath)), "document", Path.GetFileName(filePath));

        var response = _client.PostAsync($"/scan-jobs/{jobId}/upload", formData).Result;

        if (!response.IsSuccessStatusCode)
        {
            return ("upload_rejected", null);
        }

        var responseBody = response.Content.ReadAsStringAsync().Result;
        var data = System.Text.Json.JsonSerializer.Deserialize<System.Text.Json.Nodes.JsonNode>(responseBody);

        return (
            data?["status"]?.GetValue<string>() ?? "unknown",
            data?["attachment_url"]?.GetValue<string>()
        );
    }
}
