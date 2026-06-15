<?php

use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;
use frontend\shared\web\Lista;
use frontend\shared\FrontBootstrap;

require_once __DIR__ . '/../helpers/usuarios_support.php';
require_once 'frontend/shared/FrontBootstrap.php';
require_once __DIR__ . '/../../shared/helpers/list_nav_support.php';
$oPosicion = FrontBootstrap::boot();

$Qid_usuario = (integer)filter_input(INPUT_POST, 'id_usuario');
$Qquien = (string)filter_input(INPUT_POST, 'quien');
$Qolvidar = (string)filter_input(INPUT_POST, 'olvidar');

if (empty($Qolvidar)) {
    list_nav_boot_recordar($oPosicion);
    list_nav_persist_recordar_entry($oPosicion, list_nav_build_return_parametros_from_post());

}

$data = usuarios_post_data(PostRequest::getDataFromUrl('/src/usuarios/perm_activ_lista', ['id_usuario' => $Qid_usuario]));
$lista = usuarios_lista_from_payload($data);

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
