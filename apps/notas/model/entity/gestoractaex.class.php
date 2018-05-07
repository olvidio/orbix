<?php
namespace notas\model\entity;
use core;
/**
 * GestorActa
 *
 * Classe per gestionar la llista d'objectes de la clase Acta
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 07/04/2014
 */

class GestorActaEx Extends GestorActa {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/* CONSTRUCTOR -------------------------------------------------------------- */


	/**
	 * Constructor de la classe.
	 *
	 * @return $gestor
	 *
	 */
	function __construct() {
		$oDbl = $GLOBALS['oDBR'];
		$this->setoDbl($oDbl);
		$this->setNomTabla('e_actas_ex');
	}


	/* METODES PUBLICS -----------------------------------------------------------*/

	/* METODES PROTECTED --------------------------------------------------------*/

	/* METODES GET i SET --------------------------------------------------------*/
}
?>
