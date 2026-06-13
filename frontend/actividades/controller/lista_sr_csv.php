<?php
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

require_once __DIR__ . '/../helpers/actividades_support.php';
require_once 'frontend/shared/FrontBootstrap.php';
require_once __DIR__ . '/../../shared/helpers/list_nav_support.php';

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
    echo tessera_imprimir_string($data['pref_error']);
}

if ($Qque === 'file') {
    $a_cabeceras = actividades_lista_cabeceras($data['a_cabeceras'] ?? []);
    $a_valores = actividades_lista_valores_from_payload($data['a_valores'] ?? []);
    $oTabla = new Lista();
    $oTabla->setId_tabla('lista_activ');
    $oTabla->setCabeceras($a_cabeceras);
    $oTabla->setDatos($a_valores);
    $oTabla->getCsv('actividades_sr.csv');
    die();
}

if ($Qque === 'lista') {
    $oPosicion->recordar();
    list_nav_persist_recordar_entry($oPosicion, list_nav_build_return_parametros_from_post());


    $a_campos = [
        'oPosicion' => $oPosicion,
        'titulo' => tessera_imprimir_string($data['titulo'] ?? ''),
        'html_tabla' => tessera_imprimir_string($data['html_tabla'] ?? ''),
    ];

    $oView = new ViewNewPhtml('frontend\actividades\controller');
    $oView->renderizar('lista_sr_csv.phtml', $a_campos);
}
