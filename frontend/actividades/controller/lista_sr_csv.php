<?php

use frontend\actividades\helpers\ActividadesPayload;
use frontend\actividades\helpers\ActividadesListaSupport;
use frontend\shared\helpers\PayloadCoercion;
use frontend\shared\helpers\ListNavSupport;

/**
 * Listado de actividades de SR para exportar como CSV o mostrar en pantalla.
 *
 * La logica vive en `src\actividades\application\ListaSrCsvListado`.
 * Este controlador recibe el POST, pide los datos al backend via PostRequest
 * y, segun `Qque`, devuelve:
 *   - que=lista: vista HTML con la tabla.
 *   - que=file : descarga directa como CSV usando `frontend\shared\web\Lista::getCsv()`.
 *
 * Migrado desde frontend/actividades/controller/lista_sr_csv.php.
 *
 * @package    delegacion
 * @subpackage actividades
 */

use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\web\Lista;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';
$oPosicion = FrontBootstrap::boot();
$Qque = (string)filter_input(INPUT_POST, 'que');

$Qperiodo = (string)filter_input(INPUT_POST, 'periodo');
$Qyear = (string)filter_input(INPUT_POST, 'year');
$Qdl_org = (string)filter_input(INPUT_POST, 'dl_org');
$Qempiezamin = (string)filter_input(INPUT_POST, 'empiezamin');
$Qempiezamax = (string)filter_input(INPUT_POST, 'empiezamax');
$Qa_activ = (array)filter_input(INPUT_POST, 'c_activ', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
$Qa_status = (array)filter_input(INPUT_POST, 'status', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
$Qa_id_cdc = (array)filter_input(INPUT_POST, 'id_cdc', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

$data = PostRequest::getDataFromUrl('/src/actividades/lista_sr_csv_datos', [
    'periodo' => $Qperiodo,
    'year' => $Qyear,
    'dl_org' => $Qdl_org,
    'empiezamin' => $Qempiezamin,
    'empiezamax' => $Qempiezamax,
    'c_activ' => $Qa_activ,
    'status' => $Qa_status,
    'id_cdc' => $Qa_id_cdc,
]);

if (!empty($data['pref_error'])) {
    echo PayloadCoercion::string($data['pref_error']);
}

if ($Qque === 'file') {
    $a_cabeceras = ActividadesListaSupport::cabeceras($data['a_cabeceras'] ?? []);
    $a_valores = ActividadesPayload::listaValoresFromPayload($data['a_valores'] ?? []);
    $oTabla = new Lista();
    $oTabla->setId_tabla('lista_activ');
    $oTabla->setCabeceras($a_cabeceras);
    $oTabla->setDatos($a_valores);
    $oTabla->getCsv('actividades_sr.csv');
    die();
}

if ($Qque === 'lista') {
    ListNavSupport::bootRecordar($oPosicion);
    ListNavSupport::persistRecordarEntry($oPosicion, ListNavSupport::buildReturnParametrosFromPost());


    $a_campos = [
        'oPosicion' => $oPosicion,
        'titulo' => PayloadCoercion::string($data['titulo'] ?? ''),
        'html_tabla' => PayloadCoercion::string($data['html_tabla'] ?? ''),
    ];

    $oView = new ViewNewPhtml('frontend\actividades\controller');
    $oView->renderizar('lista_sr_csv.phtml', $a_campos);
}
