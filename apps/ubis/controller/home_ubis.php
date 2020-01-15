<?php

use core\ConfigGlobal;
use ubis\model\entity as ubis;
use web\Hash;
use web\Posicion;
/**
* Para asegurar que inicia la sesion, y poder acceder a los permisos
*/
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

//En el caso de modificar cartas de presentación, quiero que quede dentro del bloque.
$oPosicion->recordar();
$bloque = (string) \filter_input(INPUT_POST, 'bloque');
if (!empty($bloque)) {
    $oPosicion->setBloque("#$bloque");
    $oPosicion->addParametro('bloque', $bloque);
}
$bloque = 'ficha';
	
$a_sel = (array)  \filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

//Si vengo por medio de Posicion, borro la última
if (isset($_POST['stack'])) {
	$stack = \filter_input(INPUT_POST, 'stack', FILTER_SANITIZE_NUMBER_INT);
	if ($stack != '') {
		// No me sirve el de global_object, sino el de la session
		$oPosicion2 = new Posicion();
		if ($oPosicion2->goStack($stack)) { // devuelve false si no puede ir
			$obj_pau = $oPosicion2->getParametro('obj_pau');
			$id_ubi = $oPosicion2->getParametro('id_ubi');
			$Qid_sel=$oPosicion2->getParametro('id_sel');
			$Qscroll_id = $oPosicion2->getParametro('scroll_id');
			$oPosicion2->olvidar($stack);
		}
	}
} 

// el scroll id es de la página anterior, hay que guardarlo allí
if (!empty($a_sel)) { //vengo de un checkbox
    $id_ubi= (integer) strtok($a_sel[0],"#");
	$oPosicion->addParametro('id_sel',$a_sel,1);
	$scroll_id = (integer) \filter_input(INPUT_POST, 'scroll_id');
	$oPosicion->addParametro('scroll_id',$scroll_id,1);
} else {
	$id_ubi = (integer) \filter_input(INPUT_POST, 'id_ubi');
}

$oUbi = ubis\Ubi::NewUbi($id_ubi);
$nombre_ubi=$oUbi->getNombre_ubi();
$dl=$oUbi->getDl();
/* TODO no sé
// para el caso de sf, podria ser que en el campo dl, se ponga 'dlbf' y no 'dlb'
if (substr($dl, -1) == 'f') {
    $dl = substr($dl,0,-1); // quito la f.
}
*/
$region=$oUbi->getRegion();
$tipo_ubi=$oUbi->getTipo_ubi();

$cDirecciones = $oUbi->getDirecciones();
$d = 0;
$direccion = '';
$poblacion = '';
$c_p = '';
$id_direccion = '';
foreach ($cDirecciones as $oDireccion) {
	$d++;
	if ($d > 1) {
		$direccion .= '<br>';
		$poblacion .= '<br>';
		$c_p .= '<br>';
		$id_direccion .= ',';
	}
	$direccion .= $oDireccion->getDireccion();
	$poblacion .= $oDireccion->getPoblacion();
	$c_p .= $oDireccion->getC_p();
	$id_direccion .= $oDireccion->getId_direccion();
}
$id_pau=$id_ubi;
$pau="u";

$mi_dele = ConfigGlobal::mi_delef();
switch ($tipo_ubi) {
	case "ctrsf":
	case "ctrdl":
		if ($dl != $mi_dele) {
			$obj_pau="Centro";
			$obj_dir="DireccionCtr";
		} else {
			$obj_pau="CentroDl";
			$obj_dir="DireccionCtrDl";
		}
		$ubi=_("centro");
		$tipo="ctr";
		break;
	case "ctrex":
		$obj_pau="CentroEx";
		$obj_dir="DireccionCtrEx";
		$ubi=_("centro");
		$tipo="ctr";
		break;
	case "cdcdl":
		if ($dl != $mi_dele) {
			$obj_pau="Casa";
			$obj_dir="DireccionCdc";
		} else {
			$obj_pau="CasaDl";
			$obj_dir="DireccionCdcDl";
		}
		$ubi=_("casa");
		$tipo="cdc";
		break;
	case "cdcex":
		$obj_pau="CasaEx";
		$obj_dir="DireccionCdcEx";
		$ubi=_("casa");
		$tipo="cdc";
		break;
	default:
		exit( _("falta definir el tipo_ubi"));
}

$gohome=Hash::link('apps/ubis/controller/home_ubis.php?'.http_build_query(array('id_ubi'=>$id_ubi,'obj_pau'=>$obj_pau))); 
$godossiers=Hash::link('apps/dossiers/controller/dossiers_ver.php?'.http_build_query(array('pau'=>$pau,'id_pau'=>$id_pau,'obj_pau'=>$obj_pau)));

$go_ubi=Hash::link('apps/ubis/controller/ubis_editar.php?'.http_build_query(array('id_ubi'=>$id_ubi,'obj_pau'=>$obj_pau,'bloque'=>$bloque)));
$go_dir=Hash::link('apps/ubis/controller/direcciones_editar.php?'.http_build_query(array('id_ubi'=>$id_ubi,'id_direccion'=>$id_direccion,'obj_dir'=>$obj_dir,'bloque'=>$bloque))); 
$go_tel=Hash::link('apps/ubis/controller/teleco_tabla.php?'.http_build_query(array('id_ubi'=>$id_ubi,'obj_pau'=>$obj_pau,'bloque'=>$bloque)));

$alt=_("ver dossiers");
$dos=_("dossiers");
$txt=ucfirst(_("formato texto"));
$titulo=$nombre_ubi;

$telfs = $oUbi->getTeleco("telf","*"," / ") ;
$fax = $oUbi->getTeleco("fax","*"," / ") ;
$mails = $oUbi->getTeleco("e-mail","*"," / ") ;

$a_campos = ['oPosicion' => $oPosicion,
			'godossiers' => $godossiers,
			'alt' => $alt,
			'dos' => $dos,
			'gohome' => $gohome,
			'titulo' => $titulo,
			'dl' => $dl,
			'region' => $region,
			'direccion' => $direccion,
			'c_p' => $c_p,
			'poblacion' => $poblacion,
			'telfs' => $telfs,
			'fax' => $fax,
			'mails' => $mails,
			'go_ubi' => $go_ubi,
			'ubi' => $ubi,
			'go_dir' => $go_dir,
			'go_tel' => $go_tel,
			'pau' => $pau,
			'id_pau'=>$id_pau,
			'Qobj_pau'=>$obj_pau
 			];

$oView = new core\View('ubis/controller');
echo $oView->render('home_ubis.phtml',$a_campos);
