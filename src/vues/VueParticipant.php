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
        $str = "<h1>$list->titre</h1>Numéro de la liste : $list->no<br />ID du créateur : $list->user_id<br />Description : $list->description<br />Expiration : $list->expiration<br />Items :";
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
            $str = $str . "<li>".$value->nom."<img src='img/$value->img' height='100px' width='100px' alt='$value->nom'>" . "<br>" . $value->descr . " <br> tarif : " .  $value->tarif . "<br>".$value->url . " </li>";
        }
        $str = $str . "</ol></section>";

        return $str;
    }

    private function affichageItem() : string {
        $item = $this->tab[0];
        $list = $item->liste;
        $str = "<h1>$item->nom</h1><img src='../img/$item->img' height='100px' width='100px' alt='$item->nom'><br />ID : $item->id<br />Description : $item->descr<br />Tarif : $item->tarif<br />URL : $item->url";
        $str = $str . "<br />Liste : " . ($list == null ? "Aucune" : "<a href='../list/$list->no'>$list->titre</a>");
        $str = $str . "<h2>Réservation</h2>".($item->etat_reserv == 1 ? "Réservé par l'utilisateur ayant l'id $item->reserv_par :<br />$item->msg_reserv" : "Réservé par personne."  .  "<br>"  ." <form action='./reservation?id=$item->id&nom=$item->nom' method='post' enctype='multipart/form-data'>  <label for='message'>Entrez un message pour réserver l'item :</label> <br> <textarea id='message' name='message'> </textarea> <button type='submit' >Réserver</button> </form> ");
        return $str;
    }

    private function confirmationReservation() : string {
        $str = "Vous avez bien réservé l'item : ". $this->tab['nom'] . " avec le message ". $this->tab['msg_reserv'];
        return $str;
    }

    public function render() {
        $content = "";
        switch ($this->selecteur) {
            case ListeController::LISTS_VIEW : {
                $content = $this->affichageListes();
                $title = 'Listes';
                break;
            }
            case ListeController::LIST_VIEW : {
                $content = $this->affichageListe();
                $title = 'Liste';
                break;
            }
            case ItemController::ITEMS_VIEW : {
                $content = $this->affichageItems();
                $from = 'ItemsStyle.css';
                $title = 'Items';
                break;
            }
            case ItemController::ITEM_VIEW : {
                $content = $this->affichageItem();
                $title = 'Item';
                break;
            }
            case ItemController::ITEM_RESERVATION : {
                $content = $this->confirmationReservation();
                $title = 'Item';
                break;
            }
        }
        $style = isset($from) ? "<link rel='stylesheet' href='Style/$from'>" : "";
        $html = isset($htmlPage) ? $htmlPage : <<<END
            <!DOCTYPE html> <html lang="fr">
            <head>
                <meta charset="UTF-8">
                <title>$title</title>
                $style
            </head>
            <body>
            <div class="content">
            $content
            </div>
            </body></html>
        END;
        return $html;
    }

}