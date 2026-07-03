<?php

use frontend\ubis\helpers\UbisPayload;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;
use frontend\shared\web\Lista;
use frontend\shared\FrontBootstrap;
use frontend\shared\helpers\ListNavSupport;

require_once 'frontend/shared/FrontBootstrap.php';
$oPosicion = FrontBootstrap::boot();
$oPosicion->setBloque('#ficha');
\frontend\shared\helpers\ListNavSupport::bootRecordar($oPosicion);
\frontend\shared\helpers\ListNavSupport::persistRecordarEntry($oPosicion, \frontend\shared\helpers\ListNavSupport::buildReturnParametrosFromPost());


$Qobj_pau = (string)filter_input(INPUT_POST, 'obj_pau');
$Qid_ubi = (int)filter_input(INPUT_POST, 'id_ubi');

$payload = UbisPayload::postData(PostRequest::getDataFromUrl('/src/ubis/teleco_tabla', [
    'obj_pau' => $Qobj_pau,
    'id_ubi' => $Qid_ubi,
]));
$data = UbisPayload::telecoFromPayload($payload);
$lista = UbisPayload::listaFromPayload($payload);

$oTabla = new Lista();
$oTabla->setId_tabla('telecos_tabla');
$oTabla->setCabeceras($lista['cabeceras']);
$oTabla->setBotones($lista['botones']);
$oTabla->setDatos($lista['valores']);

$oHash = new HashFront();
$oHash->setCamposForm('mod!sel');
$oHash->setcamposNo('mod!sel!scroll_id!refresh');
$oHash->setArraycamposHidden([
    'id_ubi' => $Qid_ubi,
    'obj_pau' => $Qobj_pau,
]);

$a_campos = [
    'botones' => $data['botones'],
    'oPosicion' => $oPosicion,
    'oHash' => $oHash,
    'ficha' => $data['ficha'],
    'tit_txt' => $data['tit_txt'],
    'oTabla' => $oTabla,
    'url_editar' => 'frontend/ubis/controller/teleco_editar.php',
    'url_eliminar' => 'src/ubis/teleco_eliminar',
    'url_tabla' => 'frontend/ubis/controller/teleco_tabla.php',
];

$oView = new ViewNewPhtml('frontend\ubis\controller');
$oView->renderizar('teleco_tabla.phtml', $a_campos);
