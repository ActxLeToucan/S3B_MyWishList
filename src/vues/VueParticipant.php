<?php

namespace wishlist\vues;

use wishlist\controllers\ItemController;
use wishlist\controllers\ListeController;
use wishlist\models\Authenticate;
use wishlist\models\Message;
use wishlist\tools;

class VueParticipant {
    private $tab;
    private $selecteur;
    public function __construct(Iterable $t, $s) {
        $this->tab = $t;
        $this->selecteur = $s;
    }

    private function affichageListe() : string {
        $list = $this->tab[0];
        $str = "<h1>$list->titre</h1>Numéro de la liste : $list->no<br />Créateur : {$list->user->username}<br />Description : $list->description<br />Expiration : $list->expiration".((strtotime($list->expiration) < strtotime(date("Y-m-d"))) ? " (expirée)" : " (en cours)")."<br />Items :";
        $str = $str . "<section><ol>";
        $items = $list->items;
        foreach ($items as $item) {
            $str = $str . "<li><a href='../item/$item->id/view?token=$list->token'>$item->nom</a></li>";
        }
        $str = $str . "</ol></section>";
        $messages = Message::where("id_list", "=", $list->no)->get();
        $str = $str . "Messages :<section><ul>";
        foreach ($messages as $message) {
            $author = (is_null($message->id_user) ? $message->pseudo : $message->user->username);
            $str = $str . "<li>($message->date) $author : $message->texte</li>";
        }
        $str = $str . "</ul></section>";

        return $str;
    }

    private function affichageListeError() : string {
        return "La liste demandée n'existe pas. Assurez-vous d'avoir le bon token.";
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
        $path = (str_contains($_SERVER['REQUEST_URI'], "/item/") ? "../.." : ".");

        $item = $this->tab[0];
        $list = $item->liste;
        $str = "<h1>$item->nom</h1><img src='$path/img/$item->img' height='100px' width='100px' alt='$item->nom'><br />ID : $item->id<br />Description : $item->descr<br />Tarif : $item->tarif<br />URL : $item->url";
        $str = $str . "<br />Liste : " . ($list == null ? "Aucune" : "<a href='$path/list/view?token=$list->token'>$list->titre</a>");


        $username = "";
        if (!isset($_SESSION['username']) || !isset($_SESSION['AccessRights'])) {
            $username = "<label for='pseudo'>Entrez un pseudo ou <a href='../../login'>connectez-vous</a> : </label><input type='text' required id='pseudo' name='pseudo'><br />";
        }


        $formulaire = <<<END
            <form action='../../reservation?id=$item->id' method='post' enctype='multipart/form-data'>
                $username
                <label for='message'>Entrez un message pour réserver l'item :</label>
                <br>
                <textarea id='message' name='message'></textarea>
                <button type='submit'>Réserver</button>
            </form>
        END;

        $user = Authenticate::where("id", "=", $item->reserv_par)->first();
        $pseudo = $item->pseudo;

        $reserveur = isset($user) ? $user->username : $pseudo;
        //$msg = ($item->msg_reserv == "" ? " sans laisser de message." : ": <br />$item->msg_reserv");

        $str = $str . "<h2>Réservation</h2>".($item->etat_reserv == 1 ? "Réservé par $reserveur." : "Réservé par personne.<br />$formulaire");
        return $str;
    }

    private function affichageItemError() : string {
        return "L'item demandé est invalide. Vérifiez que le token correspond bien à celui de la liste à laquelle il appartient, et que la liste est validée.";
    }

    private function confirmationReservation() : string {
        $item = $this->tab[0];
        $liste = $item->liste;

        $str = "Vous avez bien réservé l'item <u>$item->nom</u>";
        $str = $str . ($item->msg_reserv == "" ? (isset($item->pseudo) ? " avec le pseudo \"$item->pseudo\"" : "") . " sans laisser de message." : " avec le message \"$item->msg_reserv\".");
        return $str . tools::rewriteUrl("./item/$item->id/view?token=$liste->token");
    }

    private function errorReservation() : string {
        $item = $this->tab[0];
        $liste = $item->liste;

        $str = "Impossible de réserver l'item.";
        return $str . tools::rewriteUrl("./item/$item->id/view?token=$liste->token");
    }

    public function render() {
        $content = "";
        $notif = "";
        switch ($this->selecteur) {
            case ListeController::LIST_VIEW : {
                $content = $this->affichageListe();
                $title = 'Liste';
                break;
            }
            case ListeController::LIST_VIEW_ERROR : {
                $content = $this->affichageListeError();
                $title = 'Erreur : Liste';
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
            case ItemController::ITEM_VIEW_ERROR : {
                $content = $this->affichageItemError();
                $title = "Erreur : Item";
                break;
            }
            case ItemController::ITEM_RESERVATION : {
                $content = $this->affichageItem();
                $notif = tools::messageBox($this->confirmationReservation());
                $title = 'Item réservé !';
                break;
            }
            case ItemController::ITEM_RESERVATION_ERROR : {
                $content = $this->affichageItem();
                $notif = tools::messageBox($this->errorReservation());
                $title = 'Erreur reservation !';
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