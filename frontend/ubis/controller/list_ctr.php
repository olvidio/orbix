<?php

use frontend\shared\config\AppUrlConfig;
use frontend\shared\config\OrbixRuntime;
use frontend\shared\PostRequest;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\security\HashFront;
use frontend\shared\web\Lista;
use frontend\shared\web\Posicion;

/**
 * Página para realizar algunos listados standard de ubis
 *
 *
 *
 * @package    delegacion
 * @subpackage    ubis
 * @author    Josep Companys
 * @since        15/5/02.
 *
 * Llegamos desde menú: "centros y casas" y
 * submenú "listados"
 * Las funciones que podré hacer con los ubis son
 * idénticas a las que realizamos en submenú "buscar"
 *
 * Se tiene en cuenta si es una vuelta de un go_to
 */
require_once("frontend/shared/global_header_front.inc");

//Si vengo por medio de Posicion, borro la última
if (isset($_POST['stack'])) {
    $stack = (int)filter_input(INPUT_POST, 'stack', FILTER_SANITIZE_NUMBER_INT);
    if ($stack !== 0) {
        // No me sirve el de global_object, sino el de la session
        $oPosicion2 = new Posicion();
        if ($oPosicion2->goStack($stack)) { // devuelve false si no puede ir
            $obj_pau = $oPosicion2->getParametro('obj_pau');
            $id_ubi = $oPosicion2->getParametro('id_ubi');
            $Qid_sel = $oPosicion2->getParametro('id_sel');
            $Qscroll_id = $oPosicion2->getParametro('scroll_id');
            $oPosicion2->olvidar($stack);
        }
    }
}

$Qque_lista = (string)filter_input(INPUT_POST, 'que_lista');
$Qloc = (string)filter_input(INPUT_POST, 'loc');

if (empty($Qloc)) {
    $Qloc = OrbixRuntime::miRegionDl();
}
if (empty($Qque_lista)) {
    $Qque_lista = 'ctr_n';
}

$id_sel = isset($Qid_sel) ? (string)$Qid_sel : '';
$scroll_id = isset($Qscroll_id) ? (string)$Qscroll_id : '';

$data = PostRequest::getDataFromUrl('/src/ubis/list_ctr_data', [
    'que_lista' => $Qque_lista,
    'loc' => $Qloc,
    'id_sel' => $id_sel,
    'scroll_id' => $scroll_id,
]);
if (!empty($data['error'])) {
    exit((string)$data['error']);
}

$a_valores = $data['a_valores'] ?? [];
$baseUrl = AppUrlConfig::getPublicAppBaseUrl();
foreach ($a_valores as $idx => $fila) {
    if (!is_array($fila)) {
        continue;
    }
    foreach ($fila as $colKey => $cell) {
        if (!is_array($cell) || !isset($cell['link_spec'])) {
            continue;
        }
        $spec = $cell['link_spec'];
        $path = (string)($spec['path'] ?? '');
        $query = is_array($spec['query'] ?? null) ? $spec['query'] : [];
        if ($path === '') {
            continue;
        }
        $url = $baseUrl . '/' . ltrim($path, '/') . '?' . http_build_query($query);
        $a_valores[$idx][$colKey]['ira'] = HashFront::link($url);
        unset($a_valores[$idx][$colKey]['link_spec']);
    }
}

$aGoBack = [
    'loc' => $Qloc,
    'que_lista' => $Qque_lista,
];
$oPosicion->setParametros($aGoBack);
$oPosicion->recordar();

$oTabla = new Lista();
$oTabla->setId_tabla('list_ctr');
$oTabla->setCabeceras($data['a_cabeceras'] ?? []);
$oTabla->setBotones($data['a_botones'] ?? []);
$oTabla->setDatos($a_valores);

$oHash = new HashFront();
$oHash->setCamposForm('loc!que_lista');

$oHash1 = new HashFront();
$oHash1->setCamposForm('sel');
$oHash1->setcamposNo('scroll_id!dl_dst');
$a_camposHidden1 = [
    'que_lista' => $Qque_lista,
    'dl_dst' => '',
];
$oHash1->setArraycamposHidden($a_camposHidden1);

$oHash2 = new HashFront();
$oHash2->setUrl(AppUrlConfig::getPublicAppBaseUrl() . '/frontend/ubis/controller/delegacion_que.php');
$oHash2->setCamposForm('');
$h2 = $oHash2->linkSinVal();

$a_campos = [
    'oPosicion' => $oPosicion,
    'oHash' => $oHash,
    'opciones_loc' => $data['opciones_loc'] ?? [],
    'opciones_que_lista' => $data['opciones_que_lista'] ?? [],
    'loc' => $Qloc,
    'que_lista' => $Qque_lista,
    'oHash1' => $oHash1,
    'oTabla' => $oTabla,
    'h2' => $h2,
];

$oView = new ViewNewPhtml('frontend\ubis\controller');
$oView->renderizar('list_ctr.phtml', $a_campos);
