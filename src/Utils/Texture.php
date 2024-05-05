<?php

namespace Microwin7\PHPUtils\Utils;

use Microwin7\PHPUtils\Utils\Path;
use Microwin7\PHPUtils\Configs\TextureConfig;
use function Microwin7\PHPUtils\convertToBytes;
use function Microwin7\PHPUtils\ar_slash_string;
use Microwin7\PHPUtils\Exceptions\TextureSizeException;
use Microwin7\PHPUtils\Exceptions\TextureSizeHDException;
use Microwin7\PHPUtils\Contracts\User\UserStorageTypeEnum;
use Microwin7\PHPUtils\Contracts\Texture\Enum\ResponseTypeEnum;
use Microwin7\PHPUtils\Contracts\Texture\Enum\TextureStorageTypeEnum;

class Texture extends TextureConfig
{
    /**
     * Для вызова, сохранения, проверок
     * Может быть пустой строкой, для таких файлов как хеш сумма
     * Точка будет добавлена автоматически
     */
    private const string TEXTURE_EXTENSTION = 'png';
    /** Включить при необходимости! Использовать на версии GravitLauncher меньше 5.5.x */
    private const bool LEGACY_DIGEST = false;
    /** Максимальный размер загружаемого файла byte => Kbyte, Kbyte => MB * 2 */
    private const int MAX_SIZE_BYTES = 2 * 1024 * 1024;
    /** https://base64.guru/converter/encode/image */
    private const string SKIN_DEFAULT = "iVBORw0KGgoAAAANSUhEUgAAAEAAAAAgCAMAAACVQ462AAAAWlBMVEVHcEwsHg51Ri9qQC+HVTgjIyNO
    LyK7inGrfWaWb1udZkj///9SPYmAUjaWX0FWScwoKCgAzMwAXl4AqKgAaGgwKHImIVtGOqU6MYkAf38AmpoAr68/Pz9ra2t3xPtNAA
    AAAXRSTlMAQObYZgAAAZJJREFUeNrUzLUBwDAUA9EPMsmw/7jhNljl9Xdy0J3t5CndmcOBT4Mw8/8P4pfB6sNg9yA892wQvwzSIr8f
    5JRzSeS7AaiptpxazUq8GPQB5uSe2DH644GTsDFsNrqB9CcDgOCAmffegWWwAExnBrljqowsFBuGYShY5oakgOXs/39zF6voDG9r+w
    LvTCVUcL+uV4m6uXG/L3Ut691697tgnZgJavinQHOB7DD8awmaLWEmaNuu7YGf6XcIITRm19P1ahbARCRGEc8x/UZ4CroXAQTVIGL0
    YySrREBADFGicS8XtG8CTS+IGU2F6EgSE34VNKoNz8348mzoXGDxpxkQBpg2bWobjgZSm+uiKDYH2BAO8C4YBmbgAjpq5jUl4yGJC4
    6HQ7HJBfkeTAImIEmgmtpINi44JsHx+CKA/BTuArISXeBTR4AI5gK4C2JqRfPs0HNBkQnG8S4Yxw8IGoIZfXEBOW1D4YJDAdNSXgRe
    vP+ylK6fGBCwsWywmA19EtBkJr8K2t4N5pnAVwH0jptsBp+2gUFj4tL5ywAAAABJRU5ErkJggg==";
    private const string CAPE_DEFAULT = "iVBORw0KGgoAAAANSUhEUgAAAEAAAAAgAQMAAACYU+zHAAAAA1BMVEVHcEyC+tLSAAAAAXRSTlMAQObY
    ZgAAAAxJREFUeAFjGAV4AQABIAABL3HDQQAAAABJRU5ErkJggg==";

