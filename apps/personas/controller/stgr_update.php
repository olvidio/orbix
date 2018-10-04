<?php
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************


$Qid_nom = (integer) \filter_input(INPUT_POST, 'id_nom');
$Qobj_pau = (string) \filter_input(INPUT_POST, 'obj_pau');
$Qstgr = (string) \filter_input(INPUT_POST, 'stgr');

// según sean numerarios...
$obj = 'personas\\model\\entity\\'.$Qobj_pau;
$oPersona = new $obj($Qid_nom);

$oPersona->DBCarregar();
$oPersona->setStgr($Qstgr);
if ($oPersona->DBGuardar() === false) {
	echo _("hay un error, no se ha guardado");
}

echo $oPosicion->go_atras(1);