<?php

namespace ScannerAgent;

/**
 * Localhost HTTP server for the scanner agent.
 *
 * Binds to 127.0.0.1 only. Never exposed publicly.
 * Handles:
 *  - POST /scan — receive a scan job payload
 *  - GET /health — health check
 *  - GET /scanners — list available scanners
 */
class AgentServer
{
    public function __construct(
        private readonly AgentService $service,
        private readonly string $host = '127.0.0.1',
        private readonly int $port = 8765,
    ) {
    }

    /**
     * Start the HTTP server.
     */
    public function start(): void
    {
        $socket = stream_socket_server("tcp://{$this->host}:{$this->port}", $errno, $errstr);

        if ($socket === false) {
            throw new \RuntimeException("Failed to start server: {$errstr} ({$errno})");
        }

        echo "Scanner Agent listening on http://{$this->host}:{$this->port}\n";

        while ($connection = stream_socket_accept($socket, -1)) {
            $this->handleConnection($connection);
        }
    }

    /**
     * Handle a single HTTP connection.
     *
     * @param  mixed  $connection  The stream socket connection.
     */
    private function handleConnection($connection): void
    {
        $request = fread($connection, 8192);

        if ($request === false || $request === '') {
            fclose($connection);

            return;
        }

        [$method, $path] = $this->parseRequest($request);

        $response = match (true) {
            $method === 'GET' && $path === '/health' => $this->health(),
            $method === 'GET' && $path === '/scanners' => $this->scanners(),
            $method === 'POST' && $path === '/scan' => $this->scan($request),
            default => $this->json(['error' => 'not_found'], 404),
        };

        fwrite($connection, $response);
        fclose($connection);
    }

    /**
     * Parse the HTTP request line.
     *
     * @return array<int, string>
     */
    private function parseRequest(string $request): array
    {
        $lines = explode("\r\n", $request);
        $parts = explode(' ', $lines[0] ?? '');

        return [
            $parts[0] ?? 'GET',
            parse_url($parts[1] ?? '/', PHP_URL_PATH) ?: '/',
        ];
    }

    /**
     * Health check response.
     */
    private function health(): string
    {
        return $this->json([
            'status' => 'ok',
            'agent' => 'sgmc-scanner-agent',
            'providers' => array_map(
                fn ($provider) => $provider->name(),
                $this->service->providers(),
            ),
        ]);
    }

    /**
     * List available scanners.
     */
    private function scanners(): string
    {
        try {
            $scanners = $this->service->discoverScanners();

            return $this->json(['scanners' => $scanners]);
        } catch (\Throwable $e) {
            return $this->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Process a scan job.
     */
    private function scan(string $request): string
    {
        $body = $this->extractBody($request);

        if ($body === '') {
            return $this->json(['error' => 'empty_body'], 400);
        }

        $payload = json_decode($body, true);

        if (! is_array($payload)) {
            return $this->json(['error' => 'invalid_json'], 400);
        }

        try {
            $result = $this->service->processJob($payload);

            return $this->json($result);
        } catch (\RuntimeException $e) {
            return $this->json(['error' => $e->getMessage()], 422);
        } catch (\Throwable $e) {
            return $this->json(['error' => 'internal_error'], 500);
        }
    }

    /**
     * Extract the request body from a raw HTTP request.
     */
    private function extractBody(string $request): string
    {
        $parts = explode("\r\n\r\n", $request, 2);

        return $parts[1] ?? '';
    }

    /**
     * Build a JSON response.
     */
    private function json(array $data, int $status = 200): string
    {
        $body = json_encode($data);

        return "HTTP/1.1 {$status} " . $this->statusText($status) . "\r\n"
            . "Content-Type: application/json\r\n"
            . "Content-Length: " . strlen($body) . "\r\n"
            . "Connection: close\r\n"
            . "Access-Control-Allow-Origin: http://localhost:8000\r\n"
            . "Access-Control-Allow-Methods: GET, POST, OPTIONS\r\n"
            . "Access-Control-Allow-Headers: Content-Type\r\n"
            . "\r\n"
            . $body;
    }

    /**
     * Get HTTP status text.
     */
    private function statusText(int $status): string
    {
        return match ($status) {
            200 => 'OK',
            400 => 'Bad Request',
            404 => 'Not Found',
            422 => 'Unprocessable Entity',
            500 => 'Internal Server Error',
            default => 'OK',
        };
    }
}
