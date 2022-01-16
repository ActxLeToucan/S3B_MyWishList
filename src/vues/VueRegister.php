<?php

namespace wishlist\vues;

use wishlist\controllers\RegisterController;
use wishlist\tools;

class VueRegister{
    private $tab;
    private $selecteur;
    private array $notif;

    public function __construct(iterable $t, $s, array $n) {
        $this->tab = $t;
        $this->selecteur = $s;
        $this->notif = $n;
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
        }
        return tools::getHtml($from, $htmlPage, $title, $notif, $content, $this->notif);
    }
}