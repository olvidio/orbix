<?php
use devel\model\entity as notas;
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

	
$a_sel = (array)  \filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!empty($a_sel)) { //vengo de un checkbox
	// el scroll id es de la página anterior, hay que guardarlo allí
	$oPosicion->addParametro('id_sel',$a_sel,1);
	$scroll_id = (integer) \filter_input(INPUT_POST, 'scroll_id');
	$oPosicion->addParametro('scroll_id',$scroll_id,1);
}

$Qmod = (string) \filter_input(INPUT_POST, 'mod');
$Qid_mod = (integer) \filter_input(INPUT_POST, 'id_mod');

if (!empty($a_sel)) { //vengo de un checkbox (caso de eliminar)
	$Qid_mod=urldecode(strtok($a_sel[0],"#"));
}

$Qnom = (string) \filter_input(\INPUT_POST, 'nom');
$Qdescripcion = (string) \filter_input(\INPUT_POST, 'descripcion');
$Qsel_mods =  \filter_input(\INPUT_POST, 'sel_mods', \FILTER_VALIDATE_INT, \FILTER_REQUIRE_ARRAY);
$Qsel_apps =  \filter_input(\INPUT_POST, 'sel_apps', \FILTER_VALIDATE_INT, \FILTER_REQUIRE_ARRAY);

if(empty($Qsel_mods)) {
	$a_sel_mods = '{}';
} else {
	$a_sel_mods = str_replace(array('[',']'), array('{','}'), json_encode($Qsel_mods));
}
if(empty($Qsel_apps)) {
	$a_sel_apps = '{}';
} else {
	$a_sel_apps = str_replace(array('[',']'), array('{','}'), json_encode($Qsel_apps));
}
 
switch ($Qmod) {
	case 'nuevo':
		$oModulo = new \devel\model\entity\Modulo();
		$oModulo->setNom($Qnom);
		$oModulo->setDescripcion($Qdescripcion);
		$oModulo->setMods_req($a_sel_mods);
		$oModulo->setApps_req($a_sel_apps);
		$oModulo->DBGuardar();
	break;
	case 'eliminar':
		$oModulo = new \devel\model\entity\Modulo($Qid_mod);
		if ($oModulo->DBEliminar() === false) {
			echo _('Hay un error, no se ha guardado');
		}
		break;
	default :
		$oModulo = new \devel\model\entity\Modulo($Qid_mod);
		$oModulo->DBCarregar();
		$oModulo->setNom($Qnom);
		$oModulo->setDescripcion($Qdescripcion);
		$oModulo->setMods_req($a_sel_mods);
		$oModulo->setApps_req($a_sel_apps);
		$oModulo->DBGuardar();
}