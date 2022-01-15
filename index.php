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
        if (isset($_SESSION['username']) && isset($_SESSION['AccessRights'])) {
            $controller = new \wishlist\controllers\HomeController($this);
            return $controller->getHomePage($rq, $rs, $args);
        } else {
            $controller = new \wishlist\controllers\RegisterController($this);
            return $controller->loginPage($rq, $rs, $args);
        }
    })->setName("home");




/*************************
 * connexion
 *************************/

/**
 * pages
 */

// connexion
$app->get('/login',
    function (Request $rq, Response $rs, $args):Response {
        $controller = new \wishlist\controllers\RegisterController($this);
        return $controller->loginPage($rq, $rs, $args);

    })->setName("login");

// inscription
$app->get('/signUp',
    function (Request $rq, Response $rs, $args):Response {
        $controller = new \wishlist\controllers\RegisterController($this);
        return $controller->signUpPage($rq, $rs, $args);

    })->setName("signUp");

// deconnexion
$app->get('/logout',
    function (Request $rq, Response $rs, $args):Response {
        $controller = new \wishlist\controllers\RegisterController($this);
        return $controller->logout($rq, $rs, $args);

    })->setName("logout");


/**
 * reception de donnees
 */

// reception connexion
$app->post('/loginConfirm',
    function (Request $rq, Response $rs, $args):Response {
        $controller = new \wishlist\controllers\RegisterController($this);
        return $controller->authentification($rq, $rs, $args);

    })->setName("loginConfirm");

// reception inscription
$app->post('/signupConfirm',
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
$app->get('/formulaireItem',
    function (Request $rq, Response $rs, $args):Response {
        $controller = new \wishlist\controllers\ItemController($this);
        return $controller->createItem($rq, $rs, $args);
    })->setName("formulaireItemCreate");

// creation d'une liste
$app->get('/formulaireListe',
    function (Request $rq, Response $rs, $args):Response {
        $controller = new \wishlist\controllers\ListeController($this);
        return $controller->createList($rq, $rs, $args);
    })->setName("formulaireListCreate");

// modification liste avec token
$app->get('/list/edit',
    function (Request $rq, Response $rs, $args):Response {
        $controller = new \wishlist\controllers\ListeController($this);
        return $controller->editListByToken($rq, $rs, $args);
    })->setName('listByTokenEdit');

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

// reception ajout message
$app->post('/addmsg',
    function (Request $rq, Response $rs, $args):Response {
        $controller = new \wishlist\controllers\ListeController($this);
        return $controller->addMsg($rq, $rs, $args);
    })->setName('addmsg');

// reception edition liste
$app->post('/editList',
    function (Request $rq, Response $rs, $args) {
        $controller = new \wishlist\controllers\ListeController($this);
        return $controller->editList($rq, $rs, $args);
    })->setName('editList');



/*************************
 * consultation listes
 *************************/

/**
 * pages
 */

// affichage de mes listes
$app->get('/list',
    function (Request $rq, Response $rs, $args):Response {
        //$rs->getBody()->write("Liste des listes :");
        $controller = new \wishlist\controllers\ListeController($this);
        return $controller->getAllListe($rq, $rs, $args);
    })->setName('Listes');

// affichage liste avec token
$app->get('/list/view',
    function (Request $rq, Response $rs, $args):Response {
        $controller = new \wishlist\controllers\ListeController($this);
        return $controller->getListByToken($rq, $rs, $args);
    })->setName('listByTokenView');

// affichage item avec id et token
$app->get('/item/{id}/view',
    function (Request $rq, Response $rs, $args):Response {
        $controller = new \wishlist\controllers\ItemController($this);
        return $controller->getItemById($rq, $rs, $args);
    })->setName('Item_ID');

/**
 * reception de donnees
 */

// reception reservation d'un item
$app->post('/reservation',
    function (Request $rq, Response $rs, $args):Response {
        $controller = new \wishlist\controllers\ItemController($this);
        return $controller->reservation($rq, $rs, $args);
    })->setName('reservation');







//q2 affiche tous les items
$app->get('/item',
    function (Request $rq, Response $rs, $args):Response {
        $controller = new \wishlist\controllers\ItemController($this);
        return $controller->getAllItem($rq, $rs, $args);
    })->setName('Items');



try {
    $app->run();
} catch (Throwable $e) {
}
