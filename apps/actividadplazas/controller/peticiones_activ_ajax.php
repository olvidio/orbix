<?php
/**
 * Controlador para las peticiones ajax desde peticiones_activ.php
 * 
 * acción (que): update|borrar
 */
use actividadplazas\model\entity as actividadplazas;

// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qid_nom = (integer) \filter_input(INPUT_POST, 'id_nom');
$Qsactividad = (string) \filter_input(INPUT_POST, 'sactividad');
$Qque = (string) \filter_input(INPUT_POST, 'que');

switch ($Qque) {
	case "update":
	    // borro todo y grabo lo nuevo:
		$gesPlazasPeticion = new actividadplazas\GestorPlazaPeticion();
		$cPlazasPeticion = $gesPlazasPeticion->getPlazasPeticion(array('id_nom'=>$Qid_nom, 'tipo'=>$Qsactividad));
		foreach ($cPlazasPeticion as $oPlazaPeticion) {
			$oPlazaPeticion->DBEliminar();
		}
		// grabar
		$i = 0;
		$a_actividades = (array)  \filter_input(INPUT_POST, 'actividades', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
		foreach ($a_actividades as $id_activ) {
			if (empty($id_activ)) { continue; }
			$i++;
			$oPlazaPeticion = new actividadplazas\PlazaPeticion(array('id_nom'=>$Qid_nom, 'id_activ'=>$id_activ));
			$oPlazaPeticion->setOrden($i);
			$oPlazaPeticion->setTipo($Qsactividad);
			$oPlazaPeticion->DBGuardar();
		}
		echo $oPosicion->go_atras(1);
		break;
	case 'borrar';
		$gesPlazasPeticion = new actividadplazas\GestorPlazaPeticion();
		$cPlazasPeticion = $gesPlazasPeticion->getPlazasPeticion(array('id_nom'=>$Qid_nom, 'tipo'=>$Qsactividad));
		foreach ($cPlazasPeticion as $oPlazaPeticion) {
			$oPlazaPeticion->DBEliminar();
		}
		break;
}