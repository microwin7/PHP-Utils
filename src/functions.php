<?php

namespace Microwin7\PHPUtils;

use Microwin7\PHPUtils\Contracts\Enum\EnumRequestInterface;

if (!function_exists('str_starts_with_slash')) {
    function str_starts_with_slash(string $string, bool $needle_starts_with_slash = FALSE): string
    {
        if (str_starts_with($string, DIRECTORY_SEPARATOR)) {
            if (!$needle_starts_with_slash) $string =  substr($string, 1);
        } else {
            if ($needle_starts_with_slash) $string =  DIRECTORY_SEPARATOR . $string;
        }
        return $string;
    }
}
if (!function_exists('str_ends_with_slash')) {
    function str_ends_with_slash(string $string, bool $needle_ends_with_slash = TRUE): string
    {
        if (str_ends_with($string, DIRECTORY_SEPARATOR)) {
            if (!$needle_ends_with_slash) $string =  substr($string, 0, -1);
        } else {
            if ($needle_ends_with_slash) $string .=  DIRECTORY_SEPARATOR;
        }
        return $string;
    }
}
if (!function_exists('ar_slash_string')) {
    function ar_slash_string(string $string, bool $needle_starts_with_slash = FALSE, bool $needle_ends_with_slash = TRUE): string
    {
        return str_ends_with_slash(str_starts_with_slash($string, $needle_starts_with_slash), $needle_ends_with_slash);
    }
}
if (!function_exists('implodeRecursive')) {
    function implodeRecursive($separator, array $array)
    {
        $result = '';
        foreach ($array as $value) {
            if (is_array($value)) {
                $result .= implodeRecursive($separator, $value) . $separator;
            } else {
                if (strrpos($value, '\\') === false) {
                    $result .= $value . $separator;
                } else if (enum_exists($value)) {
                    $argumentClazz = new \ReflectionClass($value);
                    if ($argumentClazz->implementsInterface(EnumRequestInterface::class)) {
                        /** @var \BackedEnum & EnumInterface & EnumRequestInterface $enumClass */
                        $enumClass = $value;
                        $result .= $enumClass::getNameRequestVariable() . $separator;
                    }
                }
            }
        }

        // Удаляем последний разделитель
        $result = rtrim($result, $separator);

        return $result;
    }
}
