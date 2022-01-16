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
    private array $notif;

    public function __construct(iterable $t, $s, array $n) {
        $this->tab = $t;
        $this->selecteur = $s;
        $this->notif = $n;
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
        $mesListes = "";
        foreach ($this->tab as $value) {
            $mesListes = $mesListes . "<li><a href='./list/view?token=$value->token'>" . $value->titre . "</a></li>";
        }
        $listesPubliques = "";

        return <<<END
        <h1>Mes listes</h1>
        <section><ul>$mesListes</ul></section>
        <h1>Listes publiques</h1>
        <section><ul>$listesPubliques</ul></section>
        END;
    }

    private function affichageListe() : string {
        $list = $this->tab[0];
        $msgs = Message::where("id_list", "=", $list->no)->get();


        $listeVisible = ($list->validee == 1) ? "Liste visible" : "Liste invisible";
        $tokenPartage = ($list->validee == 1) ? "<span id='tokenShare'>$list->token</span> <button id='buttonGetToken' onclick='copyToken()'>Copier le lien de partage</button>" : "<i>La liste doit être visible pour être partagée.</i>";
        $listeExpiree = ((strtotime($list->expiration) < strtotime(date("Y-m-d"))) ? " (expirée)" : " (en cours)");
        $items = "";
        foreach ($list->items as $item) {
            $items = $items . "<li><a href='../item/$item->id/view?token=$list->token'>$item->nom</a></li>";
        }
        $messages = "";
        foreach ($msgs as $message) {
            $author = (is_null($message->id_user) ? $message->pseudo : $message->user->username);
            $messages = $messages . "<li>($message->date) $author : $message->texte</li>";
        }


        return <<<EOF
        <h1>$list->titre <a href='./edit?token=$list->token_edit'><button>éditer ✏️</button></a></h1>
        $listeVisible<br />
        Numéro de la liste : $list->no<br />
        Token de partage : $tokenPartage<br />
        Token d'édition : $list->token_edit<br />
        Description : $list->description<br />
        Expiration : $list->expiration $listeExpiree<br />
        Items :
        <section><ol>$items</ol></section>
        Messages :
        <section><ul>$messages</ul></section>
        <script>
            let buttonToken = document.getElementById("buttonGetToken");
            let confirm = document.createElement("span");
            confirm.innerHTML = "✅";
            
            let click = false;
            
            function copyToken() {
                let url = window.location.href;
                url = url.substring(0, url.indexOf("?"));
                let token = document.getElementById("tokenShare");
                
                navigator.clipboard.writeText(url + "?token=" + token.textContent);
                
                if (!click) buttonToken.parentNode.insertBefore(confirm, buttonToken.nextSibling);
                click = true;
                
                setInterval(clearButtonToken, 5000);
            } 
            
            function clearButtonToken() {
                click = false;
                confirm.remove();
            }
        </script>
        EOF;
    }

    private function affichageItemListeExpiree(): string {
        $path = "../..";

        $item = $this->tab[0];
        $list = $item->liste;
        $str = "<h1>$item->nom <a href='$path/item/$item->id/edit?token=$list->token_edit'><button>éditer ✏️</button></a></h1><br /><img src='$path/img/$item->img' height='100px' width='100px' alt='$item->nom'><br />ID : $item->id<br />Etat : ".($list->validee == 1 ? "Validée" : "Non validée")."<br />Description : $item->descr<br />Tarif : $item->tarif<br />URL : $item->url";
        $str = $str . "<br />Liste : " . ($list == null ? "Aucune" : "<a href='$path/list/view?token=$list->token'>$list->titre</a>");

        $user = Authenticate::where("id", "=", $item->reserv_par)->first();
        $pseudo = $item->pseudo;

        $reserveur = isset($user) ? $user->username : $pseudo;
        $msg = ($item->msg_reserv == "" ? " sans laisser de message." : ": <br />$item->msg_reserv");

        return $str . "<h2>Réservation</h2>".($item->etat_reserv == 1 ? "Réservé par $reserveur $msg" : "Réservé par personne.");
    }

    private function affichageItemListeEnCours(): string {
        $path = "../..";

        $item = $this->tab[0];
        $list = $item->liste;
        $str = "<h1>$item->nom <a href='$path/item/$item->id/edit?token=$list->token_edit'><button>éditer ✏️</button></a></h1><img src='$path/img/$item->img' height='100px' width='100px' alt='$item->nom'><br />ID : $item->id<br />Etat : ".($list->validee == 1 ? "Validée" : "Non validée")."<br />Description : $item->descr<br />Tarif : $item->tarif<br />URL : $item->url";
        $str = $str . "<br />Liste : " . ($list == null ? "Aucune" : "<a href='$path/list/view?token=$list->token'>$list->titre</a>");

        return $str . "<h2>Réservation</h2>".($item->etat_reserv == 1 ? "Réservé par quelqu'un. Attendez que la liste arrive à échéance pour voir qui." : "Réservé par personne.");
    }

    private function editList(): string {
        $list = $this->tab[0];
        $listeVisible = $list->validee == 1 ? 'checked' : "";
        $items = "";
        foreach ($list->items as $item) {
            $removeItem = <<<END
            <form style="display:inline;" action="../removeItem?token=$list->token_edit&id=$item->id" method="post">
                <input type="submit" name="removeItem" value="🗑️" />
            </form>
            END;

            $items = $items . "<li><a href='../item/$item->id/edit?token=$list->token_edit'>$item->nom</a> $removeItem</li>";
        }
        $items = $items . <<<END
        <form action="../addItem?token=$list->token_edit" method="post">
            <input type="submit" name="addItem" value="Ajouter un item" />
        </form>
        END;

        return <<<END
        <form action="../editList?token=$list->token_edit" method="post" enctype="multipart/form-data">
            <div class="nom">
                <label for="nom">Nom de la liste :</label>
                <input type="text" id="nom" name="nom" value="$list->titre" required>
            </div>
        
            <br>
        
            <div class="description">
                <label for="descr">Description de la liste :</label>
                <textarea id="descr" name="descr">$list->description</textarea>
            </div>
        
            <br>
        
            <div class="date">
                <label for="dateExp">Date d'expiration :</label>
                <input type="date" id="dateExp" name="dateExp" value="$list->expiration" required>
            </div>
        
            <br>
            
            <div class="liste_validee">
                <input type="checkbox" id="validee" name="validee" value="1" $listeVisible>
                <label for="validee"> Rendre la liste visible</label>
            </div>
            
            <br />
        
            <button type="submit">Valider les changements sur cette liste</button>
        
        
        </form>
        <br />
        Items :
        <section><ul>$items</ul></section>
        END;
    }

    public function render() : string {
        $from = "";
        $htmlPage = "";
        $title = "";
        $notif = "";
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
        }
        return tools::getHtml($from, $htmlPage, $title, $notif, $content, $this->notif);
    }
}
