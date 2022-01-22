<?php

namespace wishlist\controllers;

use wishlist\models\Authenticate;
use wishlist\models\Item;
use wishlist\models\Liste;
use wishlist\tools;
use wishlist\vues\VueCreateur;
use wishlist\vues\VueParticipant;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class ItemController {
    const ITEM_VIEW = 'item';
    const ITEM_VIEW_OWNER_EN_COURS = 'item_view_owner_en_cours';
    const ITEM_VIEW_OWNER_EXPIRE = 'item_view_owner_expirée';
    const ITEM_FORM_CREATE = 'form_item_create';
    const ITEM_EDIT = "item_edit";

    /**
     * @var object container
     */
    private object $c;

    /**
     * Constructeur d'ItemController
     * @param object $c container
     */
    public function __construct(object $c) {
        $this->c = $c;
    }

    /**
     * Traitement de l'ajout d'un item
     * @param Request $rq requête
     * @param Response $rs réponse
     * @param array $args arguments de la requête
     * @return Response
     */
    public function addItem(Request $rq, Response $rs, array $args): Response {
        $container = $this->c;
        $base = $rq->getUri()->getBasePath();
        $route_uri = $container->router->pathFor('itemVierge');
        $url = $base . $route_uri;

        $token = $rq->getQueryParams('token');
        $list = Liste::where("token_edit", "=", $token)->first();
        $user = $list->user;

        if (!isset($rq->getQueryParams()['token']) || is_null($list)) {
            $notifMsg = urlencode("Le token de modification ne correspond à aucune liste.");
            return $rs->withRedirect($base."?notif=$notifMsg");
        } else if (isset($_SESSION['username']) && isset($_SESSION['AccessRights']) && $user->username == $_SESSION['username']) {
            $newItem = new Item();
            $newItem->nom = "Nouvel item";
            $newItem->liste_id = $list->no;
            $newItem->save();

            return $rs->withRedirect($base."/list/edit?token=$list->token_edit");
        } else  {
            $notifMsg = urlencode("Vous ne pouvez pas modifier cette liste car vous n'en êtes pas le créateur.");
            return $rs->withRedirect($base."/list?notif=$notifMsg");
        }
    }

    /**
     * Traitement modification d'un item
     * @param Request $rq requête
     * @param Response $rs réponse
     * @param array $args arguments de la requête
     * @return Response
     */
    public function editItem(Request $rq, Response $rs, array $args): Response {
        $container = $this->c;
        $base = $rq->getUri()->getBasePath();
        $route_uri = $container->router->pathFor('editItem');
        $url = $base . $route_uri;

        $token = $rq->getQueryParams('token');
        $idItem = $rq->getQueryParams('id');
        $typeEdit = $rq->getQueryParams()["type"];
        $list = Liste::where("token_edit", "=", $token)->first();
        $item = Item::where("id", "=", $idItem['id'])->first();
        $user = $list->user;


        if (!isset($rq->getQueryParams()['token']) || is_null($list) || !isset($rq->getQueryParams()['id']) || is_null($item) || $list->token_edit != $token["token"]) {
            $notifMsg = urlencode("L'association token de modification / id ne correspond à aucun item.");
            return $rs->withRedirect($base."?notif=$notifMsg");
        } else if (!isset($_SESSION['username']) || !isset($_SESSION['AccessRights']) || $user->username != $_SESSION['username']) {
            $notifMsg = urlencode("Vous ne pouvez pas modifier cet item car vous n'en êtes pas le créateur.");
            return $rs->withRedirect($base."/list?notif=$notifMsg");
        } else {
            if ($item->etat_reserv == 1) {
                $notifMsg = urlencode("Vous ne pouvez pas modifier l'item \"$item->nom\" car quelqu'un l'a réservé.");
            } else {
                $content = $rq->getParsedBody();

                switch ($typeEdit) {
                    case "edit" : {
                        $nomItem = filter_var($content['nom'], FILTER_SANITIZE_STRING);
                        $descr = filter_var($content['descr'], FILTER_SANITIZE_STRING);
                        $url = filter_var($content['url'], FILTER_SANITIZE_STRING);
                        $tarif = filter_var($content['tarif'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

                        Item::where('id', '=', $item->id)->update(['nom' => $nomItem]);
                        Item::where('id', '=', $item->id)->update(['descr' => $descr]);
                        Item::where('id', '=', $item->id)->update(['url' => $url]);
                        Item::where('id', '=', $item->id)->update(['tarif' => $tarif]);

                        $notifMsg = urlencode("L'item a été mis à jour.");
                        break;
                    }
                    case "rmImg" : {
                        is_null($item->img) || $item->img == "" ? : unlink("$base/img/$item->img");
                        Item::where('id', '=', $item->id)->update(['img' => ""]);

                        $notifMsg = urlencode("L'image a été supprimée.");
                        break;
                    }
                    case "addImg" : {
                        $extension = $_FILES['photo']['type'];
                        $cheminServeur = $_FILES['photo']['tmp_name'];
                        $fileName = str_replace('image/', time() . "_" . tools::getRandomString() . '.', $extension);
                        $uploadFile = "./img/$fileName";
                        move_uploaded_file($cheminServeur, $uploadFile);

                        Item::where('id', '=', $item->id)->update(['img' => $fileName]);

                        $notifMsg = urlencode("L'image a été ajoutée.");
                        break;
                    }
                    default : {
                        $notifMsg = urlencode("Le type d'édition est invalide.");
                        break;
                    }
                }
            }
            return $rs->withRedirect($base."/item/$item->id/view?token={$item->liste->token}&notif=$notifMsg");
        }
    }

    /**
     * Traitement suppression d'un item
     * @param Request $rq requête
     * @param Response $rs réponse
     * @param array $args arguments de la requête
     * @return Response
     */
    public function removeItem(Request $rq, Response $rs, array $args): Response {
        $container = $this->c;
        $base = $rq->getUri()->getBasePath();
        $route_uri = $container->router->pathFor('supprItem');
        $url = $base . $route_uri;

        $token = $rq->getQueryParams('token');
        $idItem = $rq->getQueryParams('id');
        $list = Liste::where("token_edit", "=", $token)->first();
        $item = Item::where("id", "=", $idItem['id'])->first();
        $user = $list->user;

        if (!isset($rq->getQueryParams()['token']) || is_null($list) || !isset($rq->getQueryParams()['id']) || is_null($item) || $item->liste_id != $list->no) {
            $notifMsg = urlencode("L'association token de modification / id ne correspond à aucun item.");
            return $rs->withRedirect($base."?notif=$notifMsg");
        } else if (isset($_SESSION['username']) && isset($_SESSION['AccessRights']) && $user->username == $_SESSION['username']) {
            if ($item->etat_reserv == 1) {
                $notifMsg = urlencode("Vous ne pouvez pas supprimer l'item \"$item->nom\" car quelqu'un l'a réservé.");
            } else {
                $image = $item->img;
                is_null($image) || $image == "" ? : unlink("./img/$image");
                $item->delete();

                $notifMsg = urlencode("L'item \"$item->nom\" a bien été supprimé.");
            }
            return $rs->withRedirect($base."/list/edit?token=$list->token_edit&notif=$notifMsg");
        } else  {
            $notifMsg = urlencode("Vous ne pouvez pas modifier cette liste car vous n'en êtes pas le créateur.");
            return $rs->withRedirect($base."/list?notif=$notifMsg");
        }
    }

    /**
     * Affichage d'un item
     * @param Request $rq requête
     * @param Response $rs réponse
     * @param array $args arguments de la requête
     * @return Response
     */
    public function getItemById(Request $rq, Response $rs, array $args): Response {
        $container = $this->c;
        $base = $rq->getUri()->getBasePath();
        $route_uri = $container->router->pathFor('Item_ID', $args);
        $url = $base . $route_uri;

        $id = $args['id'];
        $token = $rq->getQueryParams('token');
        $item = Item::where('id','=',$id)->first();
        $liste = $item->liste;
        $user = $liste->user;

        $notif = tools::prepareNotif($rq);

        if (!isset($rq->getQueryParams()['token']) || is_null($item) || $liste->token != $token["token"]) {
            $notifMsg = urlencode("L'item demandé est invalide. Vérifiez que le token correspond bien à celui de la liste à laquelle il appartient.");
            return $rs->withRedirect($base."?notif=$notifMsg");
        } else if (isset($_SESSION['username']) && isset($_SESSION['AccessRights']) && $user->username == $_SESSION['username']) {
            if (strtotime($liste->expiration) < strtotime(date("Y-m-d"))) {
                $affichage = ItemController::ITEM_VIEW_OWNER_EXPIRE;
            } else {
                $affichage = ItemController::ITEM_VIEW_OWNER_EN_COURS;
            }
            $v = new VueCreateur([$item], $affichage, $notif, $base);
        } else if ($liste->validee != 1) {
            $notifMsg = urlencode("Cet item n'est pas visible car la liste à laquelle il appartient n'a pas été validée.");
            return $rs->withRedirect($base."?notif=$notifMsg");
        } else {
            $affichage = ItemController::ITEM_VIEW;
            $v = new VueParticipant([$item], $affichage, $notif, $base);
        }
        $rs->getBody()->write($v->render());
        return $rs;
    }

    /**
     * Affichage de la page permettant d'éditer un item
     * @param Request $rq requête
     * @param Response $rs réponse
     * @param array $args arguments de la requête
     * @return Response
     */
    public function editItemPage(Request $rq, Response $rs, array $args): Response {
        $container = $this->c;
        $base = $rq->getUri()->getBasePath();
        $route_uri = $container->router->pathFor('editItemPage', $args);
        $url = $base . $route_uri;

        $id = $args['id'];
        $token = $rq->getQueryParams('token');
        $item = Item::where('id','=',$id)->first();
        $list = $item->liste;
        $user = $list->user;

        if (!isset($rq->getQueryParams()['token']) || is_null($list) || is_null($item) || $list->token_edit != $token["token"]) {
            $notifMsg = urlencode("L'association token de modification / id ne correspond à aucun item.");
            return $rs->withRedirect($base."?notif=$notifMsg");
        } else if (!isset($_SESSION['username']) || !isset($_SESSION['AccessRights']) || $user->username != $_SESSION['username']) {
            $notifMsg = urlencode("Vous ne pouvez pas modifier cet item car vous n'en êtes pas le créateur.");
            return $rs->withRedirect($base."/list?notif=$notifMsg");
        } else {
            if ($item->etat_reserv == 1) {
                $notifMsg = urlencode("Vous ne pouvez pas modifier l'item \"$item->nom\" car quelqu'un l'a réservé.");
                return $rs->withRedirect($base."/item/$item->id/view?token={$item->liste->token}&notif=$notifMsg");
            }
        }

        $notif = tools::prepareNotif($rq);

        $v = new VueCreateur([$item], ItemController::ITEM_EDIT, $notif, $base);
        $rs->getBody()->write($v->render());
        return $rs;
    }

    /**
     * Traitement de la réservation d'un item
     * @param Request $rq requête
     * @param Response $rs réponse
     * @param array $args arguments de la requête
     * @return Response
     */
    public function reservation(Request $rq, Response $rs, array $args): Response {
        $container = $this->c;
        $base = $rq->getUri()->getBasePath();
        $route_uri = $container->router->pathFor('reservation');
        $url = $base . $route_uri;

        $content = $rq->getParsedBody();
        $message = isset($content['message']) ? filter_var($content['message'], FILTER_SANITIZE_STRING): "Aucun message.";
        $item_id = $rq->getQueryParams('id');
        $item = Item::where('id',$item_id)->first();
        $list = $item->liste;

        if ($item->etat_reserv == 1) {
            $notifMsg = urlencode("Cet item a déjà été réservé par quelqu'un.");
        } else if (strtotime($list->expiration) < strtotime(date("Y-m-d"))) {
            $notifMsg = urlencode("Vous ne pouvez pas réserver cet item car la liste a expirée.");
        } else if (isset($_SESSION['username']) && isset($_SESSION['AccessRights'])) {
            $user = Authenticate::where("username", "=", $_SESSION['username'])->first();
            Item::where('id', $item_id)->update(['msg_reserv' => $message]);
            Item::where('id', $item_id)->update(['etat_reserv' => 1]);
            Item::where('id', $item_id)->update(['reserv_par' => $user->id]);

            $item = Item::where('id',$item_id)->first();
            $avecOuSansMsg = $item->msg_reserv == "" ? "sans laisser de message." : "avec le message : $item->msg_reserv";
            $notifMsg = urlencode("Vous avez bien réservé l'item \"$item->nom\" $avecOuSansMsg");
        } else if (!isset($content["pseudo"])) {
            $notifMsg = urlencode("Impossible de réserver l'item, pseudo non valide.");
        } else {
            $pseudo = $content["pseudo"];
            Item::where('id', $item_id)->update(['msg_reserv' => $message]);
            Item::where('id', $item_id)->update(['etat_reserv' => 1]);
            Item::where('id', $item_id)->update(['pseudo' => $pseudo]);

            $item = Item::where('id',$item_id)->first();
            $avecOuSansMsg = $item->msg_reserv == "" ? "sans laisser de message." : "avec le message : $item->msg_reserv";
            $notifMsg = urlencode("Vous avez bien réservé l'item \"$item->nom\" avec le pseudo \"$item->pseudo\" $avecOuSansMsg");
        }
        $item = Item::where('id',$item_id)->first();

        return $rs->withRedirect($base."/item/$item->id/view?token={$item->liste->token}&notif=$notifMsg");
    }
}