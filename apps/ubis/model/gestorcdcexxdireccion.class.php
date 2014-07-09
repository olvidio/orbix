<?php
namespace ubis\model;
use core;
/**
 * GestorUbixDireccion
 *
 * Classe per gestionar la llista d'objectes de la clase UbixDireccion
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 11/02/2014
 */

class GestorCdcExxDireccion Extends GestorCdcxDireccion {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/* CONSTRUCTOR -------------------------------------------------------------- */

	/**
	 * Constructor de la classe.
	 *
	 * @return GestorCdcxDireccion
	 *
	 */
	function __construct() {
		$oDbl = $GLOBALS['oDBRC'];
		$this->setoDbl($oDbl);
		$this->setNomTabla('u_cross_cdc_ex_dir');
	}

	/* METODES PUBLICS -----------------------------------------------------------*/
	/* METODES PROTECTED --------------------------------------------------------*/
	/* METODES GET i SET --------------------------------------------------------*/
}
?>
