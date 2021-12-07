<?php

namespace wishlist\vues;

class VueParticipant {
    private $tab = array();
    private $selecteur;
    public function __construct(array $t, $s) {
        $this->tab = $t;
        $this->selecteur = $s;
    }

    public function render() {
        switch ($this->selecteur) {
            case LIST_VIEW : {
                $content = $this->affichage();
                break;
            }
            case ITEM_VIEW : {
                $content = $this->htmlUnItem();
                break;
            }
        }
        $html = <<<END
            <!DOCTYPE html> <html>
            <body> …
            <div class="content">
            $content
            </div>
            </body><html>
        END ;
        return $html;
    }

    private function affichage() : string {
        $str = "<section><ol>";
        foreach ($this->tab as $value) {
            $str = $str . "<li>".$value->titre."</li>";
        }
        $str = $str . "</ol></section>";

        return $str;
    }
}