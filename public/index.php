<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require __DIR__.'../vendor/autoload.php';
require __DIR__.'../src/config/db.php';

$app = new \Slim\App;

$app->get('/',function(Request $request,Response $response){
    echo "HOLA API";
});
$app->get('/api',function(Request $request,Response $response){
    echo "HOLA API 2";
});
// RUTA PRODUCTOS
// require '../src/rutas/productos.php';
// require '../src/rutas/tienda_online.php';


// $app->add(function ($req, $res, $next) {
//     $response = $next($req, $res);
//     return $response
//         ->withHeader('Access-Control-Allow-Origin', '*')
//         ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
//         ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
// });
$app->run();
