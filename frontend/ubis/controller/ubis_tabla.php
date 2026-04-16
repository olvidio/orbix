<?php

use frontend\shared\PostRequest;
use frontend\shared\model\ViewNewPhtml;
use web\Hash;
use web\Lista;
use web\Posicion;

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
    $stack = filter_input(INPUT_POST, 'stack', FILTER_SANITIZE_NUMBER_INT);
    if ($stack != '') {
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

$oTabla = new Lista();
$oTabla->setId_tabla('ubis_tabla');
$oTabla->setCabeceras($data['a_cabeceras']);
$oTabla->setBotones($data['a_botones']);
$oTabla->setDatos($data['a_valores']);

$oHash = new Hash();
$oHash->setCamposForm('!sel');
$oHash->setCamposNo('!scroll_id');
$oHash->setArrayCamposHidden($data['hash_hidden']);

$a_campos = [
    'oHash' => $oHash,
    'titulo' => $data['titulo'],
    'oTabla' => $oTabla,
    'nueva_ficha' => $data['nueva_ficha'],
    'pagina_link' => $data['pagina_link'],
];

$oView = new ViewNewPhtml('frontend\ubis\controller');
$oView->renderizar('ubis_tabla.phtml', $a_campos);
