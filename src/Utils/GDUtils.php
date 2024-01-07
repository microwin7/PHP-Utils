<?php

namespace Microwin7\PHPUtils\Utils;

class GDUtils
{
    /**
     * Функция только для вычисления данных о скине
     * @return bool
     * 
     * @throws \TypeError
     */
    public static function slim(string $data): bool
    {
        return self::checkSkinslimFromImage(imagecreatefromstring($data));
    }
    /**
     * Функция только для вычисления данных о скине
     * @return bool
     * 
     * @throws \TypeError
     */
    public static function checkSkinslimFromImage(\GdImage|false $image): bool
    {
        if ($image === false) throw new \TypeError('Error reading data. This file is not an image.');
        $fraction = imagesx($image) / 8;
        if (is_float($fraction)) throw new \TypeError("Error calculating fraction from \GdImage.");
        /** @var int $x */
        $x = $fraction * 6.75;
        /** @var int $y */
        $y = $fraction * 2.5;
        $rgba = imagecolorsforindex($image, imagecolorat($image, $x, $y));
        if ($rgba["alpha"] === 127)
            return true;
        else return false;
    }
    /**
     * Функция только для вычисления данных о скине
     * @return array{0: \GdImage, 1: int, 2: int, 3: int}
     * 
     * @throws \TypeError
     */
    public static function pre_calculation(string $data): array
    {
        $image = imagecreatefromstring($data);
        if ($image === false) throw new \TypeError('Error reading data. This file is not an image.');
        $x = imagesx($image);
        $y = imagesy($image);
        if (is_float($fraction = $x / 8)) throw new \TypeError("Error calculating fraction from \GdImage.");
        return [$image, $x, $y, $fraction];
    }
    public static function create_canvas_transparent(int $width, int $height): \GdImage
    {
        ini_set('gd.png_ignore_warning', 0);
        $canvas = imagecreatetruecolor($width, $height);
        $transparent = imagecolorallocatealpha($canvas, 255, 255, 255, 127);
        imagefill($canvas, 0, 0, $transparent);
        imagesavealpha($canvas, true);
        return $canvas;
    }
    public static function getImageMimeType(string $imagedata): int|null
    {
        $imagemimetypes = array(
            IMAGETYPE_PNG => "89504E470D0A1A0A",
            IMAGETYPE_JPEG => "FFD8",
            IMAGETYPE_GIF => "474946",
        );
        foreach ($imagemimetypes as $mime => $hexbytes) {
            $bytes = self::getBytesFromHexString($hexbytes);
            if (substr($imagedata, 0, strlen($bytes)) == $bytes)
                return $mime;
        }
        return null;
    }
    private static function getBytesFromHexString(string $hexdata): string
    {
        $bytes = [];
        for ($count = 0; $count < strlen($hexdata); $count += 2)
            $bytes[] = chr(hexdec(substr($hexdata, $count, 2)));
        return implode($bytes);
    }
}
