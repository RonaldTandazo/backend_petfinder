<?php

namespace App\Services\Webhook;

use App\Helpers\ValidationErrorHelper;
use Illuminate\Support\Facades\Log;

class MinioWebhookService
{

    public function handle(array $payload): array
    {
        $records = $payload['Records'] ?? null;

        if (!is_array($records)) {
            ValidationErrorHelper::throwValidationError([
                'Records' => 'El payload del webhook no contiene registros de eventos.',
            ]);
        }

        foreach ($records as $record) {
            $eventName = $record['eventName'] ?? 'desconocido';
            $bucket    = $record['s3']['bucket']['name'] ?? 'desconocido';
            $key       = $record['s3']['object']['key'] ?? 'desconocido';

            Log::info("Evento de MinIO recibido: {$eventName} bucket={$bucket} key={$key}");
        }

        return ['processed' => count($records)];
    }
}
