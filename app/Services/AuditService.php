<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AuditService
{
    public function record(
        string $action,
        ?Model $auditable,
        ?int $serviceId,
        string $description,
        ?array $oldValues = null,
        ?array $newValues = null,
    ): AuditLog {
        $request = request();

        return AuditLog::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'auditable_type' => $auditable?->getMorphClass(),
            'auditable_id' => $auditable?->getKey(),
            'service_id' => $serviceId,
            'description' => $description,
            'old_values' => $this->withoutSensitiveValues($oldValues),
            'new_values' => $this->withoutSensitiveValues($newValues),
            'ip_address' => $request?->ip(),
            'user_agent' => Str::limit((string) $request?->userAgent(), 1000),
        ]);
    }

    private function withoutSensitiveValues(?array $values): ?array
    {
        if ($values === null) {
            return null;
        }

        $sensitiveKeys = [
            'password',
            'password_confirmation',
            'remember_token',
            'token',
            'api_token',
        ];

        return collect($values)
            ->reject(fn (mixed $value, string|int $key) => in_array(Str::lower((string) $key), $sensitiveKeys, true))
            ->map(fn (mixed $value) => is_array($value)
                ? $this->withoutSensitiveValues($value)
                : $value)
            ->all();
    }
}
