<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/api/extractoRecarga', function (Request $request, Response $response) {
    $sql = "select * from extracto_recarga";
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

$app->get('/api/extractoRecargaPorFecha/{fechaInicio}/{fechaFin}', function (Request $request, Response $response) {
    $fechaInicio = $request->getAttribute('fechaInicio');
    $fechaFin = $request->getAttribute('fechaFin');
    $sql = "SELECT * from extracto_recarga where fecha between '$fechaInicio' and '$fechaFin'";
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

$app->post('/api/extractoRecarga/nuevo', function (Request $request, Response $response) {

    $payload = $request->getBody()->__toString();
    $payload =  stripslashes($payload);
    $data = json_decode($payload, true);

    $fecha = $data["fecha"];
    $hora = $data["hora"];
    $descripcion = $data["descripcion"];
    $servicio = $data["servicio"];
    $saldo_anterior = $data["saldo_anterior"];
    $valor = $data["valor"];
    $saldo_final = $data["saldo_final"];
    $usuario = $data["usuario"];
    $cliente_responsable = $data["cliente_responsable"];
    $cliente_afectado = $data["cliente_afectado"];

    $sql = "INSERT INTO kpi.extracto_recarga (fecha,hora,descripcion,servicio,saldo_anterior,valor,saldo_final,usuario,cliente_responsable,cliente_afectado) VALUES(
        :fecha,
        :hora,
        :descripcion,
        :servicio,
        :saldo_anterior,
        :valor,
        :saldo_final,
        :usuario,
        :cliente_responsable,
        :cliente_afectado
    )";
    $codlinea = buscarExtractoPorTodoConcepto($fecha, $hora, $descripcion, $servicio, $saldo_anterior, $valor, $saldo_final, $usuario, $cliente_responsable, $cliente_afectado);
    if ($codlinea["bRta"]) {
        $data = [
            "bRta" => false
        ];
        echo json_encode($data);
        return;
    }
    try {
        $db = new db();
        $db = $db->connectDB();
        $resultado = $db->prepare($sql);
        $resultado->bindParam(':fecha', $fecha);
        $resultado->bindParam(':hora', $hora);
        $resultado->bindParam(':descripcion', $descripcion);
        $resultado->bindParam(':servicio', $servicio);
        $resultado->bindParam(':saldo_anterior', $saldo_anterior);
        $resultado->bindParam(':valor', $valor);
        $resultado->bindParam(':saldo_final', $saldo_final);
        $resultado->bindParam(':usuario', $usuario);
        $resultado->bindParam(':cliente_responsable', $cliente_responsable);
        $resultado->bindParam(':cliente_afectado', $cliente_afectado);
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

function buscarExtractoRecargaPorTodoConcepto($fecha, $hora, $descripcion, $servicio, $saldo_anterior, $valor, $saldo_final, $usuario, $cliente_responsable, $cliente_afectado)
{
    $sql = "SELECT * from extracto_recarga where 
        fecha = $fecha and
        hora = $hora and
        descripcion = $descripcion and
        servicio = $servicio and
        saldo_anterior = $saldo_anterior and
        valor = $valor and
        saldo_final = $saldo_final and
        usuario = $usuario and
        cliente_responsable = $cliente_responsable and
        cliente_afectado = $cliente_afectado
    ";
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
            "mSmg" => $dataN['cod_informe_reparto']
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

$app->get('/api/extractoRecarga/indicadoresGenerales', function (Request $request, Response $response) {
    $data = ["bRta" => false];
    $sql = "select
				ext.fecha,
				sum(ext.valor) valor_compra,
				sum(repartos.valor) valor_reparto,
				round(abs(sum(ext.valor)) - abs(sum(repartos.valor)),2) comision
			from
				extracto_recarga ext
				inner join (select ext2.fecha,ext2.hora,ext2.valor from extracto_recarga ext2 where ext2.descripcion ='Reparto') repartos on (
					repartos.fecha = ext.fecha and
					repartos.hora = ext.hora
				)
			where
	ext.descripcion = 'Compra'
        group by ext.fecha";

    $dataConsulta = getSelectEXCT($sql);
    if ($dataConsulta['bRta']) {
        $data['bRta'] = true;
        $data['data'] = $dataConsulta['data'];
    }
    echo json_encode($data);
});

$app->get('/api/extractoRecarga/indicadoresGeneralesFecha/{fechaInicio}/{fechaFin}', function (Request $request, Response $response) {
    $data = ["bRta" => false];
    $fechaInicio = $request->getAttribute('fechaInicio');
    $fechaFin = $request->getAttribute('fechaFin');
    $sql = "select
				ext.fecha,
				sum(ext.valor) valor_compra,
				sum(repartos.valor) valor_reparto,
				round(abs(sum(ext.valor)) - abs(sum(repartos.valor)),2) comision
			from
				extracto_recarga ext
				inner join (select ext2.fecha,ext2.hora,ext2.valor from extracto_recarga ext2 where ext2.descripcion ='Reparto') repartos on (
					repartos.fecha = ext.fecha and
					repartos.hora = ext.hora
				)
			where
				ext.descripcion = 'Compra' and
            ext.fecha between '$fechaInicio' and '$fechaFin'
        group by ext.fecha";

    $dataConsulta = getSelectEXCT($sql);
    if ($dataConsulta['bRta']) {
        $data['bRta'] = true;
        $data['data'] = $dataConsulta['data'];
    }
    echo json_encode($data);
});

$app->get('/api/extractoRecarga/indicadores', function (Request $request, Response $response) {
    $data = ["bRta" => false];
    $sql = "SELECT 
                ext.fecha,
                ext.cliente_afectado cliente_compra,
                repartos.cliente_afectado cliente_reparto,
                ext.valor valor_compra,
                repartos.valor valor_reparto,
                abs(ext.valor) - abs(repartos.valor) comision
            from
                (select * from extracto_recarga where descripcion='Compra') ext
                inner join (select * from extracto_recarga where descripcion='Reparto') repartos on (
                    repartos.hora = ext.hora
                )";

    $dataConsulta = getSelectEXCT($sql);
    if ($dataConsulta['bRta']) {
        $data['bRta'] = true;
        $data['data'] = $dataConsulta['data'];
    }
    echo json_encode($data);
});

$app->get('/api/extractoRecarga/indicadoresFecha/{fechaInicio}/{fechaFin}', function (Request $request, Response $response) {
    $data = ["bRta" => false];
    $fechaInicio = $request->getAttribute('fechaInicio');
    $fechaFin = $request->getAttribute('fechaFin');
    $sql = "SELECT 
                ext.fecha,
                ext.cliente_afectado cliente_compra,
                repartos.cliente_afectado cliente_reparto,
                ext.valor valor_compra,
                repartos.valor valor_reparto,
                abs(ext.valor) - abs(repartos.valor) comision
            from
                (select * from extracto_recarga where descripcion='Compra') ext
                inner join (select * from extracto_recarga where descripcion='Reparto') repartos on (
                    repartos.hora = ext.hora
                )
            where
                ext.fecha between '$fechaInicio' and '$fechaFin'";

    $dataConsulta = getSelectEXCT($sql);
    if ($dataConsulta['bRta']) {
        $data['bRta'] = true;
        $data['data'] = $dataConsulta['data'];
    }
    echo json_encode($data);
});
