<?php
namespace ubis\model\entity;
/**
 * GestorCentro
 *
 * Classe per gestionar la llista d'objectes de la clase Centro
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 28/09/2010
 */
class GestorCentroEllas Extends GestorCentro {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/* CONSTRUCTOR -------------------------------------------------------------- */

	/**
	 * Constructor de la classe.
	 *
	 * @return GestorCentro
	 *
	 */
	function __construct() {
	    $oDbl = $GLOBALS['oDBC'];
	    $this->setoDbl($oDbl);
		$this->setNomTabla('cu_centros_dlf');
	}


	/* METODES PUBLICS -----------------------------------------------------------*/
	/* METODES PROTECTED --------------------------------------------------------*/

	/* METODES GET i SET --------------------------------------------------------*/

}