<?php

/**
 * Implements the str_starts_with function if php version < 8.0
 */
if (!function_exists('str_starts_with')) {
    function str_starts_with(string $haystack, string $needle)
    {
        return (string)$needle !== '' && strncmp($haystack, $needle, strlen($needle)) === 0;
    }
}
/**
 * Implements the str_ends_with function if php version < 8.0
 */
if (!function_exists('str_ends_with')) {
    function str_ends_with(string $haystack, string $needle): bool
    {
        return $needle !== '' && substr($haystack, -strlen($needle)) === (string)$needle;
    }
}
/**
 * Implements the str_contains function if php version < 8.0
 */
if (!function_exists('str_contains')) {
    function str_contains(string $haystack, string $needle): bool
    {
        return $needle !== '' && mb_strpos($haystack, $needle) !== false;
    }
}
