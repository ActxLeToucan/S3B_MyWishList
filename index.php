<?php
/**
 * File:  index.php
 * description: ficindex projet wishlist
 *
 * @author: canals
 */

session_start();

require_once __DIR__ . '/vendor/autoload.php';

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require 'vendor/autoload.php';

$app = new \Slim\App(\wishlist\dbInit::init());

$app->get('[/]',
    function (Request $rq, Response $rs, $args):Response {
        $controller = new \wishlist\controllers\HomeController($this);
        return $controller->getHomePage($rq, $rs, $args);
    }
)->setName("home");

$app->post('/loginConfirm[/]',
    function (Request $rq, Response $rs, $args):Response {
        $controller = new \wishlist\controllers\RegisterController($this);
        $auth=$controller->LoadUser($rq,$rs,$args);
        if(isset($_SESSION['username'])){
            session_destroy();
            session_start();
            $_SESSION['username']=$auth['username'];
            $_SESSION['AccessRights']=$auth['Niveau_acces'];
            echo ('session existe : '.json_encode($_SESSION));
        }else{
            $_SESSION['username']=$auth['username'];
            $_SESSION['AccessRights']=$auth['Niveau_acces'];
            echo ('session nouvelle : '.json_encode($_SESSION));
        }
        return $controller->authentification($rq, $rs, $args);

    })->setName("loginConfirm");

$app->get('/login[/]',
    function (Request $rq, Response $rs, $args):Response {
        $controller = new \wishlist\controllers\RegisterController($this);
        return $controller->loginPage($rq, $rs, $args);

    })->setName("login");

$app->get('/signUp[/]',
    function (Request $rq, Response $rs, $args):Response {
        $controller = new \wishlist\controllers\RegisterController($this);
        return $controller->signUpPage($rq, $rs, $args);

    })->setName("signUp");

$app->post('/signupConfirrm[/]',
    function (Request $rq, Response $rs, $args):Response {
        $controller = new \wishlist\controllers\RegisterController($this);
        return $controller->newUser($rq, $rs, $args);

    })->setName("signupConfirm");

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

/*$app->get('/list/{token}[/]',
    function (Request $rq, Response $rs, $args):Response {
        $controller = new \wishlist\controllers\ListeController($this);
        return $controller->getListByToken($rq, $rs, $args);
    }
)->setName('listByToken');*/

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

$app->post('/reservation[/]',
    function (Request $rq, Response $rs, $args):Response {
        $controller = new \wishlist\controllers\ItemController($this);
        return $controller->reservation($rq, $rs, $args);
    }
)->setName('reservation');

try {
    $app->run();
} catch (Throwable $e) {
}
