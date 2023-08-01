<?php

namespace Microwin7\PHPUtils\Utils;

use Microwin7\PHPUtils\Configs\TextureConfig;
use Microwin7\PHPUtils\Exceptions\TextureSizeException;
use Microwin7\PHPUtils\Exceptions\TextureSizeHDException;

class Texture
{
    public static function getSkinPath()
    {
        return TextureConfig::TEXTURE_PATH['skin'];
    }
    public static function getCapePath()
    {
        return TextureConfig::TEXTURE_PATH['cape'];
    }
    public static function getTexturePath($type)
    {
        return TextureConfig::TEXTURE_PATH[$type];
    }
    public static function getSkinUrl($username)
    {
        return str_replace('{LOGIN}', $username, TextureConfig::SKIN_PATCH);
    }
    public static function getCapeUrl($username)
    {
        return str_replace('{LOGIN}', $username, TextureConfig::CAPE_PATCH);
    }
    public static function validateSize($width, $height, $type)
    {
        $valid_size = false;
        foreach (TextureConfig::SIZE[$type] as $key => $value) {
            if ($value['w'] == $width && $value['h'] == $height) {
                $valid_size = true;
            }
        }
        if (!$valid_size) throw new TextureSizeException;
        return $valid_size;
    }
    public static function validateToHDSize($width, $height, $type)
    {
        $valid_size = false;
        foreach (TextureConfig::SIZE_WITH_HD[$type] as $key => $value) {
            if ($value['w'] == $width && $value['h'] == $height) {
                $valid_size = true;
            }
        }
        if (!$valid_size) throw new TextureSizeHDException;
        return $valid_size;
    }
}
