<?php

namespace App\Services;

use GdImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class TurtleImageService
{
    // Сетка карточек имеет пропорции 4:3.
    // Не увеличиваем маленькие изображения и не искажаем пропорции.
    public const MAX_WIDTH = 1200;
    public const MAX_HEIGHT = 900;

    public function store(UploadedFile $file): string
    {
        if (!extension_loaded('gd')) {
            throw ValidationException::withMessages([
                'image' => 'Для обработки изображений необходимо включить расширение GD в php.ini.',
            ]);
        }

        $sourcePath = $file->getRealPath();
        $imageInfo = $sourcePath ? @getimagesize($sourcePath) : false;

        if ($imageInfo === false) {
            throw ValidationException::withMessages([
                'image' => 'Не удалось прочитать загруженное изображение.',
            ]);
        }

        [$originalWidth, $originalHeight, $imageType] = $imageInfo;

        // Поддерживаем те же типы, что разрешены в правилах валидации.
        $extension = match ($imageType) {
            IMAGETYPE_JPEG => 'jpg',
            IMAGETYPE_PNG => 'png',
            IMAGETYPE_WEBP => 'webp',
            default => null,
        };

        if ($extension === null || ($imageType === IMAGETYPE_WEBP
            && (!function_exists('imagecreatefromwebp') || !function_exists('imagewebp')))) {
            throw ValidationException::withMessages([
                'image' => 'Формат изображения не поддерживается текущей сборкой GD.',
            ]);
        }

        $source = match ($imageType) {
            IMAGETYPE_JPEG => @imagecreatefromjpeg($sourcePath),
            IMAGETYPE_PNG => @imagecreatefrompng($sourcePath),
            IMAGETYPE_WEBP => @imagecreatefromwebp($sourcePath),
        };

        if (!$source instanceof GdImage) {
            throw ValidationException::withMessages([
                'image' => 'Не удалось обработать изображение.',
            ]);
        }

        $canvas = null;

        try {
            $scale = min(
                1,
                self::MAX_WIDTH / $originalWidth,
                self::MAX_HEIGHT / $originalHeight
            );

            $width = max(1, (int) round($originalWidth * $scale));
            $height = max(1, (int) round($originalHeight * $scale));

            $canvas = imagecreatetruecolor($width, $height);

            if (!$canvas instanceof GdImage) {
                throw ValidationException::withMessages([
                    'image' => 'Не удалось создать уменьшенное изображение.',
                ]);
            }

            // Сохраняем прозрачный фон PNG и WebP.
            if ($imageType === IMAGETYPE_PNG || $imageType === IMAGETYPE_WEBP) {
                imagealphablending($canvas, false);
                imagesavealpha($canvas, true);
                $transparent = imagecolorallocatealpha($canvas, 0, 0, 0, 127);
                imagefill($canvas, 0, 0, $transparent);
            }

            imagecopyresampled(
                $canvas,
                $source,
                0, 0, 0, 0,
                $width, $height,
                $originalWidth, $originalHeight
            );

            $path = 'images/' . Str::uuid() . '.' . $extension;
            $disk = Storage::disk('public');
            $disk->makeDirectory('images');
            $destination = $disk->path($path);

            $saved = match ($imageType) {
                IMAGETYPE_JPEG => imagejpeg($canvas, $destination, 85),
                IMAGETYPE_PNG => imagepng($canvas, $destination, 6),
                IMAGETYPE_WEBP => imagewebp($canvas, $destination, 85),
            };

            if (!$saved || !$disk->exists($path) || $disk->size($path) === 0) {
                $disk->delete($path);

                throw ValidationException::withMessages([
                    'image' => 'Не удалось сохранить изображение.',
                ]);
            }

            return $path;
        } finally {
            if ($canvas instanceof GdImage) {
                imagedestroy($canvas);
            }

            imagedestroy($source);
        }
    }
}
