<?php

namespace wishlist\controllers;

use wishlist\tools;
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

    public function getHomePage($rq, $rs, $args) {
        $container = $this->c;
        $base = $rq->getUri()->getBasePath();
        $route_uri = $container->router->pathFor('home');
        $url = $base . $route_uri;

        $notif = tools::prepareNotif($rq);

        $v = new VueHome([], HomeController::HOME, $notif);
        $rs->getBody()->write($v->render());
        return $rs;
    }
}