<?php

namespace wishlist\controllers;

use wishlist\models\Item;
use wishlist\vues\VueParticipant;

class ItemController {
    const ITEM_VIEW = 'items';
    private $c;

    /**
     * @param $c
     */
    public function __construct($c) {
        $this->c = $c;
    }

    public function getAllItem( $rq, $rs, $args ) {
        $container = $this->c ;
        $base = $rq->getUri()->getBasePath() ;
        $route_uri = $container->router->pathFor('Items');
        $url = $base . $route_uri ;

        $items = Item::select()->get();
        $v = new VueParticipant($items, ItemController::ITEM_VIEW);
        $rs->getBody()->write($v->render()) ;
        return $rs ;
    }
}