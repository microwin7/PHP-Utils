<?php

namespace Microwin7\PHPUtils\Utils;

class GDUtils
{
    public static function slim(string $data): bool
    {
        try {
            $image = @imagecreatefromstring($data);
            $fraction = imagesx($image) / 8;
            $x = $fraction * 6.75;
            $y = $fraction * 2.5;
            $rgba = imagecolorsforindex($image, imagecolorat($image, $x, $y));
            if ($rgba["alpha"] === 127)
                return true;
            else return false;
        } catch (\TypeError $e) {
            throw new \TypeError('Error read data. This file is not an image.');
        }
    }
    public static function pre_calculation($data)
    {
        try {
            $image = @imagecreatefromstring($data);
            $x = imagesx($image);
            $y = imagesy($image);
            $fraction = $x / 8;
            return [$image, $x, $y, $fraction];
        } catch (\TypeError $e) {
            throw new \TypeError('Error read data. This file is not an image.');
        }
    }
    public static function create_canvas_transparent($width, $height): \GdImage
    {
        ini_set('gd.png_ignore_warning', 0);
        $canvas = imagecreatetruecolor($width, $height);
        $transparent = imagecolorallocatealpha($canvas, 255, 255, 255, 127);
        imagefill($canvas, 0, 0, $transparent);
        imagesavealpha($canvas, TRUE);
        return $canvas;
    }
}
