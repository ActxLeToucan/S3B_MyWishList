<?php

namespace wishlist\login;

use wishlist\controllers\RegisterController;

class Authentication{


    public static function createUser(string $userid,string $password, string $email, $c, $rq, $rs ){
        $cleanID=filter_var($userid,FILTER_SANITIZE_STRING);
        $cleanPWD=filter_var($password,FILTER_SANITIZE_STRING);
        $cleanEMAIL=filter_var($email,FILTER_SANITIZE_STRING);
        $hashedPWD=password_hash($cleanPWD, PASSWORD_DEFAULT,['cost'=> 12] );
        $cleanUserAuth=array('Username' => $cleanID, 'Password' =>$hashedPWD, 'Email'=>$cleanEMAIL);
        $controller = new \wishlist\controllers\RegisterController($c);
        $controller->newUser($rq,$rs,$cleanUserAuth);
        //Normalement, l'utilisateur est créé
    }

    public static function Authenticate(string $userid,string $password, $c, $rq, $rs){
        $cleanID=filter_var($userid,FILTER_SANITIZE_STRING);
        $cleanPWD=filter_var($password,FILTER_SANITIZE_STRING);
        $hashedPWD=password_hash($cleanPWD, PASSWORD_DEFAULT,['cost'=> 12] );
        $cleanUserAuth=array('Username' => $cleanID, 'Password' =>$hashedPWD);
        $controller = new \wishlist\controllers\RegisterController($c);
        $controller->authentification($rq,$rs,$cleanUserAuth);
    }

    public static function loadProfile(string $userid ){
        //
    }
    public static function checkAccessRights($required ){

    }
}