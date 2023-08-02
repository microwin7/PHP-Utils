<?php

namespace Microwin7\PHPUtils\Security\Hashers;

use Microwin7\PHPUtils\Exceptions\NumberFormatException;

class PHPass
{
    private const PHPASS_ITOA64 = './0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';

    public static function phpass_validation($password, $encryptedPassword)
    {
        $id = substr($encryptedPassword, 0, 3);
        $entry = strpos(self::PHPASS_ITOA64, $encryptedPassword[3]);
        $salt = substr($encryptedPassword, 4, 8);
        $hash = substr($encryptedPassword, 12);
        if ($id !== '$P$' && $id !== '$H$')
            throw new NumberFormatException($id);
        if ($entry < 7 || $entry > 30) {
            // Восстановить при необходимости
        }
        $count = 1 << $entry;
        $hash_new = md5($salt . $password, TRUE);
        do {
            $hash_new = md5($hash_new . $password, TRUE);
        } while (--$count);
        $enc = self::enc64($hash_new, 16);
        return $enc === $hash;
    }
    private static function enc64($input, $count)
    {
        $itoa64 = self::PHPASS_ITOA64;
        $output = '';
        $i = 0;
        do {
            $value = ord($input[$i++]);
            $output .= $itoa64[$value & 0x3f];
            if ($i < $count)
                $value |= ord($input[$i]) << 8;
            $output .= $itoa64[($value >> 6) & 0x3f];
            if ($i++ >= $count)
                break;
            if ($i < $count)
                $value |= ord($input[$i]) << 16;
            $output .= $itoa64[($value >> 12) & 0x3f];
            if ($i++ >= $count)
                break;
            $output .= $itoa64[($value >> 18) & 0x3f];
        } while ($i < $count);
        return $output;
    }
}