<?php
/**
* En el fichero config tenemos las variables genéricas del sistema
*/
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

if (!empty($_POST['sel'])) { //vengo de un checkbox
	$s_pkey=$_POST['sel'][0];
	$s_pkey=strtok($s_pkey,'#');
} else {
	empty($_POST['s_pkey'])? $s_pkey="" : $s_pkey=$_POST['s_pkey'];
}
$a_pkey=unserialize(core\urlsafe_b64decode($s_pkey));

/***************  datos  **********************************/
$padre='datos_update'; // para indicarle al $dir_datos lo que quiero.

include(core\ConfigGlobal::$directorio.'/'.$_POST['datos_tabla']);
// En el caso de aop, la base de datos és distinta. Debo incluir en $_POST['datos_tabla'] la conexión
/*************** fin datos  **********************************/

//------------ BORRAR --------
if ($_POST['mod']=="eliminar") {
	if ($oFicha->DBEliminar() === false) {
		echo _("Hay un error, no se ha guardado.");
	}
}
//------------ NUEVO --------
if ($_POST['mod']=="nuevo") {
	foreach ($oFicha->getDatosCampos() as $oDatosCampo) {
		$nom_camp=$oDatosCampo->getNom_camp();	
		// si es un checkbox y está vacío, no pasa nada
		$tipo=$oDatosCampo->getTipo();	
		if ($tipo=='check' && empty($_POST[$nom_camp])) $_POST[$nom_camp]='f';
		// si es con decimales, cambio coma por punto
		if ($tipo=='decimal' && !empty($_POST[$nom_camp])) $_POST[$nom_camp]=str_replace(',','.',$_POST[$nom_camp]);
		$oFicha->$nom_camp=$_POST[$nom_camp];
	}
	if ($oFicha->DBGuardar() === false) {
		echo _("Hay un error, no se ha guardado.");
	}
} 
//------------ EDITAR --------
if ($_POST['mod']=="editar") {
	foreach ($oFicha->getDatosCampos() as $oDatosCampo) {
		$nom_camp=$oDatosCampo->getNom_camp();	
		// si es un checkbox y está vacío, no pasa nada
		$tipo=$oDatosCampo->getTipo();	
		if ($tipo=='check' && empty($_POST[$nom_camp])) $_POST[$nom_camp]='f';
		// si es con decimales, cambio coma por punto
		if ($tipo=='decimal' && !empty($_POST[$nom_camp])) $_POST[$nom_camp]=str_replace(',','.',$_POST[$nom_camp]);
		$oFicha->$nom_camp=$_POST[$nom_camp];
	}
	if ($oFicha->DBGuardar() === false) {
		echo _("Hay un error, no se ha guardado.");
	}
}

?>
