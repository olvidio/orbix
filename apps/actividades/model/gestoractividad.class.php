<?php
namespace actividades\model;
use core;
use web;
/**
 * GestorActividad
 *
 * Classe per gestionar la llista d'objectes de la clase Actividad
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 01/10/2010
 */

class GestorActividad extends GestorActividadAll {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/* CONSTRUCTOR -------------------------------------------------------------- */

	/**
	 * Constructor de la classe.
	 *
	 * @return GestorActividad
	 *
	 */
	function __construct() {
		$oDbl = $GLOBALS['oDBC'];
		$this->setoDbl($oDbl);
		$this->setNomTabla('av_actividades_pub');
	}


	/* METODES PUBLICS -----------------------------------------------------------*/

}
?>
