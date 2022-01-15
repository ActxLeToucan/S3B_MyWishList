<?php

namespace wishlist\vues;

use wishlist\controllers\RegisterController;
use wishlist\tools;

class VueRegister{
    private $tab;
    private $selecteur;
    private array $params;

    public function __construct(iterable $t, $s, array $p) {
        $this->tab = $t;
        $this->selecteur = $s;
        $this->params = $p;
    }

    private function loginPage() : string {
        $file =  "HTML/formLogin.html";
        return file_get_contents($file);
    }

    public function signUpPage() : string {
        $file =  "HTML/formSignUp.html";
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
        }
        return tools::getHtml($from, $htmlPage, $title, $notif, $content, $this->params);
    }
}