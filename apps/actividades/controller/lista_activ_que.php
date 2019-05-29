<?php 
/**
* Página del formulario para listados particulares de sr 
* Llama a la página list_activ.php con las variables:
* $que $seccion, $status[], $asist[], c_activ[], $tit_list_sr,
* $empiezamin, $empiezamax 
*
*@package	delegacion
*@subpackage	actividades
*@author	Josep Companys
*@since		30/9/03.
*		
*/

use web\Hash;

// INICIO Cabecera global de URL de controlador *********************************
require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$oPosicion->recordar();
	
$Qque = (string) \filter_input(INPUT_POST, 'que');
/*
que=list_activ_sr
que=list_activ_inv_sg
*/

$permiso_des = FALSE;
if (($_SESSION['oPerm']->have_perm("vcsd")) or ($_SESSION['oPerm']->have_perm("des"))) {
    $permiso_des = TRUE;
}

$oHash = new Hash();
$oHash->setcamposForm('seccion!status!empiezamin!empiezamax!asist!c_activ!tit_list_grupo');
$a_camposHidden = array(
    'que' => $Qque,
);
$oHash->setArraycamposHidden($a_camposHidden);

switch ($Qque) {
    case "list_activ_sr":
        $titulo = _("datos del listado actividades san rafael");
        $sr_sg = 'sr';
        break;
    case "list_activ_inv_sg":
        $titulo = _("datos del listado actividades san gabriel");
        $sr_sg = 'sg';
        break;
}

$a_campos = ['oPosicion' => $oPosicion,
    'oHash' => $oHash,
    'permiso_des' => $permiso_des,
    'titulo' => $titulo,
    'sr_sg' => $sr_sg,
];

$oView = new core\ViewTwig('actividades/controller');
echo $oView->render('lista_activ_que.html.twig',$a_campos);
