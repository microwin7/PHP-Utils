<?php

namespace Microwin7\PHPUtils\Security;

use Microwin7\PHPUtils\Configs\MainConfig;

class BearerToken
{
    /**
     * Getting BearerToken
     * */
    public static function getBearerToken(): ?string
    {
        $headers = static::get_authorization_header();
        // HEADER: Getting BearerToken from header
        if (!empty($headers)) {
            if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
                return $matches[1];
            }
        }
        return null;
    }
    /** 
     * @deprecated
     * @return string
     */
    public static function getBearer(): string
    {
        $headers = array_change_key_case(getallheaders());
        /** @var string */
        $authorizationHeader = $headers['authorization'] ?? '';

        if (strlen($authorizationHeader) >= 7) {
            return substr($authorizationHeader, 7);
        }
        return '';
    }

    public static function validateBearer(): bool
    {
        return static::getBearerToken() === MainConfig::BEARER_TOKEN;
    }
    /**
     * Getting Authorization header
     *
     * @return null|string
     */
    public static function get_authorization_header(): string|null
    {
        $headers = null;
        if (isset($_SERVER['Authorization'])) {
            $headers = trim($_SERVER["Authorization"]);
        } else if (isset($_SERVER['HTTP_AUTHORIZATION'])) { //Nginx or fast CGI
            $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
        } elseif (function_exists('apache_request_headers')) {
            $requestHeaders = apache_request_headers();
            // Server-side fix for bug in old Android versions (a nice side-effect of this fix means we don't care about capitalization for Authorization)
            $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
            /** @var string $requestHeaders['Authorization'] */
            if (isset($requestHeaders['Authorization'])) {
                $headers = trim($requestHeaders['Authorization']);
            }
        }
        return $headers;
    }
}
