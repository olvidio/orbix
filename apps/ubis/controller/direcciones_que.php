<?php

use core\ViewPhtml;
use src\ubis\domain\entity\Ubi;
use web\Hash;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qid_ubi = (integer)filter_input(INPUT_POST, 'id_ubi');
$Qobj_dir = (string)filter_input(INPUT_POST, 'obj_dir');

$oUbi = Ubi::newUbi($Qid_ubi);
$nombre_ubi = $oUbi->getNombre_ubi();
$dl = $oUbi->getDl();
$region = $oUbi->getRegion();
$tipo_ubi = $oUbi->getTipo_ubi();

$tituloGros = ucfirst(_("introduzca un valor para buscar una dirección existente"));

$oHash = new Hash();
$oHash->setCamposForm('c_p!ciudad!id_ubi!obj_dir!pais');
$a_camposHidden = array(
    'obj_dir' => $Qobj_dir,
    'id_ubi' => $Qid_ubi
);
$oHash->setArraycamposHidden($a_camposHidden);

$a_campos = ['oPosicion' => $oPosicion,
    'oHash' => $oHash,
    'tipo_ubi' => $tipo_ubi,
];

$oView = new ViewPhtml('ubis\controller');
$oView->renderizar('direcciones_que.phtml', $a_campos);
