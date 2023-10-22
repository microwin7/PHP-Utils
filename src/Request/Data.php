<?php

namespace Microwin7\PHPUtils\Request;

class Data
{
    public static function getData()
    {
        return json_decode(file_get_contents('php://input'), true);
    }
    public static function requiredUrl(): string
    {
        return $_SERVER['QUERY_STRING'];
    }
    public static function getDataFromUrl($url): string|false
    {
        $data = file_get_contents($url, false, stream_context_create(['http' => ['ignore_errors' => true]]));
        $headers = self::parseHeaders($http_response_header);
        if ($headers['reponse_code'] != 200) return false;
        return $data;
    }
    public static function parseHeaders($headers)
    {
        $head = [];
        foreach ($headers as $key => $value) {
            $t = explode(':', $value, 2);
            if (isset($t[1]))
                $head[trim($t[0])] = trim($t[1]);
            else {
                $head[] = $value;
                if (preg_match("#HTTP/[0-9\.]+\s+([0-9]+)#", $value, $out))
                    $head['reponse_code'] = intval($out[1]);
            }
        }
        return $head;
    }
    public static function base64_url_encode(string $string): string
    {
        return strtr(base64_encode($string), '+/', '-_');
    }
    public static function base64_url_decode(string $string): string
    {
        return base64_decode(strtr($string, '-_', '+/'));
    }
}
