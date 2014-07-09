<?php
namespace ubis\model;
use core;
/**
 * GestorCasaEx
 *
 * Classe per gestionar la llista d'objectes de la clase CasaEx
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 28/09/2010
 */

class GestorCasaEx Extends  GestorCasa {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/* CONSTRUCTOR -------------------------------------------------------------- */

	/**
	 * Constructor de la classe.
	 *
	 * @return GestorCasaEx
	 *
	 */
	function __construct() {
		$oDbl = $GLOBALS['oDBRC'];
		$this->setoDbl($oDbl);
		$this->setNomTabla('u_cdc_ex');
	}


	/* METODES PUBLICS -----------------------------------------------------------*/
	/* METODES PROTECTED --------------------------------------------------------*/

	/* METODES GET i SET --------------------------------------------------------*/

}
?>
