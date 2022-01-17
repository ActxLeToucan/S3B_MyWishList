<?php

namespace wishlist\controllers;

use wishlist\models\Authenticate;
use wishlist\models\Liste;
use wishlist\models\Message;
use wishlist\tools;
use wishlist\vues\VueCreateur;
use wishlist\vues\VueParticipant;

class ListeController {
    const LISTS_VIEW = 'lists';
    const LIST_VIEW = 'list_view';
    const LIST_FORM_CREATE = 'list_form_create';
    const LIST_EDIT = 'list_edit';

    private $c;

    /**
     * @param $c
     */
    public function __construct($c) {
        $this->c = $c;
    }

    /**
     * pour qqn connecté
     */
    public function getAllListe($rq, $rs, $args) {
        $container = $this->c;
        $base = $rq->getUri()->getBasePath();
        $route_uri = $container->router->pathFor('Listes');
        $url = $base . $route_uri;

        $notif = tools::prepareNotif($rq);

        if (isset($_SESSION['username']) && isset($_SESSION['AccessRights'])) {
            $user = Authenticate::where('username','=',$_SESSION['username'])->first();
            $lists = Liste::where('user_id','=',$user->id)->get();

            $v = new VueCreateur($lists, ListeController::LISTS_VIEW, $notif, $base);
        } else {
            $notifMsg = urlencode("Vous devez être connecté pour accéder à cette page.");
            return $rs->withRedirect($base."/login?notif=$notifMsg");
        }
        $rs->getBody()->write($v->render());
        return $rs;
    }

    public function getListByToken($rq, $rs, $args) {
        $container = $this->c;
        $base = $rq->getUri()->getBasePath();
        $route_uri = $container->router->pathFor('listByTokenView', $args);
        $url = $base . $route_uri;

        $token = $rq->getQueryParams('token');
        $liste = Liste::select()->where('token','=',$token)->first();
        $user = $liste->user;

        $notif = tools::prepareNotif($rq);

        if (isset($_SESSION['username']) && isset($_SESSION['AccessRights']) && $user->username == $_SESSION['username']) {
            $v = new VueCreateur([$liste], ListeController::LIST_VIEW, $notif, $base);
        } else if (!isset($rq->getQueryParams()['token']) || is_null($liste)) {
            $notifMsg = urlencode("La liste demandée n'existe pas. Assurez-vous d'avoir le bon token.");
            return $rs->withRedirect($base."?notif=$notifMsg");
        } else {
            $v = new VueParticipant([$liste], ListeController::LIST_VIEW, $notif, $base);
        }

        $rs->getBody()->write($v->render());
        return $rs;
    }

    public function editListByToken($rq, $rs, $args) {
        $container = $this->c;
        $base = $rq->getUri()->getBasePath();
        $route_uri = $container->router->pathFor('listByTokenView', $args);
        $url = $base . $route_uri;

        $token = $rq->getQueryParams('token');
        $liste = Liste::select()->where('token_edit','=',$token)->first();
        if (isset($_SESSION['username']) && isset($_SESSION['AccessRights'])) {
            $user = Authenticate::where("username", "=", $_SESSION["username"])->first();
        } else {
            $user = null;
        }
        if (!isset($rq->getQueryParams()['token']) || is_null($liste)) {
            $notifMsg = urlencode("La liste demandée n'existe pas. Assurez-vous d'avoir le bon token.");
            return $rs->withRedirect($base."/list?notif=$notifMsg");
        } else if (is_null($user) || $user->id != $liste->user_id) {
            $notifMsg = urlencode("Vous ne pouvez pas modifier cette liste car vous n'en êtes pas le créateur.");
            return $rs->withRedirect($base."/list/view?token=$liste->token&notif=$notifMsg");
        }

        $notif = tools::prepareNotif($rq);

        $v = new VueCreateur([$liste], ListeController::LIST_EDIT, $notif, $base);
        $rs->getBody()->write($v->render());
        return $rs;
    }

