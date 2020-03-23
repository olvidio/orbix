<?php
namespace actividadestudios\model\entity;
/**
 * GestorMatriculaDl
 *
 * Classe per gestionar la llista d'objectes de la clase MatriculaDl
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 07/04/2014
 */

class GestorMatriculaDl Extends GestorMatricula {
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
		$this->setNomTabla('d_matriculas_activ_dl');
	}


	/* METODES PUBLICS -----------------------------------------------------------*/

	/* METODES PROTECTED --------------------------------------------------------*/

	/* METODES GET i SET --------------------------------------------------------*/
}
