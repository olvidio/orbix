<?php
namespace actividades\model;
use core;
use web;
/**
 * GestorActividadPub
 *
 * Classe per gestionar la llista d'objectes de la clase ActividadPub
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 01/10/2010
 */

class GestorActividadPub extends GestorActividadAll {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/* CONSTRUCTOR -------------------------------------------------------------- */

	/**
	 * Constructor de la classe.
	 *
	 * @return GestorActividadPub
	 *
	 */
	function __construct() {
		$oDbl = $GLOBALS['oDBPC'];
		$this->setoDbl($oDbl);
		$this->setNomTabla('av_actividades_pub');
	}


	/* METODES PUBLICS -----------------------------------------------------------*/
}
?>
