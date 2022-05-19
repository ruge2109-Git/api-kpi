<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/api/extractoAhorro', function (Request $request, Response $response) {
    $sql = "select * from extracto_ahorro";
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

$app->post('/api/extractoAhorro/nuevo', function (Request $request, Response $response) {

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

    $sql = "INSERT INTO kpi.extracto_ahorro (fecha,hora,descripcion,servicio,saldo_anterior,valor,saldo_final,usuario,cliente_responsable,cliente_afectado) VALUES(
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

function buscarExtractoPorTodoConcepto($fecha, $hora, $descripcion, $servicio, $saldo_anterior, $valor, $saldo_final, $usuario, $cliente_responsable, $cliente_afectado)
{
    $sql = "SELECT * from extracto_ahorro where 
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

$app->get('/api/extractoAhorro/indicadores', function (Request $request, Response $response) {
    $data = ["bRta" => false];
    $sql = "SELECT 
        ext.fecha,
        saldo_inicial.saldo_anterior,
        entradas,
        salidas,
        abs(salidas)-abs(entradas) diferencia,
        saldo_inicial.saldo_anterior + entradas + salidas saldo_final
    from
        extracto_ahorro ext
        inner join (select min(hora),fecha,saldo_anterior,usuario from extracto_ahorro group by fecha) saldo_inicial on (saldo_inicial.fecha = ext.fecha)
        inner join (select fecha,sum(case when descripcion = 'Reversa ingreso' then 0  else valor end) entradas from extracto_ahorro where valor >=0 group by fecha ) entradas on (entradas.fecha = ext.fecha)
        inner join (select fecha,sum(abs(valor) * (-1)) salidas from extracto_ahorro where (valor <0 or descripcion = 'Reversa ingreso') group by fecha) salidas on (salidas.fecha = ext.fecha)
    group by 
        ext.fecha,
        saldo_inicial.saldo_anterior,
        entradas,
        salidas";

    $dataConsulta = getSelectEXCT($sql);
    if ($dataConsulta['bRta']) {
        $data['bRta'] = true;
        $data['data'] = $dataConsulta['data'];
    }
    echo json_encode($data);
});

$app->get('/api/extractoAhorro/indicadoresFecha/{fechaInicio}/{fechaFin}', function (Request $request, Response $response) {
    $data = ["bRta" => false];
    $fechaInicio = $request->getAttribute('fechaInicio');
    $fechaFin = $request->getAttribute('fechaFin');
    $sql = "SELECT 
            ext.fecha,
            saldo_inicial.saldo_anterior,
            entradas,
            salidas,
            abs(salidas)-abs(entradas) diferencia,
            saldo_inicial.saldo_anterior + entradas + salidas saldo_final
        from
            extracto_ahorro ext
            inner join (select min(hora),fecha,saldo_anterior,usuario from extracto_ahorro group by fecha) saldo_inicial on (saldo_inicial.fecha = ext.fecha)
            inner join (select fecha,sum(case when descripcion = 'Reversa ingreso' then 0  else valor end) entradas from extracto_ahorro where valor >=0 group by fecha ) entradas on (entradas.fecha = ext.fecha)
            inner join (select fecha,sum(abs(valor) * (-1)) salidas from extracto_ahorro where (valor <0 or descripcion = 'Reversa ingreso') group by fecha) salidas on (salidas.fecha = ext.fecha)
        where
            ext.fecha between '$fechaInicio' and '$fechaFin'
        group by 
            ext.fecha,
            saldo_inicial.saldo_anterior,
            entradas,
            salidas";

    $dataConsulta = getSelectEXCT($sql);
    if ($dataConsulta['bRta']) {
        $data['bRta'] = true;
        $data['data'] = $dataConsulta['data'];
    }
    echo json_encode($data);
});

$app->get('/api/extractoAhorro/filtroFecha/{fechaInicio}/{fechaFin}', function (Request $request, Response $response) {
    $data = ["bRta" => false];
    $fechaInicio = $request->getAttribute('fechaInicio');
    $fechaFin = $request->getAttribute('fechaFin');
    $sql = "SELECT * from extracto_ahorro where fecha between '$fechaInicio' and '$fechaFin'";

    $dataConsulta = getSelectEXCT($sql);
    if ($dataConsulta['bRta']) {
        $data['bRta'] = true;
        $data['data'] = $dataConsulta['data'];
    }
    echo json_encode($data);
});

$app->get('/api/extractoAhorro/totalIngresos/{fechaInicio}/{fechaFin}', function (Request $request, Response $response) {
    $data = ["bRta" => false];
    $fechaInicio = $request->getAttribute('fechaInicio');
    $fechaFin = $request->getAttribute('fechaFin');
    $sql = "SELECT * from extracto_ahorro where valor >=0 and fecha between '$fechaInicio' and '$fechaFin' and descripcion <> 'Reversa ingreso'";

    $dataConsulta = getSelectEXCT($sql);
    if ($dataConsulta['bRta']) {
        $data['bRta'] = true;
        $data['data'] = $dataConsulta['data'];
    }
    echo json_encode($data);
});

$app->get('/api/extractoAhorro/totalEgresos/{fechaInicio}/{fechaFin}', function (Request $request, Response $response) {
    $data = ["bRta" => false];
    $fechaInicio = $request->getAttribute('fechaInicio');
    $fechaFin = $request->getAttribute('fechaFin');
    $sql = "SELECT cod_extracto_ahorro,fecha,hora,descripcion,servicio,saldo_anterior,abs(valor) * (-1) valor, saldo_final, usuario,cliente_responsable,cliente_afectado 
    from extracto_ahorro where (valor <0 or descripcion = 'Reversa ingreso') and fecha between '$fechaInicio' and '$fechaFin'";

    $dataConsulta = getSelectEXCT($sql);
    if ($dataConsulta['bRta']) {
        $data['bRta'] = true;
        $data['data'] = $dataConsulta['data'];
    }
    echo json_encode($data);
});

$app->get('/api/extractoAhorro/sinComisiones/{fechaInicio}/{fechaFin}', function (Request $request, Response $response) {
    $data = ["bRta" => false];
    $fechaInicio = $request->getAttribute('fechaInicio');
    $fechaFin = $request->getAttribute('fechaFin');
    $sql = "SELECT * from extracto_ahorro where valor =0 and fecha between '$fechaInicio' and '$fechaFin'";

    $dataConsulta = getSelectEXCT($sql);
    if ($dataConsulta['bRta']) {
        $data['bRta'] = true;
        $data['data'] = $dataConsulta['data'];
    }
    echo json_encode($data);
});


function getSelectEXCT(String $sql)
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