<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/api/informe_reparto', function (Request $request, Response $response) {
    $sql = "select * from informe_reparto";
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

$app->post('/api/informe_reparto/nuevo', function (Request $request, Response $response) {

    $payload = $request->getBody()->__toString();
    $payload =  stripslashes($payload);
    $data = json_decode($payload, true);

    $consecutivo = $data["consecutivo"];
    $tipo_informe = $data["tipo_informe"];
    $id_cliente = $data["id_cliente"];
    $cliente = $data["cliente"];
    $solicitante = $data["solicitante"];
    $valor_compra = $data["valor_compra"];
    $valor_comision = $data["valor_comision"];
    $valor_acreditado = $data["valor_acreditado"];
    $tipo_movimiento = $data["tipo_movimiento"];
    $fecha_aprobacion = $data["fecha_aprobacion"];
    $hora_aprobacion = $data["hora_aprobacion"];
    $fecha_deposito = $data["fecha_deposito"];
    $hora_deposito = $data["hora_deposito"];
    $num_reparto = $data["num_reparto"];
    $tarjeta_recaudo = $data["tarjeta_recaudo"];
    $banco = $data["banco"];
    $pdc = $data["pdc"];
    $detalle_pdf = $data["detalle_pdf"];
    $cuenta = $data["cuenta"];
    $num_comprobante = $data["num_comprobante"];
    $num_CashOut = $data["num_CashOut"];
    $tipo = $data["tipo"];
    $usuario_responsable = $data["usuario_responsable"];
    $fecha_registro = $data["fecha_registro"];
    $hora_registro = $data["hora_registro"];
    $observacion = $data["observacion"];
    $modo = $data["modo"];

    $sql = "INSERT INTO informe_reparto (consecutivo,tipo_informe,id_cliente,cliente,solicitante,valor_compra,valor_comision,valor_acreditado,tipo_movimiento,fecha_aprobacion,hora_aprobacion,fecha_deposito,hora_deposito,num_reparto,tarjeta_recaudo,banco,pdc,detalle_pdf,cuenta,num_comprobante,num_CashOut,tipo,usuario_responsable,fecha_registro,hora_registro,observacion,modo) VALUES(
        :consecutivo,
        :tipo_informe,
        :id_cliente,
        :cliente,
        :solicitante,
        :valor_compra,
        :valor_comision,
        :valor_acreditado,
        :tipo_movimiento,
        :fecha_aprobacion,
        :hora_aprobacion,
        :fecha_deposito,
        :hora_deposito,
        :num_reparto,
        :tarjeta_recaudo,
        :banco,
        :pdc,
        :detalle_pdf,
        :cuenta,
        :num_comprobante,
        :num_CashOut,
        :tipo,
        :usuario_responsable,
        :fecha_registro,
        :hora_registro,
        :observacion,
        :modo
    )";
    $codlinea = buscarExtractoPorConsecutivo($consecutivo);
    if ($codlinea["bRta"]) {
        $sql = "UPDATE kpi.informe_reparto SET
            tipo_informe = :tipo_informe,
            id_cliente = :id_cliente,
            cliente = :cliente,
            solicitante = :solicitante,
            valor_compra = :valor_compra,
            valor_comision = :valor_comision,
            valor_acreditado = :valor_acreditado,
            tipo_movimiento = :tipo_movimiento,
            fecha_aprobacion = :fecha_aprobacion,
            hora_aprobacion = :hora_aprobacion,
            fecha_deposito = :fecha_deposito,
            hora_deposito = :hora_deposito,
            num_reparto = :num_reparto,
            tarjeta_recaudo = :tarjeta_recaudo,
            banco = :banco,
            pdc = :pdc,
            detalle_pdf = :detalle_pdf,
            cuenta = :cuenta,
            num_comprobante = :num_comprobante,
            num_CashOut = :num_CashOut,
            tipo = :tipo,
            usuario_responsable = :usuario_responsable,
            fecha_registro = :fecha_registro,
            hora_registro = :hora_registro,
            observacion = :observacion,
            modo = :modo
        WHERE 
            consecutivo = :consecutivo";
    }
    try {
        $db = new db();
        $db = $db->connectDB();
        $resultado = $db->prepare($sql);
        $resultado->bindParam(':consecutivo',$consecutivo);
        $resultado->bindParam(':tipo_informe',$tipo_informe);
        $resultado->bindParam(':id_cliente',$id_cliente);
        $resultado->bindParam(':cliente',$cliente);
        $resultado->bindParam(':solicitante',$solicitante);
        $resultado->bindParam(':valor_compra',$valor_compra);
        $resultado->bindParam(':valor_comision',$valor_comision);
        $resultado->bindParam(':valor_acreditado',$valor_acreditado);
        $resultado->bindParam(':tipo_movimiento',$tipo_movimiento);
        $resultado->bindParam(':fecha_aprobacion',$fecha_aprobacion);
        $resultado->bindParam(':hora_aprobacion',$hora_aprobacion);
        $resultado->bindParam(':fecha_deposito',$fecha_deposito);
        $resultado->bindParam(':hora_deposito',$hora_deposito);
        $resultado->bindParam(':num_reparto',$num_reparto);
        $resultado->bindParam(':tarjeta_recaudo',$tarjeta_recaudo);
        $resultado->bindParam(':banco',$banco);
        $resultado->bindParam(':pdc',$pdc);
        $resultado->bindParam(':detalle_pdf',$detalle_pdf);
        $resultado->bindParam(':cuenta',$cuenta);
        $resultado->bindParam(':num_comprobante',$num_comprobante);
        $resultado->bindParam(':num_CashOut',$num_CashOut);
        $resultado->bindParam(':tipo',$tipo);
        $resultado->bindParam(':usuario_responsable',$usuario_responsable);
        $resultado->bindParam(':fecha_registro',$fecha_registro);
        $resultado->bindParam(':hora_registro',$hora_registro);
        $resultado->bindParam(':observacion',$observacion);
        $resultado->bindParam(':modo',$modo);
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

function buscarExtractoPorConsecutivo($consecutivo)
{
    $sql = "SELECT * from informe_reparto where 
        consecutivo = '$consecutivo'   
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
