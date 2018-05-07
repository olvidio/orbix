<?php
namespace ubis\model\entity;
use core;
use web;
/**
 * GestorCasaDl
 *
 * Classe per gestionar la llista d'objectes de la clase CasaDl
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 28/09/2010
 */

class GestorCasaDl Extends  GestorCasa {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/* CONSTRUCTOR -------------------------------------------------------------- */

	/**
	 * Constructor de la classe.
	 *
	 * @return GestorCasaDl
	 *
	 */
	function __construct() {
		$oDbl = $GLOBALS['oDBC'];
		$this->setoDbl($oDbl);
		$this->setNomTabla('u_cdc_dl');
	}


	/* METODES PUBLICS -----------------------------------------------------------*/

	/* METODES PROTECTED --------------------------------------------------------*/

	/* METODES GET i SET --------------------------------------------------------*/

}
?>
