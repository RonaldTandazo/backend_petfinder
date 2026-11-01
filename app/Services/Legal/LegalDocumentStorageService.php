<?php

namespace App\Services\Legal;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LegalDocumentStorageService
{
    private const DISK = 's3_docs';

    public function upload(string $tag, string $version, UploadedFile $markdownFile, UploadedFile $pdfFile): array
    {
        $directory = "legal-documents/{$tag}/v{$version}";

        $disk = Storage::disk(self::DISK);

        if (
            $disk->exists("{$directory}/{$tag}-v{$version}.md") ||
            $disk->exists("{$directory}/{$tag}-v{$version}.pdf")
        ) {
            throw new RuntimeException(
                "Ya existen archivos almacenados para {$tag}, versión {$version}"
            );
        }

        $uploadedPaths = [];

        try {
            $markdownPath = $disk->putFileAs(
                $directory,
                $markdownFile,
                "{$tag}-v{$version}.md"
            );

            if (!$markdownPath) {
                throw new RuntimeException('No se pudo almacenar el archivo Markdown');
            }

            $uploadedPaths[] = $markdownPath;

            $pdfPath = $disk->putFileAs(
                $directory,
                $pdfFile,
                "{$tag}-v{$version}.pdf"
            );

            if (!$pdfPath) {
                throw new RuntimeException('No se pudo almacenar el archivo PDF');
            }

            $uploadedPaths[] = $pdfPath;

            return [
                'md_path' => $markdownPath,
                'pdf_path' => $pdfPath,
            ];

        } catch (\Throwable $e) {
            $this->delete($uploadedPaths);

            throw $e;
        }
    }

    public function readMarkdown(string $path): string
    {
        $disk = Storage::disk(self::DISK);

        if (!$disk->exists($path)) {
            throw new RuntimeException('El archivo Markdown no existe en el almacenamiento');
        }

        $content = $disk->get($path);

        if ($content === false) {
            throw new RuntimeException('No se pudo leer el archivo Markdown');
        }

        return $content;
    }

    public function downloadPdf(string $path, string $filename): StreamedResponse
    {
        $disk = Storage::disk(self::DISK);

        if (!$disk->exists($path)) {
            throw new RuntimeException('El archivo PDF no existe en el almacenamiento');
        }

        return $disk->download($path, $filename, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    public function delete(array $paths): void
    {
        if (empty($paths)) {
            return;
        }

        Storage::disk(self::DISK)->delete($paths);
    }
}