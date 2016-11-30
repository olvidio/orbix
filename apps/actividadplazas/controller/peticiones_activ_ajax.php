<?php
use actividadplazas\model as actividadplazas;
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
$tipo_activ = empty($_POST['tipo_activ'])? '' : $_POST['tipo_activ'];

switch ($_POST['que']) {
	case "update":
		$i = 0;
		foreach ($_POST['actividades'] as $id_activ) {
			$i++;
			if (empty($id_activ)) { continue; }
			$oPlazaPeticion = new actividadplazas\PlazaPeticion(array('id_nom'=>$id_nom, 'id_activ'=>$id_activ));
			$oPlazaPeticion->setOrden($i);
			$oPlazaPeticion->setTipo($tipo_activ);
			$oPlazaPeticion->DBGuardar();
		}
		$oPosicion->setId_div('ir_a');
		echo $oPosicion->atras();
		break;
	case 'borrar';
		$gesPlazasPeticion = new actividadplazas\GestorPlazaPeticion();
		$cPlazasPeticion = $gesPlazasPeticion->getPlazasPeticion(array('id_nom'=>$id_nom, 'tipo'=>$tipo_activ));
		foreach ($cPlazasPeticion as $oPlazaPeticion) {
			$oPlazaPeticion->DBEliminar();
		}
		break;
}