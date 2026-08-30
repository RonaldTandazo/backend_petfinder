<?php

namespace App\Services\Storage;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TemporaryFileService
{
    protected const DISK = 's3_temp';

    public function upload(UploadedFile $file, ?string $uuid = null): array
    {
        $uuidKey = $uuid ?? Str::uuid()->toString();
        $extension = $file->getClientOriginalExtension() ?: $file->extension() ?: 'jpg';
        $key = "{$uuidKey}.{$extension}";
        
        $file->storeAs('', $key, ['disk' => self::DISK]);

        return [
            'key' => $key,
            'url' => $this->previewUrl($key),
        ];
    }

    public function previewUrl(string $key): string
    {
        // return Storage::disk(self::DISK)->temporaryUrl($key, now()->addMinutes(15));
        return Storage::disk(self::DISK)->url($key);
    }

    public function delete(string $key): void
    {
        Storage::disk(self::DISK)->delete($key);
    }
}
