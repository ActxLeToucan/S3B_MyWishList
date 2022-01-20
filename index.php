<?php
/**
 * File:  index.php
 * description: ficindex projet wishlist
 *
 * @author: canals
 */

session_start();

require_once __DIR__ . '/vendor/autoload.php';

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\App;
use wishlist\controllers\HomeController;
use wishlist\controllers\ItemController;
use wishlist\controllers\ListeController;
use wishlist\controllers\RegisterController;
use wishlist\dbInit;

require 'vendor/autoload.php';

$app = new App(dbInit::init());

/*************************
 * page d'accueil
 *************************/
$app->get('[/]',
    function (Request $rq, Response $rs, $args):Response {
        if (isset($_SESSION['username']) && isset($_SESSION['AccessRights'])) {
            $controller = new ListeController($this);
            return $controller->getHomePage($rq, $rs, $args);

        } else {
            $controller = new RegisterController($this);
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
        $controller = new RegisterController($this);
        return $controller->loginPage($rq, $rs, $args);

    })->setName("login");

// inscription
$app->get('/signUp',
    function (Request $rq, Response $rs, $args):Response {
        $controller = new RegisterController($this);
        return $controller->signUpPage($rq, $rs, $args);

    })->setName("signUp");

// token access
$app->get('/token',
    function (Request $rq, Response $rs, $args):Response {
        $controller = new RegisterController($this);
        return $controller->accessListToken($rq, $rs, $args);

    })->setName("token");

// deconnexion
$app->get('/logout',
    function (Request $rq, Response $rs, $args):Response {
        $controller = new RegisterController($this);
        return $controller->logout($rq, $rs, $args);

    })->setName("logout");


/**
 * reception de donnees
 */

// reception connexion
$app->post('/loginConfirm',
    function (Request $rq, Response $rs, $args):Response {
        $controller = new RegisterController($this);
        return $controller->authentification($rq, $rs, $args);

    })->setName("loginConfirm");

// reception inscription
$app->post('/signupConfirm',
    function (Request $rq, Response $rs, $args):Response {
        $controller = new RegisterController($this);
        return $controller->newUser($rq, $rs, $args);

    })->setName("signupConfirm");




/*************************
 * gestion du compte
 *************************/

/**
 * pages
 */

// mon compte
$app->get('/monCompte',
    function (Request $rq, Response $rs, $args):Response {
        $controller = new RegisterController($this);
        return $controller->monComptePage($rq, $rs, $args);

    })->setName("monCompte");

// gestion mail 
$app->get('/changeMail',
    function (Request $rq, Response $rs, $args):Response {
        $controller = new RegisterController($this);
        return $controller->changeMailPage($rq, $rs, $args);

    })->setName("changeMailPage");

// gestion mot de passe 
$app->get('/changePassword',
    function (Request $rq, Response $rs, $args):Response {
        $controller = new RegisterController($this);
        return $controller->changePswPage($rq, $rs, $args);

    })->setName("changePassword");

// gestion supprimer compte
$app->get('/deleteAccount',
    function (Request $rq, Response $rs, $args):Response {
        $controller = new RegisterController($this);
        return $controller->deleteAccountPage($rq, $rs, $args);

    })->setName("deleteAccountPage");


 /**
 * reception de donnees
 */

// changement de mail 
$app->post('/changeMailConfirm',
    function (Request $rq, Response $rs, $args):Response {
        $controller = new RegisterController($this);
        return $controller->changeMail($rq, $rs, $args);

})->setName("changeMailConfirm");

// changement de mot de passe
$app->post('/changePasswordConfirm',
    function (Request $rq, Response $rs, $args):Response {
        $controller = new RegisterController($this);
        return $controller->changePsw($rq, $rs, $args);

})->setName("changePasswordConfirm");

// reception suppression compte
$app->post('/deleteAccountConfirm',
    function (Request $rq, Response $rs, $args) : Response {
        $controller = new RegisterController($this);
        return $controller->deleteAccount($rq, $rs, $args);
    })->setName('delAccConfirm');




/*************************
 * gestion listes
 *************************/

/**
 * pages
 */

// creation d'une liste
$app->get('/formulaireListe',
    function (Request $rq, Response $rs, $args):Response {
        $controller = new ListeController($this);
        return $controller->createList($rq, $rs, $args);
    })->setName("formulaireListCreate");

// modification liste avec token
$app->get('/list/edit',
    function (Request $rq, Response $rs, $args):Response {
        $controller = new ListeController($this);
        return $controller->editListByToken($rq, $rs, $args);
    })->setName('listByTokenEdit');

// modification item
$app->get('/item/{id}/edit',
    function (Request $rq, Response $rs, $args):Response {
        $controller = new ItemController($this);
        return $controller->editItemPage($rq, $rs, $args);
    })->setName('editItemPage');


/**
 * reception de donnees
 */

// reception creation d'un item vierge
$app->post('/addItem',
    function (Request $rq, Response $rs, $args):Response {
        $controller = new ItemController($this);
        return $controller->addItem($rq, $rs, $args);
    })->setName('itemVierge');

// reception edition item
$app->post('/editItem',
    function (Request $rq, Response $rs, $args):Response {
        $controller = new ItemController($this);
        return $controller->editItem($rq, $rs, $args);
    })->setName('editItem');

// reception suppression item
$app->post('/removeItem',
    function (Request $rq, Response $rs, $args):Response {
        $controller = new ItemController($this);
        return $controller->removeItem($rq, $rs, $args);
    })->setName('supprItem');

// reception creation creation d'une liste
$app->post('/newListe',
    function (Request $rq, Response $rs, $args):Response {
        $controller = new ListeController($this);
        return $controller->newListe($rq, $rs, $args);
    })->setName('New_Liste');

// reception ajout message
$app->post('/addmsg',
    function (Request $rq, Response $rs, $args):Response {
        $controller = new ListeController($this);
        return $controller->addMsg($rq, $rs, $args);
    })->setName('addmsg');

// reception edition liste
$app->post('/editList',
    function (Request $rq, Response $rs, $args) {
        $controller = new ListeController($this);
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
        $controller = new ListeController($this);
        return $controller->getAllListe($rq, $rs, $args);
    })->setName('Listes');

// affichage liste avec token
$app->get('/list/view',
    function (Request $rq, Response $rs, $args):Response {
        $controller = new ListeController($this);
        return $controller->getListByToken($rq, $rs, $args);
    })->setName('listByTokenView');

// affichage item avec id et token
$app->get('/item/{id}/view',
    function (Request $rq, Response $rs, $args):Response {
        $controller = new ItemController($this);
        return $controller->getItemById($rq, $rs, $args);
    })->setName('Item_ID');


/**
 * reception de donnees
 */

// reception reservation d'un item
$app->post('/reservation',
    function (Request $rq, Response $rs, $args):Response {
        $controller = new ItemController($this);
        return $controller->reservation($rq, $rs, $args);
    })->setName('reservation');





try {
    $app->run();
} catch (Throwable $e) {
}
