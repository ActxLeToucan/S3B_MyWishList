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
        $list = $this->tab[0];
        $str = "La liste du nom de <u>$list->titre</u> a été créée et expirera le $list->expiration.<br />Utilisez ce token pour accéder à la liste : <b>$list->token</b>";

        return $str;
    }

    private function confirmationNewItem() : string {
        $item = $this->tab[0];
        $str = "L'item du nom de <u>$item->nom</u> dans la liste numéro $item->liste_id.";

        return $str;
    }

    private function itemCreate() : string {
        $file =  "HTML/FormItem.html";
        return file_get_contents($file);
    }

    private function listCreate() : string {
        $file =  "HTML/FormListe.html";
        return file_get_contents($file);
    }

    public function render() {
        $content = "";
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
            case ItemController::ITEM_FORM_CREATE : {
                $htmlPage = $this->itemCreate();
                $title = 'Création d\'un item';
                break;
            }
            case ListeController::LIST_FORM_CREATE : {
                $htmlPage = $this->listCreate();
                $title = 'Création d\'une liste';
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
