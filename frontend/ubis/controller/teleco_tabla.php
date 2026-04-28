<?php

use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;
use frontend\shared\web\Lista;

require_once("frontend/shared/global_header_front.inc");

$oPosicion->setBloque('#ficha');
$oPosicion->recordar();

$Qobj_pau = (string)filter_input(INPUT_POST, 'obj_pau');
$Qid_ubi = (int)filter_input(INPUT_POST, 'id_ubi');

$data = PostRequest::getDataFromUrl('/src/ubis/teleco_tabla', [
    'obj_pau' => $Qobj_pau,
    'id_ubi' => $Qid_ubi,
]);

$oTabla = new Lista();
$oTabla->setId_tabla('telecos_tabla');
$oTabla->setCabeceras($data['a_cabeceras']);
$oTabla->setBotones($data['a_botones']);
$oTabla->setDatos($data['a_valores']);

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
