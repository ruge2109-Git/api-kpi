<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/api/tienda', function (Request $request, Response $response) {
    $sql = "select * from tienda_streaming";
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

$app->post('/api/tienda/nuevo', function (Request $request, Response $response) {

    $payload = $request->getBody()->__toString();
    $payload =  stripslashes($payload);
    $data = json_decode($payload, true);

    $cod_tienda_streaming = $data["cod_tienda_streaming"];
    $fecha_transaccion = $data["fecha_transaccion"];
    $usuario_cliente = $data["usuario_cliente"];
    $usuario_vendedor = $data["usuario_vendedor"];
    $descripcion = $data["descripcion"];
    $producto = $data["producto"];
    $id_transaccion = $data["id_transaccion"];
    $valor_venta = $data["valor_venta"];
    $saldo = $data["saldo"];
    $saldo_adicional = $data["saldo_adicional"];
    $saldo_total = $data["saldo_total"];

    $sql = "INSERT INTO tienda_streaming (cod_tienda_streaming, fecha_transaccion, usuario_cliente, usuario_vendedor, descripcion, producto, id_transaccion, valor_venta, saldo, saldo_adicional, saldo_total) VALUES (
        :cod_tienda_streaming,
        :fecha_transaccion,
        :usuario_cliente,
        :usuario_vendedor,
        :descripcion,
        :producto,
        :id_transaccion,
        :valor_venta,
        :saldo,
        :saldo_adicional,
        :saldo_total
    )";
    $codProducto = buscarMovimientoPorIdTransaccion($id_transaccion);
    if ($codProducto["bRta"]) {
        $sql = "UPDATE tienda_streaming set
                    fecha_transaccion = :fecha_transaccion,
                    usuario_cliente = :usuario_cliente,
                    usuario_vendedor = :usuario_vendedor,
                    descripcion = :descripcion,
                    producto = :producto,
                    id_transaccion = :id_transaccion,
                    valor_venta = :valor_venta,
                    saldo = :saldo,
                    saldo_adicional = :saldo_adicional,
                    saldo_total = :saldo_total
                WHERE 
                    cod_tienda_streaming = :cod_tienda_streaming
        ";
    }
    try {
        $db = new db();
        $db = $db->connectDB();
        $resultado = $db->prepare($sql);
        $resultado->bindParam(':cod_tienda_streaming', $cod_tienda_streaming);
        $resultado->bindParam(':fecha_transaccion', $fecha_transaccion);
        $resultado->bindParam(':usuario_cliente', $usuario_cliente);
        $resultado->bindParam(':usuario_vendedor', $usuario_vendedor);
        $resultado->bindParam(':descripcion', $descripcion);
        $resultado->bindParam(':producto', $producto);
        $resultado->bindParam(':id_transaccion', $id_transaccion);
        $resultado->bindParam(':valor_venta', $valor_venta);
        $resultado->bindParam(':saldo', $saldo);
        $resultado->bindParam(':saldo_adicional', $saldo_adicional);
        $resultado->bindParam(':saldo_total', $saldo_total);
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

function buscarMovimientoPorIdTransaccion(String $idTransaccion)
{
    $sql = "select * from producto where id_transaccion = $idTransaccion";
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
            "mSmg" => $dataN['cod_tienda_streaming']
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

//Indicadores y consultas de gestión
$app->get('/api/tienda/indicadores', function (Request $request, Response $response) {
    $data = ["bRta" => false];
    $sqlVentaProducto = "select count(*) cantidad, sum(saldo_adicional) total,'Ventas' titulo from tienda_streaming where descripcion= 'Venta de Producto'";
    $sqlComisionVenta = "select count(*) cantidad, sum(saldo_adicional) total,'Comisiones' titulo from tienda_streaming where descripcion= 'Comision venta por'";
    $sqlRecargaDirecta = "select count(*) cantidad, sum(saldo_adicional) total,'Recargas' titulo from tienda_streaming where descripcion= 'Recarga directa por el Administrador'";

    $dataVenta = getSelect($sqlVentaProducto);
    if ($dataVenta['bRta']) {
        $data['data'][] = $dataVenta['data'][0];
    }
    $dataVenta = getSelect($sqlComisionVenta);
    if ($dataVenta['bRta']) {
        $data['data'][] = $dataVenta['data'][0];
    }
    $dataVenta = getSelect($sqlRecargaDirecta);
    if ($dataVenta['bRta']) {
        $data['data'][] = $dataVenta['data'][0];
    }
    if (isset($data['data'])) {
        $data['bRta'] = true;
    }

    echo json_encode($data);
});

$app->get('/api/tienda/recargas', function (Request $request, Response $response) {
    $data = ["bRta" => false];
    $sql = "SELECT 
            ts.cod_tienda_streaming,
            usuario_vendedor administrador, 
            usuario_cliente cliente, 
            saldo_adicional valor_recarga, 
            fecha_transaccion fecha_recarga ,
            (select count(*) from transaccion t where t.cod_tienda_streaming = ts.cod_tienda_streaming) tiene_archivos
        from 
            tienda_streaming ts
        where 
            descripcion= 'Recarga directa por el Administrador' or
            descripcion= 'Descuento de mi saldo por recarga a distribuidor' 
        order by saldo_adicional desc, fecha_recarga desc
    ";
    $dataConsulta = getSelect($sql);
    if ($dataConsulta['bRta']) {
        $data['bRta'] = true;
        $data['data'] = $dataConsulta['data'];
    }
    echo json_encode($data);
});

$app->get('/api/tienda/recargasFiltro/{fechaInicio}/{fechaFin}', function (Request $request, Response $response) {
    $data = ["bRta" => false];
    $fechaInicio = $request->getAttribute('fechaInicio');
    $fechaFin = $request->getAttribute('fechaFin');
    $sql = "SELECT 
            cod_tienda_streaming,
            usuario_vendedor administrador, 
            usuario_cliente cliente, 
            saldo_adicional valor_recarga, 
            fecha_transaccion fecha_recarga 
        from 
            tienda_streaming 
        where 
            descripcion= 'Recarga directa por el Administrador' and
            fecha_transaccion between '$fechaInicio' and '$fechaFin'
        order by saldo_adicional desc, fecha_recarga desc
    ";
    $dataConsulta = getSelect($sql);
    if ($dataConsulta['bRta']) {
        $data['bRta'] = true;
        $data['data'] = $dataConsulta['data'];
    }
    echo json_encode($data);
});

$app->get('/api/tienda/recargasTotalizado', function (Request $request, Response $response) {
    $data = ["bRta" => false];
    $sql = "SELECT 
            fecha_transaccion fecha_recarga ,
            sum(saldo_adicional) valor_recarga
        from 
            tienda_streaming 
        where 
            descripcion= 'Recarga directa por el Administrador'
        group by fecha_transaccion
        order by valor_recarga desc, fecha_recarga desc
    ";
    $dataConsulta = getSelect($sql);
    if ($dataConsulta['bRta']) {
        $data['bRta'] = true;
        $data['data'] = $dataConsulta['data'];
    }
    echo json_encode($data);
});

$app->get('/api/tienda/recargasTotalizadoFiltro/{fechaInicio}/{fechaFin}', function (Request $request, Response $response) {
    $data = ["bRta" => false];
    $fechaInicio = $request->getAttribute('fechaInicio');
    $fechaFin = $request->getAttribute('fechaFin');
    $sql = "SELECT 
            fecha_transaccion fecha_recarga ,
            sum(saldo_adicional) valor_recarga
        from 
            tienda_streaming 
        where 
            descripcion= 'Recarga directa por el Administrador' and
            fecha_transaccion between '$fechaInicio' and '$fechaFin'
        group by 
            fecha_transaccion
        order by 
            valor_recarga desc, 
            fecha_recarga desc;
    ";
    $dataConsulta = getSelect($sql);
    if ($dataConsulta['bRta']) {
        $data['bRta'] = true;
        $data['data'] = $dataConsulta['data'];
    }
    echo json_encode($data);
});

$app->get('/api/tienda/detalleRecarga/{persona}/{fecha}', function (Request $request, Response $response) {

    $persona = $request->getAttribute('persona');
    $fecha = $request->getAttribute('fecha');


    $data = ["bRta" => false];
    $sql = "SELECT 
            ts.producto,
            ts.usuario_vendedor persona,
            recargante.recarga,
            sum(ts.saldo_adicional) total_ventas
        from 
            tienda_streaming ts
            inner join (select descripcion,usuario_vendedor,usuario_cliente,fecha_transaccion,sum(saldo_adicional) recarga from tienda_streaming group by descripcion,usuario_vendedor,usuario_cliente,fecha_transaccion) recargante on(
                recargante.descripcion= 'Recarga directa por el Administrador' and
                recargante.usuario_vendedor= 'robertoadmin7874' and
                recargante.usuario_cliente = ts.usuario_cliente and
                recargante.fecha_transaccion = ts.fecha_transaccion
            )
        where 
            ts.descripcion= 'Venta de Producto' and 
            ts.usuario_cliente = '$persona' and 
            ts.fecha_transaccion ='$fecha'
        group by 
            ts.usuario_vendedor,
            recargante.recarga
    ";
    $dataConsulta = getSelect($sql);
    if ($dataConsulta['bRta']) {
        $data['bRta'] = true;
        $data['data'] = $dataConsulta['data'];
    }
    echo json_encode($data);
});

$app->get('/api/tienda/ventasPersonaProducto/{persona}/{producto}', function (Request $request, Response $response) {
    $persona = base64_decode($request->getAttribute('persona'));
    $producto = base64_decode($request->getAttribute('producto'));


    $data = ["bRta" => false];
    $sql = "SELECT
        ts.usuario_vendedor nombre_persona,
        ts.producto nombre_producto,
        ts.fecha_transaccion,
        count(*) cantidad_ventas,
        ts.saldo_adicional valor_unitario,
        count(*) * ts.saldo_adicional valor_total,
        count(*) * p.costo costo,
        (count(*) * ts.saldo_adicional) - (count(*) * p.costo)  utilidad
    from 
        tienda_streaming ts
        inner join  producto p on (ts.producto = p.nombre)
    where
        ts.descripcion = 'Venta de Producto'
    ";

    if ($persona != 'None') {
        $sql .= " AND ts.usuario_vendedor = '$persona'";
    }
    if ($producto != 'None') {
        $sql .= " AND ts.producto = '$producto'";
    }

    $sql .= " GROUP BY 
        ts.usuario_vendedor ,
        ts.producto ,
        ts.fecha_transaccion
        order by cantidad_ventas desc";

    $dataConsulta = getSelect($sql);
    if ($dataConsulta['bRta']) {
        $data['bRta'] = true;
        $data['data'] = $dataConsulta['data'];
    }

    echo json_encode($data);
});

$app->get('/api/tienda/ventasPersonaProductoFiltro/{persona}/{producto}/{fechaInicio}/{fechaFin}', function (Request $request, Response $response) {
    $persona = base64_decode($request->getAttribute('persona'));
    $producto = base64_decode($request->getAttribute('producto'));
    $fechaInicio = $request->getAttribute('fechaInicio');
    $fechaFin = $request->getAttribute('fechaFin');

    $data = ["bRta" => false];
    $sql = "SELECT
        ts.usuario_vendedor nombre_persona,
        ts.producto nombre_producto,
        ts.fecha_transaccion,
        count(*) cantidad_ventas,
        ts.saldo_adicional valor_unitario,
        count(*) * ts.saldo_adicional valor_total,
        count(*) * p.costo costo,
        (count(*) * ts.saldo_adicional) - (count(*) * p.costo)  utilidad
    from 
        tienda_streaming ts
        inner join  producto p on (ts.producto = p.nombre)
    where
        ts.descripcion = 'Venta de Producto'
    ";

    if ($persona != 'None') {
        $sql .= " AND ts.usuario_vendedor = '$persona' ";
    }
    if ($producto != 'None') {
        $sql .= " AND ts.producto = '$producto' ";
    }

    $sql .= "AND ts.fecha_transaccion between '$fechaInicio' and '$fechaFin' GROUP BY 
        ts.usuario_vendedor ,
        ts.producto ,
        ts.fecha_transaccion
        order by cantidad_ventas desc";

    $dataConsulta = getSelect($sql);
    if ($dataConsulta['bRta']) {
        $data['bRta'] = true;
        $data['data'] = $dataConsulta['data'];
    }

    echo json_encode($data);
});

$app->get('/api/tienda/detalleVenta/{persona}/{producto}', function (Request $request, Response $response) {
    $persona = base64_decode($request->getAttribute('persona'));
    $producto = base64_decode($request->getAttribute('producto'));

    $data = ["bRta" => false];
    $sql = "SELECT
        ts.id_transaccion,
        ts.usuario_vendedor,
        ts.usuario_cliente,
        ts.producto producto,
        ts.fecha_transaccion,
        ts.saldo_adicional,
        p.costo costo_producto,
        ts.saldo_adicional - p.costo utilidad
	
    from 
        tienda_streaming ts
        inner join  producto p on (ts.producto = p.nombre)
    where
        ts.usuario_vendedor = '$persona' and
        ts.producto = '$producto' and
        ts.descripcion = 'Venta de Producto'
    group by 
        ts.id_transaccion";

    $dataConsulta = getSelect($sql);
    if ($dataConsulta['bRta']) {
        $data['bRta'] = true;
        $data['data'] = $dataConsulta['data'];
    }

    echo json_encode($data);
});

$app->get('/api/tienda/detalleVentaFiltro/{persona}/{producto}/{fechaInicio}/{fechaFin}', function (Request $request, Response $response) {
    $persona = base64_decode($request->getAttribute('persona'));
    $producto = base64_decode($request->getAttribute('producto'));
    $fechaInicio = $request->getAttribute('fechaInicio');
    $fechaFin = $request->getAttribute('fechaFin');
    $data = ["bRta" => false];
    $sql = "SELECT
        ts.id_transaccion,
        ts.usuario_vendedor,
        ts.usuario_cliente,
        ts.producto producto,
        ts.fecha_transaccion,
        ts.saldo_adicional,
        p.costo costo_producto,
        ts.saldo_adicional - p.costo utilidad
	
    from 
        tienda_streaming ts
        inner join  producto p on (ts.producto = p.nombre)
    where
        ts.usuario_vendedor = '$persona' and
        ts.producto = '$producto' and
        ts.descripcion = 'Venta de Producto' and
        ts.fecha_transaccion between '$fechaInicio' and '$fechaFin'
    group by 
        ts.id_transaccion";

    $dataConsulta = getSelect($sql);
    if ($dataConsulta['bRta']) {
        $data['bRta'] = true;
        $data['data'] = $dataConsulta['data'];
    }

    echo json_encode($data);
});

$app->get('/api/tienda/ventasProducto', function (Request $request, Response $response) {

    $data = ["bRta" => false];
    $sql = "SELECT 
            producto nombre_producto, 
            count(*) cantidad,
            sum(ts.saldo_adicional) total_venta,
            ts.fecha_transaccion
        from 
            tienda_streaming ts
        where
            ts.descripcion = 'Venta de Producto'
        group by 
            producto,
            ts.fecha_transaccion
        order by cantidad desc";

    $dataConsulta = getSelect($sql);
    if ($dataConsulta['bRta']) {
        $data['bRta'] = true;
        $data['data'] = $dataConsulta['data'];
    }

    echo json_encode($data);
});

$app->get('/api/tienda/ventasProductoFiltro/{fechaInicio}/{fechaFin}', function (Request $request, Response $response) {
    $data = ["bRta" => false];
    $fechaInicio = $request->getAttribute('fechaInicio');
    $fechaFin = $request->getAttribute('fechaFin');
    $sql = "SELECT 
            producto nombre_producto, 
            count(*) cantidad,
            sum(ts.saldo_adicional) total_venta,
            ts.fecha_transaccion
        from 
            tienda_streaming ts
        where
            ts.descripcion = 'Venta de Producto' and
            fecha_transaccion between '$fechaInicio' and '$fechaFin'
        group by 
            producto,
            ts.fecha_transaccion
        order by cantidad desc";

    $dataConsulta = getSelect($sql);
    if ($dataConsulta['bRta']) {
        $data['bRta'] = true;
        $data['data'] = $dataConsulta['data'];
    }

    echo json_encode($data);
});

$app->get('/api/tienda/ventasPersona', function (Request $request, Response $response) {

    $data = ["bRta" => false];
    $sql = "SELECT 
            ts.usuario_vendedor nombre_persona, 
            count(*) cantidad,
            sum(ts.saldo_adicional) total_venta,
            ts.fecha_transaccion
        from 
            tienda_streaming ts
        where
            ts.descripcion = 'Venta de Producto'
        group by 
            ts.usuario_vendedor,
            ts.fecha_transaccion
        order by cantidad desc";

    $dataConsulta = getSelect($sql);
    if ($dataConsulta['bRta']) {
        $data['bRta'] = true;
        $data['data'] = $dataConsulta['data'];
    }

    echo json_encode($data);
});

$app->get('/api/tienda/ventasPersonaFiltro/{fechaInicio}/{fechaFin}', function (Request $request, Response $response) {

    $data = ["bRta" => false];
    $fechaInicio = $request->getAttribute('fechaInicio');
    $fechaFin = $request->getAttribute('fechaFin');
    $sql = "SELECT 
            ts.usuario_vendedor nombre_persona, 
            count(*) cantidad,
            sum(ts.saldo_adicional) total_venta,
            ts.fecha_transaccion
        from 
            tienda_streaming ts
        where
            ts.descripcion = 'Venta de Producto' and
            fecha_transaccion between '$fechaInicio' and '$fechaFin'
        group by 
            ts.usuario_vendedor,
            ts.fecha_transaccion
        order by cantidad desc";

    $dataConsulta = getSelect($sql);
    if ($dataConsulta['bRta']) {
        $data['bRta'] = true;
        $data['data'] = $dataConsulta['data'];
    }

    echo json_encode($data);
});

$app->get('/api/tienda/comisionesPorPersona/{persona}', function (Request $request, Response $response) {
    $persona = base64_decode($request->getAttribute('persona'));
    $data = ["bRta" => false];
    $sql = "SELECT
            dat.usuario_vendedor,
            dat.producto,
            dat.fecha_transaccion,
            dat.comisiones
        from 
            (
                select
                    distinct id_transaccion,
                    ts.usuario_vendedor,
                    producto,
                    fecha_transaccion,
                    max(saldo_adicional) comisiones
                from
                    tienda_streaming ts
                where
                    ts.descripcion = 'Comision venta por' and
                    usuario_vendedor = '$persona'
                group by id_transaccion,producto,fecha_transaccion
            ) dat";
    $dataConsulta = getSelect($sql);
    if ($dataConsulta['bRta']) {
        $data['bRta'] = true;
        $data['data'] = $dataConsulta['data'];
    }

    echo json_encode($data);
});

$app->get('/api/tienda/comisionesPorPersona/{persona}/{fechaInicio}/{fechaFin}', function (Request $request, Response $response) {
    $persona = base64_decode($request->getAttribute('persona'));
    $fechaInicio = $request->getAttribute('fechaInicio');
    $fechaFin = $request->getAttribute('fechaFin');
    $data = ["bRta" => false];
    $sql = "SELECT
            dat.usuario_vendedor,
            dat.producto,
            dat.fecha_transaccion,
            dat.comisiones
        from 
            (
                select
                    distinct id_transaccion,
                    ts.usuario_vendedor,
                    producto,
                    fecha_transaccion,
                    max(saldo_adicional) comisiones
                from
                    tienda_streaming ts
                where
                    ts.descripcion = 'Comision venta por' and
                    usuario_vendedor = '$persona' and
                    fecha_transaccion between '$fechaInicio' and '$fechaFin'
                group by id_transaccion,producto,fecha_transaccion
            ) dat";
    $dataConsulta = getSelect($sql);
    if ($dataConsulta['bRta']) {
        $data['bRta'] = true;
        $data['data'] = $dataConsulta['data'];
    }

    echo json_encode($data);
});

$app->get('/api/tienda/archivosPorRecarga/{codTiendaStreaming}', function (Request $request, Response $response) {
    $codTiendaStreaming = base64_decode($request->getAttribute('codTiendaStreaming'));
    $data = ["bRta" => false];
    $sql = "SELECT 
            *
        from 
            transaccion 
        where 
            cod_tienda_streaming=$codTiendaStreaming";
    $dataConsulta = getSelect($sql);
    if ($dataConsulta['bRta']) {
        $data['bRta'] = true;
        $data['data'] = $dataConsulta['data'];
    }

    echo json_encode($data);
});

$app->get('/api/tienda/archivosPorRecargaFiltro/{codTiendaStreaming}/{fechaInicio}/{fechaFin}', function (Request $request, Response $response) {
    $codTiendaStreaming = base64_decode($request->getAttribute('codTiendaStreaming'));
    $fechaInicio = $request->getAttribute('fechaInicio');
    $fechaFin = $request->getAttribute('fechaFin');
    $data = ["bRta" => false];
    $sql = "SELECT 
                *
            from 
                transaccion 
            where 
                cod_tienda_streaming=$codTiendaStreaming
                and fecha between '$fechaInicio' and '$fechaFin'";
    $dataConsulta = getSelect($sql);
    if ($dataConsulta['bRta']) {
        $data['bRta'] = true;
        $data['data'] = $dataConsulta['data'];
    }

    echo json_encode($data);
});



$app->post('/api/tienda/saveArchivo', function (Request $request, Response $response) {
    $payload = $request->getBody()->__toString();
    $payload =  stripslashes($payload);
    $data = json_decode($payload, true);

    $cod_tienda_streaming = $data["cod_tienda_streaming"];
    $banco = $data["banco"];
    $cuenta = $data["cuenta"];
    $fecha = $data["fecha"];
    $hora = $data["hora"];
    $nombre_archivo = $data["nombre_archivo"];
    $numero_comprobante = $data["numero_comprobante"];
    $valor = $data["valor"];
    $archivo = $data["archivo"];


    $sql = "INSERT INTO transaccion (cod_tienda_streaming, banco, cuenta, valor, fecha, hora, numero_comprobante, archivo, nombre_archivo) VALUES (
        :cod_tienda_streaming,
        :banco,
        :cuenta,
        :valor,
        :fecha,
        :hora,
        :numero_comprobante,
        :archivo,
        :nombre_archivo
    )";
    $codProducto = buscarArchivoPorNumComprobante($numero_comprobante);
    if ($codProducto["bRta"]) {
        $data = [
            "bRta" => false,
            "mSmg" => "Ya existe un archivo con ese número de transacción"
        ];
        echo json_encode($data);
        return;
    }
    try {
        $db = new db();
        $db = $db->connectDB();
        $resultado = $db->prepare($sql);
        $resultado->bindParam(':cod_tienda_streaming', $cod_tienda_streaming);
        $resultado->bindParam(':banco', $banco);
        $resultado->bindParam(':cuenta', $cuenta);
        $resultado->bindParam(':fecha', $fecha);
        $resultado->bindParam(':hora', $hora);
        $resultado->bindParam(':nombre_archivo', $nombre_archivo);
        $resultado->bindParam(':valor', $valor);
        $resultado->bindParam(':numero_comprobante', $numero_comprobante);
        $resultado->bindParam(':archivo', $archivo);
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

$app->delete('/api/tienda/deleteArchivo/{idTransaccion}', function (Request $request, Response $response) {
    $idTransaccion = $request->getAttribute('idTransaccion');
    $data = ["bRta" => false];
    $sql = "DELETE from transaccion where cod_transaccion =:idTransaccion";
    try {
        $db = new db();
        $db = $db->connectDB();
        $resultado = $db->prepare($sql);
        $resultado->bindParam(':idTransaccion', $idTransaccion);
        $resultado->execute();

        $data = [
            "bRta" => true
        ];
        $resultado = null;
        $db = null;
    } catch (PDOException $e) {
        $data =  [
            "bRta" => false,
            "mSmg" => "Error de conexión: " . $e->getMessage()
        ];
        echo json_encode($data);
    }

    

    echo json_encode($data);
});

function getSelect(String $sql)
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

function buscarArchivoPorNumComprobante(String $idTransaccion)
{
    $sql = "select * from transaccion where numero_comprobante = $idTransaccion";
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
            "mSmg" => $dataN['cod_tienda_streaming']
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