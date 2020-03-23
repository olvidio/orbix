<?php
namespace actividadplazas\model\entity;
use core;
/**
 * GestorPlazaPeticion
 *
 * Classe per gestionar la llista d'objectes de la clase PlazaPeticion
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 22/11/2016
 */

class GestorPlazaPeticion Extends core\ClaseGestor {
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
		$this->setNomTabla('dap_plazas_peticion_dl');
	}


	/* METODES PUBLICS -----------------------------------------------------------*/

	/**
	 * retorna l'array d'objectes de tipus PlazaPeticion
	 *
	 * @param string sQuery la query a executar.
	 * @return array Una col·lecció d'objectes de tipus PlazaPeticion
	 */
	function getPlazasPeticionQuery($sQuery='') {
		$oDbl = $this->getoDbl();
		$oPlazaPeticionSet = new core\Set();
		if (($oDbl->query($sQuery)) === false) {
			$sClauError = 'GestorPlazaPeticion.query';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return false;
		}
		foreach ($oDbl->query($sQuery) as $aDades) {
			$a_pkey = array('id_nom' => $aDades['id_nom'],
							'id_activ' => $aDades['id_activ']);
			$oPlazaPeticion= new PlazaPeticion($a_pkey);
			$oPlazaPeticion->setAllAtributes($aDades);
			$oPlazaPeticionSet->add($oPlazaPeticion);
		}
		return $oPlazaPeticionSet->getTot();
	}

	/**
	 * retorna l'array d'objectes de tipus PlazaPeticion
	 *
	 * @param array aWhere associatiu amb els valors de les variables amb les quals farem la query
	 * @param array aOperators associatiu amb els valors dels operadors que cal aplicar a cada variable
	 * @return array Una col·lecció d'objectes de tipus PlazaPeticion
	 */
	function getPlazasPeticion($aWhere=array(),$aOperators=array()) {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
		$oPlazaPeticionSet = new core\Set();
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
		if ($sLimit===false) return;
		$sOrdre = '';
		if (isset($aWhere['_ordre']) && $aWhere['_ordre']!='') $sOrdre = ' ORDER BY '.$aWhere['_ordre'];
		if (isset($aWhere['_ordre'])) unset($aWhere['_ordre']);
		$sQry = "SELECT * FROM $nom_tabla ".$sCondi.$sOrdre.$sLimit;
		if (($oDblSt = $oDbl->prepare($sQry)) === false) {
			$sClauError = 'GestorPlazaPeticion.llistar.prepare';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return false;
		}
		if (($oDblSt->execute($aWhere)) === false) {
			$sClauError = 'GestorPlazaPeticion.llistar.execute';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
			return false;
		}
		foreach ($oDblSt as $aDades) {
			$a_pkey = array('id_nom' => $aDades['id_nom'],
							'id_activ' => $aDades['id_activ']);
			$oPlazaPeticion= new PlazaPeticion($a_pkey);
			$oPlazaPeticion->setAllAtributes($aDades);
			$oPlazaPeticionSet->add($oPlazaPeticion);
		}
		return $oPlazaPeticionSet->getTot();
	}

	/* METODES PROTECTED --------------------------------------------------------*/

	/* METODES GET i SET --------------------------------------------------------*/
}
?>