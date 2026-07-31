<?php

use frontend\usuarios\helpers\UsuariosPayload;
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
    'quien' => 'usuario',
];
$navState = ListNavSupport::mergeSelectionIntoReturnParametros($filterState, $Qid_sel, $Qscroll_id);
$oPosicion->nav()->enter(
    PayloadCoercion::string($_SERVER['PHP_SELF'] ?? ''),
    '#main',
    [],
    $navState,
);
ListNavSupport::syncNavStateAt($oPosicion, 1, $filterState);

$data = UsuariosPayload::postData(PayloadCoercion::stringKeyedArray(PostRequest::getDataFromUrl('/src/usuarios/usuario_lista', ['username' => $Qusername])));
if (!empty($data['error'])) {
   exit(PayloadCoercion::string($data['error']));
}

$lista = UsuariosPayload::listaFromPayload($data);
$idSelStr = is_array($Qid_sel) ? PayloadCoercion::string($Qid_sel[0] ?? '') : PayloadCoercion::string($Qid_sel);
$a_valores = UsuariosPayload::listaApplyNav($lista['valores'], $idSelStr, PayloadCoercion::string($Qscroll_id));

$oTabla = new Lista();
$oTabla->setId_tabla('usuario_lista');
$oTabla->setCabeceras($lista['cabeceras']);
$oTabla->setBotones($lista['botones']);
$oTabla->setDatos($a_valores);

$oHash = new HashFront();
$oHash->setCamposForm('username');
$oHash->setcamposNo('scroll_id');
$oHash->setArraycamposHidden(array('quien' => 'usuario'));

$oHash1 = new HashFront();
$oHash1->setCamposForm('sel');
$oHash1->setcamposNo('scroll_id');
$oHash1->setArraycamposHidden(array('que' => 'eliminar'));

$aQuery = ['nuevo' => 1, 'quien' => 'usuario'];
$url_nuevo = HashFront::link(AppUrlConfig::getPublicAppBaseUrl()
    . '/frontend/usuarios/controller/usuario_form.php?'
    . http_build_query($aQuery)
);

$url_lista = HashFront::link(AppUrlConfig::getPublicAppBaseUrl()
    . '/frontend/usuarios/controller/usuario_lista.php'
);
$url_eliminar = HashFront::link(AppUrlConfig::srcBrowserUrl('/src/usuarios/usuario_eliminar')
);

$a_campos = [
    'oPosicion' => $oPosicion,
    'oHash' => $oHash,
    'username' => $Qusername,
    'oHash1' => $oHash1,
    'oTabla' => $oTabla,
    'url_lista' => $url_lista,
    'url_nuevo' => $url_nuevo,
    'url_eliminar' => $url_eliminar,
];

$oView = new ViewNewPhtml('frontend\usuarios\controller');
$oView->renderizar('usuario_lista.phtml', $a_campos);
