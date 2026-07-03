<?php

use frontend\actividades\helpers\ActividadesPostInput;
use frontend\usuarios\helpers\UsuariosPayload;
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


$Qusername = (string)filter_input(INPUT_POST, 'username');
$oPosicion->setParametros(array('username' => $Qusername), 1);

$data = UsuariosPayload::postData(PostRequest::getDataFromUrl('/src/usuarios/usuario_lista', ['username' => $Qusername]));
if (!empty($data['error'])) {
   exit($data['error']);
}

$lista = UsuariosPayload::listaFromPayload($data);
$a_valores = UsuariosPayload::listaApplyNav($lista['valores'], $Qid_sel, $Qscroll_id);

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
$url_eliminar = HashFront::link(AppUrlConfig::getPublicAppBaseUrl()
    . '/src/usuarios/usuario_eliminar'
);

$a_campos = [
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
