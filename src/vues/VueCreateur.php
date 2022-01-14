<?php

namespace wishlist\vues;

use wishlist\controllers\ItemController;
use wishlist\controllers\ListeController;
use wishlist\models\Authenticate;
use wishlist\models\Message;
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

    private function affichageListe() : string {
        $list = $this->tab[0];
        $str = "<h1>$list->titre <a href='./edit?token=$list->token_edit'><button>éditer ✏️</button></a></h1>Numéro de la liste : $list->no<br />Créateur : {$list->user->username}<br />Description : $list->description<br />Expiration : $list->expiration".((strtotime($list->expiration) < strtotime(date("Y-m-d"))) ? " (expirée)" : " (en cours)")."<br />Items :";
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

    private function affichageItemListeExpiree(): string {
        $path = (str_contains($_SERVER['REQUEST_URI'], "/item/") ? "../.." : ".");

        $item = $this->tab[0];
        $list = $item->liste;
        $str = "<h1>$item->nom <a href='$path/item/$item->id/edit?token=$list->token_edit'><button>éditer ✏️</button></a></h1><br /><img src='$path/img/$item->img' height='100px' width='100px' alt='$item->nom'><br />ID : $item->id<br />Etat : ".($list->validee == 1 ? "Validée" : "Non validée")."<br />Description : $item->descr<br />Tarif : $item->tarif<br />URL : $item->url";
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
        $str = "<h1>$item->nom <a href='$path/item/$item->id/edit?token=$list->token_edit'><button>éditer ✏️</button></a></h1><img src='$path/img/$item->img' height='100px' width='100px' alt='$item->nom'><br />ID : $item->id<br />Etat : ".($list->validee == 1 ? "Validée" : "Non validée")."<br />Description : $item->descr<br />Tarif : $item->tarif<br />URL : $item->url";
        $str = $str . "<br />Liste : " . ($list == null ? "Aucune" : "<a href='$path/list/view?token=$list->token'>$list->titre</a>");

        $str = $str . "<h2>Réservation</h2>".($item->etat_reserv == 1 ? "Réservé par quelqu'un. Attendez que la liste arrive à échéance pour voir qui." : "Réservé par personne.");
        return $str;
    }

    //TODO
    private function editList(): string {
        return "TODO";
    }

    private function editListError(): string {
        switch ($this->selecteur) {
            case ListeController::LIST_EDIT_TOKEN_ERROR : {
                $msg = "Token de modification invalide.";
                break;
            }
            case ListeController::LIST_EDIT_OWNER_ERROR : {
                $msg = "Vous ne pouvez pas modifier cette liste car vous n'en êtes pas le créateur.";
            }
        }
        return $msg;
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
            case ListeController::LIST_VIEW : {
                $content = $this->affichageListe();
                $title = 'Liste';
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
            case ListeController::LIST_EDIT : {
                $content = $this->editList();
                $title = "Modification liste";
                break;
            }
            case ListeController::LIST_EDIT_OWNER_ERROR :
            case ListeController::LIST_EDIT_TOKEN_ERROR : {
                $content = $this->editListError();
                $title = "Erreur : Modification liste";
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
