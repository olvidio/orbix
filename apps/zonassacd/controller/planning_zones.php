<?php 
use core\ConfigGlobal;
use usuarios\model\entity\Usuario;
use web\Hash;
use zonassacd\model\entity\GestorZona;

/**
* Página que presentará los formularios de los distintos plannings 
* Según sea el submenú seleccionado seleccionará el formulario
* correspondiente
*
*@package	delegacion
*@subpackage	actividades
*@author	Josep Companys
*@since		15/5/02.
*		
*/

// INICIO Cabecera global de URL de controlador *********************************
require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$year=date("Y");
$mes=date("m");
$chk_trim1='';
$chk_trim2='';
$chk_trim3='';
$chk_trim4='';
if ($mes < 4) $chk_trim1='checked';
if ($mes > 3 && $mes < 7) $chk_trim2='checked';
if ($mes > 8 && $mes < 10) $chk_trim3='checked';
if ($mes > 9 && $mes < 13) $chk_trim4='checked';

$id_nom_jefe = '';
$id_usuario = ConfigGlobal::mi_id_usuario();
$oUsuario = new Usuario($id_usuario);
$id_role=$oUsuario->getId_role();

if ($id_role == 7) { //sacd
	$id_nom_jefe=$oUsuario->getId_pau();
	if (ConfigGlobal::is_jefeCalendario()) $id_nom_jefe = '';
}


$GesZonas = new GestorZona();
$oDesplZonas = $GesZonas->getListaZonas($id_nom_jefe);
$oDesplZonas->setBlanco(0);
// miro si se tiene opcion a ver alguna zona. La opcion blanco tiene que ser 0, sino la rta es <option></option>.
$algo = $oDesplZonas->options();
if (strlen($algo) < 1) exit(_('No tiene permiso para ver esta página'));

$perm_des = FALSE;
if (($_SESSION['oPerm']->have_perm("des")) or ($_SESSION['oPerm']->have_perm("vcsd"))) {
    $perm_des = TRUE;
}

$is_jefeCalendario = ConfigGlobal::is_jefeCalendario();

$url = 'apps/zonassacd/controller/planning_zones_crida_calendari.php';

$oHash = new Hash();
$oHash->setUrl($url);
$a_camposHidden = [ 'modelo' => 1 ];
$oHash->setArraycamposHidden($a_camposHidden);
$oHash->setcamposForm('actividad!year!id_zona!trimestre');
$oHash->setCamposNo('modelo');

$a_campos = ['oPosicion' => $oPosicion,
    'oHash' => $oHash,
    'url' => $url,
    'is_jefeCalendario' => $is_jefeCalendario,
    'oDesplZonas' => $oDesplZonas,
    'year' => $year,
    'chk_trim1' => $chk_trim1,
    'chk_trim2' => $chk_trim2,
    'chk_trim3' => $chk_trim3,
    'chk_trim4' => $chk_trim4,
];

$oView = new core\ViewTwig('zonassacd/controller');
echo $oView->render('planning_zones.html.twig',$a_campos);