<?php
namespace ubis\model\entity;
/**
 * GestorTelecoUbi
 *
 * Classe per gestionar la llista d'objectes de la clase TelecoUbi
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 01/10/2010
 */

class GestorTelecoCdcEx Extends GestorTelecoCdc {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/* CONSTRUCTOR -------------------------------------------------------------- */

	/**
	 * Constructor de la classe.
	 *
	 * @return GestorTelecoUbi
	 *
	 */
	function __construct() {
		$oDbl = $GLOBALS['oDBRC'];
		$this->setoDbl($oDbl);
		$this->setNomTabla('d_teleco_cdc_ex');
	}
	/* METODES PUBLICS -----------------------------------------------------------*/
	/* METODES PROTECTED --------------------------------------------------------*/
	/* METODES GET i SET --------------------------------------------------------*/
}
?>
