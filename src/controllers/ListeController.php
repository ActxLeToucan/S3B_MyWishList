<?php

namespace wishlist\controllers;

use Illuminate\Database\Eloquent\Model;
use wishlist\models\Item;
use wishlist\models\Liste;
use wishlist\vues\VueParticipant;

class ListeController {
    const LIST_VIEW = 'listes';
    private $c;

    /**
     * @param $c
     */
    public function __construct($c) {
        $this->c = $c;
    }


    public function getAllListe( $rq, $rs, $args ) {
        $container = $this->c ;
        $base = $rq->getUri()->getBasePath() ;
        $route_uri = $container->router->pathFor('Listes');
        $url = $base . $route_uri ;

        $lists = Liste::select()->get();
        $v = new VueParticipant($lists, ListeController::LIST_VIEW);
        $rs->getBody()->write($v->render()) ;
        return $rs ;
    }

    public function newListe( $rq, $rs, $args ) {
        $container = $this->c ;
        $base = $rq->getUri()->getBasePath() ;
        $route_uri = $container->router->pathFor('New_Liste');
        $url = $base . $route_uri ;

        $content = $rq->getParsedBody();


        $nomItem = $content['nom'];
        $descr = $content['descr'];
        $exp = $content['dateExp'];


        $newListe = new Liste();
        $newListe->titre = $nomItem;
        $newListe->description = $descr;
        $newListe->expiration = $exp;
        $newListe->save();



        $v = new VueParticipant($content, ItemController::LISTE_NEW);
        $rs->getBody()->write($v->render()) ;
        return $rs ;
    }
}