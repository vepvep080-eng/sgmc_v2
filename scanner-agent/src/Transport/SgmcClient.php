<?php

namespace ScannerAgent\Transport;

/**
 * HTTP client for communicating with the SGMC Laravel application.
 *
 * Uses only short-lived one-time tokens. Never stores SGMC credentials.
 */
class SgmcClient
{
    public function __construct(
        private readonly string $baseUrl,
        private readonly int $timeout = 30,
    ) {
    }

    /**
     * Upload a scanned document to SGMC.
     *
     * @param  string  $jobId  The scan job public_id.
     * @param  string  $token  The one-time token.
     * @param  string  $filePath  Absolute path to the scanned file.
     * @return array{status: string, attachment_url?: string}
     *
     * @throws \RuntimeException If upload fails.
     */
    public function uploadDocument(string $jobId, string $token, string $filePath): array
    {
        if (! is_file($filePath)) {
            throw new \RuntimeException('file_not_found');
        }

        $url = rtrim($this->baseUrl, '/').'/scan-jobs/'.$jobId.'/upload';

        $curl = curl_init($url);
        curl_setopt_array($curl, [
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => $this->timeout,
            CURLOPT_POSTFIELDS => [
                'token' => $token,
                'document' => new \CURLFile($filePath),
            ],
        ]);

        $response = curl_exec($curl);
        $statusCode = (int) curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $error = curl_error($curl);
        curl_close($curl);

        if ($response === false) {
            throw new \RuntimeException('upload_failed: '.$error);
        }

        $data = json_decode((string) $response, true);

        if ($statusCode >= 400) {
            throw new \RuntimeException('upload_rejected: '.$statusCode.' '.($data['message'] ?? ''));
        }

        return $data ?? ['status' => 'unknown'];
    }

    /**
     * Report agent status to SGMC.
     *
     * @param  string  $jobId  The scan job public_id.
     * @param  string  $token  The one-time token.
     * @param  string  $status  'connected' or 'disconnected'.
     * @param  string|null  $scannerName  Scanner name if connected.
     * @param  string|null  $errorMessage  Error message if disconnected.
     */
    public function reportStatus(
        string $jobId,
        string $token,
        string $status,
        ?string $scannerName = null,
        ?string $errorMessage = null,
        ?int $courrierId = null,
    ): void {
        $url = rtrim($this->baseUrl, '/').'/courriers/'.($courrierId ?? 0).'/scan-jobs/'.$jobId.'/agent-status';

        $payload = [
            'status' => $status,
            'scanner_name' => $scannerName,
            'error_message' => $errorMessage,
        ];

        $curl = curl_init($url);
        curl_setopt_array($curl, [
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => $this->timeout,
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            CURLOPT_POSTFIELDS => json_encode($payload),
        ]);

        curl_exec($curl);
        curl_close($curl);
    }
}
