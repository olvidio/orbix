<?php
namespace ubis\model\entity;
use core;
/**
 * GestorUbixDireccion
 *
 * Classe per gestionar la llista d'objectes de la clase UbixDireccion
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 11/02/2014
 */

class GestorUbixDireccion Extends  core\ClaseGestor {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/* CONSTRUCTOR -------------------------------------------------------------- */

	/**
	 * Constructor de la classe.
	 *
	 * @return GestorUbixDireccion
	 *
	 */
	function __construct() {
		$oDbl = $GLOBALS['oDBPC'];
		$this->setoDbl($oDbl);
		$this->setNomTabla('u_cross_ubi_dir');
	}


	/* METODES PUBLICS -----------------------------------------------------------*/

	
	/**
	 * retorna l'array d'objectes de tipus UbixDireccion
	 *
	 * @param array aWhere associatiu amb els valors de les variables amb les quals farem la query
	 * @param array aOperators associatiu amb els valors dels operadors que cal aplicar a cada variable
	 * @return array Una col·lecció d'objectes de tipus UbixDireccion
	 */
	function getUbixDirecciones($aWhere=array(),$aOperators=array()) {
		$a_Clases[] = array('clase'=>'CtrxDireccion','get'=>'getCtrxDirecciones');
		$a_Clases[] = array('clase'=>'CdcxDireccion','get'=>'getCdcxDirecciones');

		$namespace = __NAMESPACE__;
		return $this->getConjunt($a_Clases,$namespace,$aWhere,$aOperators);
	}

	/* METODES PROTECTED --------------------------------------------------------*/

	/* METODES GET i SET --------------------------------------------------------*/

}
?>
