<?php
namespace notas\model;
use core;
/**
 * GestorActaTribunalEx
 *
 * Classe per gestionar la llista d'objectes de la clase ActaTribunalEx
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 07/04/2014
 */

class GestorActaTribunalEx Extends GestorActaTribunal {
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
		$this->setNomTabla('e_actas_tribunal_ex');
	}


}
?>
