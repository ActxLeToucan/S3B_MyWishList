<?php

namespace wishlist\vues;

use wishlist\controllers\RegisterController;
use wishlist\tools;

class VueRegister{
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

    private function loginPage() : string {
        $file =  "HTML/formLogin.html";
        return file_get_contents($file);
    }

    public function signUpPage() : string {
        $file =  "HTML/formSignUp.html";
        return file_get_contents($file);
    }

    public function tokenPage() : string {
        $file =  "HTML/formToken.html";
        return file_get_contents($file);
    }

    private function monCompte() : string {
        $user = $this->tab[0];

        return <<<END
        Nom d'utilisateur : $user->username<br />
        Adresse e-mail : $user->email<br />
        
        <a href="./changeMail"><button>Changer d'adresse email</button></a>
        <a href="./changePassword"><button>Changer de mot de passe</button></a>
        <a href="./deleteAccount"><button>Supprimer mon compte</button></a>
        END;
    }

    private function changeMail() : string {
        $user = $this->tab[0];

        return <<<END
        <form action="$this->base/changeMailConfirm" method="post" enctype="multipart/form-data">
            <div class="email">
                <label for="email">Nouvel email : </label>
                <input type=email required id="email" name="email" />
            </div>
            
            <br />
            
            <div class="email_confirm">
                <label for="email_confirm">Confirmation de l'email : </label>
                <input type=email required id="email_confirm" name="email_confirm" />
            </div>
            
            <br />
            
            <button type="submit">Valider</button>
        </form>
        END;
    }

    private function changePass() : string {
        $user = $this->tab[0];

        return <<<END
        <form action="$this->base/changePasswordConfirm" method="post" enctype="multipart/form-data">
            <div class="oldpass">
                <label for="oldpass">Mot de passe actuel : </label>
                <input type="password" required id="oldpass" name="oldpass">
            </div>
            
            <br />
            
            <div class="newpass1">
                <label for="newpass1">Nouveau mot de passe : </label>
                <input type="password" required id="newpass1" name="newpass1">
            </div>
            
            <br />
            
            <div class="newpass2">
                <label for="newpass2">Confirmer le nouveau mot de passe : </label>
                <input type="password" required id="newpass2" name="newpass2">
            </div>
            
            <br />
            
            <div class="show">
                <input type="checkbox" onclick="showOrNot()"> Afficher le mot passe
            </div>
            
            <br />
            
            <button type="submit">Valider</button>
        </form>

        <script>
            function showOrNot() {
                let pass = []; 
                pass.push(document.getElementById("oldpass"));
                pass.push(document.getElementById("newpass1"));
                pass.push(document.getElementById("newpass2"));
                
                pass.forEach(p => {
                    if (p.type === "password") p.type = "text";
                    else p.type = "password";
                });
            }
        </script>
        END;
    }

    public function render() : string {
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
                break;
            }
            case RegisterController::CHANGEMAIL : {
                $content = $this->changeMail();
                $title = "Changement email";
                break;
            }
            case RegisterController::CHANGEPSW : {
                $content = $this->changePass();
                $title = "Changement mot de passe";
                break;
            }
        }
        return tools::getHtml($from, $htmlPage, $title, $notif, $content, $this->notif, $this->base);
    }
}