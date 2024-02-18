<?php

namespace Microwin7\PHPUtils\Utils;

use Microwin7\PHPUtils\Configs\TextureConfig;
use Microwin7\PHPUtils\Contracts\User\UserStorageTypeEnum;
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
     * @param key-of<TextureConfig::TEXTURE_PATH> $type Допустимые параметры: <SKIN|CAPE>
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
    /**
     * @param key-of<TextureConfig::TEXTURE_PATH> $type
     * @return string
     */
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
    /**
     * @param int $width
     * @param int $height
     * @param key-of<TextureConfig::SIZE> $type
     * @return true
     * 
     * @throws TextureSizeException
     */
    public static function validateSize(int $width, int $height, string $type): true
    {
        $valid_size = false;
        foreach (TextureConfig::SIZE[strtoupper($type)] as $value) {
            if ($value['w'] == $width && $value['h'] == $height) {
                $valid_size = true;
            }
        }
        return $valid_size ?: throw new TextureSizeException;
    }
    /**
     * @param int $width
     * @param int $height
     * @param key-of<TextureConfig::SIZE_WITH_HD> $type
     * @return true
     * 
     * @throws TextureSizeHDException
     */
    public static function validateHDSize(int $width, int $height, string $type): true
    {
        $valid_size = false;
        foreach (TextureConfig::SIZE_WITH_HD[strtoupper($type)] as $value) {
            if ($value['w'] == $width && $value['h'] == $height) {
                $valid_size = true;
            }
        }
        return $valid_size ?: throw new TextureSizeHDException;
    }
    /**
     * @return string
     *
     * @psalm-return ''|'.png'
     */
    public static function EXT(): string
    {
        return empty(TextureConfig::EXT) ? '' : '.' . TextureConfig::EXT;
    }
    public static function digest(string $data, UserStorageTypeEnum $hashType = UserStorageTypeEnum::DB_SHA256): string
    {
        if (TextureConfig::LEGACY_DIGEST) return strtr(base64_encode(md5($data, true)), '+/', '-_');
        return match ($hashType) {
            UserStorageTypeEnum::DB_SHA1 => hash('sha1', $data),
            UserStorageTypeEnum::DB_SHA256 => hash('sha256', $data),
            default => md5($data)
        };
    }
}
