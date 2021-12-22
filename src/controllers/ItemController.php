<?php

namespace wishlist\controllers;

use wishlist\models\Item;
use wishlist\vues\VueParticipant;

class ItemController {
    const ITEM_VIEW = 'items';
    const ITEM_NEW = 'newItems';
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

    public function newItem( $rq, $rs, $args ) {
        $container = $this->c ;
        $base = $rq->getUri()->getBasePath() ;
        $route_uri = $container->router->pathFor('New_Item');
        $url = $base . $route_uri ;

        $content = $rq->getParsedBody();

        $extension = $_FILES['photo']['type'];
        $cheminServeur = $_FILES['photo']['tmp_name'];
        $fileName = str_replace('image/',time()."_".RandomString().'.',$extension);
        $uploadfile = './img/'.$fileName;

        $nomItem = $content['nom'];
        $listeId = $content['liste_id'];
        $descr = $content['descr'];

        $url = $content['url'];
        $tarif = $content['tarif'];

        $newItem = new Item();
        $newItem->nom = $nomItem;
        $newItem->liste_id = $listeId;
        $newItem->descr = $descr;
        $newItem->img = $fileName;
        $newItem->url = $url;
        $newItem->tarif = $tarif;
        $newItem->save();




        $allo = move_uploaded_file($cheminServeur, $uploadfile);


        $v = new VueParticipant($content, ItemController::ITEM_NEW);
        $rs->getBody()->write($v->render()) ;
        return $rs ;
    }
}