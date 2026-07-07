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
/** @var Posicion $oPosicion */

/** @var string|list<string> $Qid_sel */
$Qid_sel = ListNavSupport::idSelFromPost();
$Qscroll_id = ListNavSupport::scrollIdFromPost();

$navState = ListNavSupport::mergeSelectionForRecordar(
    ListNavSupport::buildReturnParametrosFromPost(),
    $Qid_sel,
    $Qscroll_id,
);
$oPosicion->nav()->enter(
    (string) ($_SERVER['PHP_SELF'] ?? ''),
    '#main',
    [],
    $navState,
);

$params = UbisPayload::postData($_POST);
if (!ListNavSupport::idSelIsEmpty($Qid_sel)) {
    $params['id_sel'] = PayloadCoercion::string(is_array($Qid_sel) ? implode(',', $Qid_sel) : $Qid_sel);
}
if ($Qscroll_id !== '') {
    $params['scroll_id'] = PayloadCoercion::string($Qscroll_id);
}

$tabla = UbisPayload::tablaFromPayload(UbisPayload::postData(PostRequest::getDataFromUrl('/src/ubis/ubis_tabla_data', $params)));

ListNavSupport::syncNavStateAt($oPosicion, 1, $tabla['go_back']);

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
    'oPosicion' => $oPosicion,
    'oHash' => $oHash,
    'titulo' => $tabla['titulo'],
    'oTabla' => $oTabla,
    'nueva_ficha' => $tabla['nueva_ficha'],
    'pagina_link' => $pagina_link,
];

$oView = new ViewNewPhtml('frontend\ubis\controller');
$oView->renderizar('ubis_tabla.phtml', $a_campos);
