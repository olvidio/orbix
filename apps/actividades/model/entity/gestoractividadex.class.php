<?php
namespace actividades\model\entity;
/**
 * GestorActividadEx
 *
 * Classe per gestionar la llista d'objectes de la clase ActividadEx
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 01/10/2010
 */

class GestorActividadEx extends GestorActividadAll {
	/* ATRIBUTS ----------------------------------------------------------------- */


	/* CONSTRUCTOR -------------------------------------------------------------- */

	/**
	 * Constructor de la classe.
	 *
	 * @return GestorActividadEx
	 *
	 */
	function __construct() {
		$oDbl = $GLOBALS['oDBRC'];
		$this->setoDbl($oDbl);
		$this->setNomTabla('a_actividades_ex');
	}


	/* METODES PUBLICS -----------------------------------------------------------*/

}
?>
