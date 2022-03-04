<?php
namespace ubis\model\entity;
use core;
/**
 * GestorDescTeleco
 *
 * Classe per gestionar la llista d'objectes de la clase DescTeleco
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 01/10/2010
 */

class GestorDescTeleco Extends  core\ClaseGestor {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/* CONSTRUCTOR -------------------------------------------------------------- */

	/**
	 * Constructor de la classe.
	 *
	 * @return GestorDescTeleco
	 *
	 */
	function __construct() {
		$oDbl = $GLOBALS['oDBPC'];
		$this->setoDbl($oDbl);
		$this->setNomTabla('xd_desc_teleco');
	}


	/* METODES PUBLICS -----------------------------------------------------------*/

	/**
	 * retorna una lista id_item=>desc_teleco segons el tipo_teleco per les persones
	 *
	 * @return array Una Llista
	 */
	function getListaDescTelecoPersonas($sdepende) {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
		$sQuery="SELECT id_item, desc_teleco
				FROM $nom_tabla
				WHERE persona='t' AND tipo_teleco='$sdepende'
				ORDER BY orden";
		if (($oDblSt = $oDbl->query($sQuery)) === false) {
			$sClauError = 'GestorTipoCasa.lista';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return false;
		}
		return $oDblSt;
	}
	/**
	 * retorna una lista id_item=>desc_teleco segons el tipo_teleco per els ubis
	 *
	 * @return array Una Llista
	 */
	function getListaDescTelecoUbis($sdepende) {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
		$sQuery="SELECT id_item, desc_teleco
				FROM $nom_tabla
				WHERE ubi='t' AND tipo_teleco='$sdepende'
				ORDER BY orden";
		if (($oDblSt = $oDbl->query($sQuery)) === false) {
			$sClauError = 'GestorTipoCasa.lista';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return false;
		}
		return $oDblSt;
	}

	/**
	 * retorna l'array d'objectes de tipus DescTeleco
	 *
	 * @param string sQuery la query a executar.
	 * @return array Una col·lecció d'objectes de tipus DescTeleco
	 */
	function getDescsTelecoQuery($sQuery='') {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
		$oDescTelecoSet = new core\Set();
		if (($oDblSt = $oDbl->query($sQuery)) === false) {
			$sClauError = 'GestorDescTeleco.query';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return false;
		}
		foreach ($oDbl->query($sQuery) as $aDades) {
			$a_pkey = array('id_item' => $aDades['id_item']);
			$oDescTeleco= new DescTeleco($a_pkey);
			$oDescTelecoSet->add($oDescTeleco);
		}
		return $oDescTelecoSet->getTot();
	}

	/**
	 * retorna l'array d'objectes de tipus DescTeleco
	 *
	 * @param array aWhere associatiu amb els valors de les variables amb les quals farem la query
	 * @param array aOperators associatiu amb els valors dels operadors que cal aplicar a cada variable
	 * @return array Una col·lecció d'objectes de tipus DescTeleco
	 */
	function getDescsTeleco($aWhere=array(),$aOperators=array()) {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
		$oDescTelecoSet = new core\Set();
		$oCondicion = new core\Condicion();
		$aCondi = array();
		foreach ($aWhere as $camp => $val) {
			if ($camp === '_ordre') continue;
			$sOperador = isset($aOperators[$camp])? $aOperators[$camp] : '';
			if ($a = $oCondicion->getCondicion($camp,$sOperador,$val)) $aCondi[]=$a;
			// operadores que no requieren valores
			if ($sOperador == 'BETWEEN' || $sOperador == 'IS NULL' || $sOperador == 'IS NOT NULL' || $sOperador == 'OR') unset($aWhere[$camp]);
			if ($sOperador == 'IN' || $sOperador == 'NOT IN') unset($aWhere[$camp]);
			if ($sOperador == 'TXT') unset($aWhere[$camp]);
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
			$sClauError = 'GestorDescTeleco.llistar';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return false;
		}
		if (($oDblSt->execute($aWhere)) === false) {
			$sClauError = 'GestorDescTeleco.llistar';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
			return false;
		}
		foreach ($oDblSt as $aDades) {
			$a_pkey = array('id_item' => $aDades['id_item']);
			$oDescTeleco= new DescTeleco($a_pkey);
			$oDescTelecoSet->add($oDescTeleco);
		}
		return $oDescTelecoSet->getTot();
	}

	/* METODES PROTECTED --------------------------------------------------------*/

	/* METODES GET i SET --------------------------------------------------------*/

}
?>
