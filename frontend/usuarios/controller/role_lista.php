<?php

use frontend\actividades\helpers\ActividadesPostInput;
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


$Qid_sel = (string)filter_input(INPUT_POST, 'id_sel');
$Qscroll_id = (string)filter_input(INPUT_POST, 'scroll_id');
if (isset($_POST['stack'])) {
    $stack = (int)filter_input(INPUT_POST, 'stack', FILTER_SANITIZE_NUMBER_INT);
    if ($stack !== 0) {
        $oPosicion2 = new frontend\shared\web\Posicion();
        if ($oPosicion2->goStack($stack)) {
            $Qid_sel = ActividadesPostInput::posicionString($oPosicion2->getParametro('id_sel'));
            $Qscroll_id = ActividadesPostInput::posicionString($oPosicion2->getParametro('scroll_id'));
            $oPosicion2->olvidar($stack);
        }
    }
}
ListNavSupport::bootRecordar($oPosicion);
ListNavSupport::persistRecordarEntry($oPosicion, ListNavSupport::mergeSelectionForRecordar(ListNavSupport::buildReturnParametrosFromPost(), $Qid_sel, $Qscroll_id));


$data = UsuariosPayload::postData(PostRequest::getDataFromUrl('/src/usuarios/role_lista'));
$lista = UsuariosPayload::listaFromPayload($data);
$a_valores = UsuariosPayload::listaApplyNav($lista['valores'], $Qid_sel, $Qscroll_id);
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
    'oHash' => $oHash,
    'oTabla' => $oTabla,
    'permiso' => $permiso,
    'url_nuevo' => $url_nuevo,
];

$oView = new ViewNewPhtml('frontend\usuarios\controller');
$oView->renderizar('role_lista.phtml', $a_campos);
