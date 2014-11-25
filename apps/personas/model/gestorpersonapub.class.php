<?php
namespace personas\model;
/**
 * GestorPersonaPub
 *
 * Classe per gestionar la llista d'objectes de la clase PersonaPub
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 11/03/2014
 */

class GestorPersonaPub Extends GestorPersonaGlobal {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/* CONSTRUCTOR -------------------------------------------------------------- */

	/**
	 * Constructor de la classe.
	 *
	 * @return $gestor
	 *
	 */
	function __construct() {
		$oDbl = $GLOBALS['oDBP'];
		$this->setoDbl($oDbl);
		$this->setNomTabla('p_de_paso');
	}


	/* METODES PUBLICS -----------------------------------------------------------*/

	/* METODES PROTECTED --------------------------------------------------------*/

	/* METODES GET i SET --------------------------------------------------------*/
}
?>
