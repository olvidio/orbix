<?php

use frontend\usuarios\helpers\UsuariosPayload;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;
use frontend\shared\web\Lista;
use frontend\shared\FrontBootstrap;
use frontend\shared\helpers\ListNavSupport;

require_once 'frontend/shared/FrontBootstrap.php';
$oPosicion = FrontBootstrap::boot();

$Qid_usuario = (integer)filter_input(INPUT_POST, 'id_usuario');
$Qquien = (string)filter_input(INPUT_POST, 'quien');
$Qolvidar = (string)filter_input(INPUT_POST, 'olvidar');

if (empty($Qolvidar)) {
    \frontend\shared\helpers\ListNavSupport::bootRecordar($oPosicion);
    \frontend\shared\helpers\ListNavSupport::persistRecordarEntry($oPosicion, \frontend\shared\helpers\ListNavSupport::buildReturnParametrosFromPost());

}

$data = UsuariosPayload::postData(PostRequest::getDataFromUrl('/src/usuarios/perm_activ_lista', ['id_usuario' => $Qid_usuario]));
$lista = UsuariosPayload::listaFromPayload($data);

$oHash3 = new HashFront();
$oHash3->setCamposForm('que!sel');
$oHash3->setcamposNo('sel!refresh!scroll_id');
$a_camposHidden = array(
    'id_usuario' => $Qid_usuario,
    'quien' => $Qquien
);
$oHash3->setArraycamposHidden($a_camposHidden);
$oHash3->setPrefix('perm');

$oTabla = new Lista();
$oTabla->setId_tabla('perm_activ_lista');
$oTabla->setCabeceras($lista['cabeceras']);
$oTabla->setBotones($lista['botones']);
$oTabla->setDatos($lista['valores']);

$a_campos = [
    'oPosicion' => $oPosicion,
    'oHash' => $oHash3,
    'oTabla' => $oTabla,
];

$oView = new ViewNewPhtml('frontend\usuarios\controller');
$oView->renderizar('perm_activ_lista.phtml', $a_campos);
