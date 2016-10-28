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


$id_nom = empty($_POST['id_nom'])? '' : $_POST['id_nom'];
$obj_pau = empty($_POST['obj_pau'])? '' : $_POST['obj_pau'];
$stgr = empty($_POST['stgr'])? '' : $_POST['stgr'];

// según sean numerarios...
$obj = 'personas\\model\\'.$obj_pau;
$oPersona = new $obj($id_nom);

$oPersona->DBCarregar();
$oPersona->setStgr($stgr);
if ($oPersona->DBGuardar() === false) {
	echo _('Hay un error, no se ha guardado');
}

echo $oPosicion->go_atras();
?>
