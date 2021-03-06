<?php

namespace wishlist\vues;

use wishlist\controllers\ItemController;
use wishlist\controllers\ListeController;
use wishlist\models\Authenticate;
use wishlist\models\Message;
use wishlist\tools;

class VueParticipant {
    /**
     * @var iterable Tableau d'éléments à afficher
     */
    private iterable $tab;
    /**
     * @var string Sélecteur de l'affichage à fournir
     */
    private string $selecteur;
    /**
     * @var array Propriétés de la notification
     */
    private array $notif;
    /**
     * @var string Base du site
     */
    private string $base;

    /**
     * Constructeur de la vue participant
     * @param iterable $t Tableau d'éléments à afficher
     * @param string $s Sélecteur de l'affichage à fournir
     * @param array $n Propriétés de la notification
     * @param string $b Base du site
     */
    public function __construct(iterable $t, string $s, array $n, string $b) {
        $this->tab = $t;
        $this->selecteur = $s;
        $this->notif = $n;
        $this->base = $b;
    }

    /**
     * Affichage d'une liste
     * @return string
     */
    private function affichageListe(): string {
        $list = $this->tab[0];
        $msgs = Message::where("id_list", "=", $list->no)->get();


        $listeExpiree = ((strtotime($list->expiration) < strtotime(date("Y-m-d"))) ? " (expirée)" : " (en cours)");
        $items = "";
        foreach ($list->items as $item) {
            $items = $items . "<li><a href='$this->base/item/$item->id/view?token=$list->token'>$item->nom</a></li>";
        }

        $iter = 0;
        $messages = "";
        foreach ($msgs as $message) {

            $iter ++;

            $messageType = ($iter % 2 == 0) ? 'my-message' : 'other-message';
            $messageAlign = ($iter % 2 == 0) ? '' : 'align-right';
            $messageFloat = ($iter % 2 == 0) ? '' : 'float-right';

            $author = ($message->id_user == 0 ? $message->pseudo : $message->user->username);
            $messages = $messages . "<li class='clearfix'> 
                                        <div class='message-data $messageAlign'>
                                            <span class='message-data-time' >$message->date</span>
                                            <span class='message-data-name' >$author</span>
                                                
                                        </div>
                                        <div class='message $messageType $messageFloat'>
                                            $message->texte
                                        </div>
                                      </li>";
        }
        $username = "";
        if (!isset($_SESSION['username']) || !isset($_SESSION['AccessRights'])) {
            $username = "<label for='pseudo'>Entrez un pseudo ou <a href='$this->base/login'>connectez-vous</a> : </label><input type='text' required id='pseudo' name='pseudo'><br />";
        }


        return <<<EOF
        <h1>$list->titre</h1>
        Numéro de la liste : $list->no<br />
        Créateur : {$list->user->username}<br />
        Description : $list->description<br />
        Expiration : $list->expiration $listeExpiree<br />
        Items :
        <section class="S1"><ul>$items</ul></section>
                <form action='$this->base/addmsg?token=$list->token' method='post' enctype='multipart/form-data'>
            $username
            <textarea id='texte' name='texte'></textarea>
            <button type='submit'>Ajouter un message</button>
        </form>
        Messages :
        <section>
            <div class="chat"> 
                    <div class="chat-history"> 
                        <ul>
                            $messages
                        </ul>
                    </div>
            </div>  
        </section>

        EOF;
    }

    /**
     * Affichage d'un item
     * @return string
     */
    private function affichageItem(): string {
        $item = $this->tab[0];
        $list = $item->liste;
        $src = (isset($item->img)  ? "$this->base/img/$item->img" : "$this->base/img/giftbox2.png");
        $str = "
                <h1>$item->nom</h1>
                <img src=$src height='100px' alt='$item->nom' />
                <br />
                <p>ID : $item->id</p>
     
                <br />
                
                <p>Description : $item->descr</p>
                <br />
                
                <p>Tarif : $item->tarif</p>
                <br />
                <p>URL : $item->url</p>
                ";
        $str = $str . "<br /> <p>Liste : " . ($list == null ? "Aucune" : "<a href='$this->base/list/view?token=$list->token'>$list->titre</a></p>");


        $username = "";
        if (!isset($_SESSION['username']) || !isset($_SESSION['AccessRights'])) {
            $username = "<label for='pseudo'>Entrez un pseudo ou <a href='$this->base/login'>connectez-vous</a> : </label><input type='text' required id='pseudo' name='pseudo'><br />";
        }


        $formulaire = (strtotime($list->expiration) < strtotime(date("Y-m-d"))) ? "" : <<<END
            <form action='$this->base/reservation?id=$item->id' method='post' enctype='multipart/form-data'>
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

    /**
     * Affichage des listes publiques
     * @return string
     */
    private function listesPubliques() :string{
        $listesPubliques = "";
        foreach ($this->tab as $value) {
            if ($value->validee == 1) $listesPubliques = $listesPubliques . "<li><a href='$this->base/list/view?token=$value->token'>" . $value->titre . "</a></li>";
        }
        return <<<END
        <h1>Listes publiques</h1>
        <br>
        <section class="S1"><ul>$listesPubliques</ul></section>
        END;
    }

    /**
     * Affichage de la liste des créateurs
     * @return string
     */
    private function createurs(): string {
        $users = "";
        foreach ($this->tab as $user) {
            $users = $users . "<li><a href='$this->base/createurs/$user->username'>$user->username</a></li>";
        }
        return <<<END
        <h1>Les créateurs :</h1> 
        <br>
        <section><ul>$users</ul></section>
        END;
    }

    /**
     * Affichage des listes d'un créateur
     * @return string
     */
    private function createur(): string {
        $user = $this->tab[0];
        if (is_null($user)) {
            $lists = "Cet utilisateur n'existe pas, ou il ne possède aucune liste publique.";
        } else {
            $lists = "";
            foreach ($user->lists as $list) {
                if ($list->validee == 1 && $list->publique == 1) $lists = "<li><a href='$this->base/list/view?token=$list->token'>$list->titre</a></li>";
            }
        }
        return $lists;
    }

    /**
     * Retourne l'affichage sélectionné
     * @return string
     */
    public function render(): string {
        $from = "";
        $htmlPage = "";
        $title = "";
        $notif = "";
        $content = "";
        switch ($this->selecteur) {
            case ListeController::LIST_VIEW : {
                $content = $this->affichageListe();
                $title = 'Liste';
                $from = "ListeInfoStyle.css";
                break;
            }
            case ItemController::ITEM_VIEW : {
                $content = $this->affichageItem();
                $title = 'Item';
                $from = 'ItemsStyle.css';
                break;
            }
            case ListeController::PUBLIC : {
                $content = $this->listespubliques();
                $title = 'MyWishList - Accueil';
                $from = "LesListesStyle.css";
                break;
            }
            case ListeController::CREATEURS : {
                $content = $this->createurs();
                $title = 'Créateurs';
                $from = 'LesListesStyle.css';
                break;
            }
            case ListeController::CREATEUR : {
                $content = $this->createur();
                $title = 'Créateur';
                $from = 'LesListesStyle.css';
                break;
            }
        }
        return tools::getHtml($from, $htmlPage, $title, $notif, $content, $this->notif, $this->base);
    }
}