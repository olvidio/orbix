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
 * @created 2/01/2020
 */
class GestorCentroEllos Extends GestorCentro {
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
		$this->setNomTabla('cu_centros_dl');
	}


	/* METODES PUBLICS -----------------------------------------------------------*/
	/* METODES PROTECTED --------------------------------------------------------*/

	/* METODES GET i SET --------------------------------------------------------*/

}