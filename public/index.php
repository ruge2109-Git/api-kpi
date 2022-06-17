<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require '../vendor/autoload.php';
require '../src/config/db.php';

$app = new \Slim\App;

$app->get('/',function(Request $request,Response $response){
    echo "HOLA API";
});

// RUTA PRODUCTOS
require '../src/rutas/productos.php';
require '../src/rutas/tienda_online.php';
require '../src/rutas/recargas.php';
require '../src/rutas/informe_repartos.php';
require '../src/rutas/extracto_cta_ahorro.php';
require '../src/rutas/extracto_recarga.php';
require '../src/rutas/mi-entretenimiento.php';
require '../src/rutas/recargas_jv.php';


$app->add(function ($req, $res, $next) {
    $response = $next($req, $res);
    return $response
        ->withHeader('Access-Control-Allow-Origin', '*')
        ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
});
$app->run();
