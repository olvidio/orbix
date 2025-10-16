<?php

use core\ConfigGlobal;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use web\Hash;
use web\Lista;

// Crea los objetos de uso global **********************************************
require_once("frontend/shared/global_header_front.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qid_usuario = (integer)filter_input(INPUT_POST, 'id_usuario');
$Qquien = (string)filter_input(INPUT_POST, 'quien');
$Qolvidar = (string)filter_input(INPUT_POST, 'olvidar');

if (empty($Qolvidar)) {
    $oPosicion->recordar();
}

$url_backend = '/src/usuarios/infrastructure/controllers/perm_activ_lista.php';
$a_campos = ['id_usuario' => $Qid_usuario];
$data = PostRequest::getDataFromUrl($url_backend, $a_campos);

$a_cabeceras = $data['a_cabeceras'];
$a_botones = $data['a_botones'];
$a_valores = $data['a_valores'];


$oHash3 = new Hash();
$oHash3->setCamposForm('que!sel');
$oHash3->setcamposNo('sel!refresh!scroll_id');
$a_camposHidden = array(
    'id_usuario' => $Qid_usuario,
    'quien' => $Qquien
);
$oHash3->setArraycamposHidden($a_camposHidden);
$oHash3->setPrefix('perm'); // prefijo par el id.

$oTabla = new Lista();
$oTabla->setId_tabla('perm_activ_lista');
$oTabla->setCabeceras($a_cabeceras);
$oTabla->setBotones($a_botones);
$oTabla->setDatos($a_valores);

$a_campos = [
    'oPosicion' => $oPosicion,
    'oHash' => $oHash3,
    'oTabla' => $oTabla,
];

$oView = new ViewNewPhtml('frontend\usuarios\controller');
$oView->renderizar('perm_activ_lista.phtml', $a_campos);
