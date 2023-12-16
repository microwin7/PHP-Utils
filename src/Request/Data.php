<?php

namespace Microwin7\PHPUtils\Request;

class Data
{
    /**
     * @return array<int|string, non-empty-array<int|string, array<int|string, mixed>|string>|string>
     */
    public static function getData(): array
    {
        /** @var array<int|string, non-empty-array<int|string, array<int|string, mixed>|string>|string> $decode */
        $decode = json_decode(file_get_contents('php://input'), true);
        return $decode;
    }
    public static function requiredUrl(): string
    {
        /** @psalm-suppress PossiblyUndefinedArrayOffset */
        return $_SERVER['QUERY_STRING'];
    }
    public static function getDataFromUrl(string $url): string|false
    {
        $data = file_get_contents($url, false, stream_context_create(['http' => ['ignore_errors' => true]]));
        /** @psalm-var non-empty-list<non-falsy-string> $http_response_header */
        $headers = self::parseHeaders($http_response_header);
        if ($headers['reponse_code'] != 200) return false;
        return $data;
    }
    /**
     * @param string[] $headers
     *
     * @psalm-param non-empty-list<non-falsy-string> $headers
     *
     * @return (int|string)[]
     *
     * @psalm-return array<int<0, max>|string, int|string>
     */
    public static function parseHeaders(array $headers): array
    {
        $head = [];
        foreach ($headers as $value) {
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
