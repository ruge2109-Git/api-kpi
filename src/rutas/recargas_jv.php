<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/api/recargasJV/ganancias', function (Request $request, Response $response) {
    $sql = "select * from jv_consumido_ganancias";
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

$app->get('/api/recargasJV/gananciasFecha/{fechaInicio}/{fechaFin}', function (Request $request, Response $response) {
    $fechaInicio = $request->getAttribute('fechaInicio');
    $fechaFin = $request->getAttribute('fechaFin');
    $data = ["bRta" => false];
    $sql = "SELECT * from jv_consumido_saldo where fecha_cargue between '$fechaInicio' and '$fechaFin'";

    $dataConsulta = getSelect($sql);
    if ($dataConsulta['bRta']) {
        $data['bRta'] = true;
        $data['data'] = $dataConsulta['data'];
    }

    echo json_encode($data);
});

$app->post('/api/recargasJV/nuevaGanancia', function (Request $request, Response $response) {

    $payload = $request->getBody()->__toString();
    $payload =  stripslashes($payload);
    $data = json_decode($payload, true);

    $fecha_cargue = $data["fecha_cargue"];
    $nombre = $data["nombre"];
    $codigo_cliente = $data["codigo_cliente"];
    $cc = $data["cc"];
    $perfil = $data["perfil"];
    $davivienda_pago_tarjeta_de_credito = $data["davivienda_pago_tarjeta_de_credito"];
    $davivienda_pago_de_credito = $data["davivienda_pago_de_credito"];
    $davivienda_deposito_davivienda = $data["davivienda_deposito_davivienda"];
    $davivienda_retiro_davivienda = $data["davivienda_retiro_davivienda"];
    $aval_tarjeta_de_credito_ban_bogota = $data["aval_tarjeta_de_credito_ban_bogota"];
    $aval_cred_rotativo_crediservices_dinero_extra_ban_bogota = $data["aval_cred_rotativo_crediservices_dinero_extra_ban_bogota"];
    $aval_credito_hipotecario_ban_bogota = $data["aval_credito_hipotecario_ban_bogota"];
    $aval_otros_creditos_ban_bogota = $data["aval_otros_creditos_ban_bogota"];
    $aval_credito_motos_vehiculos_ban_bogota = $data["aval_credito_motos_vehiculos_ban_bogota"];
    $xbox_plata = $data["xbox_plata"];
    $imvu = $data["imvu"];
    $keo = $data["keo"];
    $sisteCredito = $data["sisteCredito"];
    $claro = $data["claro"];
    $paquetes_claro = $data["paquetes_claro"];
    $sura = $data["sura"];
    $movistar = $data["movistar"];
    $paquetes_movistar = $data["paquetes_movistar"];
    $tigo = $data["tigo"];
    $paquetes_tigo = $data["paquetes_tigo"];
    $avantel = $data["avantel"];
    $paquetes_avantel = $data["paquetes_avantel"];
    $virgin = $data["virgin"];
    $paquetes_virgin = $data["paquetes_virgin"];
    $etb = $data["etb"];
    $paquete_etb = $data["paquete_etb"];
    $exito = $data["exito"];
    $paquetes_exito = $data["paquetes_exito"];
    $paquetes_conectame = $data["paquetes_conectame"];
    $flashMobile = $data["flashMobile"];
    $direcTv = $data["direcTv"];
    $wom = $data["wom"];
    $paquetes_wom = $data["paquetes_wom"];
    $kalley_mobile = $data["kalley_mobile"];
    $paquetes_kalley_mobile = $data["paquetes_kalley_mobile"];
    $wings_mobile = $data["wings_mobile"];
    $paquetes_wings_mobile = $data["paquetes_wings_mobile"];
    $unicorn = $data["unicorn"];
    $wplay = $data["wplay"];
    $mega_apuesta = $data["mega_apuesta"];
    $ya_juego = $data["ya_juego"];
    $aqui_juego = $data["aqui_juego"];
    $rushBet = $data["rushBet"];
    $rivalo = $data["rivalo"];
    $miJugada = $data["miJugada"];
    $facturas_otros = $data["facturas_otros"];
    $facturas_de_gas_energia = $data["facturas_de_gas_energia"];
    $sms = $data["sms"];
    $baloto = $data["baloto"];
    $baloto_pago_de_premios = $data["baloto_pago_de_premios"];
    $certificado_de_tradicion = $data["certificado_de_tradicion"];
    $runt = $data["runt"];
    $energia_prepago_essa = $data["energia_prepago_essa"];
    $energia_Prepago_epm = $data["energia_Prepago_epm"];
    $soat_moto_estado = $data["soat_moto_estado"];
    $soat_carro_estado = $data["soat_carro_estado"];
    $soat_bus_estado = $data["soat_bus_estado"];
    $axa_soat_moto = $data["axa_soat_moto"];
    $axa_soat_carro = $data["axa_soat_carro"];
    $axa_soat_publico = $data["axa_soat_publico"];
    $movii_recargas = $data["movii_recargas"];
    $movii_retiros = $data["movii_retiros"];
    $daviplata_recargas = $data["daviplata_recargas"];
    $daviplata_retiros = $data["daviplata_retiros"];
    $taxia_recargas = $data["taxia_recargas"];
    $tpaga_retiros = $data["tpaga_retiros"];
    $razer_gold = $data["razer_gold"];
    $netflix = $data["netflix"];
    $spotify = $data["spotify"];
    $payValida = $data["payValida"];
    $freeFire = $data["freeFire"];
    $noggin = $data["noggin"];
    $crunchyroll = $data["crunchyroll"];
    $office = $data["office"];
    $win_sport = $data["win_sport"];
    $dataCredito = $data["dataCredito"];
    $paramount = $data["paramount"];
    $xbox_suscripciones = $data["xbox_suscripciones"];
    $play_station = $data["play_station"];
    $play_station_suscripciones = $data["play_station_suscripciones"];
    $minecraft = $data["minecraft"];
    $rixty = $data["rixty"];
    $payCash = $data["payCash"];
    $total = $data["total"];

    $sql = "INSERT INTO jv_consumido_ganancias (nombre,fecha_cargue, codigo_cliente, cc, perfil, davivienda_pago_tarjeta_de_credito, davivienda_pago_de_credito, davivienda_deposito_davivienda, davivienda_retiro_davivienda, aval_tarjeta_de_credito_ban_bogota, aval_cred_rotativo_crediservices_dinero_extra_ban_bogota, aval_credito_hipotecario_ban_bogota, aval_otros_creditos_ban_bogota, aval_credito_motos_vehiculos_ban_bogota, xbox_plata, imvu, keo, sisteCredito, claro, paquetes_claro, sura, movistar, paquetes_movistar, tigo, paquetes_tigo, avantel, paquetes_avantel, virgin, paquetes_virgin, etb, paquete_etb, exito, paquetes_exito, paquetes_conectame, flashMobile, direcTv, wom, paquetes_wom, kalley_mobile, paquetes_kalley_mobile, wings_mobile, paquetes_wings_mobile, unicorn, wplay, mega_apuesta, ya_juego, aqui_juego, rushBet, rivalo, miJugada, facturas_otros, facturas_de_gas_energia, sms, baloto, baloto_pago_de_premios, certificado_de_tradicion, runt, energia_prepago_essa, energia_Prepago_epm, soat_moto_estado, soat_carro_estado, soat_bus_estado, axa_soat_moto, axa_soat_carro, axa_soat_publico, movii_recargas, movii_retiros, daviplata_recargas, daviplata_retiros, taxia_recargas, tpaga_retiros, razer_gold, netflix, spotify, payValida, freeFire, noggin, crunchyroll, office, win_sport, dataCredito, paramount, xbox_suscripciones, play_station, play_station_suscripciones, minecraft, rixty, payCash, total) VALUES(
        :nombre,
        :fecha_cargue,
        :codigo_cliente,
        :cc,
        :perfil,
        :davivienda_pago_tarjeta_de_credito,
        :davivienda_pago_de_credito,
        :davivienda_deposito_davivienda,
        :davivienda_retiro_davivienda,
        :aval_tarjeta_de_credito_ban_bogota,
        :aval_cred_rotativo_crediservices_dinero_extra_ban_bogota,
        :aval_credito_hipotecario_ban_bogota,
        :aval_otros_creditos_ban_bogota,
        :aval_credito_motos_vehiculos_ban_bogota,
        :xbox_plata,
        :imvu,
        :keo,
        :sisteCredito,
        :claro,
        :paquetes_claro,
        :sura,
        :movistar,
        :paquetes_movistar,
        :tigo,
        :paquetes_tigo,
        :avantel,
        :paquetes_avantel,
        :virgin,
        :paquetes_virgin,
        :etb,
        :paquete_etb,
        :exito,
        :paquetes_exito,
        :paquetes_conectame,
        :flashMobile,
        :direcTv,
        :wom,
        :paquetes_wom,
        :kalley_mobile,
        :paquetes_kalley_mobile,
        :wings_mobile,
        :paquetes_wings_mobile,
        :unicorn,
        :wplay,
        :mega_apuesta,
        :ya_juego,
        :aqui_juego,
        :rushBet,
        :rivalo,
        :miJugada,
        :facturas_otros,
        :facturas_de_gas_energia,
        :sms,
        :baloto,
        :baloto_pago_de_premios,
        :certificado_de_tradicion,
        :runt,
        :energia_prepago_essa,
        :energia_Prepago_epm,
        :soat_moto_estado,
        :soat_carro_estado,
        :soat_bus_estado,
        :axa_soat_moto,
        :axa_soat_carro,
        :axa_soat_publico,
        :movii_recargas,
        :movii_retiros,
        :daviplata_recargas,
        :daviplata_retiros,
        :taxia_recargas,
        :tpaga_retiros,
        :razer_gold,
        :netflix,
        :spotify,
        :payValida,
        :freeFire,
        :noggin,
        :crunchyroll,
        :office,
        :win_sport,
        :dataCredito,
        :paramount,
        :xbox_suscripciones,
        :play_station,
        :play_station_suscripciones,
        :minecraft,
        :rixty,
        :payCash,
        :total)";

    try {
        // $dateCargue = date('Y-m-d');
        $db = new db();
        $db = $db->connectDB();
        $resultado = $db->prepare($sql);
        $resultado->bindParam(':nombre', $nombre);
        $resultado->bindParam(':fecha_cargue', $fecha_cargue);
        $resultado->bindParam(':codigo_cliente', $codigo_cliente);
        $resultado->bindParam(':cc', $cc);
        $resultado->bindParam(':perfil', $perfil);
        $resultado->bindParam(':davivienda_pago_tarjeta_de_credito', $davivienda_pago_tarjeta_de_credito);
        $resultado->bindParam(':davivienda_pago_de_credito', $davivienda_pago_de_credito);
        $resultado->bindParam(':davivienda_deposito_davivienda', $davivienda_deposito_davivienda);
        $resultado->bindParam(':davivienda_retiro_davivienda', $davivienda_retiro_davivienda);
        $resultado->bindParam(':aval_tarjeta_de_credito_ban_bogota', $aval_tarjeta_de_credito_ban_bogota);
        $resultado->bindParam(':aval_cred_rotativo_crediservices_dinero_extra_ban_bogota', $aval_cred_rotativo_crediservices_dinero_extra_ban_bogota);
        $resultado->bindParam(':aval_credito_hipotecario_ban_bogota', $aval_credito_hipotecario_ban_bogota);
        $resultado->bindParam(':aval_otros_creditos_ban_bogota', $aval_otros_creditos_ban_bogota);
        $resultado->bindParam(':aval_credito_motos_vehiculos_ban_bogota', $aval_credito_motos_vehiculos_ban_bogota);
        $resultado->bindParam(':xbox_plata', $xbox_plata);
        $resultado->bindParam(':imvu', $imvu);
        $resultado->bindParam(':keo', $keo);
        $resultado->bindParam(':sisteCredito', $sisteCredito);
        $resultado->bindParam(':claro', $claro);
        $resultado->bindParam(':paquetes_claro', $paquetes_claro);
        $resultado->bindParam(':sura', $sura);
        $resultado->bindParam(':movistar', $movistar);
        $resultado->bindParam(':paquetes_movistar', $paquetes_movistar);
        $resultado->bindParam(':tigo', $tigo);
        $resultado->bindParam(':paquetes_tigo', $paquetes_tigo);
        $resultado->bindParam(':avantel', $avantel);
        $resultado->bindParam(':paquetes_avantel', $paquetes_avantel);
        $resultado->bindParam(':virgin', $virgin);
        $resultado->bindParam(':paquetes_virgin', $paquetes_virgin);
        $resultado->bindParam(':etb', $etb);
        $resultado->bindParam(':paquete_etb', $paquete_etb);
        $resultado->bindParam(':exito', $exito);
        $resultado->bindParam(':paquetes_exito', $paquetes_exito);
        $resultado->bindParam(':paquetes_conectame', $paquetes_conectame);
        $resultado->bindParam(':flashMobile', $flashMobile);
        $resultado->bindParam(':direcTv', $direcTv);
        $resultado->bindParam(':wom', $wom);
        $resultado->bindParam(':paquetes_wom', $paquetes_wom);
        $resultado->bindParam(':kalley_mobile', $kalley_mobile);
        $resultado->bindParam(':paquetes_kalley_mobile', $paquetes_kalley_mobile);
        $resultado->bindParam(':wings_mobile', $wings_mobile);
        $resultado->bindParam(':paquetes_wings_mobile', $paquetes_wings_mobile);
        $resultado->bindParam(':unicorn', $unicorn);
        $resultado->bindParam(':wplay', $wplay);
        $resultado->bindParam(':mega_apuesta', $mega_apuesta);
        $resultado->bindParam(':ya_juego', $ya_juego);
        $resultado->bindParam(':aqui_juego', $aqui_juego);
        $resultado->bindParam(':rushBet', $rushBet);
        $resultado->bindParam(':rivalo', $rivalo);
        $resultado->bindParam(':miJugada', $miJugada);
        $resultado->bindParam(':facturas_otros', $facturas_otros);
        $resultado->bindParam(':facturas_de_gas_energia', $facturas_de_gas_energia);
        $resultado->bindParam(':sms', $sms);
        $resultado->bindParam(':baloto', $baloto);
        $resultado->bindParam(':baloto_pago_de_premios', $baloto_pago_de_premios);
        $resultado->bindParam(':certificado_de_tradicion', $certificado_de_tradicion);
        $resultado->bindParam(':runt', $runt);
        $resultado->bindParam(':energia_prepago_essa', $energia_prepago_essa);
        $resultado->bindParam(':energia_Prepago_epm', $energia_Prepago_epm);
        $resultado->bindParam(':soat_moto_estado', $soat_moto_estado);
        $resultado->bindParam(':soat_carro_estado', $soat_carro_estado);
        $resultado->bindParam(':soat_bus_estado', $soat_bus_estado);
        $resultado->bindParam(':axa_soat_moto', $axa_soat_moto);
        $resultado->bindParam(':axa_soat_carro', $axa_soat_carro);
        $resultado->bindParam(':axa_soat_publico', $axa_soat_publico);
        $resultado->bindParam(':movii_recargas', $movii_recargas);
        $resultado->bindParam(':movii_retiros', $movii_retiros);
        $resultado->bindParam(':daviplata_recargas', $daviplata_recargas);
        $resultado->bindParam(':daviplata_retiros', $daviplata_retiros);
        $resultado->bindParam(':taxia_recargas', $taxia_recargas);
        $resultado->bindParam(':tpaga_retiros', $tpaga_retiros);
        $resultado->bindParam(':razer_gold', $razer_gold);
        $resultado->bindParam(':netflix', $netflix);
        $resultado->bindParam(':spotify', $spotify);
        $resultado->bindParam(':payValida', $payValida);
        $resultado->bindParam(':freeFire', $freeFire);
        $resultado->bindParam(':noggin', $noggin);
        $resultado->bindParam(':crunchyroll', $crunchyroll);
        $resultado->bindParam(':office', $office);
        $resultado->bindParam(':win_sport', $win_sport);
        $resultado->bindParam(':dataCredito', $dataCredito);
        $resultado->bindParam(':paramount', $paramount);
        $resultado->bindParam(':xbox_suscripciones', $xbox_suscripciones);
        $resultado->bindParam(':play_station', $play_station);
        $resultado->bindParam(':play_station_suscripciones', $play_station_suscripciones);
        $resultado->bindParam(':minecraft', $minecraft);
        $resultado->bindParam(':rixty', $rixty);
        $resultado->bindParam(':payCash', $payCash);
        $resultado->bindParam(':total', $total);

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



$app->get('/api/recargasJV/saldos', function (Request $request, Response $response) {
    $sql = "select * from jv_consumido_saldo";
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

$app->get('/api/recargasJV/saldosFecha/{fechaInicio}/{fechaFin}', function (Request $request, Response $response) {
    $fechaInicio = $request->getAttribute('fechaInicio');
    $fechaFin = $request->getAttribute('fechaFin');
    $data = ["bRta" => false];
    $sql = "SELECT * from jv_consumido_saldo where fecha_cargue between '$fechaInicio' and '$fechaFin'";

    $dataConsulta = getSelect($sql);
    if ($dataConsulta['bRta']) {
        $data['bRta'] = true;
        $data['data'] = $dataConsulta['data'];
    }

    echo json_encode($data);
});

$app->post('/api/recargasJV/nuevoSaldo', function (Request $request, Response $response) {

    $payload = $request->getBody()->__toString();
    $payload =  stripslashes($payload);
    $data = json_decode($payload, true);

    $fecha_cargue = $data["fecha_cargue"];
    $nombre = $data["nombre"];
    $codigo_cliente = $data["codigo_cliente"];
    $cc = $data["cc"];
    $perfil = $data["perfil"];
    $davivienda_pago_tarjeta_de_credito = $data["davivienda_pago_tarjeta_de_credito"];
    $davivienda_pago_de_credito = $data["davivienda_pago_de_credito"];
    $davivienda_deposito_davivienda = $data["davivienda_deposito_davivienda"];
    $davivienda_retiro_davivienda = $data["davivienda_retiro_davivienda"];
    $aval_tarjeta_de_credito_ban_bogota = $data["aval_tarjeta_de_credito_ban_bogota"];
    $aval_cred_rotativo_crediservices_dinero_extra_ban_bogota = $data["aval_cred_rotativo_crediservices_dinero_extra_ban_bogota"];
    $aval_credito_hipotecario_ban_bogota = $data["aval_credito_hipotecario_ban_bogota"];
    $aval_otros_creditos_ban_bogota = $data["aval_otros_creditos_ban_bogota"];
    $aval_credito_motos_vehiculos_ban_bogota = $data["aval_credito_motos_vehiculos_ban_bogota"];
    $xbox_plata = $data["xbox_plata"];
    $imvu = $data["imvu"];
    $keo = $data["keo"];
    $sisteCredito = $data["sisteCredito"];
    $claro = $data["claro"];
    $paquetes_claro = $data["paquetes_claro"];
    $sura = $data["sura"];
    $movistar = $data["movistar"];
    $paquetes_movistar = $data["paquetes_movistar"];
    $tigo = $data["tigo"];
    $paquetes_tigo = $data["paquetes_tigo"];
    $avantel = $data["avantel"];
    $paquetes_avantel = $data["paquetes_avantel"];
    $virgin = $data["virgin"];
    $paquetes_virgin = $data["paquetes_virgin"];
    $etb = $data["etb"];
    $paquete_etb = $data["paquete_etb"];
    $exito = $data["exito"];
    $paquetes_exito = $data["paquetes_exito"];
    $paquetes_conectame = $data["paquetes_conectame"];
    $flashMobile = $data["flashMobile"];
    $direcTv = $data["direcTv"];
    $wom = $data["wom"];
    $paquetes_wom = $data["paquetes_wom"];
    $kalley_mobile = $data["kalley_mobile"];
    $paquetes_kalley_mobile = $data["paquetes_kalley_mobile"];
    $wings_mobile = $data["wings_mobile"];
    $paquetes_wings_mobile = $data["paquetes_wings_mobile"];
    $unicorn = $data["unicorn"];
    $wplay = $data["wplay"];
    $mega_apuesta = $data["mega_apuesta"];
    $ya_juego = $data["ya_juego"];
    $aqui_juego = $data["aqui_juego"];
    $rushBet = $data["rushBet"];
    $rivalo = $data["rivalo"];
    $miJugada = $data["miJugada"];
    $facturas_otros = $data["facturas_otros"];
    $facturas_de_gas_energia = $data["facturas_de_gas_energia"];
    $sms = $data["sms"];
    $baloto = $data["baloto"];
    $baloto_pago_de_premios = $data["baloto_pago_de_premios"];
    $certificado_de_tradicion = $data["certificado_de_tradicion"];
    $runt = $data["runt"];
    $energia_prepago_essa = $data["energia_prepago_essa"];
    $energia_Prepago_epm = $data["energia_Prepago_epm"];
    $soat_moto_estado = $data["soat_moto_estado"];
    $soat_carro_estado = $data["soat_carro_estado"];
    $soat_bus_estado = $data["soat_bus_estado"];
    $axa_soat_moto = $data["axa_soat_moto"];
    $axa_soat_carro = $data["axa_soat_carro"];
    $axa_soat_publico = $data["axa_soat_publico"];
    $movii_recargas = $data["movii_recargas"];
    $movii_retiros = $data["movii_retiros"];
    $daviplata_recargas = $data["daviplata_recargas"];
    $daviplata_retiros = $data["daviplata_retiros"];
    $taxia_recargas = $data["taxia_recargas"];
    $tpaga_retiros = $data["tpaga_retiros"];
    $razer_gold = $data["razer_gold"];
    $netflix = $data["netflix"];
    $spotify = $data["spotify"];
    $payValida = $data["payValida"];
    $freeFire = $data["freeFire"];
    $noggin = $data["noggin"];
    $crunchyroll = $data["crunchyroll"];
    $office = $data["office"];
    $win_sport = $data["win_sport"];
    $dataCredito = $data["dataCredito"];
    $paramount = $data["paramount"];
    $xbox_suscripciones = $data["xbox_suscripciones"];
    $play_station = $data["play_station"];
    $play_station_suscripciones = $data["play_station_suscripciones"];
    $minecraft = $data["minecraft"];
    $rixty = $data["rixty"];
    $payCash = $data["payCash"];
    $total = $data["total"];

    $sql = "INSERT INTO jv_consumido_saldo (nombre,fecha_cargue, codigo_cliente, cc, perfil, davivienda_pago_tarjeta_de_credito, davivienda_pago_de_credito, davivienda_deposito_davivienda, davivienda_retiro_davivienda, aval_tarjeta_de_credito_ban_bogota, aval_cred_rotativo_crediservices_dinero_extra_ban_bogota, aval_credito_hipotecario_ban_bogota, aval_otros_creditos_ban_bogota, aval_credito_motos_vehiculos_ban_bogota, xbox_plata, imvu, keo, sisteCredito, claro, paquetes_claro, sura, movistar, paquetes_movistar, tigo, paquetes_tigo, avantel, paquetes_avantel, virgin, paquetes_virgin, etb, paquete_etb, exito, paquetes_exito, paquetes_conectame, flashMobile, direcTv, wom, paquetes_wom, kalley_mobile, paquetes_kalley_mobile, wings_mobile, paquetes_wings_mobile, unicorn, wplay, mega_apuesta, ya_juego, aqui_juego, rushBet, rivalo, miJugada, facturas_otros, facturas_de_gas_energia, sms, baloto, baloto_pago_de_premios, certificado_de_tradicion, runt, energia_prepago_essa, energia_Prepago_epm, soat_moto_estado, soat_carro_estado, soat_bus_estado, axa_soat_moto, axa_soat_carro, axa_soat_publico, movii_recargas, movii_retiros, daviplata_recargas, daviplata_retiros, taxia_recargas, tpaga_retiros, razer_gold, netflix, spotify, payValida, freeFire, noggin, crunchyroll, office, win_sport, dataCredito, paramount, xbox_suscripciones, play_station, play_station_suscripciones, minecraft, rixty, payCash, total) VALUES(
        :nombre,
        :fecha_cargue,
        :codigo_cliente,
        :cc,
        :perfil,
        :davivienda_pago_tarjeta_de_credito,
        :davivienda_pago_de_credito,
        :davivienda_deposito_davivienda,
        :davivienda_retiro_davivienda,
        :aval_tarjeta_de_credito_ban_bogota,
        :aval_cred_rotativo_crediservices_dinero_extra_ban_bogota,
        :aval_credito_hipotecario_ban_bogota,
        :aval_otros_creditos_ban_bogota,
        :aval_credito_motos_vehiculos_ban_bogota,
        :xbox_plata,
        :imvu,
        :keo,
        :sisteCredito,
        :claro,
        :paquetes_claro,
        :sura,
        :movistar,
        :paquetes_movistar,
        :tigo,
        :paquetes_tigo,
        :avantel,
        :paquetes_avantel,
        :virgin,
        :paquetes_virgin,
        :etb,
        :paquete_etb,
        :exito,
        :paquetes_exito,
        :paquetes_conectame,
        :flashMobile,
        :direcTv,
        :wom,
        :paquetes_wom,
        :kalley_mobile,
        :paquetes_kalley_mobile,
        :wings_mobile,
        :paquetes_wings_mobile,
        :unicorn,
        :wplay,
        :mega_apuesta,
        :ya_juego,
        :aqui_juego,
        :rushBet,
        :rivalo,
        :miJugada,
        :facturas_otros,
        :facturas_de_gas_energia,
        :sms,
        :baloto,
        :baloto_pago_de_premios,
        :certificado_de_tradicion,
        :runt,
        :energia_prepago_essa,
        :energia_Prepago_epm,
        :soat_moto_estado,
        :soat_carro_estado,
        :soat_bus_estado,
        :axa_soat_moto,
        :axa_soat_carro,
        :axa_soat_publico,
        :movii_recargas,
        :movii_retiros,
        :daviplata_recargas,
        :daviplata_retiros,
        :taxia_recargas,
        :tpaga_retiros,
        :razer_gold,
        :netflix,
        :spotify,
        :payValida,
        :freeFire,
        :noggin,
        :crunchyroll,
        :office,
        :win_sport,
        :dataCredito,
        :paramount,
        :xbox_suscripciones,
        :play_station,
        :play_station_suscripciones,
        :minecraft,
        :rixty,
        :payCash,
        :total)";

    try {
        $db = new db();
        $db = $db->connectDB();
        $resultado = $db->prepare($sql);
        $resultado->bindParam(':nombre', $nombre);
        $resultado->bindParam(':fecha_cargue', $fecha_cargue);
        $resultado->bindParam(':codigo_cliente', $codigo_cliente);
        $resultado->bindParam(':cc', $cc);
        $resultado->bindParam(':perfil', $perfil);
        $resultado->bindParam(':davivienda_pago_tarjeta_de_credito', $davivienda_pago_tarjeta_de_credito);
        $resultado->bindParam(':davivienda_pago_de_credito', $davivienda_pago_de_credito);
        $resultado->bindParam(':davivienda_deposito_davivienda', $davivienda_deposito_davivienda);
        $resultado->bindParam(':davivienda_retiro_davivienda', $davivienda_retiro_davivienda);
        $resultado->bindParam(':aval_tarjeta_de_credito_ban_bogota', $aval_tarjeta_de_credito_ban_bogota);
        $resultado->bindParam(':aval_cred_rotativo_crediservices_dinero_extra_ban_bogota', $aval_cred_rotativo_crediservices_dinero_extra_ban_bogota);
        $resultado->bindParam(':aval_credito_hipotecario_ban_bogota', $aval_credito_hipotecario_ban_bogota);
        $resultado->bindParam(':aval_otros_creditos_ban_bogota', $aval_otros_creditos_ban_bogota);
        $resultado->bindParam(':aval_credito_motos_vehiculos_ban_bogota', $aval_credito_motos_vehiculos_ban_bogota);
        $resultado->bindParam(':xbox_plata', $xbox_plata);
        $resultado->bindParam(':imvu', $imvu);
        $resultado->bindParam(':keo', $keo);
        $resultado->bindParam(':sisteCredito', $sisteCredito);
        $resultado->bindParam(':claro', $claro);
        $resultado->bindParam(':paquetes_claro', $paquetes_claro);
        $resultado->bindParam(':sura', $sura);
        $resultado->bindParam(':movistar', $movistar);
        $resultado->bindParam(':paquetes_movistar', $paquetes_movistar);
        $resultado->bindParam(':tigo', $tigo);
        $resultado->bindParam(':paquetes_tigo', $paquetes_tigo);
        $resultado->bindParam(':avantel', $avantel);
        $resultado->bindParam(':paquetes_avantel', $paquetes_avantel);
        $resultado->bindParam(':virgin', $virgin);
        $resultado->bindParam(':paquetes_virgin', $paquetes_virgin);
        $resultado->bindParam(':etb', $etb);
        $resultado->bindParam(':paquete_etb', $paquete_etb);
        $resultado->bindParam(':exito', $exito);
        $resultado->bindParam(':paquetes_exito', $paquetes_exito);
        $resultado->bindParam(':paquetes_conectame', $paquetes_conectame);
        $resultado->bindParam(':flashMobile', $flashMobile);
        $resultado->bindParam(':direcTv', $direcTv);
        $resultado->bindParam(':wom', $wom);
        $resultado->bindParam(':paquetes_wom', $paquetes_wom);
        $resultado->bindParam(':kalley_mobile', $kalley_mobile);
        $resultado->bindParam(':paquetes_kalley_mobile', $paquetes_kalley_mobile);
        $resultado->bindParam(':wings_mobile', $wings_mobile);
        $resultado->bindParam(':paquetes_wings_mobile', $paquetes_wings_mobile);
        $resultado->bindParam(':unicorn', $unicorn);
        $resultado->bindParam(':wplay', $wplay);
        $resultado->bindParam(':mega_apuesta', $mega_apuesta);
        $resultado->bindParam(':ya_juego', $ya_juego);
        $resultado->bindParam(':aqui_juego', $aqui_juego);
        $resultado->bindParam(':rushBet', $rushBet);
        $resultado->bindParam(':rivalo', $rivalo);
        $resultado->bindParam(':miJugada', $miJugada);
        $resultado->bindParam(':facturas_otros', $facturas_otros);
        $resultado->bindParam(':facturas_de_gas_energia', $facturas_de_gas_energia);
        $resultado->bindParam(':sms', $sms);
        $resultado->bindParam(':baloto', $baloto);
        $resultado->bindParam(':baloto_pago_de_premios', $baloto_pago_de_premios);
        $resultado->bindParam(':certificado_de_tradicion', $certificado_de_tradicion);
        $resultado->bindParam(':runt', $runt);
        $resultado->bindParam(':energia_prepago_essa', $energia_prepago_essa);
        $resultado->bindParam(':energia_Prepago_epm', $energia_Prepago_epm);
        $resultado->bindParam(':soat_moto_estado', $soat_moto_estado);
        $resultado->bindParam(':soat_carro_estado', $soat_carro_estado);
        $resultado->bindParam(':soat_bus_estado', $soat_bus_estado);
        $resultado->bindParam(':axa_soat_moto', $axa_soat_moto);
        $resultado->bindParam(':axa_soat_carro', $axa_soat_carro);
        $resultado->bindParam(':axa_soat_publico', $axa_soat_publico);
        $resultado->bindParam(':movii_recargas', $movii_recargas);
        $resultado->bindParam(':movii_retiros', $movii_retiros);
        $resultado->bindParam(':daviplata_recargas', $daviplata_recargas);
        $resultado->bindParam(':daviplata_retiros', $daviplata_retiros);
        $resultado->bindParam(':taxia_recargas', $taxia_recargas);
        $resultado->bindParam(':tpaga_retiros', $tpaga_retiros);
        $resultado->bindParam(':razer_gold', $razer_gold);
        $resultado->bindParam(':netflix', $netflix);
        $resultado->bindParam(':spotify', $spotify);
        $resultado->bindParam(':payValida', $payValida);
        $resultado->bindParam(':freeFire', $freeFire);
        $resultado->bindParam(':noggin', $noggin);
        $resultado->bindParam(':crunchyroll', $crunchyroll);
        $resultado->bindParam(':office', $office);
        $resultado->bindParam(':win_sport', $win_sport);
        $resultado->bindParam(':dataCredito', $dataCredito);
        $resultado->bindParam(':paramount', $paramount);
        $resultado->bindParam(':xbox_suscripciones', $xbox_suscripciones);
        $resultado->bindParam(':play_station', $play_station);
        $resultado->bindParam(':play_station_suscripciones', $play_station_suscripciones);
        $resultado->bindParam(':minecraft', $minecraft);
        $resultado->bindParam(':rixty', $rixty);
        $resultado->bindParam(':payCash', $payCash);
        $resultado->bindParam(':total', $total);

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

$app->get('/api/recargasJV/indicadores', function (Request $request, Response $response) {

    $data = ["bRta" => false];
    $sql = "SELECT 
                ganancias.fecha_cargue,
                sum(ganancias.total) ganancias, 
                sum(saldos.total) saldos,
                0 recargas
            from 
                jv_consumido_ganancias ganancias
                inner join jv_consumido_saldo saldos on (saldos.fecha_cargue = ganancias.fecha_cargue and saldos.codigo_cliente = ganancias.codigo_cliente)
            group by 
                ganancias.fecha_cargue
            order by ganancias.fecha_cargue desc";

    $dataConsulta = getSelectRecargasJV($sql);
    if ($dataConsulta['bRta']) {
        $data['bRta'] = true;
        $data['data'] = $dataConsulta['data'];
    }

    echo json_encode($data);
});

$app->get('/api/recargasJV/indicadoresFecha/{fechaInicio}/{fechaFin}', function (Request $request, Response $response) {
    $fechaInicio = $request->getAttribute('fechaInicio');
    $fechaFin = $request->getAttribute('fechaFin');
    $data = ["bRta" => false];
    $sql = "SELECT 
                ganancias.fecha_cargue,
                sum(ganancias.total) ganancias, 
                sum(saldos.total) saldos,
                0 recargas
            from 
                jv_consumido_ganancias ganancias
                inner join jv_consumido_saldo saldos on (saldos.fecha_cargue = ganancias.fecha_cargue and saldos.codigo_cliente = ganancias.codigo_cliente)
            where
	            ganancias.fecha_cargue between '$fechaInicio' and '$fechaFin'
            group by 
                ganancias.fecha_cargue
            order by ganancias.fecha_cargue desc";

    $dataConsulta = getSelect($sql);
    if ($dataConsulta['bRta']) {
        $data['bRta'] = true;
        $data['data'] = $dataConsulta['data'];
    }

    echo json_encode($data);
});

function getSelectRecargasJV(String $sql)
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