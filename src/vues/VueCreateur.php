<?php

namespace wishlist\vues;

use wishlist\controllers\ItemController;
use wishlist\controllers\ListeController;

class VueCreateur {
    private $tab;
    private $selecteur;

    public function __construct(iterable $t, $s) {
        $this->tab = $t;
        $this->selecteur = $s;
    }

    private function confirmationNewListe() : string {
        $str = "la liste du nom de ".filter_var($this->tab['nom'], FILTER_SANITIZE_STRING)."a été crée et expirera le ".filter_var($this->tab['dateExp'], FILTER_SANITIZE_STRING);

        return $str;
    }

    private function confirmationNewItem() : string {
        $str = "l'item du nom de ".filter_var($this->tab['nom'], FILTER_SANITIZE_STRING)." dans la liste numéro ".filter_var($this->tab['liste_id'], FILTER_SANITIZE_STRING);

        return $str;
    }

    public function render() {
        switch ($this->selecteur) {
            case ListeController::LIST_NEW : {
                $content = $this->confirmationNewListe();
                $title = 'NewListe';
                break;
            }
            case ItemController::ITEM_NEW : {
                $content = $this->confirmationNewItem();
                $title = 'NewItems';
                break;
            }
        }
        $style = isset($from) ? "<link rel='stylesheet' href='Style/$from'>" : "";
        $html = <<<END
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
        END ;
        return $html;
    }
}
