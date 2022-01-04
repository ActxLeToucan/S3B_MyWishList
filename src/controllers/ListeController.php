<?php

namespace wishlist\controllers;

use Illuminate\Database\Eloquent\Model;
use wishlist\models\Item;
use wishlist\models\Liste;
use wishlist\vues\VueCreateur;
use wishlist\vues\VueParticipant;

class ListeController {
    const LISTS_VIEW = 'lists';
    const LIST_VIEW = 'list';
    const LIST_NEW = 'newList';
    const LIST_FORM_CREATE = 'list_form_create';

    private $c;

    /**
     * @param $c
     */
    public function __construct($c) {
        $this->c = $c;
    }


    public function getAllListe( $rq, $rs, $args ) {
        $container = $this->c;
        $base = $rq->getUri()->getBasePath();
        $route_uri = $container->router->pathFor('Listes');
        $url = $base . $route_uri;

        $lists = Liste::select()->get();
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

    public function newListe( $rq, $rs, $args ) {
        $container = $this->c;
        $base = $rq->getUri()->getBasePath();
        $route_uri = $container->router->pathFor('New_Liste');
        $url = $base . $route_uri;

        $content = $rq->getParsedBody();


        $nomItem = filter_var($content['nom'], FILTER_SANITIZE_STRING);
        $descr = filter_var($content['descr'], FILTER_SANITIZE_STRING);
        $exp =filter_var($content['dateExp'], FILTER_SANITIZE_STRING);


        $newListe = new Liste();
        $newListe->titre = $nomItem;
        $newListe->description = $descr;
        $newListe->expiration = $exp;
        $newListe->save();



        $v = new VueCreateur($content, ListeController::LIST_NEW);
        $rs->getBody()->write($v->render());
        return $rs;
    }

    public function createList($rq, $rs, $args) {
        $container = $this->c ;
        $base = $rq->getUri()->getBasePath() ;
        $route_uri = $container->router->pathFor('formulaireListCreate');
        $url = $base . $route_uri ;

        $v = new VueCreaateur([], ListeController::LIST_FORM_CREATE);
        $rs->getBody()->write($v->render()) ;
        return $rs ;
    }
}