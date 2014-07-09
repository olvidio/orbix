<?php
namespace ubis\model;
use core;
/**
 * Classe que implementa l'entitat u_direcciones_global
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 01/10/2010
 */

class Direccion Extends core\ClasePropiedades {
	/* ATRIBUTS ----------------------------------------------------------------- */
	/* ATRIBUTS QUE NO SÓN CAMPS------------------------------------------------- */
	/* CONSTRUCTOR -------------------------------------------------------------- */

	/**
	 * Constructor de la classe vuit.
	 *
	 */
	function __construct($a_id='') {
	}

	/* METODES PUBLICS ----------------------------------------------------------*/
	
	public static function NewDireccion($id_direccion) { 
		$gesDireccion = new GestorDirecccionCtr;
		$cDirecciones = $gesDireccion->getDirecciones(array('id_ubi'=>$id_direccion));
		if (count($cDirecciones) > 0) {
			$oDireccion = $cDirecciones[0];
		} else {
			$oDireccion = new DireccionCdc($id_direccion);
		}
		return $oDireccion;
	}
}
?>