    public function editList($rq, $rs, $args) {
        $container = $this->c;
        $base = $rq->getUri()->getBasePath();
        $route_uri = $container->router->pathFor('editList', $args);
        $url = $base . $route_uri;

        $token = $rq->getQueryParams('token');
        $liste = Liste::where('token_edit','=',$token)->first();
        if (isset($_SESSION['username']) && isset($_SESSION['AccessRights'])) {
            $user = Authenticate::where("username", "=", $_SESSION["username"])->first();
        } else {
            $user = null;
        }
        if (!isset($rq->getQueryParams()['token']) || is_null($liste)) {
            $notifMsg = urlencode("La liste demandée n'existe pas. Assurez-vous d'avoir le bon token.");
            return $rs->withRedirect($base."/list?notif=$notifMsg");
        } else if (is_null($user) || $user->id != $liste->user_id) {
            $notifMsg = urlencode("Vous ne pouvez pas modifier cette liste car vous n'en êtes pas le créateur.");
            return $rs->withRedirect($base."/list/view?token=$liste->token&notif=$notifMsg");
        } else {
            $content = $rq->getParsedBody();

            $titre = filter_var($content['nom'], FILTER_SANITIZE_STRING);
            $descr = filter_var($content['descr'], FILTER_SANITIZE_STRING);
            $exp = filter_var($content['dateExp'], FILTER_SANITIZE_STRING);
            $validee = (isset($content['validee']) && filter_var($content['validee'], FILTER_SANITIZE_NUMBER_INT) == 1 ? 1 : 0);

            Liste::where('token_edit', '=', $token)->update(['titre' => $titre]);
            Liste::where('token_edit', '=', $token)->update(['description' => $descr]);
            Liste::where('token_edit', '=', $token)->update(['expiration' => $exp]);
            Liste::where('token_edit', '=', $token)->update(['validee' => $validee]);

            $liste = Liste::where('token_edit','=',$token)->first();
            $notifMsg = urlencode("La liste a été mise à jour.");
            return $rs->withRedirect($base."/list/view?token=$liste->token&notif=$notifMsg");
        }
    }

    public function newListe($rq, $rs, $args) {
        $container = $this->c;
        $base = $rq->getUri()->getBasePath();
        $route_uri = $container->router->pathFor('New_Liste');
        $url = $base . $route_uri;

        $content = $rq->getParsedBody();

        if (isset($_SESSION['username']) && isset($_SESSION['AccessRights'])) {
            $nomItem = filter_var($content['nom'], FILTER_SANITIZE_STRING);
            $descr = filter_var($content['descr'], FILTER_SANITIZE_STRING);
            $exp =filter_var($content['dateExp'], FILTER_SANITIZE_STRING);

            $token = tools::generateToken();
            $user = Authenticate::where('username','=',$_SESSION['username'])->first();

            $newListe = new Liste();
            $newListe->titre = $nomItem;
            $newListe->description = $descr;
            $newListe->expiration = $exp;
            $newListe->token = $token;
            $newListe->user_id = $user->id;
            $newListe->save();

            $liste = Liste::where('token_edit','=',$token)->first();
            $notifMsg = urlencode("Liste créée !");
            return $rs->withRedirect($base."/list/view?token=$liste->token&notif=$notifMsg");
        } else {
            $notifMsg = urlencode("Impossible de créer une nouvelle liste. Reconnectez-vous.");
            return $rs->withRedirect($base."/login?notif=$notifMsg");
        }
    }

    public function createList($rq, $rs, $args) {
        $container = $this->c;
        $base = $rq->getUri()->getBasePath();
        $route_uri = $container->router->pathFor('formulaireListCreate');
        $url = $base . $route_uri;

        $notif = tools::prepareNotif($rq);

        if (!isset($_SESSION['username']) || !isset($_SESSION['AccessRights'])) {
            $notifMsg = urlencode("Impossible de créer une nouvelle liste. Reconnectez-vous.");
            return $rs->withRedirect($base."/login?notif=$notifMsg");
        }

        $v = new VueCreateur([], ListeController::LIST_FORM_CREATE, $notif, $base);
        $rs->getBody()->write($v->render());
        return $rs ;
    }

    public function addMsg($rq, $rs, $args) {
        $container = $this->c;
        $base = $rq->getUri()->getBasePath();
        $route_uri = $container->router->pathFor('addmsg');
        $url = $base . $route_uri;

        $content = $rq->getParsedBody();

        $msg = new Message();
        $msg->id_list = Liste::where("token", "=", $rq->getQueryParams('token'))->first()->no;
        if (isset($_SESSION['username']) && isset($_SESSION['AccessRights'])) {
            $user = Authenticate::where("username", "=", $_SESSION['username'])->first();
            $msg->id_user = $user->id;
            $msg->pseudo = null;
        } else {
            $msg->id_user = null;
            $msg->pseudo = filter_var($content['pseudo'], FILTER_SANITIZE_STRING);
        }
        $msg->texte = filter_var($content['texte'], FILTER_SANITIZE_STRING);
        $msg->save();

        return $rs->withRedirect($base."/list/view?token={$msg->list->token}");
    }
}