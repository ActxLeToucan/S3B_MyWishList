<?php

namespace wishlist\vues;

use wishlist\controllers\HomeController;
use wishlist\tools;

class VueHome {
    private $tab;
    private $selecteur;
    private array $notif;
    private string $base;

    public function __construct(iterable $t, $s, array $n, string $b) {
        $this->tab = $t;
        $this->selecteur = $s;
        $this->notif = $n;
        $this->base = $b;
    }

    private function home() : string {
        $user = $_SESSION['username'] ?? "";
        $file =  "HTML/index.html";
        return tools::insertIntoBody(file_get_contents($file), "<li id='L1'> <a href='logout'>Se déconnecter</a> </li> <li id='L2'>  <a href='monCompte'> 👤 $user </a>  </li>");
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
        return tools::getHtml($from, $htmlPage, $title, $notif, $content, $this->notif, $this->base);
    }
}
