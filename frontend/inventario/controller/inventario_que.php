<?php

use core\ConfigGlobal;
use frontend\shared\model\ViewNewPhtml;
use web\Hash;

// Crea los objetos de uso global **********************************************
require_once("frontend/shared/global_header_front.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$oPosicion->recordar();

// 12
$url_ctr = ConfigGlobal::getWeb() . '/frontend/inventario/controller/doc_de_ctr.php?';
// 13
$url_dlb = ConfigGlobal::getWeb() . '/frontend/inventario/controller/doc_de_dlb.php?';

$oHash = new Hash();
$oHash->setArrayCamposHidden(['inventario' => 1]);

$a_campos = [
    'oHash' => $oHash,
    'url_ctr' => $url_ctr,
    'url_dlb' => $url_dlb,
];

$oView = new ViewNewPhtml('frontend\inventario\controller');
$oView->renderizar('inventario_que.phtml', $a_campos);

