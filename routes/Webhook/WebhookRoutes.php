<?php

use App\Http\Controllers\Webhook\MinioWebhookController;
use Illuminate\Support\Facades\Route;

Route::post('webhooks/minio', [MinioWebhookController::class, 'handle']);
