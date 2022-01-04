<?php
/**
 * File:  index.php
 * description: ficindex projet wishlist
 *
 * @author: canals
 */

require_once __DIR__ . '/vendor/autoload.php';

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use wishlist\models\Item;
use wishlist\models\Liste;

require 'vendor/autoload.php';

$app = new \Slim\App(\wishlist\dbInit::init());

$app->get('[/]',
    function (Request $rq, Response $rs, $args):Response {
        $controller = new \wishlist\controllers\HomeController($this);
        return $controller->getHomePage($rq, $rs, $args);
    }
)->setName("home");


$app->get('/formulaireItem[/]',
    function (Request $rq, Response $rs, $args):Response {
        $controller = new \wishlist\controllers\ItemController($this);
        return $controller->createItem($rq, $rs, $args);
    }
)->setName("formulaireItemCreate");


$app->get('/formulaireListe[/]',
    function (Request $rq, Response $rs, $args):Response {
        $controller = new \wishlist\controllers\ListeController($this);
        return $controller->createList($rq, $rs, $args);
    }
)->setName("formulaireListCreate");



//q1 affiche toutes les listes
$app->get('/list[/]',
    function (Request $rq, Response $rs, $args):Response {
        //$rs->getBody()->write("Liste des listes :");
        $controller = new \wishlist\controllers\ListeController($this);
        return $controller->getAllListe($rq, $rs, $args);
    }
)->setName('Listes');




//q2 affiche tous les items
$app->get('/item[/]',
    function (Request $rq, Response $rs, $args):Response {
        $controller = new \wishlist\controllers\ItemController($this);
        return $controller->getAllItem($rq, $rs, $args);
    }
)->setName('Items');

//q3 créé un nouvel item dans une liste donnée
$app->post('/newItem',
    function (Request $rq, Response $rs, $args):Response {
        $controller = new \wishlist\controllers\ItemController($this);
        return $controller->newItem($rq, $rs, $args);
    }
)->setName('New_Item');

//création d'une liste
$app->post('/newListe',
    function (Request $rq, Response $rs, $args):Response {
        $controller = new \wishlist\controllers\ListeController($this);
        return $controller->newListe($rq, $rs, $args);
    }
)->setName('New_Liste');

//TD 10 Q2.2 lister les items d'une liste donnée dont l'id est passé en paramètre.
$app->get('/list/{id}[/]',
    function (Request $rq, Response $rs, $args):Response {
        $controller = new \wishlist\controllers\ListeController($this);
        return $controller->getListById($rq, $rs, $args);
    }
)->setName('listById');

//q4 donne un item avec un id donné
$app->get('/item/{id}[/]',
    function (Request $rq, Response $rs, $args):Response {
        $controller = new \wishlist\controllers\ItemController($this);
        return $controller->getItemById($rq, $rs, $args);
    }
)->setName('Item_ID');

//TD10 Q2.1 indiquer le nom de la liste de souhait dans la liste des items
$app->get('/item/liste/items[/]',
    function (Request $rq, Response $rs,$args):Response{
        $rs->getBody()->write("Liste des items :");
        $rs->getBody()->write("<ol>");
        $items = Item::select()->get();
        foreach ($items as $item){
            $list=$item->liste;
            $rs->getBody()->write("<li><a href='../../item/$item->id'>$item->nom</a> " . ($list == null ? "n'appartient pas à une liste" : "est dans la liste <a href='../../list/$list->no'>$list->titre</a>") . ".</li>");
        }
        $rs->getBody()->write("</ol>");
        return $rs;
    });

try {
    $app->run();
} catch (Throwable $e) {
}
