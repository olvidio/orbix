<?php
namespace ubis\model\entity;
use core;
use web;
/**
 * GestorRegion
 *
 * Classe per gestionar la llista d'objectes de la clase Region
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 20/11/2010
 */

class GestorRegion Extends  core\ClaseGestor {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/* CONSTRUCTOR -------------------------------------------------------------- */

	/**
	 * Constructor de la classe.
	 *
	 * @return GestorRegion
	 *
	 */
	function __construct() {
		$oDbl = $GLOBALS['oDBPC'];
		$this->setoDbl($oDbl);
		$this->setNomTabla('xu_region');
	}


	/* METODES PUBLICS -----------------------------------------------------------*/

	/**
	 * retorna un objecte del tipus Desplegable
	 *
	 * @return object Una Llista de regions.
	 */
	function getListaRegiones() {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
		$cond = "status = 't'";
		$sQuery="SELECT region, nombre_region
				FROM $nom_tabla
				WHERE $cond 
				ORDER BY nombre_region";
		if (($oDblSt = $oDbl->query($sQuery)) === false) {
			$sClauError = 'GestorRole.lista';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return false;
		}
		return new web\Desplegable('',$oDblSt,'',true);
	}

	/**
	 * retorna l'array d'objectes de tipus Region
	 *
	 * @param string sQuery la query a executar.
	 * @return array Una col·lecció d'objectes de tipus Region
	 */
	function getRegionesQuery($sQuery='') {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
		$oRegionSet = new core\Set();
		if (($oDblSt = $oDbl->query($sQuery)) === false) {
			$sClauError = 'GestorRegion.query';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return false;
		}
		foreach ($oDbl->query($sQuery) as $aDades) {
			$a_pkey = array('region' => $aDades['region']);
			$oRegion= new Region($a_pkey);
			$oRegion->setAllAtributes($aDades);
			$oRegionSet->add($oRegion);
		}
		return $oRegionSet->getTot();
	}

	/**
	 * retorna l'array d'objectes de tipus Region
	 *
	 * @param array aWhere associatiu amb els valors de les variables amb les quals farem la query
	 * @param array aOperators associatiu amb els valors dels operadors que cal aplicar a cada variable
	 * @return array Una col·lecció d'objectes de tipus Region
	 */
	function getRegiones($aWhere=array(),$aOperators=array()) {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
		$oRegionSet = new core\Set();
		$oCondicion = new core\Condicion();
		$aCondi = array();
		foreach ($aWhere as $camp => $val) {
			if ($camp === '_ordre') continue;
			$sOperador = isset($aOperators[$camp])? $aOperators[$camp] : '';
			if ($a = $oCondicion->getCondicion($camp,$sOperador,$val)) $aCondi[]=$a;
			// operadores que no requieren valores
			if ($sOperador == 'BETWEEN' || $sOperador == 'IS NULL' || $sOperador == 'IS NOT NULL' || $sOperador == 'OR') unset($aWhere[$camp]);
		}
		$sCondi = implode(' AND ',$aCondi);
		if ($sCondi!='') $sCondi = " WHERE ".$sCondi;
		if (isset($GLOBALS['oGestorSessioDelegación'])) {
		   	$sLimit = $GLOBALS['oGestorSessioDelegación']->getLimitPaginador('a_actividades',$sCondi,$aWhere);
		} else {
			$sLimit='';
		}
		if ($sLimit===false) return;
		$sOrdre = '';
		if (isset($aWhere['_ordre']) && $aWhere['_ordre']!='') $sOrdre = ' ORDER BY '.$aWhere['_ordre'];
		if (isset($aWhere['_ordre'])) unset($aWhere['_ordre']);
		$sQry = "SELECT * FROM $nom_tabla ".$sCondi.$sOrdre.$sLimit;
		if (($oDblSt = $oDbl->prepare($sQry)) === false) {
			$sClauError = 'GestorRegion.llistar.prepare';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return false;
		}
		if (($oDblSt->execute($aWhere)) === false) {
			$sClauError = 'GestorRegion.llistar.execute';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
			return false;
		}
		foreach ($oDblSt as $aDades) {
			$a_pkey = array('region' => $aDades['region']);
			$oRegion= new Region($a_pkey);
			$oRegion->setAllAtributes($aDades);
			$oRegionSet->add($oRegion);
		}
		return $oRegionSet->getTot();
	}

	/* METODES PROTECTED --------------------------------------------------------*/

	/* METODES GET i SET --------------------------------------------------------*/

}
?>
