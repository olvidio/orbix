<?php

use frontend\ubis\helpers\UbisPayload;
use frontend\shared\helpers\PayloadCoercion;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;
use frontend\shared\web\Lista;
use frontend\shared\web\Posicion;
use frontend\shared\FrontBootstrap;
use frontend\shared\helpers\ListNavSupport;

require_once 'frontend/shared/FrontBootstrap.php';
$oPosicion = FrontBootstrap::boot();

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
ListNavSupport::bootRecordar($oPosicion);
ListNavSupport::persistRecordarEntry($oPosicion, ListNavSupport::mergeSelectionForRecordar(ListNavSupport::buildReturnParametrosFromPost(), $Qid_sel, $Qscroll_id));


$params = UbisPayload::postData($_POST);
if ($Qid_sel !== null && $Qid_sel !== '') {
    $params['id_sel'] = PayloadCoercion::string($Qid_sel);
}
if ($Qscroll_id !== null && $Qscroll_id !== '') {
    $params['scroll_id'] = PayloadCoercion::string($Qscroll_id);
}

$tabla = UbisPayload::tablaFromPayload(UbisPayload::postData(PostRequest::getDataFromUrl('/src/ubis/ubis_tabla_data', $params)));

$oPosicion->setParametros($tabla['go_back'], 1);

$a_valores = UbisPayload::signListaValores($tabla['valores']);
$pagina_link = UbisPayload::paginaLinkFromTabla($tabla);

$oTabla = new Lista();
$oTabla->setId_tabla('ubis_tabla');
$oTabla->setCabeceras($tabla['cabeceras']);
$oTabla->setBotones($tabla['botones']);
$oTabla->setDatos($a_valores);

$oHash = new HashFront();
$oHash->setCamposForm('!sel');
$oHash->setCamposNo('!scroll_id');
$oHash->setArrayCamposHidden($tabla['hash_hidden']);

$a_campos = [
    'oHash' => $oHash,
    'titulo' => $tabla['titulo'],
    'oTabla' => $oTabla,
    'nueva_ficha' => $tabla['nueva_ficha'],
    'pagina_link' => $pagina_link,
];

$oView = new ViewNewPhtml('frontend\ubis\controller');
$oView->renderizar('ubis_tabla.phtml', $a_campos);
