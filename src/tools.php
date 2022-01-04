<?php

namespace wishlist;

class tools {
    public static function getRandomString(int $length = 10) : string {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randstring = '';
        for ($i = 0; $i < $length; $i++) {
            $randstring = $randstring.$characters[Rand(0, strlen($characters)-1)];
        }
        return $randstring;
    }

    public static function generateToken() : string {
        return time().self::getRandomString(20);
    }
}