    /** BASE DIR */
    public static function STORAGE_DIR(): string
    {
        $STORAGE_DIR = getenv()['STORAGE_DIR'] ?? '';
        $STORAGE_DIR = !empty($STORAGE_DIR) ? $STORAGE_DIR : TextureStorageTypeEnum::getNameRequestVariable();
        return ar_slash_string($STORAGE_DIR);
    }
    /** BASE DIR
     * @param ResponseTypeEnum::SKIN|ResponseTypeEnum::CAPE|ResponseTypeEnum::AVATAR|
     * ResponseTypeEnum::FRONT|ResponseTypeEnum::FRONT_CAPE|ResponseTypeEnum::FRONT_WITH_CAPE|
     * ResponseTypeEnum::BACK|ResponseTypeEnum::BACK_CAPE|ResponseTypeEnum::BACK_WITH_CAPE|
     * ResponseTypeEnum::CAPE_RESIZE|
     * TextureStorageTypeEnum::MOJANG|TextureStorageTypeEnum::COLLECTION $type
     */
    public static function TEXTURE_STORAGE_DIR(ResponseTypeEnum|TextureStorageTypeEnum $type): string
    {
        return match ($type) {
            ResponseTypeEnum::SKIN, ResponseTypeEnum::CAPE, ResponseTypeEnum::AVATAR,
            ResponseTypeEnum::FRONT, ResponseTypeEnum::FRONT_CAPE, ResponseTypeEnum::FRONT_WITH_CAPE,
            ResponseTypeEnum::BACK, ResponseTypeEnum::BACK_CAPE, ResponseTypeEnum::BACK_WITH_CAPE,
            ResponseTypeEnum::CAPE_RESIZE => self::STORAGE_DIR() . ar_slash_string(getenv()['TEXTURE_' . $type->name . '_PATH'] ?? (strtolower($type->name) . 's')),
            TextureStorageTypeEnum::MOJANG, TextureStorageTypeEnum::COLLECTION => self::STORAGE_DIR() . ar_slash_string(getenv()['TEXTURE_' . $type->name . '_PATH'] ?? $type->name),
            default => throw new \InvalidArgumentException(sprintf('Un-supported TEXTURE_STORAGE_DIR: %s', $type->name))
        };
    }
    public static function EXTENSTION(): string
    {
        return empty($EXT = getenv()['TEXTURE_EXTENSTION'] ?? self::TEXTURE_EXTENSTION) ? '' : '.' . $EXT;
    }

    /** FOR PHP ONLY
     * @param ResponseTypeEnum::SKIN|ResponseTypeEnum::CAPE|ResponseTypeEnum::AVATAR|
     * ResponseTypeEnum::FRONT|ResponseTypeEnum::FRONT_CAPE|ResponseTypeEnum::FRONT_WITH_CAPE|
     * ResponseTypeEnum::BACK|ResponseTypeEnum::BACK_CAPE|ResponseTypeEnum::BACK_WITH_CAPE|
     * ResponseTypeEnum::CAPE_RESIZE|
     * TextureStorageTypeEnum::MOJANG|TextureStorageTypeEnum::COLLECTION $type
     */
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
    /** @return array<array{w: int, h: int}> */
    public static function SIZE(ResponseTypeEnum $type = ResponseTypeEnum::SKIN): array
    {
        return match ($type) {
            ResponseTypeEnum::SKIN => parent::SKIN_SIZE,
            ResponseTypeEnum::CAPE => parent::CAPE_SIZE,
            default => throw new \InvalidArgumentException(sprintf('Un-supported texture size type: %s', $type->name))
        };
    }
    /** @return array<array{w: int, h: int}> */
    public static function SIZE_WITH_HD(ResponseTypeEnum $type = ResponseTypeEnum::SKIN): array
    {
        return match ($type) {
            ResponseTypeEnum::SKIN => parent::SKIN_SIZE_HD,
            ResponseTypeEnum::CAPE => parent::CAPE_SIZE_HD,
            default => throw new \InvalidArgumentException(sprintf('Un-supported texture size type: %s', $type->name))
        };
    }
    /** @throws TextureSizeException */
    public static function validateSize(int $width, int $height, ResponseTypeEnum $type = ResponseTypeEnum::SKIN): true
    {
        $valid_size = false;
        foreach (self::SIZE($type) as $value) {
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
        foreach (self::SIZE_WITH_HD($type) as $value) {
            if ($value['w'] == $width && $value['h'] == $height) {
                $valid_size = true;
            }
        }
        return $valid_size ?: throw new TextureSizeHDException;
    }
    public static function LEGACY_DIGEST(): bool
    {
        return ($ENV = getenv(__FUNCTION__)) === false ? self::LEGACY_DIGEST : filter_var($ENV, FILTER_VALIDATE_BOOLEAN);
    }
    public static function MAX_SIZE_BYTES(): int
    {
        return ($ENV = getenv(__FUNCTION__)) === false ? self::MAX_SIZE_BYTES : convertToBytes($ENV);
    }
    public static function SKIN_DEFAULT(): string
    {
        return base64_decode(getenv()[__FUNCTION__] ?? self::SKIN_DEFAULT) ?: throw new \InvalidArgumentException('Error base64_decode: ' . __FUNCTION__);
    }
    public static function CAPE_DEFAULT(): string
    {
        return base64_decode(getenv()[__FUNCTION__] ?? self::CAPE_DEFAULT) ?: throw new \InvalidArgumentException('Error base64_decode: ' . __FUNCTION__);
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
