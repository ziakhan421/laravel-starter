<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

trait StorageTrait
{
    /**
     * Save a file to the storage.
     *
     * @param object $file
     * @param $folder
     * @param null $fileName
     * @param string $disk
     * @return bool|string|null
     */
    public function saveFileToDisk($file, $folder, $fileName = null, $disk = 'public'): bool|string|null
    {
        $ext = $file->getClientOriginalExtension();
        $fileName = !empty($fileName) ? $fileName : time();

        $fullFilePath = "{$folder}/{$fileName}.{$ext}";
        $fileContent = file_get_contents($file->getRealPath());

        if (Storage::disk($disk)->exists($fullFilePath)) {
            Storage::disk($disk)->delete($fullFilePath);
        }

        return Storage::disk($disk)->put($fullFilePath, $fileContent) ? $fullFilePath : null;
    }

    /**
     * Get path of local path
     *
     * @param string $filePath
     * @param string $disk
     * @return bool|string
     */
    public function getStoragePath(string $filePath, string $disk = 'public'): bool|string
    {
        // get storage path
        return Storage::disk($disk)->path($filePath);
    }

    /**
     * Move a file on disk
     *
     * @param string $filePath
     * @param string $newPath
     * @param string $disk
     * @return bool|string
     */
    public function moveFileOnDisk(string $filePath, string $newPath, string $disk = 'public'): bool|string
    {
        if (Storage::disk($disk)->exists($filePath)) {
            return Storage::disk($disk)->move($filePath, $newPath) ? $newPath : false;
        }

        return false;
    }

    /**
     * Delete a file from the storage.
     *
     * @param string $filePath
     * @param string $disk
     * @return bool
     */
    public function deleteFileFromDisk(string $filePath, string $disk = 'public'): bool
    {
        if (Storage::disk($disk)->exists($filePath)) {
            return Storage::disk($disk)->delete($filePath);
        }

        return true;
    }

    /**
     * Get a file from the storage.
     *
     * @param string $filePath
     * @param string $disk
     * @return string|bool
     */
    public function getFileFromDisk(string $filePath, string $disk = 'public'): string|bool
    {
        if (Storage::disk($disk)->exists($filePath)) {
            return Storage::disk($disk)->get($filePath);
        }
        return false;
    }

    /**
     * Check file exists in the storage.
     *
     * @param string $filePath
     * @param string $disk
     * @return bool
     */
    public function fileExists(string $filePath, string $disk = 'public'): bool
    {
        return Storage::disk($disk)->exists($filePath);
    }

    /**
     * Download a file from the storage.
     *
     * @param string $filePath
     * @param string $fileName
     * @param string $disk
     * @return StreamedResponse
     */
    public function downloadFileFromDisk(string $filePath, string $fileName, string $disk = 'public'): StreamedResponse
    {
        if (Storage::disk($disk)->exists($filePath)) {
            $headers = [
                'Content-Type' => 'application/zip, application/octet-stream',
            ];

            return Storage::disk($disk)->download($filePath, $fileName, $headers);
        }

        // TODO: add exception
    }
}
