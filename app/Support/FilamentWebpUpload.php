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
        bool $responsiveVariants = false,
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

        if ($responsiveVariants) {
            // Optional derivatives must never invalidate a successfully saved primary.
            try {
                self::generateResponsiveVariants($path, min($cropWidth, $targetWidth));
            } catch (\Throwable $exception) {
                report($exception);
            }
        }

        return $path;
    }

    /**
     * Generate derivatives from the saved primary so framing always matches.
     * A crop-versioned suffix excludes uncropped Phase 1 derivatives from delivery.
     *
     * @return array<int, string> Actual width => public-disk path
     */
    public static function generateResponsiveVariants(string $primaryPath, ?int $maxWidth = null): array
    {
        if (! function_exists('imagewebp') || ! function_exists('imagecreatetruecolor')) {
            return [];
        }

        $disk = Storage::disk('public');
        $sourcePath = $disk->path($primaryPath);
        $info = getimagesize($sourcePath);
        $source = match ($info['mime'] ?? null) {
            'image/jpeg' => imagecreatefromjpeg($sourcePath),
            'image/png' => imagecreatefrompng($sourcePath),
            'image/webp' => imagecreatefromwebp($sourcePath),
            default => null,
        };

        if (! $source) {
            return [];
        }

        if (($info['mime'] ?? null) === 'image/jpeg' && function_exists('exif_read_data')) {
            $source = self::fixImageOrientation($source, $sourcePath);
        }

        $paths = [];
        try {
            $sourceWidth = imagesx($source);
            $sourceHeight = imagesy($source);
            $base = substr($primaryPath, 0, -(strlen(pathinfo($primaryPath, PATHINFO_EXTENSION)) + 1));

            foreach ([480, 800, 1200] as $targetWidth) {
                $width = min($targetWidth, $sourceWidth, $maxWidth ?? $sourceWidth);
                if (isset($paths[$width])) {
                    continue;
                }

                $path = $base.'-crop-w'.$width.'.webp';
                if ($path === $primaryPath) {
                    continue;
                }

                if (! $disk->exists($path)) {
                    $height = max(1, (int) round($sourceHeight * $width / $sourceWidth));
                    $image = imagecreatetruecolor($width, $height);
                    $stream = fopen('php://temp', 'w+b');
                    try {
                        imagealphablending($image, false);
                        imagesavealpha($image, true);
                        imagecopyresampled($image, $source, 0, 0, 0, 0, $width, $height, $sourceWidth, $sourceHeight);
                        if (! imagewebp($image, $stream, 78)) {
                            throw new \RuntimeException('Unable to encode responsive image: '.$path);
                        }
                        rewind($stream);
                        // Exclusive creation prevents concurrent regeneration from
                        // overwriting a derivative another request already saved.
                        $destination = @fopen($disk->path($path), 'x+b');
                        if ($destination === false) {
                            if (! $disk->exists($path)) {
                                throw new \RuntimeException('Unable to store responsive image: '.$path);
                            }
                        } else {
                            try {
                                $bytes = fstat($stream)['size'];
                                if (stream_copy_to_stream($stream, $destination) !== $bytes) {
                                    throw new \RuntimeException('Incomplete responsive image: '.$path);
                                }
                            } finally {
                                fclose($destination);
                            }
                        }
                    } finally {
                        imagedestroy($image);
                        fclose($stream);
                    }
                }

                $paths[$width] = $path;
            }
        } finally {
            imagedestroy($source);
        }

        return $paths;
    }

    /** Read-only discovery: never generate derivatives during page requests. */
    public static function responsiveSrcset(string $primaryPath): ?string
    {
        $disk = Storage::disk('public');
        if (! $disk->exists($primaryPath)) {
            return null;
        }

        $primary = @getimagesize($disk->path($primaryPath));
        if (! $primary) {
            return null;
        }

        $base = substr($primaryPath, 0, -(strlen(pathinfo($primaryPath, PATHINFO_EXTENSION)) + 1));
        $candidates = [];
        foreach ($disk->files(dirname($primaryPath)) as $path) {
            if (! preg_match('/^'.preg_quote($base, '/').'-crop-w(\d+)\.webp$/', $path, $match)) {
                continue;
            }
            $width = (int) $match[1];
            $info = @getimagesize($disk->path($path));
            if ($info && $width <= min(1200, $primary[0]) && $info[0] === $width && abs($info[1] - $primary[1] * $width / $primary[0]) <= 1) {
                $candidates[$width] = $disk->url($path).' '.$width.'w';
            }
        }
        if (! $candidates) {
            return null;
        }
        $candidates[$primary[0]] ??= $disk->url($primaryPath).' '.$primary[0].'w';
        ksort($candidates);

        return implode(', ', $candidates);
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
        $path = $directory.'/'.$baseName.'.'.$extension;
        $counter = 2;
        $existingFiles = $disk->files($directory);

        while ($disk->exists($path) || collect($existingFiles)->contains(
            fn (string $existing) => preg_match('/^'.preg_quote(substr($path, 0, -(strlen($extension) + 1)), '/').'-(?:crop-)?w\d+\.webp$/', $existing)
        )) {
            $path = $directory.'/'.$baseName.'-'.$counter.'.'.$extension;
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
