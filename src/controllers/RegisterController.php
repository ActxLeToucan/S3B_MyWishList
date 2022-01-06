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
    const NEW_USER = 'newUser';
    const CONNECTED ='connected';
    const CONNECTIONFAILED='failed';
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
        $route_uri = $container->router->pathFor('signup');
        $url = $base . $route_uri;
        $content = $rq->getParsedBody();

        $NomUtilisateur = $content['username'];
        $MotDePasse = $content['password'];
        $Email=$content['email'];
        $level = $content['level'];

        $newUser= new Authenticate();
        $newUser->username=$NomUtilisateur;
        $newUser->password=$MotDePasse;
        $newUser->email=$Email;
        $newUser->Niveau_acces=$level;
        $newUser->save();

        $v = new VueRegister($content, RegisterController::CONNECTED);
        $rs->getBody()->write($v->render());
        return $rs;
    }

    public function authentification($rq,$rs,$args){
        $container = $this->c;
        $base = $rq->getUri()->getBasePath();
        $route_uri = $container->router->pathFor('login');
        $url = $base . $route_uri;

        $content = $rq->getParsedBody();

        $NomUtilisateur = $content['username'];
        $MotDePasse = $content['password'];

        $MatchThese=['username'=>$NomUtilisateur, 'password'=>$MotDePasse];
        $authen = Authenticate::where($MatchThese)->count();

        $v = new VueRegister($content, ($authen == 1 ? RegisterController::CONNECTED : RegisterController::CONNECTIONFAILED));
        $rs->getBody()->write($v->render());
        return $rs;
    }
    public function LoadUser($rq,$rs,$args){
        $container = $this->c;
        $base = $rq->getUri()->getBasePath();
        $route_uri = $container->router->pathFor('AccessRights');
        $url = $base . $route_uri;

        $NomUtilisateur = $args['Username'];

        $authen=Authenticate::where('username','=',$NomUtilisateur);

        $v = new VueRegister($authen, RegisterController::CONNECTED);
        $rs->getBody()->write($v->render());
        return $rs;
    }
}