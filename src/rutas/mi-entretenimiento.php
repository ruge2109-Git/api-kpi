<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/api/miEntretenimiento', function (Request $request, Response $response) {
    $sql = "select * from mientretenimiento_movimientos";
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

$app->post('/api/miEntretenimiento/nuevo', function (Request $request, Response $response) {

    $payload = $request->getBody()->__toString();
    $payload =  stripslashes($payload);
    $data = json_decode($payload, true);

    $id = $data["id"];
    $movimiento = $data["movimiento"];
    $producto = $data["producto"];
    $descripcion = $data["descripcion"];
    $valor = $data["valor"];
    $saldo_anterior = $data["saldo_anterior"];
    $nuevo_saldo = $data["nuevo_saldo"];
    $fecha = $data["fecha"];
    $tipo = $data["tipo"];

    $sql = "INSERT INTO mientretenimiento_movimientos (id, movimiento, producto, descripcion, valor, saldo_anterior, nuevo_saldo, fecha, tipo) VALUES (
        :id,
        :movimiento,
        :producto,
        :descripcion,
        :valor,
        :saldo_anterior,
        :nuevo_saldo,
        :fecha,
        :tipo
    )";
    $codMovimiento = buscarMovimientoPorIdMientretenimiento($id);
    if ($codMovimiento["bRta"]) {
        $sql = "UPDATE mientretenimiento_movimientos set
                    producto = :producto,
                    descripcion = :descripcion,
                    valor = :valor,
                    saldo_anterior = :saldo_anterior,
                    nuevo_saldo = :nuevo_saldo,
                    fecha = :fecha,
                    tipo = :tipo
                WHERE 
                    id = :id
        ";
    }
    try {
        $db = new db();
        $db = $db->connectDB();
        $resultado = $db->prepare($sql);
        $resultado->bindParam(':id', $id);
        $resultado->bindParam(':movimiento', $movimiento);
        $resultado->bindParam(':producto', $producto);
        $resultado->bindParam(':descripcion', $descripcion);
        $resultado->bindParam(':valor', $valor);
        $resultado->bindParam(':producto', $producto);
        $resultado->bindParam(':saldo_anterior', $saldo_anterior);
        $resultado->bindParam(':nuevo_saldo', $nuevo_saldo);
        $resultado->bindParam(':fecha', $fecha);
        $resultado->bindParam(':tipo', $tipo);
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
            "mSmg" => "Error de conexión: " . $e->getMessage()
        ];
        echo json_encode($data);
    }
});

$app->get('/api/miEntretenimientoRespuesta', function (Request $request, Response $response) {
    $sql = "select * from mientretenimiento_respuestas";
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

$app->post('/api/miEntretenimientoRespuesta/nuevo', function (Request $request, Response $response) {

    $payload = $request->getBody()->__toString();
    $payload =  stripslashes($payload);
    $data = json_decode($payload, true);

    $id = $data["id"];
    $cuenta_afectada = $data["cuenta_afectada"];
    $password = base64_decode($data["password"]);
    $resuelto = $data["resuelto"];
    $fecha_respuesta = $data["fecha_respuesta"];
    $respuesta = base64_decode($data["respuesta"]);

    $sql = "INSERT INTO mientretenimiento_respuestas (id, cuenta_afectada, password, resuelto, fecha_respuesta, respuesta) VALUES (
        :id,
        :cuenta_afectada,
        :password,
        :resuelto,
        :fecha_respuesta,
        :respuesta
    )";
    $codMovimiento = buscarRespuestaPorIdMientretenimiento($id);
    if ($codMovimiento["bRta"]) {
        $sql = "UPDATE mientretenimiento_respuestas set
                    cuenta_afectada = :cuenta_afectada,
                    password = :password,
                    resuelto = :resuelto,
                    fecha_respuesta = :fecha_respuesta,
                    respuesta = :respuesta
                WHERE 
                    id = :id
        ";
    }
    try {
        $db = new db();
        $db = $db->connectDB();
        $resultado = $db->prepare($sql);
        $resultado->bindParam(':id', $id);
        $resultado->bindParam(':cuenta_afectada', $cuenta_afectada);
        $resultado->bindParam(':password', $password);
        $resultado->bindParam(':resuelto', $resuelto);
        $resultado->bindParam(':fecha_respuesta', $fecha_respuesta);
        $resultado->bindParam(':respuesta', $respuesta);
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
            "mSmg" => "Error de conexión: " . $e->getMessage()
        ];
        echo json_encode($data);
    }
});

