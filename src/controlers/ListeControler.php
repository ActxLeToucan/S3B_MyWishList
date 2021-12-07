<?php

namespace wishlist\controlers;

use wishlist\vues\VueParticipant;

class ListeControler {
    public function getAll( $rq, $rs, $args ) {
        $container = $this->c ;
        $base = $rq->getUri()->getBasePath() ;
        $route_uri = $container->router->pathFor( 'Listes');
        $url = $base . $route_uri ;

        $lists = Liste::select()->get();
        $v = new VueParticipant($lists) ;
        $rs->getBody()->write($v->render()) ;
        return $rs ;
    }
}