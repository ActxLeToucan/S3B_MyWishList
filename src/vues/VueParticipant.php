<?php

namespace wishlist\vues;

use wishlist\controllers\ItemController;
use wishlist\controllers\ListeController;

class VueParticipant {
    private $tab;
    private $selecteur;
    public function __construct(Iterable $t, $s) {
        $this->tab = $t;
        $this->selecteur = $s;
    }

    private function affichageListes() : string {
        $str = "<section><ol>";
        foreach ($this->tab as $value) {
            $str = $str . "<li>" . $value->titre . "</li>";

        }

        return $str;
    }

    private function affichageItems() : string {
        $str = "<section><ol>";
        foreach ($this->tab as $value) {
            $str = $str . "<li>".$value->nom."<img src='img/$value->img' height='100px' width='100px'>" . "<br>" . $value->descr . " <br> tarif : " .  $value->tarif . " </li>";
        }
        $str = $str . "</ol></section>";

        return $str;
    }

    public function render() {
        switch ($this->selecteur) {
            case ListeController::LIST_VIEW : {
                $content = $this->affichageListes();
                $title = 'Listes';
                break;
            }
            case ItemController::ITEM_VIEW : {
                $content = $this->affichageItems();
                $from = 'ItemsStyle.css';
                $title = 'Items';
                break;
            }
        }
        $html = <<<END
            <!DOCTYPE html> <html>
            <head>
                <meta charset="UTF-8">
                <title>$title</title>
                <link rel="stylesheet" href="Style/$from">
            </head>
            <body>
            <div class="content">
            $content
            </div>
            </body><html>
        END ;
        return $html;
    }

}