<?php
namespace personas\model\entity;
/**
 * GestorTelecoPersona
 *
 * Classe per gestionar la llista d'objectes de la clase TelecoPersona
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 01/10/2010
 */

class GestorTelecoPersonaDl Extends GestorTelecoPersona {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/* CONSTRUCTOR -------------------------------------------------------------- */

	/**
	 * Constructor de la classe.
	 *
	 * @return GestorTelecoPersona
	 *
	 */
	function __construct() {
		$oDbl = $GLOBALS['oDB'];
		$this->setoDbl($oDbl);
		$this->setNomTabla('d_teleco_personas_dl');
	}
	/* METODES PUBLICS -----------------------------------------------------------*/
	/* METODES PROTECTED --------------------------------------------------------*/
	/* METODES GET i SET --------------------------------------------------------*/
}
?>
