<?php

namespace wishlist\vues;

use wishlist\controllers\HomeController;
use wishlist\tools;

class VueHome {
    private $tab;
    private $selecteur;

    public function __construct(iterable $t, $s) {
        $this->tab = $t;
        $this->selecteur = $s;
    }

    private function home() : string {
        $user = $_SESSION['username'] ?? "";
        return tools::insertIntoBody(tools::getHomePage(), "<p>".(isset($_SESSION['username']) ? "Connecté en tant que $user.<a href='logout'>Se déconnecter</a>" : "<a href='login'>Se connecter</a>")."</p>");
    }

    public function render() {
        $content = "";
        $notif = "";
        switch ($this->selecteur) {
            case HomeController::HOME : {
                $htmlPage = $this->home();
                $title = 'MyWishList - Accueil';
                break;
            }
        }
        $style = isset($from) ? "<link rel='stylesheet' href='Style/$from'>" : "";
        $html = $htmlPage ?? <<<END
            <!DOCTYPE html> <html lang="fr">
            <head>
                <meta charset="UTF-8">
                <title>$title</title>
                $style
            </head>
            <body>
            $notif
            <div class="content">
            $content
            </div>
            </body></html>
        END;
        return $html;
    }
}
