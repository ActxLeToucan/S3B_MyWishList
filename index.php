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
use Illuminate\Database\Capsule\Manager as DB;
use wishlist\models\Item;
use wishlist\models\Liste;

require 'vendor/autoload.php';

$tabFile = parse_ini_file("src\conf\conf.init.dist");

$db = new DB();

$db->addConnection( [
    'driver' => $tabFile[ 'driver'],
    'host' => $tabFile[ 'host'],
    'database' => $tabFile[ 'database'],
    'username' => $tabFile[ 'username'],
    'password' => $tabFile[ 'password'],
    'charset' => $tabFile[ 'charset'],
    'collation' => $tabFile[ 'collation'],
    'prefix' => ''
] );

$db->setAsGlobal();
$db->bootEloquent();

$configuration = [
    'settings' => [
        'displayErrorDetails' => true,
        'dbconf' => '/conf/db.conf.ini' ]
];
$c = new \Slim\Container($configuration);


$app = new \Slim\App($c);


function RandomString()
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randstring = '';
    for ($i = 0; $i < 10; $i++) {
        $randstring = $randstring.$characters[Rand(0, strlen($characters))];
    }
    return $randstring;
}



$app->get('[/]',
    function (Request $rq, Response $rs, $args):Response {
        $file =  "HTML/index.html";
        return $rs->write(file_get_contents($file));
    }
)->setName("home");



$app->get('/formulaireItem[/]',
    function (Request $rq, Response $rs, $args):Response {
        $file =  "HTML/FormItem.html";
        return $rs->write(file_get_contents($file));
    }
)->setName("formulaireItem");


$app->get('/formulaireListe[/]',
    function (Request $rq, Response $rs, $args):Response {
        $file =  "HTML/FormListe.html";
        return $rs->write(file_get_contents($file));
    }
)->setName("formulaireItem");



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
        $id = $args['id'];
        $rs->getBody()->write("Item " . $id . " :");
        $res = Liste::where('no', '=', $id)
            ->first();

        $titre = $res->titre;

        $rs->getBody()->write("item dans la liste avec l'id " . $id . " est : " . $titre);
        return $rs;
    }
)->setName('Item_ID');

//TD10 Q2.1 indiquer le nom de la liste de souhait dans la liste des items
$app->get('/item/liste/items[/]',
    function (Request $rq, Response $rs,$args):Response{
        $rs->getBody()->write("Liste des items :");
        $rs->getBody()->write("<ol>");
        $res = Item::select()->get();
        foreach ($res as $value){
            $listetest=$value->liste;
            //$rs->getBody()->write("On test pour voir si c'est null:".$value->liste .'oui');
            if($listetest==null){
                $rs->getBody()->write("<li>" . $value->nom . ', n\'appartient pas à une liste' );
            }else {
                $rs->getBody()->write("<li>" . $value->nom . ', Et qui est dans la liste ' . $listetest->titre . "</li>");
            }
        }
        $rs->getBody()->write("</ol>");
        return $rs;
    });

try {
    $app->run();
} catch (Throwable $e) {
}