function buscarMovimientoPorIdMientretenimiento(String $idTransaccion)
{
    $sql = "select * from mientretenimiento_movimientos where id = $idTransaccion";
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
            "mSmg" => $dataN['cod_movimientos']
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

function buscarRespuestaPorIdMientretenimiento(String $idTransaccion)
{
    $sql = "select * from mientretenimiento_respuestas where id = $idTransaccion";
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
            "mSmg" => $dataN['cod_respuesta']
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

$app->get('/api/miEntretenimiento/indicadores', function (Request $request, Response $response) {

    $data = ["bRta" => false];
    $sql = "SELECT 
            DATE(fecha) fecha,
            sum(case movimiento when 'Venta' then valor else 0 end ) ventas,
            sum(case movimiento when 'Cambio cuenta' then valor else 0 end )cambio_cuenta,
            sum(case movimiento when 'Comisión por venta' then valor else 0 end )comision_venta,
            sum(case movimiento when 'Compra de saldo' then valor else 0 end )compra_saldo,
            sum(case movimiento when 'Comisión por renovación' then valor else 0 end ) comision_renovacion,
            sum(case movimiento when 'Renovación' then valor else 0 end )renovacion,
            sum(case movimiento when 'Devolución renovación' then valor else 0 end ) devolucion_renovacion,
            sum(case movimiento when 'Recepción de saldo' then valor else 0 end ) recepcion_saldo
        from 
            mientretenimiento_movimientos
        group by DATE(fecha)
        order by fecha desc";

    $dataConsulta = getSelect($sql);
    if ($dataConsulta['bRta']) {
        $data['bRta'] = true;
        $data['data'] = $dataConsulta['data'];
    }

    echo json_encode($data);
});

$app->get('/api/miEntretenimiento/indicadoresFecha/{fechaInicio}/{fechaFin}', function (Request $request, Response $response) {
    $fechaInicio = $request->getAttribute('fechaInicio');
    $fechaFin = $request->getAttribute('fechaFin');
    $data = ["bRta" => false];
    $sql = "SELECT 
            DATE(fecha) fecha,
            sum(case movimiento when 'Venta' then valor else 0 end ) ventas,
            sum(case movimiento when 'Cambio cuenta' then valor else 0 end )cambio_cuenta,
            sum(case movimiento when 'Comisión por venta' then valor else 0 end )comision_venta,
            sum(case movimiento when 'Compra de saldo' then valor else 0 end )compra_saldo,
            sum(case movimiento when 'Comisión por renovación' then valor else 0 end ) comision_renovacion,
            sum(case movimiento when 'Renovación' then valor else 0 end )renovacion,
            sum(case movimiento when 'Devolución renovación' then valor else 0 end ) devolucion_renovacion,
            sum(case movimiento when 'Recepción de saldo' then valor else 0 end ) recepcion_saldo
        from 
            mientretenimiento_movimientos
        where
	        fecha between '$fechaInicio 00:00:00' and '$fechaFin 23:59:59'
        group by DATE(fecha)
        order by fecha desc";

    $dataConsulta = getSelect($sql);
    if ($dataConsulta['bRta']) {
        $data['bRta'] = true;
        $data['data'] = $dataConsulta['data'];
    }

    echo json_encode($data);
});


function getSelectMiEntretenimiento(String $sql)
{
    try {
        $db = new db();
        $db = $db->connectDB();
        $resultado = $db->query($sql);
        if ($resultado->rowCount() == 0) {
            $resultado = null;
            $db = null;
            return ["bRta" => false];
        }
        $dataDB = $resultado->fetchAll(PDO::FETCH_OBJ);

        $data = ["bRta" => true, "data" => $dataDB];

        $resultado = null;
        $db = null;
        return $data;
    } catch (PDOException $e) {
        return  ["bRta" => false, "mSmg" => "Error de conexión"];
    }
}
