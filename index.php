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

/*************************
 * page d'accueil
 *************************/
$app->get('[/]',
    function (Request $rq, Response $rs, $args):Response {
        $controller = new \wishlist\controllers\HomeController($this);
        return $controller->getHomePage($rq, $rs, $args);
    })->setName("home");




/*************************
 * connexion
 *************************/

/**
 * pages
 */

// connexion
$app->get('/login[/]',
    function (Request $rq, Response $rs, $args):Response {
        $controller = new \wishlist\controllers\RegisterController($this);
        return $controller->loginPage($rq, $rs, $args);

    })->setName("login");

// inscription
$app->get('/signUp[/]',
    function (Request $rq, Response $rs, $args):Response {
        $controller = new \wishlist\controllers\RegisterController($this);
        return $controller->signUpPage($rq, $rs, $args);

    })->setName("signUp");

// deconnexion
$app->get('/logout[/]',
    function (Request $rq, Response $rs, $args):Response {
        $controller = new \wishlist\controllers\RegisterController($this);
        return $controller->logout($rq, $rs, $args);

    })->setName("logout");


/**
 * reception de donnees
 */

// reception connexion
$app->post('/loginConfirm[/]',
    function (Request $rq, Response $rs, $args):Response {
        $controller = new \wishlist\controllers\RegisterController($this);
        return $controller->authentification($rq, $rs, $args);

    })->setName("loginConfirm");

// reception inscription
$app->post('/signupConfirm[/]',
    function (Request $rq, Response $rs, $args):Response {
        $controller = new \wishlist\controllers\RegisterController($this);
        return $controller->newUser($rq, $rs, $args);

    })->setName("signupConfirm");




/*************************
 * gestion listes
 *************************/

/**
 * pages
 */

// creation d'un item
$app->get('/formulaireItem[/]',
    function (Request $rq, Response $rs, $args):Response {
        $controller = new \wishlist\controllers\ItemController($this);
        return $controller->createItem($rq, $rs, $args);
    })->setName("formulaireItemCreate");

// creation d'une liste
$app->get('/formulaireListe[/]',
    function (Request $rq, Response $rs, $args):Response {
        $controller = new \wishlist\controllers\ListeController($this);
        return $controller->createList($rq, $rs, $args);
    })->setName("formulaireListCreate");

/**
 * reception de donnees
 */

// reception creation d'un item
$app->post('/newItem',
    function (Request $rq, Response $rs, $args):Response {
        $controller = new \wishlist\controllers\ItemController($this);
        return $controller->newItem($rq, $rs, $args);
    })->setName('New_Item');

// reception creation creation d'une liste
$app->post('/newListe',
    function (Request $rq, Response $rs, $args):Response {
        $controller = new \wishlist\controllers\ListeController($this);
        return $controller->newListe($rq, $rs, $args);
    })->setName('New_Liste');




/*************************
 * consultation listes
 *************************/

/**
 * pages
 */

// affichage liste avec id
$app->get('/list/{id}[/]',
    function (Request $rq, Response $rs, $args):Response {
        $controller = new \wishlist\controllers\ListeController($this);
        return $controller->getListById($rq, $rs, $args);
    })->setName('listById');

// affichage liste avec token
/*$app->get('/list/{token}[/]',
    function (Request $rq, Response $rs, $args):Response {
        $controller = new \wishlist\controllers\ListeController($this);
        return $controller->getListByToken($rq, $rs, $args);
    })->setName('listByToken');*/

// affichage item avec id
$app->get('/item/{id}[/]',
    function (Request $rq, Response $rs, $args):Response {
        $controller = new \wishlist\controllers\ItemController($this);
        return $controller->getItemById($rq, $rs, $args);
    })->setName('Item_ID');

/**
 * reception de donnees
 */

// reception reservation d'un item
$app->post('/reservation[/]',
    function (Request $rq, Response $rs, $args):Response {
        $controller = new \wishlist\controllers\ItemController($this);
        return $controller->reservation($rq, $rs, $args);
    })->setName('reservation');




//q1 affiche toutes les listes
$app->get('/list[/]',
    function (Request $rq, Response $rs, $args):Response {
        //$rs->getBody()->write("Liste des listes :");
        $controller = new \wishlist\controllers\ListeController($this);
        return $controller->getAllListe($rq, $rs, $args);
    })->setName('Listes');




//q2 affiche tous les items
$app->get('/item[/]',
    function (Request $rq, Response $rs, $args):Response {
        $controller = new \wishlist\controllers\ItemController($this);
        return $controller->getAllItem($rq, $rs, $args);
    })->setName('Items');


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
