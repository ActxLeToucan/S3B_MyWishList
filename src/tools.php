<?php

namespace wishlist;

use JetBrains\PhpStorm\Pure;

class tools {
    public static function getRandomString(int $length = 10) : string {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randstring = '';
        for ($i = 0; $i < $length; $i++) {
            $randstring = $randstring.$characters[Rand(0, strlen($characters)-1)];
        }
        return $randstring;
    }

    public static function generateToken() : string {
        return time().self::getRandomString(20);
    }

    public static function insertIntoBody(string $page, string $text) : string {
        $positionBody = strpos($page, "<body>") + 6;
        return substr_replace($page, $text, $positionBody, 0);
    }

    public static function messageBox(string $message) : string {
        return <<<END
        <script>
            window.addEventListener("load", () => {
                let notif = document.getElementById('notif-box');
                
                let interval = setInterval(disparaitre, 15000);
                
                function disparaitre() {
                    notif.style.display = "none";
                        
                    let url = window.location.href;
                    url = url.substring(0, url.indexOf("notif")-1)
                    history.pushState(null, "", url);
                    
                    clearInterval(interval);
                }
                notif.addEventListener("click", disparaitre);
            });
        </script>
        <style>
        #notif-box {
            top:100px;
            right:20px;
            position:fixed;
            z-index:300;
        }
        #notif {
            background-color:white;
            border-radius:5px;
            box-shadow:0 5px 12px 0 rgba(0,0,0,.3);
            color:black;
            margin-bottom:3px;
            max-height:232px;
            overflow:hidden;
            padding:18px 18px 18px 24px;
            position:relative;
            width:420px
        }
        #notif:before {
            background-color:#656565;
            border-radius:5px 0 0 5px;
            content:"";
            display:block;
            height:100%;
            left:0;
            position:absolute;
            top:0;
            width:6px
        }
        #notif-content {
            flex:1;
            font-size:14px;
            font-weight:300;
            line-height:1.5;
            text-align:left
        }
        #notif-box:hover {
            cursor:pointer;
        }
        </style>
        <div id="notif-box">
            <div id="notif">
                <div id="notif-content">
                    <p>$message</p>
                </div>
            </div>
        </div>
        END;
    }

    public static function prepareNotif($rq) : array {
        return array(
            "notif" => isset($rq->getQueryParams()['notif']) ? urldecode($rq->getQueryParams('notif')["notif"]) : null,
            "link" => isset($rq->getQueryParams()['notif']) && isset($rq->getQueryParams()['link']) ? filter_var($rq->getQueryParams('link')["link"], FILTER_SANITIZE_ADD_SLASHES): null,
        );
    }

    /**
     * @param string $from
     * @param string $htmlPage
     * @param string $title
     * @param string $notif
     * @param string $content
     * @param array $notifParams
     * @param string $base
     * @return string
     */
    #[Pure] public static function getHtml(string $from, string $htmlPage, string $title, string $notif, string $content, array $notifParams, string $base): string {
        $style = $from != "" ? "<link rel='stylesheet' href='$base/Style/$from'>" : "";
        $connexion = !isset($_SESSION['username'])
            ? "<li><a href='$base/login'>Connexion</a></li>"
            : <<<END
            <li><a href="$base/list">Mes listes</a></li>
            <li><a href='monCompte'>👤 {$_SESSION['username']}</a></li>
            <li><a href='$base/logout'>Se déconnecter</a></li>"
            END;

        $html = $htmlPage != "" ? $htmlPage : <<<END
            <!DOCTYPE html> <html lang="fr">
            <head>
                <meta charset="UTF-8">
                <link rel="stylesheet" href="$base/Style/indexStyle.css">
                <title>$title</title>
                $style
            </head>
            <body>
            $notif
            <nav id="navbar" class="">
                <div class="nav-wrapper">
                    <!-- Navbar Logo -->
                    <div class="logo">
                        <!-- Logo Placeholder for Inlustration -->
                        <a href="$base/"> MyWishList</a>
                    </div>
            
            
                    <ul id="menu">
                        <li><a href="$base/formulaireListe">Créer ma liste</a></li>
                        <li><a href="$base/token">Trouer une liste avec un token</a></li>
                        $connexion
                    </ul>
                </div>
            </nav>
            <div class="content">
            $content
            </div>
            </body></html>
        END;

        if (!is_null($notifParams["notif"])) {
            $texte = $notifParams["notif"];
            if (is_null($notifParams["link"])) {
                $html = tools::insertIntoBody($html, tools::messageBox($texte));
            } else {
                $lien = $notifParams['link'];
                $html = tools::insertIntoBody($html, tools::messageBox("<a href='$lien'>$texte</a>"));
            }
        }
        return $html;
    }
}