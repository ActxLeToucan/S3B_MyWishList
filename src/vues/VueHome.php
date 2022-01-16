<?php

namespace wishlist\vues;

use wishlist\controllers\HomeController;
use wishlist\tools;

class VueHome {
    private $tab;
    private $selecteur;
    private array $notif;

    public function __construct(iterable $t, $s, array $n) {
        $this->tab = $t;
        $this->selecteur = $s;
        $this->notif = $n;
    }

    private function home() : string {
        $user = $_SESSION['username'] ?? "";
        $file =  "HTML/index.html";
        return tools::insertIntoBody(file_get_contents($file), "<p>".(isset($_SESSION['username']) ? "Connecté en tant que $user. <a href='logout'>Se déconnecter</a>" : "<a href='login'>Se connecter</a>")."</p>");
    }

    public function render() : string {
        $from = "";
        $htmlPage = "";
        $title = "";
        $notif = "";
        $content = "";
        switch ($this->selecteur) {
            case HomeController::HOME : {
                $htmlPage = $this->home();
                $title = 'MyWishList - Accueil';
                break;
            }
        }
        return tools::getHtml($from, $htmlPage, $title, $notif, $content, $this->notif);
    }
}
