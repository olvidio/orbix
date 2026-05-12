<?php

use frontend\shared\config\AppUrlConfig;
use frontend\shared\PostRequest;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\security\HashFront;
use frontend\shared\web\Lista;
use frontend\shared\web\Posicion;

/**
 * Esta página muestra una tabla con los ubis seleccionados.
 *
 * @package    delegacion
 * @subpackage    ubis
 * @author    Daniel Serrabou
 * @since        15/5/02.
 *
 * Se tiene en cuenta si es una vuelta de un go_to
 */
require_once("frontend/shared/global_header_front.inc");

$oPosicion->recordar();

//Si vengo por medio de Posicion, borro la última
$Qid_sel = null;
$Qscroll_id = null;
if (isset($_POST['stack'])) {
    $stack = (int)filter_input(INPUT_POST, 'stack', FILTER_SANITIZE_NUMBER_INT);
    if ($stack !== 0) {
        $oPosicion2 = new Posicion();
        if ($oPosicion2->goStack($stack)) {
            $Qid_sel = $oPosicion2->getParametro('id_sel');
            $Qscroll_id = $oPosicion2->getParametro('scroll_id');
            $oPosicion2->olvidar($stack);
        }
    }
}

$params = $_POST;
if ($Qid_sel !== null && $Qid_sel !== '') {
    $params['id_sel'] = $Qid_sel;
}
if ($Qscroll_id !== null && $Qscroll_id !== '') {
    $params['scroll_id'] = $Qscroll_id;
}

$data = PostRequest::getDataFromUrl('/src/ubis/ubis_tabla_data', $params);

$oPosicion->setParametros($data['go_back'], 1);

$a_valores = $data['a_valores'];
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

$pagina_link = '';
if (!empty($data['pagina_link_spec']) && is_array($data['pagina_link_spec'])) {
    $spec = $data['pagina_link_spec'];
    $path = (string)($spec['path'] ?? '');
    $query = is_array($spec['query'] ?? null) ? $spec['query'] : [];
    if ($path !== '') {
        $pagina_link = HashFront::link($baseUrl . '/' . ltrim($path, '/') . '?' . http_build_query($query));
    }
}

$oTabla = new Lista();
$oTabla->setId_tabla('ubis_tabla');
$oTabla->setCabeceras($data['a_cabeceras']);
$oTabla->setBotones($data['a_botones']);
$oTabla->setDatos($a_valores);

$oHash = new HashFront();
$oHash->setCamposForm('!sel');
$oHash->setCamposNo('!scroll_id');
$oHash->setArrayCamposHidden($data['hash_hidden']);

$a_campos = [
    'oHash' => $oHash,
    'titulo' => $data['titulo'],
    'oTabla' => $oTabla,
    'nueva_ficha' => $data['nueva_ficha'],
    'pagina_link' => $pagina_link,
];

$oView = new ViewNewPhtml('frontend\ubis\controller');
$oView->renderizar('ubis_tabla.phtml', $a_campos);
