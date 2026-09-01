<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class FilamentWebpUpload
{
    public static function storeOriginal(
        TemporaryUploadedFile $file,
        string $directory,
        ?string $fileName = null,
    ): string {
        $disk = Storage::disk('public');

        $disk->makeDirectory($directory);

        $extension = strtolower($file->getClientOriginalExtension() ?: $file->extension() ?: 'bin');
        $path = self::uniquePath($directory, $fileName, $extension);

        $disk->put($path, file_get_contents($file->getRealPath()));

        return $path;
    }

    public static function store(
        TemporaryUploadedFile $file,
        string $directory,
        int $targetWidth,
        int $targetHeight,
        ?string $fileName = null,
        int $quality = 82,
    ): string {
        $disk = Storage::disk('public');

        $disk->makeDirectory($directory);

        if (! function_exists('imagecreatetruecolor') || ! function_exists('imagewebp')) {
            return $file->store($directory, 'public');
        }

        $sourcePath = $file->getRealPath();
        $mimeType = $file->getMimeType();

        $sourceImage = match ($mimeType) {
            'image/jpeg', 'image/jpg' => imagecreatefromjpeg($sourcePath),
            'image/png' => imagecreatefrompng($sourcePath),
            'image/webp' => imagecreatefromwebp($sourcePath),
            default => null,
        };

        if (! $sourceImage) {
            return $file->store($directory, 'public');
        }

        if (in_array($mimeType, ['image/jpeg', 'image/jpg'], true) && function_exists('exif_read_data')) {
            $sourceImage = self::fixImageOrientation($sourceImage, $sourcePath);
        }

        $sourceWidth = imagesx($sourceImage);
        $sourceHeight = imagesy($sourceImage);

        $sourceRatio = $sourceWidth / $sourceHeight;
        $targetRatio = $targetWidth / $targetHeight;

        if ($sourceRatio > $targetRatio) {
            $cropWidth = (int) round($sourceHeight * $targetRatio);
            $cropHeight = $sourceHeight;
        } else {
            $cropWidth = $sourceWidth;
            $cropHeight = (int) round($sourceWidth / $targetRatio);
        }

        $cropX = (int) round(($sourceWidth - $cropWidth) / 2);
        $cropY = (int) round(($sourceHeight - $cropHeight) / 2);

        $finalImage = imagecreatetruecolor($targetWidth, $targetHeight);

        imagealphablending($finalImage, false);
        imagesavealpha($finalImage, true);

        imagecopyresampled(
            $finalImage,
            $sourceImage,
            0,
            0,
            $cropX,
            $cropY,
            $targetWidth,
            $targetHeight,
            $cropWidth,
            $cropHeight
        );

        imagedestroy($sourceImage);

        $path = self::uniquePath($directory, $fileName, 'webp');
        $fullPath = $disk->path($path);

        imagewebp($finalImage, $fullPath, $quality);

        imagedestroy($finalImage);

        return $path;
    }

    private static function uniquePath(string $directory, ?string $fileName, string $extension): string
    {
        $disk = Storage::disk('public');
        $baseName = trim((string) $fileName);

        if ($baseName !== '') {
            $baseName = pathinfo($baseName, PATHINFO_FILENAME);
            $baseName = Str::slug($baseName);
        }

        if ($baseName === '') {
            $baseName = (string) Str::uuid();
        }

        $directory = trim($directory, '/');
        $extension = trim($extension, '.');
        $path = $directory . '/' . $baseName . '.' . $extension;
        $counter = 2;

        while ($disk->exists($path)) {
            $path = $directory . '/' . $baseName . '-' . $counter . '.' . $extension;
            $counter++;
        }

        return $path;
    }

    private static function fixImageOrientation($image, string $sourcePath)
    {
        $exif = @exif_read_data($sourcePath);

        if (! isset($exif['Orientation'])) {
            return $image;
        }

        return match ((int) $exif['Orientation']) {
            3 => imagerotate($image, 180, 0),
            6 => imagerotate($image, -90, 0),
            8 => imagerotate($image, 90, 0),
            default => $image,
        };
    }
}
