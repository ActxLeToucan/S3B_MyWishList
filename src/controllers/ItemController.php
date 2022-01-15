<?php

namespace wishlist\controllers;

use wishlist\models\Authenticate;
use wishlist\models\Item;
use wishlist\tools;
use wishlist\vues\VueCreateur;
use wishlist\vues\VueParticipant;

class ItemController {
    const ITEMS_VIEW = 'items';
    const ITEM_VIEW = 'item';
    const ITEM_VIEW_OWNER_EN_COURS = 'item_view_owner_en_cours';
    const ITEM_VIEW_OWNER_EXPIRE = 'item_view_owner_expirée';
    const ITEM_FORM_CREATE = 'form_item_create';

    private $c;

    /**
     * @param $c
     */
    public function __construct($c) {
        $this->c = $c;
    }

    public function newItem($rq, $rs, $args) {
        $container = $this->c;
        $base = $rq->getUri()->getBasePath();
        $route_uri = $container->router->pathFor('New_Item');
        $url = $base . $route_uri;

        $content = $rq->getParsedBody();

        $extension = $_FILES['photo']['type'];
        $cheminServeur = $_FILES['photo']['tmp_name'];
        $fileName = str_replace('image/',time()."_".tools::getRandomString().'.',$extension);
        $uploadfile = './img/'.$fileName;

        move_uploaded_file($cheminServeur, $uploadfile);

        $nomItem = filter_var($content['nom'], FILTER_SANITIZE_STRING);
        $listeId = filter_var($content['liste_id'], FILTER_SANITIZE_STRING);
        $descr = filter_var($content['descr'], FILTER_SANITIZE_STRING);

        $url = filter_var($content['url'], FILTER_SANITIZE_STRING);
        $tarif = filter_var($content['tarif'], FILTER_SANITIZE_NUMBER_FLOAT);

        $newItem = new Item();
        $newItem->nom = $nomItem;
        $newItem->liste_id = $listeId;
        $newItem->descr = $descr;
        $newItem->img = $fileName;
        $newItem->url = $url;
        $newItem->tarif = $tarif;
        $newItem->save();

        $notif = urlencode("L'item $newItem->nom a été créé et ajouté dans la liste {$newItem->liste->titre}.");
        return $rs->withRedirect($base."/item/$newItem->id/view?token={$newItem->liste->token}&notif=$notif");
    }

    public function getItemById($rq, $rs, $args) {
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

        if (!isset($rq->getQueryParams()['token']) || is_null($item) || $liste->token != $token["token"] || $liste->validee != 1) {
            $notifMsg = urlencode("L'item demandé est invalide. Vérifiez que le token correspond bien à celui de la liste à laquelle il appartient, et que la liste a été validée par le créateur.");
            return $rs->withRedirect($base."?notif=$notifMsg");
        } else if (isset($_SESSION['username']) && isset($_SESSION['AccessRights']) && $user->username == $_SESSION['username']) {
            if (strtotime($liste->expiration) < strtotime(date("Y-m-d"))) {
                $affichage = ItemController::ITEM_VIEW_OWNER_EXPIRE;
            } else {
                $affichage = ItemController::ITEM_VIEW_OWNER_EN_COURS;
            }
            $v = new VueCreateur([$item], $affichage, $notif);
        } else  {
            $affichage = ItemController::ITEM_VIEW;
            $v = new VueParticipant([$item], $affichage, $notif);
        }

        $rs->getBody()->write($v->render());
        return $rs;
    }

    public function createItem($rq, $rs, $args) {
        $container = $this->c;
        $base = $rq->getUri()->getBasePath();
        $route_uri = $container->router->pathFor('formulaireItemCreate');
        $url = $base . $route_uri;

        $notif = tools::prepareNotif($rq);

        $v = new VueCreateur([], ItemController::ITEM_FORM_CREATE, $notif);
        $rs->getBody()->write($v->render());
        return $rs;
    }

    public function reservation($rq, $rs, $args) {
        $container = $this->c;
        $base = $rq->getUri()->getBasePath();
        $route_uri = $container->router->pathFor('reservation');
        $url = $base . $route_uri;

        $content = $rq->getParsedBody();
        $message = isset($content['message']) ? filter_var($content['message'], FILTER_SANITIZE_STRING) : "Aucun message.";
        $item_id = $rq->getQueryParams('id');

        if (isset($_SESSION['username']) && isset($_SESSION['AccessRights'])) {
            $user = Authenticate::where("username", "=", $_SESSION['username'])->first();
            Item::where('id', $item_id)->update(['msg_reserv' => $message]);
            Item::where('id', $item_id)->update(['etat_reserv' => 1]);
            Item::where('id', $item_id)->update(['reserv_par' => $user->id]);

            $item = Item::where('id',$item_id)->first();
            $avecOuSansMsg = $item->msg_reserv == "" ? "sans laisser de message." : "avec le message : $item->msg_reserv";
            $notif = urlencode("Vous avez bien réservé l'item \"$item->nom\" $avecOuSansMsg");
        } else if (isset($content["pseudo"])) {
            $pseudo = $content["pseudo"];
            Item::where('id', $item_id)->update(['msg_reserv' => $message]);
            Item::where('id', $item_id)->update(['etat_reserv' => 1]);
            Item::where('id', $item_id)->update(['pseudo' => $pseudo]);

            $item = Item::where('id',$item_id)->first();
            $avecOuSansMsg = $item->msg_reserv == "" ? "sans laisser de message." : "avec le message : $item->msg_reserv";
            $notif = urlencode("Vous avez bien réservé l'item \"$item->nom\" avec le pseudo \"$item->pseudo\" $avecOuSansMsg");
        } else {
            $notif = urlencode("Impossible de réserver l'item.");
        }
        $item = Item::where('id',$item_id)->first();

        return $rs->withRedirect($base."/item/$item->id/view?token={$item->liste->token}&notif=$notif");
    }
}