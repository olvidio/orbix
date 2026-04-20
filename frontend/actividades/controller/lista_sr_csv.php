<?php
/**
 * Listado de actividades de SR para exportar como CSV o mostrar en pantalla.
 *
 * La logica vive en `src\actividades\application\ListaSrCsvListado`.
 * Este controlador recibe el POST, pide los datos al backend via PostRequest
 * y, segun `Qque`, devuelve:
 *   - que=lista: vista HTML con la tabla.
 *   - que=file : descarga directa como CSV usando `web\Lista::getCsv()`.
 *
 * Migrado desde apps/actividades/controller/lista_sr_csv.php.
 *
 * @package    delegacion
 * @subpackage actividades
 */

use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use web\Lista;

require_once("frontend/shared/global_header_front.inc");

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
    echo $data['pref_error'];
}

if ($Qque === 'file') {
    $a_cabeceras = (array)($data['a_cabeceras'] ?? []);
    $a_valores = (array)($data['a_valores'] ?? []);
    $oTabla = new Lista();
    $oTabla->setId_tabla('lista_activ');
    $oTabla->setCabeceras($a_cabeceras);
    $oTabla->setDatos($a_valores);
    $oTabla->getCsv('actividades_sr.csv');
    die();
}

if ($Qque === 'lista') {
    $oPosicion->recordar();

    $a_campos = [
        'oPosicion' => $oPosicion,
        'titulo' => (string)($data['titulo'] ?? ''),
        'html_tabla' => (string)($data['html_tabla'] ?? ''),
    ];

    $oView = new ViewNewPhtml('frontend\actividades\controller');
    $oView->renderizar('lista_sr_csv.phtml', $a_campos);
}
