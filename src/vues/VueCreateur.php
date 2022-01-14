<?php

namespace wishlist\vues;

use wishlist\controllers\ItemController;
use wishlist\controllers\ListeController;
use wishlist\models\Authenticate;
use wishlist\tools;

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

    private function affichageListes() : string {
        $str = "<section><ol>";
        foreach ($this->tab as $value) {
            $str = $str . "<li><a href='./list/view?token=$value->token'>" . $value->titre . "</a></li>";
        }
        $str = $str . "</ol></section>";

        return $str;
    }

    private function affichageItemListeExpiree(): string {
        $path = (str_contains($_SERVER['REQUEST_URI'], "/item/") ? "../.." : ".");

        $item = $this->tab[0];
        $list = $item->liste;
        $str = "<h1>$item->nom</h1><img src='$path/img/$item->img' height='100px' width='100px' alt='$item->nom'><br />ID : $item->id<br />Description : $item->descr<br />Tarif : $item->tarif<br />URL : $item->url";
        $str = $str . "<br />Liste : " . ($list == null ? "Aucune" : "<a href='$path/list/view?token=$list->token'>$list->titre</a>");

        $user = Authenticate::where("id", "=", $item->reserv_par)->first();
        $pseudo = $item->pseudo;

        $reserveur = isset($user) ? $user->username : $pseudo;
        $msg = ($item->msg_reserv == "" ? " sans laisser de message." : ": <br />$item->msg_reserv");

        $str = $str . "<h2>Réservation</h2>".($item->etat_reserv == 1 ? "Réservé par $reserveur $msg" : "Réservé par personne.");
        return $str;
    }

    private function affichageItemListeEnCours(): string {
        $path = (str_contains($_SERVER['REQUEST_URI'], "/item/") ? "../.." : ".");

        $item = $this->tab[0];
        $list = $item->liste;
        $str = "<h1>$item->nom</h1><img src='$path/img/$item->img' height='100px' width='100px' alt='$item->nom'><br />ID : $item->id<br />Description : $item->descr<br />Tarif : $item->tarif<br />URL : $item->url";
        $str = $str . "<br />Liste : " . ($list == null ? "Aucune" : "<a href='$path/list/view?token=$list->token'>$list->titre</a>");

        $str = $str . "<h2>Réservation</h2>".($item->etat_reserv == 1 ? "Réservé par quelqu'un. Attendez que la liste arrive à échéance pour voir qui." : "Réservé par personne.");
        return $str;
    }

    public function render() {
        $content = "";
        $notif = "";
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
            case ListeController::LIST_NEW_ERROR: {
                $content = $this->listCreate().tools::messageBox("Impossible de créer une nouvelle liste. <a href='../login'>Reconnectez-vous.</a>").tools::rewriteUrl("formulaireListe", "Création d'une liste");
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
            case ItemController::ITEM_VIEW_OWNER_EXPIRE : {
                $content = $this->affichageItemListeExpiree();
                $title = "Item";
                break;
            }
            case ItemController::ITEM_VIEW_OWNER_EN_COURS : {
                $content = $this->affichageItemListeEnCours();
                $title = "Item";
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
