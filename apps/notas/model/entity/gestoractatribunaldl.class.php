<?php
namespace notas\model\entity;
/**
 * GestorActaTribunalDl
 *
 * Classe per gestionar la llista d'objectes de la clase ActaTribunalDl
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 07/04/2014
 */

class GestorActaTribunalDl Extends GestorActaTribunal {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/* CONSTRUCTOR -------------------------------------------------------------- */


	/**
	 * Constructor de la classe.
	 *
	 * @return $gestor
	 *
	 */
	function __construct() {
		$oDbl = $GLOBALS['oDB'];
		$this->setoDbl($oDbl);
		$this->setNomTabla('e_actas_tribunal_dl');
	}
	
}