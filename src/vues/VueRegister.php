<?php

namespace wishlist\vues;

use wishlist\controllers\RegisterController;
class VueRegister{
    private $tab;
    private $selecteur;

    public function __construct(iterable $t, $s) {
        $this->tab = $t;
        $this->selecteur = $s;
    }

    public function confirmationConnected() : string{
        $str = "L'utilisateur ".$this->tab['username']." est connecté.";

        return $str;
    }
    public function notConnected():string{
        $str = "Mot de passe ou nom d'utilisateur incorrect.";

        return $str.$this->tab["username"]." ".$this->tab["password"];
    }

    public function loginPage() : string {
        $file =  "HTML/formLogin.html";
        return file_get_contents($file);
    }

    public function render() {
        $content = "";
        switch ($this->selecteur) {
            case RegisterController::CONNECTED : {
                $content = $this->confirmationConnected();
                $title = 'Connected';
                break;
            }
            case RegisterController::CONNECTIONFAILED : {
                $content = $this->notConnected();
                $title = 'Failed';
                break;
            }
            case RegisterController::LOGIN : {
                $htmlPage = $this->loginPage();
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