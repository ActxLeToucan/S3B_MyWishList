<?php

namespace wishlist;

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

    public static function getHomePage() : string {
        $file =  "HTML/index.html";
        return file_get_contents($file);
    }

    public static function insertIntoBody(string $page, string $text) : string {
        $positionBody = strpos($page, "<body>") + 6;
        return substr_replace($page, $text, $positionBody, 0);
    }

    public static function messageBox(string $message) : string {
        $html = <<<END
        <script>
            window.addEventListener("load", () => {
                let notif = document.getElementById('notif-box');
                notif.addEventListener("click", () => {
                    notif.style.display = "none";
                });
            });
        </script>
        <style>
        #notif-box {
            top:18px;
            right:84px;
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
            background-color:#e1e1e1;
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

        return $html;
    }

    public static function rewriteUrl(string $page = "", string $titre = "") {
        if (substr($_SERVER['REQUEST_URI'], strlen($_SERVER['REQUEST_URI'])-1) == "/") {
            $page = "../$page";
        }
        $titre = ($titre = "" ? "" : "document.title = '$titre'");

        $script = <<<END
        <script>
        window.addEventListener("load", () => {
            history.pushState(null, "", "$page");
            $titre
        });
        </script>
        END;

        return $script;
    }
}