<?php
namespace actividadestudios\model\entity;
use asignaturas\model\entity as asignaturas;
use core;
/**
 * GestorActividadAsignatura
 *
 * Classe per gestionar la llista d'objectes de la clase ActividadAsignaturaDl
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 14/11/2014
 */

class GestorActividadAsignaturaDl Extends GestorActividadAsignatura {
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
		$this->setNomTabla('d_asignaturas_activ_dl');
	}

	/* METODES PUBLICS -----------------------------------------------------------*/
	/* METODES PROTECTED --------------------------------------------------------*/
	/* METODES GET i SET --------------------------------------------------------*/
}
?>
