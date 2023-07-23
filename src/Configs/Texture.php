<?php

namespace Microwin7\PHPUtils\Configs;

use microwin7\Exceptions\TextureSizeException;
use microwin7\Exceptions\TextureSizeHDException;

class Texture
{
    private const TEXTURE_PATH = [
        'skin' => __DIR__ . '/skins/',
        'cape' => __DIR__ . '/capes/'
    ];
    private const SKIN_PATCH = self::TEXTURE_PATH['skin'] . '{LOGIN}.png';
    private const CAPE_PATCH = self::TEXTURE_PATH['cape'] . '{LOGIN}.png';
    private const SIZE = [
        'skin' => [['w' => 64, 'h' => 64], ['w' => 64, 'h' => 32]],
        'cape' => [['w' => 64, 'h' => 32]]
    ];
    private const SIZE_HD = [
        'skin' => [
            ['w' => 64, 'h' => 64], ['w' => 64, 'h' => 32],
            ['w' => 128, 'h' => 64], ['w' => 128, 'h' => 128],
            ['w' => 256, 'h' => 128], ['w' => 256, 'h' => 256],
            ['w' => 512, 'h' => 256], ['w' => 512, 'h' => 512],
            ['w' => 1024, 'h' => 512], ['w' => 1024, 'h' => 1024]
        ],
        'cape' => [
            ['w' => 64, 'h' => 32], ['w' => 128, 'h' => 64], ['w' => 256, 'h' => 128],
            ['w' => 512, 'h' => 256], ['w' => 1024, 'h' => 512]
        ]
    ];
    public const MAX_SIZE_BYTES = 2 * 1024 * 1024; // byte => Kbyte, Kbyte => MB * 2

    public static function getSkinPath()
    {
        return self::TEXTURE_PATH['skin'];
    }
    public static function getCapePath()
    {
        return self::TEXTURE_PATH['cape'];
    }
    public static function getTexturePath($type)
    {
        return self::TEXTURE_PATH[$type];
    }
    public static function getSkinUrl($username)
    {
        return str_replace('{LOGIN}', $username, self::SKIN_PATCH);
    }
    public static function getCapeUrl($username)
    {
        return str_replace('{LOGIN}', $username, self::CAPE_PATCH);
    }
    public static function validateSize($width, $height, $type)
    {
        $valid_size = false;
        foreach (self::SIZE[$type] as $key => $value) {
            if ($value['w'] == $width && $value['h'] == $height) {
                $valid_size = true;
            }
        }
        return $valid_size ?: throw new TextureSizeException;
    }
    public static function validateToHDSize($width, $height, $type)
    {
        $valid_size = false;
        foreach (self::SIZE_HD[$type] as $key => $value) {
            if ($value['w'] == $width && $value['h'] == $height) {
                $valid_size = true;
            }
        }
        return $valid_size ?: throw new TextureSizeHDException;
    }
}
