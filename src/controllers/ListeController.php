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
    const LIST_VIEW_ERROR = 'list_view_error';
    const LIST_NEW = 'newList';
    const LIST_FORM_CREATE = 'list_form_create';
    const LIST_NEW_ERROR = 'liste_new_error';
    const LIST_EDIT = 'list_edit';
    const LIST_EDIT_TOKEN_ERROR = 'list_edit_token_error';
    const LIST_EDIT_OWNER_ERROR = 'list_edit_token_edit';

    private $c;

    /**
     * @param $c
     */
    public function __construct($c) {
        $this->c = $c;
    }

    /**
     * pour un gars connecté
     */
    public function getAllListe( $rq, $rs, $args ) {
        $container = $this->c;
        $base = $rq->getUri()->getBasePath();
        $route_uri = $container->router->pathFor('Listes');
        $url = $base . $route_uri;


        $user = Authenticate::where('username','=',$_SESSION['username'])->first();
        $lists = Liste::where('user_id','=',$user->id)->get();
        $v = new VueCreateur($lists, ListeController::LISTS_VIEW);
        $rs->getBody()->write($v->render());
        return $rs;
    }

    public function getListById($rq, $rs, $args) {
        $container = $this->c;
        $base = $rq->getUri()->getBasePath();
        $route_uri = $container->router->pathFor('listById', $args);
        $url = $base . $route_uri;

        $id = $args['id'];
        $l = Liste::where('no','=',$id)->first();
        $v = new VueParticipant([$l], ListeController::LIST_VIEW);
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
        if (isset($_SESSION['username']) && isset($_SESSION['AccessRights']) && $user->username == $_SESSION['username']) {
            $affichage = ListeController::LIST_VIEW;
            $v = new VueCreateur([$liste], $affichage);
        } else if (!isset($rq->getQueryParams()['token']) || is_null($liste)) {
            $affichage = ListeController::LIST_VIEW_ERROR;
            $v = new VueParticipant([$liste], $affichage);
        } else {
            $affichage = ListeController::LIST_VIEW;
            $v = new VueParticipant([$liste], $affichage);
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
            $affichage = ListeController::LIST_EDIT_TOKEN_ERROR;
        } else if (is_null($user) || $user->id != $liste->user_id) {
            $affichage = ListeController::LIST_EDIT_OWNER_ERROR;
        } else {
            $affichage = ListeController::LIST_EDIT;
        }

        $v = new VueCreateur([$liste], $affichage);
        $rs->getBody()->write($v->render());
        return $rs;
    }

    public function newListe( $rq, $rs, $args ) {
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
            $affichage = ListeController::LIST_NEW;
        } else {
            $affichage = ListeController::LIST_NEW_ERROR;
        }






        $v = new VueCreateur([$newListe], $affichage);
        $rs->getBody()->write($v->render());
        return $rs;
    }

    public function createList($rq, $rs, $args) {
        $container = $this->c ;
        $base = $rq->getUri()->getBasePath() ;
        $route_uri = $container->router->pathFor('formulaireListCreate');
        $url = $base . $route_uri ;

        $v = new VueCreateur([], ListeController::LIST_FORM_CREATE);
        $rs->getBody()->write($v->render()) ;
        return $rs ;
    }

    public function addMsg($rq, $rs, $args) {
        $container = $this->c ;
        $base = $rq->getUri()->getBasePath() ;
        $route_uri = $container->router->pathFor('addmsg');
        $url = $base . $route_uri ;

        $content = $rq->getParsedBody();

        $msg = new Message();
        $msg->id_list = Liste::where("token", "=", $rq->getQueryParams('token'))->first()->no;
        if (isset($_SESSION['username']) && isset($_SESSION['AccessRights'])) {
            $user = Authenticate::where("username", "=", $_SESSION['username'])->first();
            $msg->id_user = $user->id;
            $msg->pseudo = null;
        } else {
            $msg->id_user = null;
            $msg->pseudo = filter_var($content['pseudo'], FILTER_SANITIZE_STRING);;
        }
        $msg->texte = filter_var($content['texte'], FILTER_SANITIZE_STRING);
        $msg->save();

        $v = new VueParticipant([$msg->list], ListeController::LIST_VIEW);
        $rs->getBody()->write($v->render());
        return $rs;
    }
}