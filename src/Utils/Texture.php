<?php

namespace Microwin7\PHPUtils\Utils;

use Microwin7\PHPUtils\Configs\MainConfig;
use Microwin7\PHPUtils\Exceptions\TextureSizeException;
use Microwin7\PHPUtils\Exceptions\TextureSizeHDException;

class Texture extends MainConfig
{

    private const SKIN_PATCH = parent::TEXTURE_PATH['skin'] . '{LOGIN}.png';
    private const CAPE_PATCH = parent::TEXTURE_PATH['cape'] . '{LOGIN}.png';
    public const SIZE = [
        'skin' => [['w' => 64, 'h' => 64], ['w' => 64, 'h' => 32]],
        'cape' => [['w' => 64, 'h' => 32]]
    ];
    public const SIZE_WITH_HD = [
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
        return parent::TEXTURE_PATH['skin'];
    }
    public static function getCapePath()
    {
        return parent::TEXTURE_PATH['cape'];
    }
    public static function getTexturePath($type)
    {
        return parent::TEXTURE_PATH[$type];
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
        foreach (self::SIZE_WITH_HD[$type] as $key => $value) {
            if ($value['w'] == $width && $value['h'] == $height) {
                $valid_size = true;
            }
        }
        return $valid_size ?: throw new TextureSizeHDException;
    }
}
