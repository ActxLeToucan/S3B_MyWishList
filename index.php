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

//q1
$app->get('/list[/]',
    function (Request $rq, Response $rs, $args):Response {
        $rs->getBody()->write("Liste des listes :");
        $rs->getBody()->write("<ol>");
        $res = Liste::select('titre')->get();
        foreach ($res as $value){
            $rs->getBody()->write("<li>".$value->titre."</li>");
        }
        $rs->getBody()->write("</ol>");
        return $rs;
    }
);
//q2
$app->get('/item[/]',
    function (Request $rq, Response $rs, $args):Response {
        $rs->getBody()->write("Liste des items :");
        $rs->getBody()->write("<ol>");
        $res = Item::select('nom')->get();
        foreach ($res as $value){
            $rs->getBody()->write("<li>".$value->nom."</li>");
        }
        $rs->getBody()->write("</ol>");
        return $rs;
    }
);

//q3
$app->get('/list/{id}/new',
    function (Request $rq, Response $rs, $args):Response {
        $nomItem = $_GET['nomItem'];
        $id = $args['id'];
        $nomListe = Liste::where('no','=',$id)->first();
        $rs->getBody()->write("Création d'un nouvel item dans la liste \"".$nomListe->titre."\" avec le nom \"".$nomItem."\"");
        $nomListe->insert;
        return $rs;
    }
);

/*$app->get('/list/{id}[/]',
    function (Request $rq, Response $rs, $args):Response {
        $id = $args['id'];
        $rs->getBody()->write("Liste des items de la liste " . $id . " :");
        $rs->getBody()->write("<ol>");
        $res = Item::select('nom')->where('liste.id', '=', $id)->get();
        foreach ($res as $value){
            $rs->getBody()->write("<li>".$value->nom."</li>");
        }
        $rs->getBody()->write("</ol>");
        return $rs;
    }
);*/

//q4
$app->get('/item/{id}[/]',
    function (Request $rq, Response $rs, $args):Response {
        $id = $args['id'];
        $rs->getBody()->write("Item " . $id . " :");
        $res = Liste::where( 'no', '=', $id )
            ->first() ;

        $titre = $res->titre;

        $rs->getBody()->write("item dans la liste avec l'id ".$id. " est : ".$titre);
        return $rs;
    }
);

$app->run();

try {
    $app->run();
} catch (Throwable $e) {
}
