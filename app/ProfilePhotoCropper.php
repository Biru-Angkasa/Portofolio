<?php

namespace App;

use GdImage;
use Illuminate\Validation\ValidationException;
use RuntimeException;

class ProfilePhotoCropper
{
    /** @param array{x: float, y: float, zoom: float} $crop */
    public function render(string $path, array $crop): string
    {
        $dimensions = @getimagesize($path);

        if ($dimensions === false || max($dimensions[0], $dimensions[1]) > 4096) {
            throw ValidationException::withMessages(['photo' => 'Gunakan gambar valid berukuran maksimal 4096 × 4096 piksel.']);
        }

        $contents = @file_get_contents($path);
        $source = $contents !== false ? @imagecreatefromstring($contents) : false;

        if ($source === false) {
            throw ValidationException::withMessages(['photo' => 'Gambar tidak dapat dibaca. Pilih foto lain.']);
        }

        if ($dimensions[2] === IMAGETYPE_JPEG && function_exists('exif_read_data')) {
            $metadata = @exif_read_data($path);
            $source = $this->orient($source, (int) ($metadata['Orientation'] ?? 1));
        }

        $width = imagesx($source);
        $height = imagesy($source);
        $minSide = min($width, $height);
        $size = min($minSide, max(1, (int) round($minSide / $crop['zoom'])));
        $left = max(0, min($width - $size, (int) round(($width - $size) * $crop['x'])));
        $top = max(0, min($height - $size, (int) round(($height - $size) * $crop['y'])));
        $outputSize = min(1024, $size);
        $output = imagecreatetruecolor($outputSize, $outputSize);
        imagealphablending($output, false);
        imagesavealpha($output, true);
        imagefill($output, 0, 0, imagecolorallocatealpha($output, 0, 0, 0, 127));
        imagecopyresampled($output, $source, 0, 0, $left, $top, $outputSize, $outputSize, $size, $size);

        ob_start();

        try {
            if (! imagepng($output, null, 6)) {
                throw new RuntimeException('Hasil crop tidak dapat dibuat.');
            }

            return (string) ob_get_contents();
        } finally {
            ob_end_clean();
        }
    }

    private function orient(GdImage $image, int $orientation): GdImage
    {
        if (in_array($orientation, [2, 5, 7], true)) {
            imageflip($image, IMG_FLIP_HORIZONTAL);
        } elseif ($orientation === 4) {
            imageflip($image, IMG_FLIP_VERTICAL);
        }

        $angle = match ($orientation) {
            3 => 180,
            5, 8 => 90,
            6, 7 => -90,
            default => 0,
        };

        if ($angle === 0) {
            return $image;
        }

        $rotated = imagerotate($image, $angle, 0);

        if ($rotated === false) {
            throw ValidationException::withMessages(['photo' => 'Orientasi foto tidak dapat diproses.']);
        }

        return $rotated;
    }
}
