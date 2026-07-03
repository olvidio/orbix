<?php

use frontend\actividades\helpers\ActividadesPostInput;
use frontend\usuarios\helpers\UsuariosPayload;
use frontend\shared\AppInstalled;
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

$data = UsuariosPayload::postData(PostRequest::getDataFromUrl('/src/usuarios/grupo_lista', ['username' => $Qusername]));
$lista = UsuariosPayload::listaFromPayload($data);
$a_valores = UsuariosPayload::listaApplyNav($lista['valores'], $Qid_sel, $Qscroll_id);

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
