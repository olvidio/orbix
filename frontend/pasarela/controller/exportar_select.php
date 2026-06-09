<?php

use frontend\shared\PostRequest;
use frontend\shared\web\Lista;
use frontend\shared\web\Periodo;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';

FrontBootstrap::boot();
/**
 * Resultado del filtro "exportar actividades": delega el armado del listado en
 * `/src/pasarela/exportar_actividades_data` (caso de uso
 * {@see \src\pasarela\application\ExportarActividadesData}) y solo se ocupa de
 * renderizar la tabla con `frontend\shared\web\Lista`.
 *
 * El cálculo de fechas (Periodo) se hace en el frontend y se envía ya en ISO
 * para que la capa `application/` no dependa de `frontend\shared\web\Periodo`.
 */

$Qid_tipo_activ = (string)filter_input(INPUT_POST, 'id_tipo_activ');
$Qisfsv_val = (string)filter_input(INPUT_POST, 'isfsv_val');
$Qiasistentes_val = (string)filter_input(INPUT_POST, 'iasistentes_val');
$Qiactividad_val = (string)filter_input(INPUT_POST, 'iactividad_val');
$Qperiodo = (string)filter_input(INPUT_POST, 'periodo');
$Qyear = (string)filter_input(INPUT_POST, 'year');
$Qempiezamin = (string)filter_input(INPUT_POST, 'empiezamin');
$Qempiezamax = (string)filter_input(INPUT_POST, 'empiezamax');
$Qid_cdc = (array)filter_input(INPUT_POST, 'id_cdc', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

// Periodo: cálculo en frontend (depende de sesión/calendario escolar) y envío
// del rango ya resuelto al backend.
$oPeriodo = new Periodo();
$oPeriodo->setDefaultAny('next');
$oPeriodo->setAny($Qyear);
$oPeriodo->setEmpiezaMin($Qempiezamin);
$oPeriodo->setEmpiezaMax($Qempiezamax);
$oPeriodo->setPeriodo($Qperiodo);
$inicioIso = (string)$oPeriodo->getF_ini_iso();
$finIso = (string)$oPeriodo->getF_fin_iso();

$data = PostRequest::getDataFromUrl('/src/pasarela/exportar_actividades_data', [
    'id_tipo_activ' => $Qid_tipo_activ,
    'isfsv_val' => $Qisfsv_val,
    'iasistentes_val' => $Qiasistentes_val,
    'iactividad_val' => $Qiactividad_val,
    'inicio_iso' => $inicioIso,
    'fin_iso' => $finIso,
    'id_cdc' => $Qid_cdc,
]);

$err = (string)($data['errores'] ?? '');
$a_cabeceras = (array)($data['a_cabeceras'] ?? []);
$a_botones = (array)($data['a_botones'] ?? []);
$a_valores = (array)($data['a_valores'] ?? []);

$oTabla = new Lista();
$oTabla->setId_tabla('actividad_select');
$oTabla->setCabeceras($a_cabeceras);
$oTabla->setBotones($a_botones);
$oTabla->setDatos($a_valores);

if ($err !== '') {
    echo $err;
}
echo $oTabla->mostrar_tabla_html();
