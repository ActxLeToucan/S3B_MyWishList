<?php

namespace wishlist\vues;

use wishlist\controllers\HomeController;

class VueHome {
    private $tab;
    private $selecteur;

    public function __construct(iterable $t, $s) {
        $this->tab = $t;
        $this->selecteur = $s;
    }

    private function home() : string {
        $file =  "HTML/index.html";
        return file_get_contents($file);
    }

    public function render() {
        $content = "";
        switch ($this->selecteur) {
            case HomeController::HOME : {
                $htmlPage = $this->home();
                $title = 'MyWishList - Accueil';
                break;
            }
        }
        $style = isset($from) ? "<link rel='stylesheet' href='Style/$from'>" : "";
        $html = isset($htmlPage) ? $htmlPage : <<<END
            <!DOCTYPE html> <html>
            <head>
                <meta charset="UTF-8">
                <title>$title</title>
                $style
            </head>
            <body>
            <div class="content">
            $content
            </div>
            </body><html>
        END;
        return $html;
    }
}
