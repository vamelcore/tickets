<?php

namespace App\Helpers;

class PasswordHelper
{
    public static function randString(int $length = 12 )
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        return substr(str_shuffle(str_repeat($chars, $length)), rand(0, strlen($chars) * $length - $length), $length);
    }
}
