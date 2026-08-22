<?php

namespace App\Services;

use InvalidArgumentException;

class QrPayloadService
{
    public function inspect(string $payload): array
    {
        $payload = trim($payload);
        if ($payload === '' || mb_strlen($payload) > 2048) throw new InvalidArgumentException('invalid_qr_payload');
        return ['display' => e($payload), 'length' => mb_strlen($payload), 'hash' => hash('sha256', $payload), 'type' => filter_var($payload, FILTER_VALIDATE_URL) ? 'url' : 'text'];
    }
}
