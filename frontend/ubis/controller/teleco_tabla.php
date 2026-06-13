<?php

use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;
use frontend\shared\web\Lista;
use frontend\shared\FrontBootstrap;

require_once __DIR__ . '/../helpers/ubis_support.php';
require_once 'frontend/shared/FrontBootstrap.php';
require_once __DIR__ . '/../../shared/helpers/list_nav_support.php';

$oPosicion = FrontBootstrap::boot();
$oPosicion->setBloque('#ficha');
$oPosicion->recordar();
list_nav_persist_recordar_entry($oPosicion, list_nav_build_return_parametros_from_post());


$Qobj_pau = (string)filter_input(INPUT_POST, 'obj_pau');
$Qid_ubi = (int)filter_input(INPUT_POST, 'id_ubi');

$payload = ubis_post_data(PostRequest::getDataFromUrl('/src/ubis/teleco_tabla', [
    'obj_pau' => $Qobj_pau,
    'id_ubi' => $Qid_ubi,
]));
$data = ubis_teleco_from_payload($payload);
$lista = ubis_lista_from_payload($payload);

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
