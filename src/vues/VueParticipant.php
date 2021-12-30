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
            $str = $str . "<li><a href='./list/$value->no'>" . $value->titre . "</a></li>";
        }
        $str = $str . "</ol></section>";

        return $str;
    }

    private function affichageListe() : string {
        $list = $this->tab[0];
        $str = "Liste des items dans la liste $list->no :";
        $str = $str . "<section><ol>";
        $items = $list->items;
        foreach ($items as $item) {
            $str = $str . "<li><a href='../item/$item->id'>$item->nom</a></li>";
        }
        $str = $str . "</ol></section>";

        return $str;
    }

    private function affichageItems() : string {
        $str = "<section><ol>";
        foreach ($this->tab as $value) {
            $str = $str . "<li>".$value->nom."<img src='img/$value->img' height='100px' width='100px'>" . "<br>" . $value->descr . " <br> tarif : " .  $value->tarif . "<br>".$value->url . " </li>";
        }
        $str = $str . "</ol></section>";

        return $str;
    }

    private function confirmationNewItem() : string {
        $str = "l'item du nom de ".$this->tab['nom']." dans la liste numéro ".$this->tab['liste_id'];

        return $str;

    }

    private function confirmationNewListe() : string {
        $str = "la liste du nom de ".$this->tab['nom']."a été crée et expirera le ".$this->tab['dateExp'];

        return $str;

    }

    public function render() {
        switch ($this->selecteur) {
            case ListeController::LISTS_VIEW : {
                $content = $this->affichageListes();
                $title = 'Listes';
                break;
            }
            case ListeController::LIST_NEW : {
                $content = $this->confirmationNewListe();
                $title = 'NewListe';
                break;
            }
            case ListeController::LIST_VIEW : {
                $content = $this->affichageListe();
                $title = 'Liste';
                break;
            }
            case ItemController::ITEM_VIEW : {
                $content = $this->affichageItems();
                $from = 'ItemsStyle.css';
                $title = 'Items';
                break;
            }
            case ItemController::ITEM_NEW : {
                $content = $this->confirmationNewItem();
                $title = 'NewItems';
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