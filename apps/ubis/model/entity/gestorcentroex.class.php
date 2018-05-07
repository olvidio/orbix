<?php
namespace ubis\model\entity;
/**
 * GestorCentroEx
 *
 * Classe per gestionar la llista d'objectes de la clase CentroEx
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 28/09/2010
 */

class GestorCentroEx Extends GestorCentro {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/* CONSTRUCTOR -------------------------------------------------------------- */

	/**
	 * Constructor de la classe.
	 *
	 * @return GestorCentroEx
	 *
	 */
	function __construct() {
		$oDbl = $GLOBALS['oDBR'];
		$this->setoDbl($oDbl);
		$this->setNomTabla('u_centros_ex');
	}

	/* METODES PUBLICS -----------------------------------------------------------*/
	/* METODES PROTECTED --------------------------------------------------------*/

	/* METODES GET i SET --------------------------------------------------------*/

}
?>
