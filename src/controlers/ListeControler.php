<?php

namespace wishlist\controlers;

use wishlist\models\Item;
use wishlist\models\Liste;
use wishlist\vues\VueParticipant;

class ListeControler {
    const LIST_VIEW = 'listes';
    const ITEM_VIEW = 'items';
    private $c;

    /**
     * @param $c
     */
    public function __construct($c)
    {
        $this->c = $c;
    }


    public function getAllListe( $rq, $rs, $args ) {
        $container = $this->c ;
        $base = $rq->getUri()->getBasePath() ;
        $route_uri = $container->router->pathFor('Listes');
        $url = $base . $route_uri ;

        $lists = Liste::select()->get();
        $v = new VueParticipant($lists, ListeControler::LIST_VIEW);
        $rs->getBody()->write($v->render()) ;
        return $rs ;
    }

    public function getAllItem( $rq, $rs, $args ) {
        $container = $this->c ;
        $base = $rq->getUri()->getBasePath() ;
        $route_uri = $container->router->pathFor('Items');
        $url = $base . $route_uri ;

        $items = Item::select()->get();
        $v = new VueParticipant($items, ListeControler::ITEM_VIEW);
        $rs->getBody()->write($v->render()) ;
        return $rs ;
    }
}