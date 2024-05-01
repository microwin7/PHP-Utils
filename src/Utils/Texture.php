<?php

namespace Microwin7\PHPUtils\Utils;

use Microwin7\PHPUtils\Main;
use Microwin7\PHPUtils\Utils\Path;
use Microwin7\PHPUtils\Configs\TextureConfig;
use function Microwin7\PHPUtils\ar_slash_string;
use function Microwin7\PHPUtils\convertToBytes;
// use function Microwin7\PHPUtils\str_ends_with_slash;
use Microwin7\PHPUtils\Exceptions\TextureSizeException;
use Microwin7\PHPUtils\Exceptions\TextureSizeHDException;
use Microwin7\PHPUtils\Contracts\User\UserStorageTypeEnum;
use Microwin7\PHPUtils\Contracts\Texture\Enum\ResponseTypeEnum;
use Microwin7\PHPUtils\Contracts\Texture\Enum\TextureStorageTypeEnum;

class Texture extends TextureConfig
{
    /** BASE DIR */
    public static function STORAGE_DIR(): string
    {
        $STORAGE_DIR = getenv()['STORAGE_DIR'] ?? '';
        $STORAGE_DIR = !empty($STORAGE_DIR) ? $STORAGE_DIR : TextureStorageTypeEnum::getNameRequestVariable();
        return ar_slash_string($STORAGE_DIR);
    }
    /** BASE DIR */
    public static function TEXTURE_STORAGE_DIR(ResponseTypeEnum|TextureStorageTypeEnum $type): string
    {
        return match ($type) {
            ResponseTypeEnum::SKIN, ResponseTypeEnum::CAPE,
            ResponseTypeEnum::AVATAR, ResponseTypeEnum::FRONT,
            ResponseTypeEnum::FRONT_CAPE, ResponseTypeEnum::FRONT_WITH_CAPE,
            ResponseTypeEnum::BACK, ResponseTypeEnum::BACK_CAPE,
            ResponseTypeEnum::BACK_WITH_CAPE, ResponseTypeEnum::CAPE_RESIZE,
            TextureStorageTypeEnum::COLLECTION => self::STORAGE_DIR() . ar_slash_string(getenv()['TEXTURE_' . $type->name . '_PATH'] ?? (strtolower($type->name) . 's')),
            default => throw new \InvalidArgumentException(sprintf('Un-supported TEXTURE_STORAGE_DIR: %s', $type->name))
        };
    }
    public static function EXTENSTION(): string
    {
        return empty($EXT = getenv()['TEXTURE_EXTENSTION'] ?? TextureConfig::TEXTURE_EXTENSTION) ? '' : '.' . $EXT;
    }

    /** FOR URL ONLY */
    public static function TEXTURE_STORAGE_PATH(ResponseTypeEnum|TextureStorageTypeEnum $type): string
    {
        return Path::SCRIPT_DIR() . self::TEXTURE_STORAGE_DIR($type);
    }
    /** FOR URL ONLY */
    public static function TEXTURE_STORAGE_URL(ResponseTypeEnum|TextureStorageTypeEnum $type): string
    {
        return Main::getPublicApplicationURL() . self::TEXTURE_STORAGE_PATH($type);
    }
    /** FOR URL ONLY */
    public static function PATH_URL(ResponseTypeEnum|TextureStorageTypeEnum $type, string $login, ?string $extension = null): string
    {
        $extension ??= self::EXTENSTION();
        return self::TEXTURE_STORAGE_URL($type) . $login . $extension;
    }

    /** FOR PHP ONLY */
    public static function TEXTURE_STORAGE_FULL_PATH(ResponseTypeEnum|TextureStorageTypeEnum $type): string
    {
        return Path::ROOT_FOLDER() . self::TEXTURE_STORAGE_DIR($type);
    }
    /** FOR PHP ONLY */
    public static function PATH(ResponseTypeEnum|TextureStorageTypeEnum $type, string $login, ?string $extension = null): string
    {
        $extension ??= self::EXTENSTION();
        return self::TEXTURE_STORAGE_FULL_PATH($type) . $login . $extension;
    }

    /** @throws TextureSizeException */
    public static function validateSize(int $width, int $height, ResponseTypeEnum $type = ResponseTypeEnum::SKIN): true
    {
        $valid_size = false;
        foreach (TextureConfig::SIZE($type) as $value) {
            if ($value['w'] == $width && $value['h'] == $height) {
                $valid_size = true;
            }
        }
        return $valid_size ?: throw new TextureSizeException;
    }
    /** @throws TextureSizeHDException */
    public static function validateHDSize(int $width, int $height, ResponseTypeEnum $type = ResponseTypeEnum::SKIN): true
    {
        $valid_size = false;
        foreach (TextureConfig::SIZE_WITH_HD($type) as $value) {
            if ($value['w'] == $width && $value['h'] == $height) {
                $valid_size = true;
            }
        }
        return $valid_size ?: throw new TextureSizeHDException;
    }
    public static function LEGACY_DIGEST(): bool
    {
        return ($ENV = getenv(__FUNCTION__)) === false ? parent::LEGACY_DIGEST : filter_var($ENV, FILTER_VALIDATE_BOOLEAN);
    }
    public static function MAX_SIZE_BYTES(): int
    {
        return ($ENV = getenv(__FUNCTION__)) === false ? parent::MAX_SIZE_BYTES : convertToBytes($ENV);
    }
    public static function SKIN_DEFAULT(): string
    {
        return base64_decode(getenv()[__FUNCTION__] ?? parent::SKIN_DEFAULT) ?: throw new \InvalidArgumentException('Error base64_decode: ' . __FUNCTION__);
    }
    public static function CAPE_DEFAULT(): string
    {
        return base64_decode(getenv()[__FUNCTION__] ?? parent::CAPE_DEFAULT) ?: throw new \InvalidArgumentException('Error base64_decode: ' . __FUNCTION__);
    }
    public static function digest(string $data, UserStorageTypeEnum $hashType = UserStorageTypeEnum::DB_SHA256): string
    {
        return match ($hashType) {
            UserStorageTypeEnum::DB_SHA1 => hash('sha1', $data),
            UserStorageTypeEnum::DB_SHA256 => hash('sha256', $data),
            default => md5($data)
        };
    }
    public static function digest_legacy(string $data): string
    {
        return strtr(base64_encode(md5($data, true)), '+/', '-_');
    }
}
