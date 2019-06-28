<?php
use casas\model\entity\Ingreso;

// INICIO Cabecera global de URL de controlador *********************************
require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ****


$Qque = (string) \filter_input(INPUT_POST, 'que');

switch ($Qque) {
	case "update":
	    $data = (string)  filter_input(INPUT_POST, 'data');
	    $colName = (string)  filter_input(INPUT_POST, 'colName');
	    $obj = json_decode($data);
	    //print_r($obj);
	    $dl = json_decode($colName);
	    //print_r($dl);
	    $id_activ =$obj->id;
	    $plazas_previstas =$obj->$dl;
	    
		$oIngreso = new Ingreso($id_activ);
		$oIngreso->DBCarregar();
		$oIngreso->setNum_asistentes_previstos($plazas_previstas);
		if ($oIngreso->DBGuardar() === false) {
			echo _('Hay un error, no se ha guardado');
		}
		break;
}
