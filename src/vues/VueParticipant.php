<?php

namespace wishlist\vues;

use wishlist\controlers\ListeControler;

class VueParticipant {
    private $tab;
    private $selecteur;
    public function __construct(Iterable $t, $s) {
        $this->tab = $t;
        $this->selecteur = $s;
    }

    private function affichageListes() : string {
        $str = "<section><ol>";
        foreach ($this->tab as $value) {
            $str = $str . "<li>".$value->titre."</li>";
        }
        $str = $str . "</ol></section>";

        return $str;
    }

    private function affichageItems() : string {
        $str = "<section><ol>";
        foreach ($this->tab as $value) {
            $str = $str . "<li>".$value->nom."</li>";
        }
        $str = $str . "</ol></section>";

        return $str;
    }

    public function render() {
        switch ($this->selecteur) {
            case ListeControler::LIST_VIEW : {
                $content = $this->affichageListes();
                break;
            }
            case ListeControler::ITEM_VIEW : {
                $content = $this->affichageItems();
                break;
            }
        }
        $html = <<<END
            <!DOCTYPE html> <html>
            <body>
            <div class="content">
            $content
            </div>
            </body><html>
        END ;
        return $html;
    }

}