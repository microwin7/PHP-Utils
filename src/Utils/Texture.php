<?php

namespace Microwin7\PHPUtils\Utils;

use Microwin7\PHPUtils\Configs\TextureConfig;
use Microwin7\PHPUtils\Exceptions\TextureSizeException;
use Microwin7\PHPUtils\Exceptions\TextureSizeHDException;
use function Microwin7\PHPUtils\str_ends_with_slash;
use function Microwin7\PHPUtils\ar_slash_string;

class Texture
{
    /**
     * Получение ссылки пути хранения скина
     *
     * @param string $login
     * @return string
     */
    public static function getSkinPath(string $login): string
    {
        return self::getSkinPathStorage() . $login . self::EXT();
    }
    /**
     * Получение ссылки пути хранения плаща
     *
     * @param string $login
     * @return string
     */
    public static function getCapePath(string $login): string
    {
        return self::getCapePathStorage() . $login . self::EXT();
    }
    /**
     * Получение ссылки пути хранения скина/плаща с указанием вызываемого типа
     *
     * @param string $login
     * @param string $type Допустимые параметры: <SKIN|CAPE>
     * @return string
     */
    public static function getTexturePath(string $login, string $type): string
    {
        return self::getTexturePathStorage($type) . $login . self::EXT();
    }

    public static function getSkinPathStorage(): string
    {
        return str_ends_with_slash(TextureConfig::SKIN_PATH);
    }
    public static function getCapePathStorage(): string
    {
        return str_ends_with_slash(TextureConfig::CAPE_PATH);
    }
    public static function getTexturePathStorage(string $type): string
    {
        return str_ends_with_slash(TextureConfig::TEXTURE_PATH[strtoupper($type)]);
    }

    public static function getSkinUrl(string $login): string
    {
        return Path::getAppUrl() .
            ar_slash_string(TextureConfig::SKIN_URL_PATH) .
            $login . self::EXT();
    }
    public static function getCapeUrl(string $login): string
    {
        return Path::getAppUrl() .
            ar_slash_string(TextureConfig::CAPE_URL_PATH) .
            $login . self::EXT();
    }
    public static function validateSize($width, $height, $type)
    {
        $valid_size = false;
        foreach (TextureConfig::SIZE[strtoupper($type)] as $value) {
            if ($value['w'] == $width && $value['h'] == $height) {
                $valid_size = true;
            }
        }
        if (!$valid_size) throw new TextureSizeException;
        return $valid_size;
    }
    public static function validateHDSize($width, $height, $type)
    {
        $valid_size = false;
        foreach (TextureConfig::SIZE_WITH_HD[strtoupper($type)] as $value) {
            if ($value['w'] == $width && $value['h'] == $height) {
                $valid_size = true;
            }
        }
        if (!$valid_size) throw new TextureSizeHDException;
        return $valid_size;
    }
    public static function EXT(): string
    {
        return empty(TextureConfig::EXT) ?: '.' . TextureConfig::EXT;
    }
}
