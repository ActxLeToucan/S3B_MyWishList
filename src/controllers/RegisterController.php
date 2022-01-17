<?php

namespace wishlist\controllers;

use wishlist\models\Authenticate;
use wishlist\tools;
use wishlist\vues\VueRegister;


class RegisterController{
    const LOGIN = 'login';
    const SIGNUP = 'signUp';
    const TOKEN = 'token';
    const TAILLE_USERNAME_MIN = 4;
    const TAILLE_USERNAME_MAX = 100;
    const TAILLE_MDP_MIN = 8;
    const TAILLE_MDP_MAX = 256;
    private $c;


    /**
     * @param $c
     */
    public function __construct($c) {
        $this->c = $c;
    }

    /**
     * @return mixed
     */

    public function newUser($rq, $rs, $args)  {
        

        $container = $this->c;
        $base = $rq->getUri()->getBasePath();
        $route_uri = $container->router->pathFor('signupConfirm');
        $url = $base . $route_uri;
        $content = $rq->getParsedBody();

        $NomUtilisateur = $content['username'];
        $MotDePasse = $content['password'];
        $options =['cost' => 12];
        $MotDePasseConfirm = $content['password_confirm'];
        $Email=$content['email'];

        $userNameExist = Authenticate::where("username", "=", $NomUtilisateur)->count();

        if (strlen($NomUtilisateur) < self::TAILLE_USERNAME_MIN) {
            $notifMsg = urlencode("Ce nom d'utilisateur est trop court. Réessayez.");
            return $rs->withRedirect($base."/signUp?notif=$notifMsg");
        } else if (strlen($NomUtilisateur) > self::TAILLE_USERNAME_MAX) {
            $notifMsg = urlencode("Ce nom d'utilisateur est trop long. Réessayez.");
            return $rs->withRedirect($base."/signUp?notif=$notifMsg");
        } else if ($userNameExist != 0) {
            $notifMsg = urlencode("Ce nom d'utilisateur est déjà pris. Réessayez.");
            return $rs->withRedirect($base."/signUp?notif=$notifMsg");
        } else if (strlen($MotDePasse) < self::TAILLE_MDP_MIN) {
            $notifMsg = urlencode("Ce mot de passe est trop court. Réessayez.");
            return $rs->withRedirect($base."/signUp?notif=$notifMsg");
        } else if (strlen($MotDePasse) > self::TAILLE_MDP_MAX) {
            $notifMsg = urlencode("Ce mot de passe est trop long. Réessayez.");
            return $rs->withRedirect($base."/signUp?notif=$notifMsg");
        } else if ($MotDePasseConfirm != $MotDePasse) {
            $notifMsg = urlencode("Les mots de passe ne correspondent pas. Réessayez.");
            return $rs->withRedirect($base."/signUp?notif=$notifMsg");
        } else {
            $MotDePasse = password_hash($MotDePasse, PASSWORD_DEFAULT, $options);
            $newUser = new Authenticate();
            $newUser->username=$NomUtilisateur;
            $newUser->password=$MotDePasse;
            $newUser->email=$Email;
            $newUser->Niveau_acces=1;
            $newUser->save();

            // gestion session
            $this->gestionSession($newUser);

            $notifMsg = urlencode("Vous êtes connecté en tant que $NomUtilisateur.");
            return $rs->withRedirect($base."?notif=$notifMsg");
        }
    }

    public function authentification($rq,$rs,$args){
        $container = $this->c;
        $base = $rq->getUri()->getBasePath();
        $route_uri = $container->router->pathFor('loginConfirm');
        $url = $base . $route_uri;

        $content = $rq->getParsedBody();

        $NomUtilisateur = $content['username'];
        $MotDePasse = $content['password'];


        $userNameExist = Authenticate::where("username", "=", $NomUtilisateur)->count();

        if ($userNameExist == 1) {
            $GetUser=Authenticate::where("username","=",$NomUtilisateur)->first();
            $HashedPassword=$GetUser->password;
            if (password_verify($MotDePasse,$HashedPassword)) {
                $user = Authenticate::where('username', '=', $NomUtilisateur)->first();

                $this->gestionSession($user);

                $notifMsg = urlencode("Vous êtes connecté en tant que $NomUtilisateur.");
                return $rs->withRedirect($base."?notif=$notifMsg");
            }
        }

        session_destroy();
        session_start();

        $notifMsg = urlencode("Mot de passe ou nom d'utilisateur incorrect.");
        return $rs->withRedirect($base."/login?notif=$notifMsg");
    }   

    public function changeMail($rq, $rs, $args){
        $container = $this->c;
        $base = $rq->getUri()->getBasePath();
        $route_uri = $container->router->pathFor('changeMail_confirm');
        $url = $base . $route_uri;

        $content = $rq->getParsedBody();

        $new_mail = filter_var($content['newMail'], FILTER_SANITIZE_STRING);
        $new_mail_confirm = filter_var($content['newMail_confirm'], FILTER_SANITIZE_STRING);

        if (isset($_SESSION['username']) && isset($_SESSION['AccessRights'])) {
            $user = Authenticate::where("username", "=", $_SESSION["username"])->first();

            if($new_mail == $new_mail_confirm){
                Authenticate::where("id", "=", $user->id)->update(['email' => $new_mail]);
                $notifMsg = 'Votre email a bien été changé en :'.$new_mail;
            }else{
                $notifMsg = 'Erreur : les deux adresses mails sont différentes !';
            }
        } else {
            $notifMsg = 'Erreur : session';
        }
        return $rs->withRedirect($base."/monCompte?notif=$notifMsg");
    }

