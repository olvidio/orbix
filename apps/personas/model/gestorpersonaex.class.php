<?php
namespace personas\model;
/**
 * GestorPersonaEx
 *
 * Classe per gestionar la llista d'objectes de la clase PersonaEx
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 11/03/2014
 */

class GestorPersonaEx Extends GestorPersonaGlobal {
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
		$this->setNomTabla('p_de_paso_ex');
	}


	/* METODES PUBLICS -----------------------------------------------------------*/

	/* METODES PROTECTED --------------------------------------------------------*/

	/* METODES GET i SET --------------------------------------------------------*/
}
?>
