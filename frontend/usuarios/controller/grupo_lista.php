<?php

use frontend\usuarios\helpers\UsuariosPayload;
use frontend\shared\AppInstalled;
use frontend\shared\config\AppUrlConfig;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;
use frontend\shared\web\Lista;
use frontend\shared\FrontBootstrap;
use frontend\shared\helpers\ListNavSupport;
use frontend\shared\helpers\PayloadCoercion;


require_once 'frontend/shared/FrontBootstrap.php';
$oPosicion = FrontBootstrap::boot();


$restored = ListNavSupport::restoreSelectionFromStackPost();
/** @var string|list<string> $Qid_sel */
$Qid_sel = !ListNavSupport::idSelIsEmpty($restored['id_sel']) ? $restored['id_sel'] : ListNavSupport::idSelFromPost();
$Qscroll_id = $restored['scroll_id'] !== '' ? $restored['scroll_id'] : ListNavSupport::scrollIdFromPost();

$Qusername = (string)filter_input(INPUT_POST, 'username');

$filterState = [
    'username' => $Qusername,
    'quien' => 'grupo',
];
$navState = ListNavSupport::mergeSelectionIntoReturnParametros($filterState, $Qid_sel, $Qscroll_id);
$oPosicion->nav()->enter(
    PayloadCoercion::string($_SERVER['PHP_SELF'] ?? ''),
    '#main',
    [],
    $navState,
);
ListNavSupport::syncNavStateAt($oPosicion, 1, $filterState);

$data = UsuariosPayload::postData(PayloadCoercion::stringKeyedArray(PostRequest::getDataFromUrl('/src/usuarios/grupo_lista', ['username' => $Qusername])));
$lista = UsuariosPayload::listaFromPayload($data);
$idSelStr = is_array($Qid_sel) ? PayloadCoercion::string($Qid_sel[0] ?? '') : PayloadCoercion::string($Qid_sel);
$a_valores = UsuariosPayload::listaApplyNav($lista['valores'], $idSelStr, PayloadCoercion::string($Qscroll_id));

$oTabla = new Lista();
$oTabla->setId_tabla('grupo_lista');
$oTabla->setCabeceras($lista['cabeceras']);
$oTabla->setBotones($lista['botones']);
$oTabla->setDatos($a_valores);

$oHashBuscar = new HashFront();
$oHashBuscar->setCamposForm('username');
$oHashBuscar->setcamposNo('scroll_id');
$oHashBuscar->setArraycamposHidden(array('quien' => 'grupo'));

$oHashSelect = new HashFront();
$oHashSelect->setCamposForm('sel');
$oHashSelect->setcamposNo('scroll_id');
$oHashSelect->setArraycamposHidden(array('que' => 'eliminar_grupo'));

$aQuery = ['nuevo' => 1, 'quien' => 'grupo'];
$url_nuevo = HashFront::link(AppUrlConfig::getPublicAppBaseUrl()
    . '/frontend/usuarios/controller/grupo_form.php?'
    . http_build_query($aQuery));

$a_campos = [
    'oPosicion' => $oPosicion,
    'procesos_installed' => AppInstalled::is('procesos'),
    'txt_nuevo_grupo' => mb_strtoupper(_("nuevo grupo"), 'UTF-8'),
    'oHashBuscar' => $oHashBuscar,
    'username' => $Qusername,
    'oHashSelect' => $oHashSelect,
    'oTabla' => $oTabla,
    'url_nuevo' => $url_nuevo,
];

$oView = new ViewNewPhtml('frontend\usuarios\controller');
$oView->renderizar('grupo_lista.phtml', $a_campos);
