<?php
namespace zonassacd\model\entity;
use core;
/**
 * GestorZonaSacd
 *
 * Classe per gestionar la llista d'objectes de la clase ZonaSacd
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 01/03/2019
 */

class GestorZonaSacd Extends core\ClaseGestor {
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
		$this->setNomTabla('zonas_sacd');
	}


	/* METODES PUBLICS -----------------------------------------------------------*/
	
	/**
	 * retorna l'array de id_nom dels sacd de la zona
	 *
	 * @param integer iid_zona.
	 * @return array id_nom
	 */
	function getSacdsZona($iid_zona='') {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
	    $aLista = array();
	    $sQuery="SELECT id_nom
				FROM $nom_tabla
				WHERE id_zona=$iid_zona
				ORDER BY id_nom";
	    if (($oDblSt = $oDbl->query($sQuery)) === false) {
	        $sClauError = 'GestorZonaSacd.sacds';
	        $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
	        return false;
	    }
	    foreach ($oDbl->query($sQuery) as $aDades) {
	        $aLista[] = $aDades['id_nom'];
	    }
	    return $aLista;
	}
	
	/**
	 * retorna l'array d'objectes de tipus ZonaSacd
	 *
	 * @param string sQuery la query a executar.
	 * @return array Una col·lecció d'objectes de tipus ZonaSacd
	 */
	function getZonasSacdsQuery($sQuery='') {
		$oDbl = $this->getoDbl();
		$oZonaSacdSet = new core\Set();
		if (($oDbl->query($sQuery)) === FALSE) {
			$sClauError = 'GestorZonaSacd.query';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return FALSE;
		}
		foreach ($oDbl->query($sQuery) as $aDades) {
			$a_pkey = array('id_item' => $aDades['id_item']);
			$oZonaSacd= new ZonaSacd($a_pkey);
			$oZonaSacd->setAllAtributes($aDades);
			$oZonaSacdSet->add($oZonaSacd);
		}
		return $oZonaSacdSet->getTot();
	}

	/**
	 * retorna l'array d'objectes de tipus ZonaSacd
	 *
	 * @param array aWhere associatiu amb els valors de les variables amb les quals farem la query
	 * @param array aOperators associatiu amb els valors dels operadors que cal aplicar a cada variable
	 * @return array Una col·lecció d'objectes de tipus ZonaSacd
	 */
	function getZonasSacds($aWhere=array(),$aOperators=array()) {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
		$oZonaSacdSet = new core\Set();
		$oCondicion = new core\Condicion();
		$aCondi = array();
		foreach ($aWhere as $camp => $val) {
			if ($camp == '_ordre') continue;
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
		if ($sLimit === FALSE) return;
		$sOrdre = '';
		if (isset($aWhere['_ordre']) && $aWhere['_ordre']!='') $sOrdre = ' ORDER BY '.$aWhere['_ordre'];
		if (isset($aWhere['_ordre'])) unset($aWhere['_ordre']);
		$sQry = "SELECT * FROM $nom_tabla ".$sCondi.$sOrdre.$sLimit;
		if (($oDblSt = $oDbl->prepare($sQry)) === FALSE) {
			$sClauError = 'GestorZonaSacd.llistar.prepare';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return FALSE;
		}
		if (($oDblSt->execute($aWhere)) === FALSE) {
			$sClauError = 'GestorZonaSacd.llistar.execute';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
			return FALSE;
		}
		foreach ($oDblSt as $aDades) {
			$a_pkey = array('id_item' => $aDades['id_item']);
			$oZonaSacd= new ZonaSacd($a_pkey);
			$oZonaSacd->setAllAtributes($aDades);
			$oZonaSacdSet->add($oZonaSacd);
		}
		return $oZonaSacdSet->getTot();
	}

	/* METODES PROTECTED --------------------------------------------------------*/

	/* METODES GET i SET --------------------------------------------------------*/
}
