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
    private array $notif;
    private string $base;

    public function __construct(iterable $t, $s, array $n, string $b) {
        $this->tab = $t;
        $this->selecteur = $s;
        $this->notif = $n;
        $this->base = $b;
    }

    private function affichageListe() : string {
        $list = $this->tab[0];
        $msgs = Message::where("id_list", "=", $list->no)->get();


        $listeExpiree = ((strtotime($list->expiration) < strtotime(date("Y-m-d"))) ? " (expirée)" : " (en cours)");
        $items = "";
        foreach ($list->items as $item) {
            $items = $items . "<li><a href='../item/$item->id/view?token=$list->token'>$item->nom</a></li>";
        }
        $messages = "";
        foreach ($msgs as $message) {
            $author = ($message->id_user == 0 ? $message->pseudo : $message->user->username);
            $messages = $messages . "<li>($message->date) $author : $message->texte</li>";
        }
        $username = "";
        if (!isset($_SESSION['username']) || !isset($_SESSION['AccessRights'])) {
            $username = "<label for='pseudo'>Entrez un pseudo ou <a href='../login'>connectez-vous</a> : </label><input type='text' required id='pseudo' name='pseudo'><br />";
        }


        return <<<EOF
        <h1>$list->titre</h1>
        Numéro de la liste : $list->no<br />
        Créateur : {$list->user->username}<br />
        Description : $list->description<br />
        Expiration : $list->expiration $listeExpiree<br />
        Items :
        <section><ol>$items</ol></section>
        Messages :
        <section><ul>$messages</ul></section>
        <form action='../addmsg?token=$list->token' method='post' enctype='multipart/form-data'>
            $username
            <textarea id='texte' name='texte'></textarea>
            <button type='submit'>Ajouter un message</button>
        </form>
        EOF;
    }

    private function affichageItems() : string {
        $str = "<section><ol>";
        foreach ($this->tab as $value) {
            $str = $str . "<li>".$value->nom."<img src='img/$value->img' height='100px' width='100px' alt='$value->nom'>" . "<br>" . $value->descr . " <br> tarif : " .  $value->tarif . "<br>".$value->url . " </li>";
        }
        return $str . "</ol></section>";
    }

    private function affichageItem() : string {
        $path = "../..";

        $item = $this->tab[0];
        $list = $item->liste;
        $str = "<h1>$item->nom</h1><img src='$path/img/$item->img' height='100px' alt='$item->nom' /><br />ID : $item->id<br />Description : $item->descr<br />Tarif : $item->tarif<br />URL : $item->url";
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

        return $str . "<h2>Réservation</h2>".($item->etat_reserv == 1 ? "Réservé par $reserveur." : "Réservé par personne.<br />$formulaire");
    }

    public function render() : string {
        $from = "";
        $htmlPage = "";
        $title = "";
        $notif = "";
        $content = "";
        switch ($this->selecteur) {
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
        }
        return tools::getHtml($from, $htmlPage, $title, $notif, $content, $this->notif, $this->base);
    }
}