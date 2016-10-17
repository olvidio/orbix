<?php
namespace actividades\model;
use core;
use web;
/**
 * GestorTipoTarifa
 *
 * Classe per gestionar la llista d'objectes de la clase TipoTarifa
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 19/11/2010
 */
class GestorTipoTarifa Extends  core\ClaseGestor {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/* CONSTRUCTOR -------------------------------------------------------------- */

	/**
	 * Constructor de la classe.
	 *
	 * @return GestorTipoTarifa
	 *
	 */
	function __construct() {
		$oDbl = $GLOBALS['oDBC'];
		$this->setoDbl($oDbl);
		$this->setNomTabla('xa_tipo_tarifa');
	}


	/* METODES PUBLICS -----------------------------------------------------------*/
	/**
	 * retorna un Array amb les lletres de les tarifas
	 *
	 * @return array (tarifa=>letra).
	 */
	function getArrayTipoTarifas($isfsv='') {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();

		$sWhere = empty($isfsv)? '' : "WHERE sfsv=$isfsv"; 
		$sQuery="SELECT tarifa,letra FROM $nom_tabla $sWhere ORDER BY tarifa";
		if (($oDblSt = $oDbl->query($sQuery)) === false) {
			$sClauError = 'GestorTipoTarifa.lista';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return false;
		}
		$aOpciones=array();
		foreach ($oDbl->query($sQuery) as $aClave) {
			$clave=$aClave[0];
			$val=$aClave[1];
			$aOpciones[$clave]=$val;
		}
		return $aOpciones;
	}
	/**
	 * retorna un objecte del tipus Desplegable
	 * Les posibles tarifes.
	 *
	 * @return array Una Llista
	 */
	function getListaTipoTarifas($isfsv) {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
		$sQuery="SELECT tarifa,letra FROM $nom_tabla WHERE sfsv=$isfsv ORDER BY tarifa";
		if (($oDblSt = $oDbl->query($sQuery)) === false) {
			$sClauError = 'GestorTipoTarifa.lista';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return false;
		}
		$aOpciones=array();
		foreach ($oDbl->query($sQuery) as $aClave) {
			$clave=$aClave[0];
			$val=$aClave[1];
			$aOpciones[$clave]=$val;
		}
		return new web\Desplegable('',$aOpciones,'',true);
	}

	/**
	 * retorna l'array d'objectes de tipus TipoTarifa
	 *
	 * @param string sQuery la query a executar.
	 * @return array Una col·lecció d'objectes de tipus TipoTarifa
	 */
	function getTipoTarifasQuery($sQuery='') {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
		$oTipoTarifaSet = new core\Set();
		if (($oDblSt = $oDbl->query($sQuery)) === false) {
			$sClauError = 'GestorTipoTarifa.query';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return false;
		}
		foreach ($oDbl->query($sQuery) as $aDades) {
			$a_pkey = array('tarifa' => $aDades['tarifa']);
			$oTipoTarifa= new TipoTarifa($a_pkey);
			$oTipoTarifa->setAllAtributes($aDades);
			$oTipoTarifaSet->add($oTipoTarifa);
		}
		return $oTipoTarifaSet->getTot();
	}

	/**
	 * retorna l'array d'objectes de tipus TipoTarifa
	 *
	 * @param array aWhere associatiu amb els valors de les variables amb les quals farem la query
	 * @param array aOperators associatiu amb els valors dels operadors que cal aplicar a cada variable
	 * @return array Una col·lecció d'objectes de tipus TipoTarifa
	 */
	function getTipoTarifas($aWhere=array(),$aOperators=array()) {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
		$oTipoTarifaSet = new core\Set();
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
			$sClauError = 'GestorTipoTarifa.llistar.prepare';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return false;
		}
		if (($oDblSt->execute($aWhere)) === false) {
			$sClauError = 'GestorTipoTarifa.llistar.execute';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return false;
		}
		foreach ($oDblSt as $aDades) {
			$a_pkey = array('tarifa' => $aDades['tarifa']);
			$oTipoTarifa= new TipoTarifa($a_pkey);
			$oTipoTarifa->setAllAtributes($aDades);
			$oTipoTarifaSet->add($oTipoTarifa);
		}
		return $oTipoTarifaSet->getTot();
	}

	/* METODES PROTECTED --------------------------------------------------------*/

	/* METODES GET i SET --------------------------------------------------------*/

}
?>
