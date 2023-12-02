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
     * Undocumented function
     * 
     * @deprecated
     * @return string
     */
    public static function getBearer(): string
    {
        return substr(@array_change_key_case(getallheaders())['authorization']  ?? '', 7) ?? ''; // в конце нужно до 8.0, после возвращается ""
    }
    public static function validateBearer(): bool
    {
        return static::getBearerToken() === MainConfig::BEARER_TOKEN;
    }
    /** 
     * Getting Authorization header
     * */
    public static function get_authorization_header()
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
            //print_r($requestHeaders);
            if (isset($requestHeaders['Authorization'])) {
                $headers = trim($requestHeaders['Authorization']);
            }
        }
        return $headers;
    }
}
