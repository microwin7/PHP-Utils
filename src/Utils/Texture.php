<?php

namespace Microwin7\PHPUtils\Utils;

use Microwin7\PHPUtils\Utils\Path;
use Microwin7\PHPUtils\Rules\Regex;
use function Microwin7\PHPUtils\convertToBytes;
use function Microwin7\PHPUtils\ar_slash_string;
use function Microwin7\PHPUtils\str_ends_with_slash;
use Microwin7\PHPUtils\Exceptions\TextureSizeException;
use Microwin7\PHPUtils\Exceptions\TextureSizeHDException;
use Microwin7\PHPUtils\Contracts\User\UserStorageTypeEnum;
use Microwin7\PHPUtils\Contracts\Texture\Enum\ResponseTypeEnum;
use Microwin7\PHPUtils\Contracts\Texture\Enum\TextureStorageTypeEnum;

class Texture
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
    private const string SKIN_DEFAULT = "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";
    private const string CAPE_DEFAULT = "iVBORw0KGgoAAAANSUhEUgAAAEAAAAAgAQMAAACYU+zHAAAAA1BMVEVHcEyC+tLSAAAAAXRSTlMAQObY
    ZgAAAAxJREFUeAFjGAV4AQABIAABL3HDQQAAAABJRU5ErkJggg==";
    private const string SKIN_DEFAULT_SHA256 = '98805f6ab41575b7ff4af11b70c074773c5bcc210f2429f6b5513150d746e4cd';
    private const string CAPE_DEFAULT_SHA256 = 'f2072fdfff5302b7c13672e54fdc8895dc75b3f675be3a43245de6894f971e38';
    /** list<array{0: int, 1: int}> */
    private const array SKIN_SIZE = [
        [64, 64],
        [64, 32]
    ];
    /** list<array{0: int, 1: int}> */
    private const array CAPE_SIZE = [
        [64, 32]
    ];
    /** list<array{0: int, 1: int}> */
    private const array SKIN_SIZE_HD = [
        [128, 64],
        [128, 128],
        [256, 128],
        [256, 256],
        [512, 256],
        [512, 512],
        [1024, 512],
        [1024, 1024]
    ];
    /** list<array{0: int, 1: int}> */
    private const array CAPE_SIZE_HD = [
        [128, 64],
        [256, 128],
        [512, 256],
        [1024, 512]
    ];

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
    public static function TEXTURE_STORAGE_TYPE_DIR(ResponseTypeEnum|TextureStorageTypeEnum $type): string
    {
        return match ($type) {
            ResponseTypeEnum::SKIN, ResponseTypeEnum::CAPE, ResponseTypeEnum::AVATAR,
            ResponseTypeEnum::FRONT, ResponseTypeEnum::FRONT_CAPE, ResponseTypeEnum::FRONT_WITH_CAPE,
            ResponseTypeEnum::BACK, ResponseTypeEnum::BACK_CAPE, ResponseTypeEnum::BACK_WITH_CAPE,
            ResponseTypeEnum::CAPE_RESIZE => self::STORAGE_DIR() . ar_slash_string(getenv()['TEXTURE_' . $type->name . '_PATH'] ?? (strtolower($type->name) . 's')),
            TextureStorageTypeEnum::MOJANG, TextureStorageTypeEnum::COLLECTION => self::STORAGE_DIR() . ar_slash_string(getenv()['TEXTURE_' . $type->name . '_PATH'] ?? $type->name),
            default => throw new \InvalidArgumentException(sprintf('Un-supported TEXTURE_STORAGE_TYPE_DIR: %s', $type->name))
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
    public static function TEXTURE_STORAGE_FULL_PATH(ResponseTypeEnum|TextureStorageTypeEnum $type, ?int $size = null): string
    {
        $size = null === $size ? '' : str_ends_with_slash((string) $size);
        return Path::ROOT_FOLDER() . self::TEXTURE_STORAGE_TYPE_DIR($type) . $size;
    }
    /** FOR PHP ONLY */
    public static function PATH(ResponseTypeEnum|TextureStorageTypeEnum $type, string $login, ?string $extension = null, ?int $size = null): string
    {
        $extension ??= self::EXTENSTION();
        return self::TEXTURE_STORAGE_FULL_PATH($type, $size) . $login . $extension;
    }
    /** @return array<array-key, object{width: int, height: int}> */
    public static function SIZE(ResponseTypeEnum $type = ResponseTypeEnum::SKIN): array
    {
        return match ($type) {
            ResponseTypeEnum::SKIN => self::parseSizeEnvArray('SKIN_SIZE'),
            ResponseTypeEnum::CAPE => self::parseSizeEnvArray('CAPE_SIZE'),
            default => throw new \InvalidArgumentException(sprintf('Un-supported texture size type: %s', $type->name))
        };
    }
    /** @return array<array-key, object{width: int, height: int}> */
    public static function SIZE_WITH_HD(ResponseTypeEnum $type = ResponseTypeEnum::SKIN): array
    {
        return match ($type) {
            ResponseTypeEnum::SKIN => self::parseSizeEnvArray('SKIN_SIZE_HD'),
            ResponseTypeEnum::CAPE => self::parseSizeEnvArray('CAPE_SIZE_HD'),
            default => throw new \InvalidArgumentException(sprintf('Un-supported texture size type: %s', $type->name))
        };
    }
    /**
     * @param "SKIN_SIZE"|"CAPE_SIZE"|"SKIN_SIZE_HD"|"CAPE_SIZE_HD"
     * @return array<array-key, object{width: int, height: int}>
     */
    public static function parseSizeEnvArray(string $env_name): array
    {
        // putenv('SKIN_SIZE=128,64|128,128|256,128|256');
        try {
            $ENV = getenv($env_name);
            if ($ENV === false || empty($ENV)) new \RuntimeException("ENV $env_name empty");
            return array_map(
                fn(string $pair) =>
                (object)array_combine(
                    ['width', 'height'],
                    array_map('intval', explode(',', $pair))
                ),
                explode('|', $ENV)
            );
        } catch (\ValueError | \RuntimeException) {
            return array_map(fn(array $item) => (object) ['width' => $item[0], 'height' => $item[1]], self::{$env_name});
        }
    }
    /** @throws TextureSizeException */
    public static function validateSize(int $width, int $height, ResponseTypeEnum $type = ResponseTypeEnum::SKIN): true
    {
        $valid_size = false;
        foreach (self::SIZE($type) as $value) {
            if ($value->width == $width && $value->height == $height) {
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
            if ($value->width == $width && $value->height == $height) {
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
        return base64_decode(self::SKIN_DEFAULT);
    }
    public static function CAPE_DEFAULT(): string
    {
        return base64_decode(self::CAPE_DEFAULT);
    }
    public static function SKIN_DEFAULT_SHA256(): string
    {
        $SKIN_DEFAULT_SHA256 = getenv()[__FUNCTION__] ?? null;
        $SKIN_DEFAULT_SHA256 !== null ? Regex::valid_with_pattern($SKIN_DEFAULT_SHA256, Regex::SHA256) : $SKIN_DEFAULT_SHA256 = self::SKIN_DEFAULT_SHA256;
        return $SKIN_DEFAULT_SHA256;
    }
    public static function CAPE_DEFAULT_SHA256(): string
    {
        $CAPE_DEFAULT_SHA256 = getenv()[__FUNCTION__] ?? null;
        $CAPE_DEFAULT_SHA256 !== null ? Regex::valid_with_pattern($CAPE_DEFAULT_SHA256, Regex::SHA256) : $CAPE_DEFAULT_SHA256 = self::CAPE_DEFAULT_SHA256;
        return $CAPE_DEFAULT_SHA256;
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
