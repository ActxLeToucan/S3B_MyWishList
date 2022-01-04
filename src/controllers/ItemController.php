<?php

namespace wishlist\controllers;

use wishlist\models\Item;
use wishlist\vues\VueCreateur;
use wishlist\vues\VueParticipant;

class ItemController {
    const ITEMS_VIEW = 'items';
    const ITEM_VIEW = 'item';
    const ITEM_NEW = 'newItems';
    const ITEM_FORM_CREATE = 'form_item_create';
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
        $v = new VueParticipant($items, ItemController::ITEMS_VIEW);
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

        move_uploaded_file($cheminServeur, $uploadfile);

        $nomItem = filter_var($content['nom'], FILTER_SANITIZE_STRING);
        $listeId = filter_var($content['liste_id'], FILTER_SANITIZE_STRING);
        $descr = filter_var($content['descr'], FILTER_SANITIZE_STRING);

        $url = filter_var($content['url'], FILTER_SANITIZE_STRING);
        $tarif = filter_var($content['tarif'], FILTER_SANITIZE_STRING);

        $newItem = new Item();
        $newItem->nom = $nomItem;
        $newItem->liste_id = $listeId;
        $newItem->descr = $descr;
        $newItem->img = $fileName;
        $newItem->url = $url;
        $newItem->tarif = $tarif;
        $newItem->save();



        $v = new VueCreateur($content, ItemController::ITEM_NEW);
        $rs->getBody()->write($v->render()) ;
        return $rs ;
    }

    public function getItemById($rq, $rs, $args) {
        $container = $this->c;
        $base = $rq->getUri()->getBasePath();
        $route_uri = $container->router->pathFor('Item_ID', $args);
        $url = $base . $route_uri;

        $id = $args['id'];
        $l = Item::where('id','=',$id)->first();
        $v = new VueParticipant([$l], ItemController::ITEM_VIEW);
        $rs->getBody()->write($v->render());
        return $rs;
    }

    public function createItem($rq, $rs, $args) {
        $container = $this->c ;
        $base = $rq->getUri()->getBasePath() ;
        $route_uri = $container->router->pathFor('formulaireItemCreate');
        $url = $base . $route_uri ;

        $v = new VueCreateur([], ItemController::ITEM_FORM_CREATE);
        $rs->getBody()->write($v->render()) ;
        return $rs ;
    }
}