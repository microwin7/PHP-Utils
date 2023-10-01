<?php

namespace Microwin7\PHPUtils;

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