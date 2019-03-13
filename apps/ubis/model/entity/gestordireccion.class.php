<?php
namespace ubis\model\entity;
use core;
use web;
/**
 * GestorDireccion
 *
 * Classe per gestionar la llista d'objectes de la clase Direccion
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 01/10/2010
 */

class GestorDireccion Extends  core\ClaseGestor {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/* CONSTRUCTOR -------------------------------------------------------------- */

	/**
	 * Constructor de la classe.
	 *
	 * @return GestorDireccion
	 *
	 */
	function __construct() {
	}


	/* METODES PUBLICS -----------------------------------------------------------*/

	/**
	 * retorna un objecte del tipus Desplegable
	 * Els posibles poblacions
	 *
	 * @return object Desplegable
	 */
	function getListaPoblaciones($sCondicion='') {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
		$sQuery="SELECT DISTINCT initcap(poblacion),initcap(poblacion)
				FROM $nom_tabla
				$sCondicion
				ORDER BY initcap(poblacion)";
		if (($oDblSt = $oDbl->query($sQuery)) === false) {
			$sClauError = 'GestorDireccion.lista';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return false;
		}
		return new web\Desplegable('',$oDblSt,'',true);
	}

	/**
	 * retorna un objecte del tipus Desplegable
	 * Els posibles paisos
	 *
	 * @return object Desplegable
	 */
	function getListaPaises($sCondicion='') {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
		$sQuery="SELECT DISTINCT initcap(pais),initcap(pais)
				FROM $nom_tabla
				$sCondicion
				ORDER BY initcap(pais)";
		if (($oDblSt = $oDbl->query($sQuery)) === false) {
			$sClauError = 'GestorDireccion.lista';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return false;
		}
		return new web\Desplegable('',$oDblSt,'',true);
	}

	/**
	 * retorna l'array d'objectes de tipus Direccion
	 *
	 * @param array aWhere associatiu amb els valors de les variables amb les quals farem la query
	 * @param array aOperators associatiu amb els valors dels operadors que cal aplicar a cada variable
	 * @return array Una col·lecció d'objectes de tipus Direccion
	 */
	function getDirecciones($aWhere=array(),$aOperators=array()) {
		$a_Clases[] = array('clase'=>'DireccionCdc','get'=>'getDirecciones');
		$a_Clases[] = array('clase'=>'DireccionCtr','get'=>'getDirecciones');

		$namespace = __NAMESPACE__;
		return $this->getConjunt($a_Clases,$namespace,$aWhere,$aOperators);
	}


	/* METODES PROTECTED --------------------------------------------------------*/

	/* METODES GET i SET --------------------------------------------------------*/

}
