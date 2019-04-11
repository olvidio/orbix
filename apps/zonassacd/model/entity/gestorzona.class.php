<?php
namespace zonassacd\model\entity;
use core;
use web\Desplegable;
/**
 * GestorZona
 *
 * Classe per gestionar la llista d'objectes de la clase Zona
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 01/03/2019
 */

class GestorZona Extends core\ClaseGestor {
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
		$this->setNomTabla('zonas');
	}


	/* METODES PUBLICS -----------------------------------------------------------*/

	
	/**
	 * retorna un objecte del tipus Desplegable
	 *
	 * @return object Una Llista de zones de misses.
	 */
	function getListaZonas($iid_nom_jefe='') {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
	    $sCondicion = '';
	    if (!empty($iid_nom_jefe)) {
	        $sCondicion = "WHERE id_nom = $iid_nom_jefe";
	    }
	    $sQuery="SELECT id_zona, nombre_zona
					FROM $nom_tabla $sCondicion
					ORDER BY orden";
	    if (($oDblSt = $oDbl->query($sQuery)) === false) {
	        $sClauError = 'GestorZona.lista';
	        $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
	        return false;
	    }
	    return new Desplegable('',$oDblSt,'',true);
	}

	/**
	 * retorna l'array d'objectes de tipus Zona
	 *
	 * @param string sQuery la query a executar.
	 * @return array Una col·lecció d'objectes de tipus Zona
	 */
	function getZonasQuery($sQuery='') {
		$oDbl = $this->getoDbl();
		$oZonaSet = new core\Set();
		if (($oDbl->query($sQuery)) === FALSE) {
			$sClauError = 'GestorZona.query';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return FALSE;
		}
		foreach ($oDbl->query($sQuery) as $aDades) {
			$a_pkey = array('id_zona' => $aDades['id_zona']);
			$oZona= new Zona($a_pkey);
			$oZona->setAllAtributes($aDades);
			$oZonaSet->add($oZona);
		}
		return $oZonaSet->getTot();
	}

	/**
	 * retorna l'array d'objectes de tipus Zona
	 *
	 * @param array aWhere associatiu amb els valors de les variables amb les quals farem la query
	 * @param array aOperators associatiu amb els valors dels operadors que cal aplicar a cada variable
	 * @return array Una col·lecció d'objectes de tipus Zona
	 */
	function getZonas($aWhere=array(),$aOperators=array()) {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
		$oZonaSet = new core\Set();
		$oCondicion = new core\Condicion();
		$aCondi = array();
		foreach ($aWhere as $camp => $val) {
			if ($camp == '_ordre') continue;
			$sOperador = isset($aOperators[$camp])? $aOperators[$camp] : '';
			if ($a = $oCondicion->getCondicion($camp,$sOperador,$val)) $aCondi[]=$a;
			// operadores que no requieren valores
			if ($sOperador == 'BETWEEN' || $sOperador == 'IS NULL' || $sOperador == 'IS NOT NULL' || $sOperador == 'OR') unset($aWhere[$camp]);
			if ($sOperador == 'IN' || $sOperador == 'NOT IN') unset($aWhere[$camp]);
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
			$sClauError = 'GestorZona.llistar.prepare';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return FALSE;
		}
		if (($oDblSt->execute($aWhere)) === FALSE) {
			$sClauError = 'GestorZona.llistar.execute';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
			return FALSE;
		}
		foreach ($oDblSt as $aDades) {
			$a_pkey = array('id_zona' => $aDades['id_zona']);
			$oZona= new Zona($a_pkey);
			$oZona->setAllAtributes($aDades);
			$oZonaSet->add($oZona);
		}
		return $oZonaSet->getTot();
	}

	/* METODES PROTECTED --------------------------------------------------------*/

	/* METODES GET i SET --------------------------------------------------------*/
}
