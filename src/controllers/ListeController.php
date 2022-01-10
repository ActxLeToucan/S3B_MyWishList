<?php

namespace wishlist\controllers;

use wishlist\models\Authenticate;
use wishlist\models\Liste;
use wishlist\tools;
use wishlist\vues\VueCreateur;
use wishlist\vues\VueParticipant;

class ListeController {
    const LISTS_VIEW = 'lists';
    const LIST_VIEW = 'list';
    const LIST_NEW = 'newList';
    const LIST_FORM_CREATE = 'list_form_create';
    const LIST_NEW_ERROR = 'liste_new_error';

    private $c;

    /**
     * @param $c
     */
    public function __construct($c) {
        $this->c = $c;
    }

    /**
     * pour un gars connecté
     */
    public function getAllListe( $rq, $rs, $args ) {
        $container = $this->c;
        $base = $rq->getUri()->getBasePath();
        $route_uri = $container->router->pathFor('Listes');
        $url = $base . $route_uri;

        $user = Authenticate::where('username','=',$_SESSION['username'])->first();
        $lists = Liste::where('user_id','=',$user->user_id)->get();
        $v = new VueParticipant($lists, ListeController::LISTS_VIEW);
        $rs->getBody()->write($v->render());
        return $rs;
    }

    public function getListById($rq, $rs, $args) {
        $container = $this->c;
        $base = $rq->getUri()->getBasePath();
        $route_uri = $container->router->pathFor('listById', $args);
        $url = $base . $route_uri;

        $id = $args['id'];
        $l = Liste::where('no','=',$id)->first();
        $v = new VueParticipant([$l], ListeController::LIST_VIEW);
        $rs->getBody()->write($v->render());
        return $rs;
    }

    public function getListByToken($rq, $rs, $args) {
        $container = $this->c;
        $base = $rq->getUri()->getBasePath();
        $route_uri = $container->router->pathFor('listByToken', $args);
        $url = $base . $route_uri;

        $token = $args['token'];
        $l = Liste::where('token','=',$token)->first();
        $v = new VueParticipant([$l], ListeController::LIST_VIEW);
        $rs->getBody()->write($v->render());
        return $rs;
    }

    public function newListe( $rq, $rs, $args ) {
        $container = $this->c;
        $base = $rq->getUri()->getBasePath();
        $route_uri = $container->router->pathFor('New_Liste');
        $url = $base . $route_uri;

        $content = $rq->getParsedBody();

        if (isset($_SESSION['username']) && isset($_SESSION['AccessRights'])) {
            $nomItem = filter_var($content['nom'], FILTER_SANITIZE_STRING);
            $descr = filter_var($content['descr'], FILTER_SANITIZE_STRING);
            $exp =filter_var($content['dateExp'], FILTER_SANITIZE_STRING);

            $token = tools::generateToken();
            $user = Authenticate::where('username','=',$_SESSION['username'])->first();

            $newListe = new Liste();
            $newListe->titre = $nomItem;
            $newListe->description = $descr;
            $newListe->expiration = $exp;
            $newListe->token = $token;
            $newListe->user_id = $user->user_id;
            $newListe->save();
            $affichage = ListeController::LIST_NEW;
        } else {
            $affichage = ListeController::LIST_NEW_ERROR;
        }






        $v = new VueCreateur([$newListe], $affichage);
        $rs->getBody()->write($v->render());
        return $rs;
    }

    public function createList($rq, $rs, $args) {
        $container = $this->c ;
        $base = $rq->getUri()->getBasePath() ;
        $route_uri = $container->router->pathFor('formulaireListCreate');
        $url = $base . $route_uri ;

        $v = new VueCreateur([], ListeController::LIST_FORM_CREATE);
        $rs->getBody()->write($v->render()) ;
        return $rs ;
    }
}