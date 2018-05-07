<?php
namespace ubis\model\entity;
/**
 * GestorDireccionCtrEx
 *
 * Classe per gestionar la llista d'objectes de la clase DireccionCtrEx
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 01/10/2010
 */

class GestorDireccionCtrEx Extends GestorDireccionCtr {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/* CONSTRUCTOR -------------------------------------------------------------- */

	/**
	 * Constructor de la classe.
	 *
	 * @return GestorDireccionCtrEx
	 *
	 */
	function __construct() {
		$oDbl = $GLOBALS['oDBR'];
		$this->setoDbl($oDbl);
		$this->setNomTabla('u_dir_ctr_ex');
	}

	/* METODES PUBLICS -----------------------------------------------------------*/

	/* METODES PROTECTED --------------------------------------------------------*/

	/* METODES GET i SET --------------------------------------------------------*/

}
?>
