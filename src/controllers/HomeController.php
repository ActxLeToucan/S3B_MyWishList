<?php

namespace wishlist\controllers;

use wishlist\vues\VueHome;

class HomeController {
    const HOME = 'home';
    private $c;

    /**
     * @param $c
     */
    public function __construct($c) {
        $this->c = $c;
    }

    public function getHomePage( $rq, $rs, $args ) {
        $container = $this->c ;
        $base = $rq->getUri()->getBasePath() ;
        $route_uri = $container->router->pathFor('home');
        $url = $base . $route_uri ;

        $v = new VueHome([], HomeController::HOME);
        $rs->getBody()->write($v->render()) ;
        return $rs ;
    }
}