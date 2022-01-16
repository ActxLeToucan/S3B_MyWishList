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
        $MotDePasse = password_hash($MotDePasse, PASSWORD_DEFAULT,$options);
        $MotDePasseConfirm = $content['password_confirm'];
        echo (password_verify($MotDePasseConfirm,$MotDePasse));
        $Email=$content['email'];

        $userNameExist = Authenticate::where("username", "=", $NomUtilisateur)->count();

        if (strlen($NomUtilisateur) < $this->TAILLE_USERNAME_MIN) {
            $notifMsg = "Ce nom d'utilisateur est trop court. Réessayez.";
            return $rs->withRedirect($base."/signUp?notif=$notifMsg");
        } else if (strlen($NomUtilisateur) > $this->TAILLE_USERNAME_MAX) {
            $notifMsg = "Ce nom d'utilisateur est trop long. Réessayez.";
            return $rs->withRedirect($base."/signUp?notif=$notifMsg");
        } else if ($userNameExist != 0) {
            $notifMsg = "Ce nom d'utilisateur est déjà pris. Réessayez.";
            return $rs->withRedirect($base."/signUp?notif=$notifMsg");
        } else if (strlen($MotDePasse) < $this->TAILLE_MDP_MIN) {
            $notifMsg = "Ce mot de passe est trop court. Réessayez.";
            return $rs->withRedirect($base."/signUp?notif=$notifMsg");
        } else if (strlen($MotDePasse) > $this->TAILLE_MDP_MAX) {
            $notifMsg = "Ce mot de passe est trop long. Réessayez.";
            return $rs->withRedirect($base."/signUp?notif=$notifMsg");
        } else if ( !password_verify($MotDePasseConfirm,$MotDePasse)) {
            $notifMsg = "Les mots de passe ne correspondent pas. Réessayez.";
            return $rs->withRedirect($base."/signUp?notif=$notifMsg");
        } else {
            $newUser = new Authenticate();
            $newUser->username=$NomUtilisateur;
            $newUser->password=$MotDePasse;
            $newUser->email=$Email;
            $newUser->Niveau_acces=1;
            $newUser->save();

            // gestion session
            $this->gestionSession($newUser);

            $notifMsg = "Vous êtes connecté en tant que $NomUtilisateur.";
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

                $notifMsg = "Vous êtes connecté en tant que $NomUtilisateur.";
                return $rs->withRedirect($base."?notif=$notifMsg");
            }
        }

        session_destroy();
        session_start();

        $notifMsg = "Mot de passe ou nom d'utilisateur incorrect.";
        return $rs->withRedirect($base."/login?notif=$notifMsg");
    }   

    public function changeMail($rq, $rs, $args){
        $container = $this->c;
        $base = $rq->getUri()->getBasePath();
        $route_uri = $container->router->pathFor('changeMail');
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
        $route_uri = $container->router->pathFor('changeMail');
        $url = $base . $route_uri;

        $content = $rq->getParsedBody();

        $new_Psw = filter_var($content['newPsw'], FILTER_SANITIZE_STRING);
        $new_Psw_confirm = filter_var($content['newPsw_confirm'], FILTER_SANITIZE_STRING);

        if (isset($_SESSION['username']) && isset($_SESSION['AccessRights'])) {
            $user = Authenticate::where("username", "=", $_SESSION["username"])->first();

            if($new_Psw == $new_Psw_confirm){
                $options =['cost' => 12];
                $new_Psw = password_hash($new_Psw, PASSWORD_DEFAULT,$options);
                Authenticate::where("id", "=", $user->id)->update(['password' => $new_Psw]);
                $args[] = "changement de mdp";
                return $this->logout($rq, $rs, $args);

            }else{
                $notifMsg = 'Erreur : les deux mots de passe sont différents !';
            }
        } else {
            $notifMsg = 'Erreur : session';
        }
        return $rs->withRedirect($base."/monCompte?notif=$notifMsg");
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
        if(in_array('changement de mdp',$args)){
            $notifMsg = "Votre mot de passe a bien été changé.";
        }else{
            $notifMsg = "Vous avez été déconnecté.";
        }
        
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



