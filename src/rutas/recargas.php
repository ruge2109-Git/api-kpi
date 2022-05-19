<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/api/sales_report', function (Request $request, Response $response) {
    $sql = "select * from sales_report";
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

$app->post('/api/sales_report/nuevo', function (Request $request, Response $response) {

    $payload = $request->getBody()->__toString();
    $payload =  stripslashes($payload);
    $data = json_decode($payload, true);

    $id_transaccion = $data["id_transaccion"];
    $id_cliente = $data["id_cliente"];
    $cliente = $data["cliente"];
    $canal = $data["canal"];
    $operador = $data["operador"];
    $linea = $data["linea"];
    $id_convenio = $data["id_convenio"];
    $nombre_convenio = $data["nombre_convenio"];
    $comision = $data["comision"];
    $fecha = $data["fecha"];
    $hora = $data["hora"];
    $valor = $data["valor"];
    $saldo_final = $data["saldo_final"];
    $bolsa = $data["bolsa"];
    $estado = $data["estado"];
    $usuario = $data["usuario"];
    $creado_por = $data["creado_por"];

    $sql = "INSERT INTO kpi.sales_report (id_transaccion, id_cliente, cliente, canal, operador, linea, id_convenio, nombre_convenio, comision, fecha, hora, valor, saldo_final, bolsa, estado, usuario, creado_por) VALUES(
        :id_transaccion,
        :id_cliente,
        :cliente,
        :canal,
        :operador,
        :linea,
        :id_convenio,
        :nombre_convenio,
        :comision,
        :fecha,
        :hora,
        :valor,
        :saldo_final,
        :bolsa,
        :estado,
        :usuario,
        :creado_por
    )";
    $codlinea = buscarRecargaPorIdTransaccion($id_transaccion,$id_cliente,$canal,$operador,$fecha,$hora,$valor,$saldo_final,$estado);
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
        $resultado->bindParam(':id_transaccion', $id_transaccion);
        $resultado->bindParam(':id_cliente', $id_cliente);
        $resultado->bindParam(':cliente', $cliente);
        $resultado->bindParam(':canal', $canal);
        $resultado->bindParam(':operador', $operador);
        $resultado->bindParam(':linea', $linea);
        $resultado->bindParam(':id_convenio', $id_convenio);
        $resultado->bindParam(':nombre_convenio', $nombre_convenio);
        $resultado->bindParam(':comision', $comision);
        $resultado->bindParam(':fecha', $fecha);
        $resultado->bindParam(':hora', $hora);
        $resultado->bindParam(':valor', $valor);
        $resultado->bindParam(':saldo_final', $saldo_final);
        $resultado->bindParam(':bolsa', $bolsa);
        $resultado->bindParam(':estado', $estado);
        $resultado->bindParam(':usuario', $usuario);
        $resultado->bindParam(':creado_por', $creado_por);
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

function buscarRecargaPorIdTransaccion($id_transaccion,$id_cliente,$canal,$operador,$fecha,$hora,$valor,$saldo_final,$estado)
{
    $sql = "SELECT * from sales_report where 
        id_transaccion = '$id_transaccion' and  
        id_cliente = '$id_cliente' and
        canal = '$canal' and
        operador = '$operador' and
        fecha = '$fecha' and
        hora = '$hora' and
        valor = '$valor' and
        saldo_final = '$saldo_final' and
        estado = '$estado'    
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
            "mSmg" => $dataN['cod_sales_report']
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

//Reportes

$app->get('/api/sales_report/indicadores', function (Request $request, Response $response) {
    $data = ["bRta" => false];
    $sql = "SELECT 
            fecha,
            sum(valor) total,
            avg(valor) promedio
        from 
            sales_report
        group by  
            fecha";

    $dataConsulta = getSelectSR($sql);
    if ($dataConsulta['bRta']) {
        $data['bRta'] = true;
        $data['data'] = $dataConsulta['data'];
    }
    echo json_encode($data);
});

$app->get('/api/sales_report/indicadoresFecha/{fechaInicio}/{fechaFin}', function (Request $request, Response $response) {
    $data = ["bRta" => false];
    $fechaInicio = $request->getAttribute('fechaInicio');
    $fechaFin = $request->getAttribute('fechaFin');
    $sql = "SELECT 
            fecha,
            sum(valor) total,
            avg(valor) promedio
        from 
            sales_report
        where
            fecha between '$fechaInicio' and '$fechaFin' 
        group by  
            fecha";

    $dataConsulta = getSelectSR($sql);
    if ($dataConsulta['bRta']) {
        $data['bRta'] = true;
        $data['data'] = $dataConsulta['data'];
    }
    echo json_encode($data);
});

$app->get('/api/sales_report/clientesCanal', function (Request $request, Response $response) {
    $data = ["bRta" => false];
    $sql = "SELECT
            id_cliente,
            cliente,
            canal,
            fecha,
            count(*) cantidad,
            sum(valor) valor
        from
            sales_report
        group by 
            id_cliente,
            cliente,
            canal,
            fecha
        order by
            count(*) desc";

    $dataConsulta = getSelectSR($sql);
    if ($dataConsulta['bRta']) {
        $data['bRta'] = true;
        $data['data'] = $dataConsulta['data'];
    }
    echo json_encode($data);
});

$app->get('/api/sales_report/clientesCanal/fechaFiltro/{fechaInicio}/{fechaFin}', function (Request $request, Response $response) {
    $data = ["bRta" => false];
    $fechaInicio = $request->getAttribute('fechaInicio');
    $fechaFin = $request->getAttribute('fechaFin');
    $sql = "SELECT
            id_cliente,
            cliente,
            canal,
            fecha,
            count(*) cantidad,
            sum(valor) valor
        from
            sales_report
        where
            fecha between '$fechaInicio' and '$fechaFin' 
        group by 
            id_cliente,
            cliente,
            canal,
            fecha
        order by
            count(*) desc";

    $dataConsulta = getSelectSR($sql);
    if ($dataConsulta['bRta']) {
        $data['bRta'] = true;
        $data['data'] = $dataConsulta['data'];
    }
    echo json_encode($data);
});

$app->get('/api/sales_report/clientesOperador', function (Request $request, Response $response) {
    $data = ["bRta" => false];
    $sql = "SELECT
            id_cliente,
            cliente,
            operador,
            fecha,
            count(*) cantidad,
            sum(valor) valor
        from
            sales_report
        group by 
            id_cliente,
            cliente,
            operador,
            fecha
        order by
            count(*) desc";

    $dataConsulta = getSelectSR($sql);
    if ($dataConsulta['bRta']) {
        $data['bRta'] = true;
        $data['data'] = $dataConsulta['data'];
    }
    echo json_encode($data);
});

$app->get('/api/sales_report/clientesOperador/fechaFiltro/{fechaInicio}/{fechaFin}', function (Request $request, Response $response) {
    $data = ["bRta" => false];
    $fechaInicio = $request->getAttribute('fechaInicio');
    $fechaFin = $request->getAttribute('fechaFin');
    $sql = "SELECT
            id_cliente,
            cliente,
            operador,
            fecha,
            count(*) cantidad,
            sum(valor) valor
        from
            sales_report
        where
            fecha between '$fechaInicio' and '$fechaFin' 
        group by 
            id_cliente,
            cliente,
            operador,
            fecha
        order by
            count(*) desc";

    $dataConsulta = getSelectSR($sql);
    if ($dataConsulta['bRta']) {
        $data['bRta'] = true;
        $data['data'] = $dataConsulta['data'];
    }
    echo json_encode($data);
});

$app->get('/api/sales_report/detalleCliente/{cliente}', function (Request $request, Response $response) {
    $data = ["bRta" => false];
    $cliente = base64_decode($request->getAttribute('cliente'));
    $sql = "SELECT
                *
        from
            sales_report
        where
        id_cliente = '$cliente'";

    $dataConsulta = getSelectSR($sql);
    if ($dataConsulta['bRta']) {
        $data['bRta'] = true;
        $data['data'] = $dataConsulta['data'];
    }
    echo json_encode($data);
});

$app->get('/api/sales_report/detalleCanal/{canal}', function (Request $request, Response $response) {
    $data = ["bRta" => false];
    $canal = base64_decode($request->getAttribute('canal'));
    $sql = "SELECT
                *
        from
            sales_report
        where
        canal = '$canal'";

    $dataConsulta = getSelectSR($sql);
    if ($dataConsulta['bRta']) {
        $data['bRta'] = true;
        $data['data'] = $dataConsulta['data'];
    }
    echo json_encode($data);
});

$app->get('/api/sales_report/detalleOperador/{operador}', function (Request $request, Response $response) {
    $data = ["bRta" => false];
    $operador = base64_decode($request->getAttribute('operador'));
    $sql = "SELECT
                *
        from
            sales_report
        where
        operador = '$operador'";

    $dataConsulta = getSelectSR($sql);
    if ($dataConsulta['bRta']) {
        $data['bRta'] = true;
        $data['data'] = $dataConsulta['data'];
    }
    echo json_encode($data);
});

$app->get('/api/sales_report/detalleCliente/{cliente}/{fechaInicio}/{fechaFin}', function (Request $request, Response $response) {
    $data = ["bRta" => false];
    $cliente = base64_decode($request->getAttribute('cliente'));
    $fechaInicio = $request->getAttribute('fechaInicio');
    $fechaFin = $request->getAttribute('fechaFin');
    $sql = "SELECT
                *
        from
            sales_report
        where
        id_cliente = '$cliente' and
        fecha between '$fechaInicio' and '$fechaFin' ";

    $dataConsulta = getSelectSR($sql);
    if ($dataConsulta['bRta']) {
        $data['bRta'] = true;
        $data['data'] = $dataConsulta['data'];
    }
    echo json_encode($data);
});

$app->get('/api/sales_report/topCanal', function (Request $request, Response $response) {
    $data = ["bRta" => false];
    $sql = "SELECT canal,fecha,count(*) cantidad,sum(valor) valor from sales_report group by canal,fecha order by count(*) desc";

    $dataConsulta = getSelectSR($sql);
    if ($dataConsulta['bRta']) {
        $data['bRta'] = true;
        $data['data'] = $dataConsulta['data'];
    }
    echo json_encode($data);
});

$app->get('/api/sales_report/topCanalFechas/{fechaInicio}/{fechaFin}', function (Request $request, Response $response) {
    $data = ["bRta" => false];
    $fechaInicio = $request->getAttribute('fechaInicio');
    $fechaFin = $request->getAttribute('fechaFin');
    $sql = "SELECT canal,fecha,count(*) cantidad,sum(valor) valor from sales_report where fecha between '$fechaInicio' and '$fechaFin' group by canal,fecha order by count(*) desc";

    $dataConsulta = getSelectSR($sql);
    if ($dataConsulta['bRta']) {
        $data['bRta'] = true;
        $data['data'] = $dataConsulta['data'];
    }
    echo json_encode($data);
});

$app->get('/api/sales_report/topOperador', function (Request $request, Response $response) {
    $data = ["bRta" => false];
    $sql = "SELECT operador,fecha,count(*) cantidad,sum(valor) valor from sales_report group by operador,fecha order by count(*) desc";

    $dataConsulta = getSelectSR($sql);
    if ($dataConsulta['bRta']) {
        $data['bRta'] = true;
        $data['data'] = $dataConsulta['data'];
    }
    echo json_encode($data);
});

$app->get('/api/sales_report/topOperadorFechas/{fechaInicio}/{fechaFin}', function (Request $request, Response $response) {
    $data = ["bRta" => false];
    $fechaInicio = $request->getAttribute('fechaInicio');
    $fechaFin = $request->getAttribute('fechaFin');
    $sql = "SELECT operador,fecha,count(*) cantidad,sum(valor) valor from sales_report where fecha between '$fechaInicio' and '$fechaFin' group by operador,fecha order by count(*) desc";
    $dataConsulta = getSelectSR($sql);
    if ($dataConsulta['bRta']) {
        $data['bRta'] = true;
        $data['data'] = $dataConsulta['data'];
    }
    echo json_encode($data);
});

$app->get('/api/sales_report/topCliente', function (Request $request, Response $response) {
    $data = ["bRta" => false];
    $sql = "SELECT id_cliente,cliente,fecha,count(*) cantidad,sum(valor) valor from sales_report group by id_cliente,cliente,fecha order by count(*) desc";

    $dataConsulta = getSelectSR($sql);
    if ($dataConsulta['bRta']) {
        $data['bRta'] = true;
        $data['data'] = $dataConsulta['data'];
    }
    echo json_encode($data);
});

$app->get('/api/sales_report/topClienteFechas/{fechaInicio}/{fechaFin}', function (Request $request, Response $response) {
    $data = ["bRta" => false];
    $fechaInicio = $request->getAttribute('fechaInicio');
    $fechaFin = $request->getAttribute('fechaFin');
    $sql = "SELECT id_cliente,cliente,fecha,count(*) cantidad,sum(valor) valor from sales_report where fecha between '$fechaInicio' and '$fechaFin' group by canal,fecha order by count(*) desc";

    $dataConsulta = getSelectSR($sql);
    if ($dataConsulta['bRta']) {
        $data['bRta'] = true;
        $data['data'] = $dataConsulta['data'];
    }
    echo json_encode($data);
});

$app->get('/api/sales_report/topComision', function (Request $request, Response $response) {
    $data = ["bRta" => false];
    $sql = "SELECT * from sales_report order by comision desc";

    $dataConsulta = getSelectSR($sql);
    if ($dataConsulta['bRta']) {
        $data['bRta'] = true;
        $data['data'] = $dataConsulta['data'];
    }
    echo json_encode($data);
});

$app->get('/api/sales_report/topComisionFechas/{fechaInicio}/{fechaFin}', function (Request $request, Response $response) {
    $data = ["bRta" => false];
    $fechaInicio = $request->getAttribute('fechaInicio');
    $fechaFin = $request->getAttribute('fechaFin');
    $sql = "SELECT * from sales_report where fecha between '$fechaInicio' and '$fechaFin' order by comision desc";

    $dataConsulta = getSelectSR($sql);
    if ($dataConsulta['bRta']) {
        $data['bRta'] = true;
        $data['data'] = $dataConsulta['data'];
    }
    echo json_encode($data);
});


function getSelectSR(String $sql)
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
