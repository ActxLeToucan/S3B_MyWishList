<?php

namespace wishlist\vues;

use wishlist\controllers\RegisterController;
use wishlist\tools;

class VueRegister{
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
     * Constructeur de la vue register
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
     * Récupère la page de connexion
     * @return string
     */
    private function loginPage(): string {
        $file =  "HTML/formLogin.html";
        return file_get_contents($file);
    }

    /**
     * Récupère la page d'inscription
     * @return string
     */
    public function signUpPage(): string {
        $file =  "HTML/formSignUp.html";
        return file_get_contents($file);
    }

    /**
     * Récupère la page permettant de trouver une liste avec un token
     * @return string
     */
    public function tokenPage(): string {
        $file =  "HTML/formToken.html";
        return file_get_contents($file);
    }

    /**
     * Affichage du compte d'un utilisateur connecté
     * @return string
     */
    private function monCompte(): string {
        $user = $this->tab[0];

        return <<<END
        Nom d'utilisateur : $user->username<br />
        Adresse e-mail : $user->email<br />
        <div class="compte">
            <a href="./changeMail"><button>Changer d'adresse email</button></a>
            <a href="./changePassword"><button>Changer de mot de passe</button></a>
            <a href="./deleteAccount"><button>Supprimer mon compte</button></a>
        </div>
        
        END;
    }

    /**
     * Affichage du changement d'adresse email
     * @return string
     */
    private function changeMail(): string {
        return <<<END
        <form action="$this->base/changeMailConfirm" method="post" enctype="multipart/form-data">
            <div class="input-group">
                <label for="email">Nouvel email : </label>
                <input type=email required id="email" name="email" />
            </div>
            
            <br />
            
            <div class="input-group">
                <label for="email_confirm">Confirmation de l'email : </label>
                <input type=email required id="email_confirm" name="email_confirm" />
            </div>
            
            <br />
            
            <button type="submit">Valider</button>
        </form>
        END;
    }

    /**
     * Affichage du changement de mot de passe
     * @return string
     */
    private function changePass(): string {
        return <<<END
        <form action="$this->base/changePasswordConfirm" method="post" enctype="multipart/form-data">
            <div class="input-group">
                <label for="oldpass">Mot de passe actuel : </label>
                <input type="password" required id="oldpass" name="oldpass">
            </div>
            
            <br />
            
            <div class="input-group">
                <label for="newpass1">Nouveau mot de passe : </label>
                <input type="password" required id="newpass1" name="newpass1">
            </div>
            
            <br />
            
            <div class="input-group">
                <label for="newpass2">Confirmer le nouveau mot de passe : </label>
                <input type="password" required id="newpass2" name="newpass2">
            </div>
            
            <br />
            
            <button type="submit">Valider</button>
        </form>

        END;
    }

    /**
     * Affichage de la suppression du compte
     * @return string
     */
    private function deleteAccount(): string {
        return <<<END
        <form action="$this->base/deleteAccountConfirm" method="post" enctype="multipart/form-data">
            <div class="confirm">
                <input type="checkbox" id="confirm" name="confirm" value="1" />
                <label for="confirm"> Je confirme vouloir supprimer mon compte définitivement ainsi que toutes les listes et items associés.</label>
            </div>
            
            <br />
        
            <button type="submit">Supprimer mon compte</button>
        </form>
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
            case RegisterController::LOGIN : {
                $htmlPage = $this->loginPage();
                break;
            }
            case RegisterController::SIGNUP : {
                $htmlPage = $this->signUpPage();
                break;
            }
            case RegisterController::TOKEN : {
                $htmlPage = $this->tokenPage();
                break;
            }
            case RegisterController::MONCOMPTE : {
                $content = $this->monCompte();
                $title = "Mon compte";
                $from = 'EditStyle.css';
                break;
            }
            case RegisterController::CHANGEMAIL : {
                $content = $this->changeMail();
                $title = "Changement email";
                $from = 'EditStyle.css';
                break;
            }
            case RegisterController::CHANGEPSW : {
                $content = $this->changePass();
                $title = "Changement mot de passe";
                $from = 'EditStyle.css';
                break;
            }
            case RegisterController::DELETE_ACCOUNT : {
                $content = $this->deleteAccount();
                $title = "Supprimer mon compte";
                $from = 'EditStyle.css';
                break;
            }
        }
        return tools::getHtml($from, $htmlPage, $title, $notif, $content, $this->notif, $this->base);
    }
}