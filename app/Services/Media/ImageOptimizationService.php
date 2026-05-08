<?php

namespace App\Services\Media;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class ImageOptimizationService
{
    public function optimize(
        UploadedFile $file,
        int $quality = 75,
        int $maxWidth = 1920
    ): array {
        $source = imagecreatefromstring(
            file_get_contents(
                $file->getRealPath()
            )
        );

        if (!$source) {
            throw new \Exception(
                'No fue posible procesar la imagen.'
            );
        }

        $width = imagesx($source);
        $height = imagesy($source);

        // REDIMENSIONAR
        if ($width > $maxWidth) {

            $newWidth = $maxWidth;

            $newHeight = intval(
                ($height / $width) * $newWidth
            );

            $resized = imagecreatetruecolor(
                $newWidth,
                $newHeight
            );

            imagecopyresampled(
                $resized,
                $source,
                0,
                0,
                0,
                0,
                $newWidth,
                $newHeight,
                $width,
                $height
            );

            imagedestroy($source);

            $source = $resized;
        }

        // NOMBRE
        $fileName =
            Str::uuid() .
            '.jpg';

        // BUFFER
        ob_start();

        imagejpeg(
            $source,
            null,
            $quality
        );

        $content = ob_get_clean();

        imagedestroy($source);

        return [
            'file_name' => $fileName,
            'content' => $content,
            'mime_type' => 'image/jpeg',
            'extension' => 'jpg',
            'size_bytes' => strlen($content),
        ];
    }
}
