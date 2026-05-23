<?php

namespace App\Services\Image;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;
use Intervention\Image\Interfaces\ImageInterface;
use Spatie\ImageOptimizer\OptimizerChainFactory;


class ImageProcessor
{
    public function processAndStore(UploadedFile $file, string $directory, int $maxWidth = 2560, int $maxHeight = 1440): array
    {
        $disk = 'public';
        $baseName = Str::uuid()->toString();

        $mainPath = trim($directory, '/').'/'.$baseName.'.webp';
        $thumbPath = trim($directory, '/').'/'.$baseName.'_thumb.webp';

        // زيادة الجودة وأبعاد الـthumb للحفاظ على تفاصيل أوضح
        $mainBinary = $this->encodeWithFallback($file, $maxWidth, $maxHeight, 92);
        $thumbBinary = $this->encodeWithFallback($file, 800, 600, 88);

        Storage::disk($disk)->put($mainPath, $mainBinary);
        Storage::disk($disk)->put($thumbPath, $thumbBinary);

        $this->optimize($disk, $mainPath);
        $this->optimize($disk, $thumbPath);

        return [
            'path_main' => $mainPath,
            'path_thumb' => $thumbPath,
        ];
    }

    /**
     * يحاول التحويل إلى WebP ثم يسقط إلى JPEG إذا لم يكن WebP مدعومًا.
     */
    protected function encodeWithFallback(UploadedFile $file, int $width, int $height, int $quality): string
    {
        try {
            /** @var ImageInterface $image */
            $image = $this->ensureSrgb($this->readImage($file));

            return (string) $this->downscale($image, $width, $height)
                ->toWebp(quality: $quality);
        } catch (\Throwable $e) {
            report($e);

            /** @var ImageInterface $fallback */
            $fallback = $this->ensureSrgb($this->readImage($file));

            return (string) $this->downscale($fallback, $width, $height)
                ->toJpeg(quality: $quality);
        }
    }

    /**
     * Read the uploaded image contents defensively so Livewire temp files decode reliably.
     */
    protected function readImage(UploadedFile $file): ImageInterface
    {
        $path = $file->getRealPath() ?: $file->getPathname();
        $contents = $path && is_readable($path) ? @file_get_contents($path) : false;

        if ($contents === false || $contents === '') {
            throw new \RuntimeException('Unable to read uploaded image contents');
        }

        return Image::read($contents)->orient();
    }

    /**
     * Safely reduce dimensions without upscaling; falls back to simple resize if scaleDown is unavailable.
     */
    protected function downscale(ImageInterface $image, int $width, int $height): ImageInterface
    {
        if (method_exists($image, 'scaleDown')) {
            return call_user_func([$image, 'scaleDown'], $width, $height);
        }

        return $image->resize($width, $height);
    }

    /**
     * Attempt to convert to target colorspace with safe guards per driver/env.
     */
    protected function ensureSrgb(ImageInterface $image): ImageInterface
    {
        if (! (bool) config('image_processing.colorspace.enabled', true)) {
            return $image;
        }

        $configuredDriver = $this->normalizeDriverName((string) config('image.driver', 'gd'));
        $allowedDrivers = array_map(
            fn (mixed $driver): string => $this->normalizeDriverName((string) $driver),
            (array) config('image_processing.colorspace.allowed_drivers', ['imagick'])
        );

        if (! in_array($configuredDriver, $allowedDrivers, true)) {
            return $image;
        }

        $target = (string) config('image_processing.colorspace.target', 'srgb');
        if ($target === '') {
            return $image;
        }

        try {
            return $image->setColorspace($target);
        } catch (\Throwable $e) {
            report($e);
            return $image;
        }
    }

    protected function normalizeDriverName(string $driver): string
    {
        $normalized = strtolower(trim($driver));

        return str_contains($normalized, 'imagick') ? 'imagick' : 'gd';
    }

    public function deleteStored(?string $path, string $disk = 'public'): void
    {
        if ($path) {
            Storage::disk($disk)->delete($path);
        }
    }

    /**
     * Reprocess an already-stored image file: create webp main + thumb, optimize, and
     * optionally delete the original file. Returns paths array or false on failure.
     */
    public function reprocessFromDisk(string $disk, string $existingPath, string $directory, bool $deleteOriginal = true, int $maxWidth = 2560, int $maxHeight = 1440): array|false
    {
        $absolute = Storage::disk($disk)->path($existingPath);

        if (! is_file($absolute)) {
            return false;
        }

        try {
            $baseName = Str::uuid()->toString();
            $mainPath = trim($directory, '/').'/'.$baseName.'.webp';
            $thumbPath = trim($directory, '/').'/'.$baseName.'_thumb.webp';

            // Read and normalize
            $image = $this->ensureSrgb(Image::read($absolute)->orient());

            $mainBinary = (string) $this->downscale($image, $maxWidth, $maxHeight)->toWebp(quality: 92);

            // regenerate thumbnail from original file again to avoid reusing resized image
            $thumbImage = $this->ensureSrgb(Image::read($absolute)->orient());
            $thumbBinary = (string) $this->downscale($thumbImage, 800, 600)->toWebp(quality: 88);

            Storage::disk($disk)->put($mainPath, $mainBinary);
            Storage::disk($disk)->put($thumbPath, $thumbBinary);

            $this->optimize($disk, $mainPath);
            $this->optimize($disk, $thumbPath);

            if ($deleteOriginal && $existingPath !== $mainPath && $existingPath !== $thumbPath) {
                Storage::disk($disk)->delete($existingPath);
            }

            return [
                'path_main' => $mainPath,
                'path_thumb' => $thumbPath,
            ];
        } catch (\Throwable $e) {
            report($e);
            return false;
        }
    }

    protected function optimize(string $disk, string $path): void
    {
        $absolutePath = Storage::disk($disk)->path($path);

        if (! is_file($absolutePath)) {
            return;
        }

        try {
            OptimizerChainFactory::create()->optimize($absolutePath);
        } catch (\Throwable $e) {
            // Ignore optimizer failures in environments without optimization binaries
            report($e);
        }
    }
}