    public function changePsw($rq, $rs, $args){
        $container = $this->c;
        $base = $rq->getUri()->getBasePath();
        $route_uri = $container->router->pathFor('changePassword_confirm');
        $url = $base . $route_uri;

        $content = $rq->getParsedBody();

        $new_Psw = filter_var($content['newPsw'], FILTER_SANITIZE_STRING);
        $new_Psw_confirm = filter_var($content['newPsw_confirm'], FILTER_SANITIZE_STRING);

        if (!isset($_SESSION['username']) && isset($_SESSION['AccessRights'])) {
            $notifMsg = urlencode('Erreur : session');
            return $rs->withRedirect($base."/monCompte?notif=$notifMsg");
        }else if ( strlen($new_Psw) >= self::TAILLE_MDP_MAX) {
            $notifMsg = urlencode('Erreur : Le mot de passe est trop long !');
            return $rs->withRedirect($base."/monCompte?notif=$notifMsg");
        }else if ( strlen($new_Psw) <= self::TAILLE_MDP_MIN) {
            $notifMsg = urlencode('Erreur : Le mot de passe est trop court !');
            return $rs->withRedirect($base."/monCompte?notif=$notifMsg");
        }else if ($new_Psw != $new_Psw_confirm){
            $notifMsg = urlencode('Erreur : les deux mots de passe sont différents !');
            return $rs->withRedirect($base."/monCompte?notif=$notifMsg");
        }else{
            $options =['cost' => 12];
            $new_Psw = password_hash($new_Psw, PASSWORD_DEFAULT,$options);

            $user = Authenticate::where("username", "=", $_SESSION["username"])->first();
            Authenticate::where("id", "=", $user->id)->update(['password' => $new_Psw]);
            
            session_destroy();
            session_start();
            $notifMsg = $notifMsg = urlencode("Votre mot de passe a bien été changé.");

            return $rs->withRedirect($base."/login?notif=$notifMsg");
        }
    }

    public function changeMailPage($rq, $rs, $args) {
            $container = $this->c;
            $base = $rq->getUri()->getBasePath();
            $route_uri = $container->router->pathFor('login');
            $url = $base . $route_uri;

            $notif = tools::prepareNotif($rq);

            $v = new VueRegister([], RegisterController::LOGIN, $notif);
            $rs->getBody()->write($v->render());
            return $rs;
    }
    
    public function changePswPage($rq, $rs, $args) {
        $container = $this->c;
        $base = $rq->getUri()->getBasePath();
        $route_uri = $container->router->pathFor('login');
        $url = $base . $route_uri;

        $notif = tools::prepareNotif($rq);

        $v = new VueRegister([], RegisterController::LOGIN, $notif);
        $rs->getBody()->write($v->render());
        return $rs;
}


    public function loginPage($rq, $rs, $args) {
        $container = $this->c;
        $base = $rq->getUri()->getBasePath();
        $route_uri = $container->router->pathFor('login');
        $url = $base . $route_uri;

        $notif = tools::prepareNotif($rq);

        $v = new VueRegister([], RegisterController::LOGIN, $notif);
        $rs->getBody()->write($v->render());
        return $rs;
    }

    public function signUpPage($rq, $rs, $args) {
        $container = $this->c;
        $base = $rq->getUri()->getBasePath();
        $route_uri = $container->router->pathFor('signUp');
        $url = $base . $route_uri;

        $notif = tools::prepareNotif($rq);

        $v = new VueRegister([], RegisterController::SIGNUP, $notif);
        $rs->getBody()->write($v->render());
        return $rs;
    }

    public function accessListToken($rq, $rs, $args) {
        $container = $this->c;
        $base = $rq->getUri()->getBasePath();
        $route_uri = $container->router->pathFor('token');
        $url = $base . $route_uri;

        $notif = tools::prepareNotif($rq);

        $v = new VueRegister([], RegisterController::TOKEN, $notif);
        $rs->getBody()->write($v->render());
        return $rs;
    }

    public function logout($rq, $rs, $args) {
        $container = $this->c;
        $base = $rq->getUri()->getBasePath();
        $route_uri = $container->router->pathFor('logout');
        $url = $base . $route_uri;


        session_destroy();
        session_start();
        
        $notifMsg = urlencode("Vous avez été déconnecté.");
        
        
        return $rs->withRedirect($base."/login?notif=$notifMsg");
    }

    /**
     * @param Authenticate $user
     * @return void
     */
    public function gestionSession(Authenticate $user): void {
        if (isset($_SESSION['username'])) {
            session_destroy();
            session_start();
        }
        $_SESSION['username'] = $user['username'];
        $_SESSION['AccessRights'] = $user['Niveau_acces'];
    }
}



