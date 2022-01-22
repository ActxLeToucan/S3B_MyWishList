<?php

namespace wishlist\controllers;

use wishlist\models\Authenticate;
use wishlist\models\Liste;
use wishlist\models\Message;
use wishlist\tools;
use wishlist\vues\VueCreateur;
use wishlist\vues\VueParticipant;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class ListeController {
    const LISTS_VIEW = 'lists';
    const LIST_VIEW = 'list_view';
    const LIST_FORM_CREATE = 'list_form_create';
    const LIST_EDIT = 'list_edit';
    const PUBLIC = "public";
    const CREATEURS = "createurs";
    const CREATEUR = "createur";

    /**
     * @var object container
     */
    private object $c;

    /**
     * Constructeur de ListeController
     * @param object $c container
     */
    public function __construct(object $c) {
        $this->c = $c;
    }

    /**
     * Affichage de la page d'accueil
     * @param Request $rq requête
     * @param Response $rs réponse
     * @param array $args arguments de la requête
     * @return Response
     */
    public function getHomePage(Request $rq, Response $rs, array $args): Response {
        $container = $this->c;
        $base = $rq->getUri()->getBasePath();
        $route_uri = $container->router->pathFor('home');
        $url = $base . $route_uri;

        $lists = Liste::where('publique','=',1)->orderBy('expiration','ASC')->get();

        $notif = tools::prepareNotif($rq);
        $v = new VueParticipant($lists, ListeController::PUBLIC, $notif, $base);
        $rs->getBody()->write($v->render());
        return $rs;
    }

    /**
     * Affichage de toutes les listes d'un utilisateur connecté
     * @param Request $rq requête
     * @param Response $rs réponse
     * @param array $args arguments de la requête
     * @return Response
     */
    public function getAllListe(Request $rq, Response $rs, array $args): Response {
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

    /**
     * Affichage d'une liste
     * @param Request $rq requête
     * @param Response $rs réponse
     * @param array $args arguments de la requête
     * @return Response
     */
    public function getListByToken(Request $rq, Response $rs, array $args): Response {
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
        } else if ($liste->validee != 1) {
            $notifMsg = urlencode("Cette liste n'est pas visible car elle n'a pas été validée.");
            return $rs->withRedirect($base."?notif=$notifMsg");
        } else {
            $v = new VueParticipant([$liste], ListeController::LIST_VIEW, $notif, $base);
        }

        $rs->getBody()->write($v->render());
        return $rs;
    }

    /**
     * Affichage de la page permettant d'éditer une liste
     * @param Request $rq requête
     * @param Response $rs réponse
     * @param array $args arguments de la requête
     * @return Response
     */
    public function editListByToken(Request $rq, Response $rs, array $args): Response {
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

    /**
     * Traitement de l'édition d'une liste
     * @param Request $rq requête
     * @param Response $rs réponse
     * @param array $args arguments de la requête
     * @return Response
     */
    public function editList(Request $rq, Response $rs, array $args): Response {
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
            $publique = (isset($content['publique']) && filter_var($content['publique'], FILTER_SANITIZE_NUMBER_INT) == 1 ? 1 : 0);


            Liste::where('token_edit', '=', $token)->update(['titre' => $titre]);
            Liste::where('token_edit', '=', $token)->update(['description' => $descr]);
            Liste::where('token_edit', '=', $token)->update(['expiration' => $exp]);
            Liste::where('token_edit', '=', $token)->update(['validee' => $validee]);
            Liste::where('token_edit', '=', $token)->update(['publique' => $publique]);

            $liste = Liste::where('token_edit','=',$token)->first();
            $notifMsg = urlencode("La liste a été mise à jour.");
            return $rs->withRedirect($base."/list/view?token=$liste->token&notif=$notifMsg");
        }
    }

    /**
     * Traitement de la création d'une liste
     * @param Request $rq requête
     * @param Response $rs réponse
     * @param array $args arguments de la requête
     * @return Response
     */
    public function newListe(Request $rq, Response $rs, array $args): Response {
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
            $newListe->token_edit = tools::generateToken();
            $newListe->user_id = $user->id;
            $newListe->save();

            $liste = Liste::where('token','=',$token)->first();
            $notifMsg = urlencode("Liste créée !");
            return $rs->withRedirect($base."/list/view?token=$liste->token&notif=$notifMsg");
        } else {
            $notifMsg = urlencode("Impossible de créer une nouvelle liste. Reconnectez-vous.");
            return $rs->withRedirect($base."/login?notif=$notifMsg");
        }
    }

    /**
     * Affichage de la page permettant de créer une liste
     * @param Request $rq requête
     * @param Response $rs réponse
     * @param array $args arguments de la requête
     * @return Response
     */
    public function createList(Request $rq, Response $rs, array $args): Response {
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
        return $rs;
    }

    /**
     * Traitement de l'ajout d'un message sur une liste
     * @param Request $rq requête
     * @param Response $rs réponse
     * @param array $args arguments de la requête
     * @return Response
     */
    public function addMsg(Request $rq, Response $rs, array $args): Response {
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
            $msg->id_user = 0;
            $msg->pseudo = filter_var($content['pseudo'], FILTER_SANITIZE_STRING);
        }
        $msg->texte = filter_var($content['texte'], FILTER_SANITIZE_STRING);
        $msg->save();

        return $rs->withRedirect($base."/list/view?token={$msg->list->token}");
    }

    /**
     * Affichage des créateurs qui ont au moins une liste à la fois validée et publique
     * @param Request $rq requête
     * @param Response $rs réponse
     * @param array $args arguments de la requête
     * @return Response
     */
    public function createurs(Request $rq, Response $rs, array $args): Response {
        $container = $this->c;
        $base = $rq->getUri()->getBasePath();
        $route_uri = $container->router->pathFor('createurs');
        $url = $base . $route_uri;

        $notif = tools::prepareNotif($rq);

        $lists = Liste::where(["publique" => 1, "validee" => 1])->get();
        $users = [];
        foreach ($lists as $list) {
            $user = $list->user;
            in_array($user, $users) ? : array_push($users, $user);
        }

        $v = new VueParticipant($users, ListeController::CREATEURS, $notif, $base);
        $rs->getBody()->write($v->render());
        return $rs;
    }

    /**
     * Affichage des listes publiques et validées d'un créateur
     * @param Request $rq requête
     * @param Response $rs réponse
     * @param array $args arguments de la requête
     * @return Response
     */
    public function createur(Request $rq, Response $rs, array $args): Response {
        $container = $this->c;
        $base = $rq->getUri()->getBasePath();
        $route_uri = $container->router->pathFor('createur', $args);
        $url = $base . $route_uri;

        $notif = tools::prepareNotif($rq);

        $username = $args['username'];
        $user = Authenticate::where("username", "=", $username)->first();

        $v = new VueParticipant([$user], ListeController::CREATEUR, $notif, $base);
        $rs->getBody()->write($v->render());
        return $rs;
    }
}