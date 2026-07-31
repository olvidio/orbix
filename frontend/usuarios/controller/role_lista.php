<?php

use frontend\usuarios\helpers\UsuariosPayload;
use frontend\shared\helpers\PayloadCoercion;
use frontend\shared\config\AppUrlConfig;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;
use frontend\shared\web\Lista;
use frontend\shared\FrontBootstrap;
use frontend\shared\helpers\ListNavSupport;


require_once 'frontend/shared/FrontBootstrap.php';
$oPosicion = FrontBootstrap::boot();


$restored = ListNavSupport::restoreSelectionFromStackPost();
/** @var string|list<string> $Qid_sel */
$Qid_sel = !ListNavSupport::idSelIsEmpty($restored['id_sel']) ? $restored['id_sel'] : ListNavSupport::idSelFromPost();
$Qscroll_id = $restored['scroll_id'] !== '' ? $restored['scroll_id'] : ListNavSupport::scrollIdFromPost();

$navState = ListNavSupport::mergeSelectionIntoReturnParametros([], $Qid_sel, $Qscroll_id);
$oPosicion->nav()->enter(
    PayloadCoercion::string($_SERVER['PHP_SELF'] ?? ''),
    '#main',
    [],
    $navState,
);
ListNavSupport::syncNavStateAt($oPosicion, 1, []);


$data = UsuariosPayload::postData(PayloadCoercion::stringKeyedArray(PostRequest::getDataFromUrl('/src/usuarios/role_lista')));
$lista = UsuariosPayload::listaFromPayload($data);
$idSelStr = is_array($Qid_sel) ? PayloadCoercion::string($Qid_sel[0] ?? '') : PayloadCoercion::string($Qid_sel);
$a_valores = UsuariosPayload::listaApplyNav($lista['valores'], $idSelStr, PayloadCoercion::string($Qscroll_id));
$permiso = PayloadCoercion::string($data['permiso'] ?? '');

$oTabla = new Lista();
$oTabla->setId_tabla('roles_lista');
$oTabla->setCabeceras($lista['cabeceras']);
$oTabla->setBotones($lista['botones']);
$oTabla->setDatos($a_valores);

$oHash = new HashFront();
$oHash->setCamposForm('sel');
$oHash->setcamposNo('scroll_id');

$url_nuevo = HashFront::link(AppUrlConfig::getPublicAppBaseUrl()
    . '/frontend/usuarios/controller/role_form.php?'
);

$a_campos = [
    'oPosicion' => $oPosicion,
    'oHash' => $oHash,
    'oTabla' => $oTabla,
    'permiso' => $permiso,
    'url_nuevo' => $url_nuevo,
];

$oView = new ViewNewPhtml('frontend\usuarios\controller');
$oView->renderizar('role_lista.phtml', $a_campos);
