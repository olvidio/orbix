<?php
namespace actividades\model;
use core;
use web;
/**
 * GestorActividadDl
 *
 * Classe per gestionar la llista d'objectes de la clase ActividadDl
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 01/10/2010
 */

class GestorActividadDl extends GestorActividadAll {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/* CONSTRUCTOR -------------------------------------------------------------- */

	/**
	 * Constructor de la classe.
	 *
	 * @return GestorActividadDl
	 *
	 */
	function __construct() {
		$oDbl = $GLOBALS['oDBC'];
		$this->setoDbl($oDbl);
		$this->setNomTabla('a_actividades_dl');
	}


	/* METODES PUBLICS -----------------------------------------------------------*/

}
?>
