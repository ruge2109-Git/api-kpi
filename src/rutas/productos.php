<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

// $app = new \Slim\App;

$app->get('/api/productos', function (Request $request, Response $response) {
    $sql = "select * from producto";
    try {
        $db = new db();
        $db = $db->connectDB();
        $resultado = $db->query($sql);
        if ($resultado->rowCount() == 0) {
            $data = ["bRta" => false];
            echo json_encode($data);
            $resultado = null;
            $db = null;
            return;
        }
        $data = [
            "bRta" => true,
            "data" => $resultado->fetchAll(PDO::FETCH_OBJ)
        ];
        $resultado = null;
        $db = null;
        echo json_encode($data);
    } catch (PDOException $e) {
        $data =  [
            "bRta" => false,
            "mSmg" => "Error de conexión"
        ];
        echo json_encode($data);
    }
});

$app->post('/api/productos/nuevo', function (Request $request, Response $response) {

    $payload = $request->getBody()->__toString();
    $payload =  stripslashes($payload);
    $data = json_decode($payload, true);

    $nombre = $data["nombre"];
    $valor = $data["costo_unitario"];

    $sql = "INSERT INTO producto(nombre,costo) VALUES (:nombre,:costo_unitario)";
    $codProducto = buscarProductoPorNombre($nombre);
    if ($codProducto["bRta"]) {
        $sql = "update producto set nombre=:nombre, costo =:costo_unitario where cod_producto = ".$codProducto["mSmg"];
    }
    try {
        $db = new db();
        $db = $db->connectDB();
        $resultado = $db->prepare($sql);
        $resultado->bindParam(':nombre', $nombre);
        $resultado->bindParam(':costo_unitario', $valor);
        $resultado->execute();

        $data = [
            "bRta" => true
        ];
        $resultado = null;
        $db = null;
        echo json_encode($data);
    } catch (PDOException $e) {
        $data =  [
            "bRta" => false,
            "mSmg" => "Error de conexión: ".$e->getMessage()
        ];
        echo json_encode($data);
    }
});

function buscarProductoPorNombre(String $nombre)
{
    $sql = "select * from producto where nombre = '$nombre'";
    try {
        $db = new db();
        $db = $db->connectDB();
        $resultado = $db->query($sql);
        if ($resultado->rowCount() <= 0) {
            $data =  [
                "bRta" => false
            ];
            return $data;
        }
        $dataR = $resultado->fetchAll(PDO::FETCH_OBJ);
        $dataN = json_decode(json_encode($dataR[0]), true);
        $data =  [
            "bRta" => true,
            "mSmg" => $dataN['cod_producto']
        ];
        $resultado = null;
        $db = null;
        return $data;
    } catch (PDOException $e) {
        $data =  [
            "bRta" => false,
            "mSmg" => $e->getMessage()
        ];
        return $data;
    }
}
