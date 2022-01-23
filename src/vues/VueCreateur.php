<?php

namespace wishlist\vues;

use wishlist\controllers\ItemController;
use wishlist\controllers\ListeController;
use wishlist\models\Authenticate;
use wishlist\models\Message;
use wishlist\tools;

class VueCreateur {
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
     * Constructeur de la vue créateur
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
     * Récupère l'affichage de la création d'un item
     * @return string
     */
    private function itemCreate(): string {
        $file =  "HTML/FormItem.html";
        return file_get_contents($file);
    }

    /**
     * Récupère l'affichage de la création d'une liste
     * @return string
     */
    private function listCreate(): string {
        $file =  "HTML/FormListe.html";
        return file_get_contents($file);
    }

    /**
     * Affichage des listes d'un utilisateur
     * @return string
     */
    private function affichageListes(): string {
        $mesListes = "";
        foreach ($this->tab as $value) {
            $mesListes = $mesListes . "<li><a href='$this->base/list/view?token=$value->token'>" . $value->titre . "</a></li>";
        }

        return <<<END
        <h1>Mes listes crées :</h1>
        <br>
        <section><ul>$mesListes</ul></section>
        END;
    }

    /**
     * Affichage d'une liste
     * @return string
     */
    private function affichageListe(): string {
        $list = $this->tab[0];
        $msgs = Message::where("id_list", "=", $list->no)->get();


        $listeVisible = ($list->validee == 1) ? "Liste visible" : "Liste invisible";
        $tokenPartage = ($list->validee == 1)
            ? "<span id='tokenShare'>$list->token</span> <button id='buttonGetToken' onclick='copyToken()'>Copier le lien de partage</button>"
            : "<i>La liste doit être visible pour être partagée.</i>";
        $listeExpiree = ((strtotime($list->expiration) < strtotime(date("Y-m-d"))) ? " (expirée)" : " (en cours)");
        $items = "";
        foreach ($list->items as $item) {
            $items = $items . "<li><a href='$this->base/item/$item->id/view?token=$list->token'>$item->nom</a></li>";
        }
        $iter =0;
        $messages = '';
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


        return <<<EOF
        <h1>$list->titre <a href='./edit?token=$list->token_edit'><button>éditer ✏️</button></a></h1>
        $listeVisible<br />
        Numéro de la liste : $list->no<br />
        Token de partage : $tokenPartage<br />
        Token d'édition : $list->token_edit<br />
        Description : $list->description<br />
        Expiration : $list->expiration $listeExpiree<br />
        Items :
        <section class="S1"><ul>$items</ul></section>
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

    /**
     * Affichage d'un item se trouvant dans une liste expirée
     * @return string
     */
    private function affichageItemListeExpiree(): string {
        $item = $this->tab[0];
        $list = $item->liste;
        
        $visible = ($list->validee == 1 ? "" : "<i><b>Attention ! Cet item n'est visible que par vous.</b></i>");
        $nomListe = ($list == null ? "Aucune" : "<a href='$this->base/list/view?token=$list->token'>$list->titre</a>");
        $src = (isset($item->img)  ? "$this->base/img/$item->img" : "$this->base/img/giftbox2.png");

        $user = Authenticate::where("id", "=", $item->reserv_par)->first();
        $pseudo = $item->pseudo;

        $reserveur = isset($user) ? $user->username : $pseudo;
        $msg = ($item->msg_reserv == "" ? " sans laisser de message." : ": <br />$item->msg_reserv");

        $reservation = ($item->etat_reserv == 1 ? "Réservé par $reserveur $msg" : "Réservé par personne.");

        return <<<END
        <h1> 
            $item->nom 
            <a href='$this->base/item/$item->id/edit?token=$list->token_edit'>
                <button>éditer ✏️</button>
            </a>
        </h1>
        $visible <br />
        <img src=$src height='100px' alt='$item->nom' /> <br /> 
        <br />
        ID : $item->id <br />
        Description : $item->descr <br />
        Tarif : $item->tarif <br />
        URL : $item->url <br />
        Liste : $nomListe <br />
        <h2>Réservation</h2>
        $reservation

        END;
    }

    /**
     * Affichage d'un item dans une liste en cours
     * @return string
     */
    private function affichageItemListeEnCours(): string {
        $item = $this->tab[0]; 
        $list = $item->liste;

        $visible = ($list->validee == 1 ? "" : "<i><b>Attention ! Cet item n'est visible que par vous.</b></i>");
        $nomListe = ($list == null ? "Aucune" : "<a href='$this->base/list/view?token=$list->token'>$list->titre</a>");
        $reservation = ($item->etat_reserv == 1 ? "Réservé par quelqu'un. Attendez que la liste arrive à échéance pour voir qui." : "Réservé par personne.");
        $src = (isset($item->img)  ? "$this->base/img/$item->img" : "$this->base/img/giftbox2.png");
        
        return <<<END
        <h1> 
            $item->nom 
            <a href='$this->base/item/$item->id/edit?token=$list->token_edit'>
                <button>éditer ✏️</button>
            </a>
        </h1>
        $visible <br />
        <img src=$src height='100px' alt='$item->nom' /> <br /> 
        <br /> 
        ID : $item->id <br />
        Description : $item->descr <br />
        Tarif : $item->tarif <br />
        URL : $item->url <br />
        Liste : $nomListe <br />
        <h2>Réservation</h2>
        $reservation

        END;
    }

    /**
     * Affichage de l'édition d'une liste
     * @return string
     */
    private function editList(): string {
        $list = $this->tab[0];
        $listeVisible = $list->validee == 1 ? 'checked' : "";
        $items = "";
        foreach ($list->items as $item) {
            $removeItem = <<<END
            <form style="display:inline;" action="$this->base/removeItem?token=$list->token_edit&id=$item->id" method="post">
                <input type="submit" name="removeItem" value="🗑️" />
            </form>
            END;

            $items = $items . "<li><a href='$this->base/item/$item->id/edit?token=$list->token_edit'>$item->nom</a> $removeItem</li>";
        }
        $items = $items . <<<END
        <form action="$this->base/addItem?token=$list->token_edit" method="post">
            <input type="submit" name="addItem" value="Ajouter un item" />
        </form>
        END;

        return <<<END
        <form action="$this->base/editList?token=$list->token_edit" method="post" enctype="multipart/form-data">
            <h3>Nom actuelle : $list->titre.</h3>
            <div class="input-group">
                <label for="nom">Nom de la liste :</label>
                <input type="text" id="nom" name="nom" value="" required />
            </div>
            
        
            <br>
            
            <h3>Description actuelle : $list->description.</h3>
            <div class="input-group">
                <label for="descr">Description de la liste :</label>
                <input type="text" id="descr" name="descr" />
            </div>
            
            <br>
        
            <h3>Date d'expiration actuelle : $list->expiration</h3>
            <div class="input-group">
                <label for="dateExp">Date d'expiration</label>
                <input type="date" id="dateExp" name="dateExp" value="" required />
            </div>
            
            
            <br>
            
            
                <input type="checkbox" id="validee" name="validee" value="1" $listeVisible />
                <label for="validee"> Rendre la liste visible</label>
            
            
           
                <input type="checkbox" id="publique" name="publique" value="1" $listeVisible />
                <label for="publique"> Rendre la liste publique</label>
          
            
            <br />
        
            <button type="submit">Valider les changements sur cette liste</button>
        </form>
        <a href="$this->base/list/view?token=$list->token"><button>Annuler ❌</button></a>
        <br /><br />
        Items :
        <section><ul>$items</ul></section>
        END;
    }

    /**
     * Affichage de l'édition d'un item
     * @return string
     */
    private function editItem(): string {
        $item = $this->tab[0];
        $list = $item->liste;

        $image = $item->img == ""
            ? <<<END
            <form action="$this->base/editItem?token=$list->token_edit&id=$item->id&type=addImg" method="post" enctype="multipart/form-data">
                Image associée à l'item :<br />
                
                <div class='file-input'>
                    <input type="file" id="photo" name="photo" accept="image/png, image/jpeg, image/pdf" />
                    <span class='button'>Choose</span>
                    <span class='label' data-js-label>No file selected</label>
                </div>
                
                
                <input type="submit" name="addItem" value="Ajouter une image" />
            </form>
            
            <script> 
                var inputs = document.querySelectorAll('.file-input')

                for (var i = 0, len = inputs.length; i < len; i++) {
                  customInput(inputs[i])
                }
                
                function customInput (el) {
                  const fileInput = el.querySelector('[type="file"]')
                  const label = el.querySelector('[data-js-label]')
                  
                  fileInput.onchange =
                  fileInput.onmouseout = function () {
                    if (!fileInput.value) return
                    
                    var value = fileInput.value.replace(/^.*[\\\/]/, '')
                    el.className += ' -chosen'
                    label.innerText = value
                  }
                }
            </script>
            END
            : <<<END
            Image associée à l'item :<br />
            <img src='$this->base/img/$item->img' height='100px' alt='$item->nom' />
            <form style="display:inline;" action="$this->base/editItem?token=$list->token_edit&id=$item->id&type=edit&type=rmImg" method="post">
                <input type="submit" name="removeImage" value="🗑️" />
            </form>
            END;


        return <<<END
        $image
        <br /><br />
        <form action="$this->base/editItem?token=$list->token_edit&id=$item->id&type=edit" method="post" enctype="multipart/form-data">
            <div class="input-group">
                <label for="nom">Nom de l'item :</label>
                <input type="text" id="nom" name="nom" value="$item->nom" required />
            </div>
        
        
            <br>
        
            <div class="input-group">
                <label for="descr">Description de l'item :</label>
                <input id="descr" name="descr">$item->descr</input>
            </div>
        
        
            <br>
        
            <div class="input-group">
                <label for="tarif">Tarif de l'item :</label>
                <input type="text" id="tarif" name="tarif" value="$item->tarif" />
            </div>
        
            <br>
        
            <div class="input-group">
                <label for="url">Lien du vers un site vendant l'objet :</label>
                <input id="url" name="url" value="$item->url" />
            </div>
            
            <br />
            
            <button type="submit">Valider les changements sur cet item</button>
        </form>
        <a href="$this->base/item/$item->id/view?token=$list->token"><button>Annuler ❌</button></a>
        END;
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
            case ListeController::LISTS_VIEW : {
                $content = $this->affichageListes();
                $title = 'Listes';
                $from = "LesListesStyle.css";
                break;
            }
            case ListeController::LIST_VIEW : {
                $content = $this->affichageListe();
                $title = 'Liste';
                $from = 'ListeInfoStyle.css';

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
                $from = 'EditStyle.css';
                break;
            }
            case ItemController::ITEM_EDIT : {
                $content = $this->editItem();
                $title = "Modification item";
                $from = 'EditStyle.css';
                break;
            }
        }
        return tools::getHtml($from, $htmlPage, $title, $notif, $content, $this->notif, $this->base);
    }
}
