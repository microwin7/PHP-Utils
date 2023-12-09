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
        $image = imagecreatefromstring($data);
        if ($image === false) throw new \TypeError('Error reading data. This file is not an image.');
        $fraction = imagesx($image) / 8;
        if (is_float($fraction)) throw new \TypeError("Error calculating fraction from \GdImage.");
        /** @var int $x */
        $x = $fraction / 8 * 6.75;
        /** @var int $y */
        $y = $fraction / 8 * 2.5;
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
        imagesavealpha($canvas, TRUE);
        return $canvas;
    }
}
