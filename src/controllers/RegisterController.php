<?php

namespace wishlist\controllers;

use wishlist\models\Authenticate;

use Illuminate\Database\Eloquent\Model;
use wishlist\models\Item;
use wishlist\models\Liste;
use wishlist\vues\VueCreaateur;
use wishlist\vues\VueParticipant;
use wishlist\vues\VueRegister;


class RegisterController{
    const CONNECTED ='connected';
    const CONNECTIONFAILED='failed';
    const LOGIN = 'login';
    const SIGNUP = 'signUp';
    const LOGOUT = 'logout';
    const INVALID_USERNAME_TROP_COURT = 'invalid_username_trop_court';
    const INVALID_USERNAME_TROP_LONG = 'invalid_username_trop_long';
    const INVALID_USERNAME_EXISTE_DEJA = 'invalid_username_existe_deja';
    const INVALID_PASSWORD_TROP_COURT = 'invalid_mdp_trop_court';
    const INVALID_PASSWORD_TROP_LONG = 'invalid_mdp_trop_long';
    const INVALID_PASSWORD_PAS_PAREIL = 'invalid_password_pas_pareil';

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
        $TAILLE_USERNAME_MIN = 4;
        $TAILLE_USERNAME_MAX = 100;
        $TAILLE_MDP_MIN = 8;
        $TAILLE_MDP_MAX = 100;

        $container = $this->c;
        $base = $rq->getUri()->getBasePath();
        $route_uri = $container->router->pathFor('signupConfirm');
        $url = $base . $route_uri;
        $content = $rq->getParsedBody();

        $NomUtilisateur = $content['username'];
        $MotDePasse = $content['password'];
        $MotDePasseConfirm = $content['password_confirm'];
        $Email=$content['email'];

        $userNameExist = Authenticate::where("username", "=", $NomUtilisateur)->count();

        if (strlen($NomUtilisateur) < $TAILLE_USERNAME_MIN) {
            $affichage = RegisterController::INVALID_USERNAME_TROP_COURT;
        } else if (strlen($NomUtilisateur) > $TAILLE_USERNAME_MAX) {
            $affichage = RegisterController::INVALID_USERNAME_TROP_LONG;
        } else if ($userNameExist != 0) {
            $affichage = RegisterController::INVALID_USERNAME_EXISTE_DEJA;
        } else if (strlen($MotDePasse) < $TAILLE_MDP_MIN) {
            $affichage = RegisterController::INVALID_PASSWORD_TROP_COURT;
        } else if (strlen($MotDePasse) > $TAILLE_MDP_MAX) {
            $affichage = RegisterController::INVALID_PASSWORD_TROP_LONG;
        } else if ($MotDePasse != $MotDePasseConfirm) {
            $affichage = RegisterController::INVALID_PASSWORD_PAS_PAREIL;
        } else {
            $affichage = RegisterController::CONNECTED;
            $newUser = new Authenticate();
            $newUser->username=$NomUtilisateur;
            $newUser->password=$MotDePasse;
            $newUser->email=$Email;
            $newUser->Niveau_acces=1;
            $newUser->save();

            // gestion session
            $this->gestionSession($newUser);
        }

        $v = new VueRegister($content, $affichage);
        $rs->getBody()->write($v->render());
        return $rs;
    }

    public function authentification($rq,$rs,$args){
        $container = $this->c;
        $base = $rq->getUri()->getBasePath();
        $route_uri = $container->router->pathFor('loginConfirm');
        $url = $base . $route_uri;

        $content = $rq->getParsedBody();

        $NomUtilisateur = $content['username'];
        $MotDePasse = $content['password'];

        $userValide = Authenticate::where(['username'=>$NomUtilisateur, 'password'=>$MotDePasse])->count();

        if ($userValide == 1) {
            $affichage = RegisterController::CONNECTED;
            $user = Authenticate::where('username', '=', $NomUtilisateur)->first();

            // gestion session
            $this->gestionSession($user);
        } else {
            $affichage = RegisterController::CONNECTIONFAILED;
            session_destroy();
            session_start();
        }

        $v = new VueRegister($content, $affichage);
        $rs->getBody()->write($v->render());
        return $rs;
    }

    public function loginPage($rq, $rs, $args) {
        $container = $this->c ;
        $base = $rq->getUri()->getBasePath() ;
        $route_uri = $container->router->pathFor('login');
        $url = $base . $route_uri ;

        $v = new VueRegister([], RegisterController::LOGIN);
        $rs->getBody()->write($v->render()) ;
        return $rs ;
    }

    public function signUpPage($rq, $rs, $args) {
        $container = $this->c ;
        $base = $rq->getUri()->getBasePath() ;
        $route_uri = $container->router->pathFor('signUp');
        $url = $base . $route_uri ;

        $v = new VueRegister([], RegisterController::SIGNUP);
        $rs->getBody()->write($v->render()) ;
        return $rs ;
    }

    public function LoadUser($rq,$rs,$args){
        $container = $this->c;
        $base = $rq->getUri()->getBasePath();
        $route_uri = $container->router->pathFor('loginConfirm');
        $url = $base . $route_uri;
        $content = $rq->getParsedBody();

        $NomUtilisateur = $content['username'];
        $auth=Authenticate::where('username','=',$NomUtilisateur)->first();
        return $auth;
    }

    public function logout($rq, $rs, $args) {
        $container = $this->c ;
        $base = $rq->getUri()->getBasePath() ;
        $route_uri = $container->router->pathFor('logout');
        $url = $base . $route_uri ;


        session_destroy();
        session_start();


        $v = new VueRegister([], RegisterController::LOGOUT);
        $rs->getBody()->write($v->render()) ;
        return $rs ;
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

