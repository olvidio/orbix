<?php
namespace actividadcargos\model;
use core;
/**
 * GestorActividadCargo
 *
 * Classe per gestionar la llista d'objectes de la clase ActividadCargo
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 19/11/2014
 */

class GestorActividadCargo Extends core\ClaseGestor {
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
		$this->setNomTabla('d_cargos_activ_dl');
	}


	/* METODES PUBLICS -----------------------------------------------------------*/

	/**
	 * retorna l'array d'objectes tipus CargoOAsistente
	 *
	 * @param integer id_nom
	 * @return array Una col·lecció d'arrays: id_activ,id_nom,propio,id_cargo;
	 */
	function getCargoOAsistente($iid_nom) {
		$oDbl = $this->getoDbl();
		// lista de id_activ ordenados, primero los propios.
		$sQuery="SELECT id_activ,propio,0 as id_cargo FROM d_asistentes_dl WHERE id_nom=$iid_nom
					UNION ALL
				SELECT id_activ,'f' as propio,id_cargo FROM d_cargos_activ_dl WHERE id_nom=$iid_nom
				ORDER BY 1,2 DESC";
		//echo "sQuery: $sQuery<br>";
		if (($oDblSt = $oDbl->query($sQuery)) === false) {
			$sClauError = 'GestorActividadAsistente.query';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return false;
		}

		$aAsis = array();
		foreach ($oDbl->query($sQuery) as $aDades) {
			if (array_key_exists($aDades['id_activ'],$aAsis)) { // si está repetido, el primero tiene propio=true.
				// Añado al primero el id_cargo del segundo.
				$aAsis[$id_activ]['id_cargo'] = $aDades['id_cargo'];
				continue;
			}
			$id_activ = $aDades['id_activ'];
			$aAsis[$id_activ] = array('id_activ'=>$id_activ,'id_nom'=>$iid_nom,'propio'=>$aDades['propio'],'id_cargo'=>$aDades['id_cargo']);
		}
		return $aAsis;
	}

	/**
	 * retorna l'array d'objectes de tipus ActividadCargo
	 *
	 * @param string sQuery la query a executar.
	 * @return array Una col·lecció d'objectes de tipus ActividadCargo
	 */
	function getActividadCargosQuery($sQuery='') {
		$oDbl = $this->getoDbl();
		$oActividadCargoSet = new core\Set();
		if (($oDblSt = $oDbl->query($sQuery)) === false) {
			$sClauError = 'GestorActividadCargo.query';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return false;
		}
		foreach ($oDbl->query($sQuery) as $aDades) {
			$a_pkey = array('id_activ' => $aDades['id_activ'],
							'id_cargo' => $aDades['id_cargo']);
			$oActividadCargo= new ActividadCargo($a_pkey);
			$oActividadCargo->setAllAtributes($aDades);
			$oActividadCargoSet->add($oActividadCargo);
		}
		return $oActividadCargoSet->getTot();
	}

	/**
	 * retorna l'array d'objectes de tipus ActividadCargo
	 *
	 * @param array aWhere associatiu amb els valors de les variables amb les quals farem la query
	 * @param array aOperators associatiu amb els valors dels operadors que cal aplicar a cada variable
	 * @return array Una col·lecció d'objectes de tipus ActividadCargo
	 */
	function getActividadCargos($aWhere=array(),$aOperators=array()) {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
		$oActividadCargoSet = new core\Set();
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
		if ($sLimit===false) return;
		$sOrdre = '';
		if (isset($aWhere['_ordre']) && $aWhere['_ordre']!='') $sOrdre = ' ORDER BY '.$aWhere['_ordre'];
		if (isset($aWhere['_ordre'])) unset($aWhere['_ordre']);
		$sQry = "SELECT * FROM $nom_tabla ".$sCondi.$sOrdre.$sLimit;
		//echo "query $sQry <br>";
		if (($oDblSt = $oDbl->prepare($sQry)) === false) {
			$sClauError = 'GestorActividadCargo.llistar.prepare';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return false;
		}
		if (($oDblSt->execute($aWhere)) === false) {
			$sClauError = 'GestorActividadCargo.llistar.execute';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return false;
		}
		foreach ($oDblSt as $aDades) {
			$a_pkey = array('id_activ' => $aDades['id_activ'],
							'id_cargo' => $aDades['id_cargo']);
			$oActividadCargo= new ActividadCargo($a_pkey);
			$oActividadCargo->setAllAtributes($aDades);
			$oActividadCargoSet->add($oActividadCargo);
		}
		return $oActividadCargoSet->getTot();
	}

	/* METODES PROTECTED --------------------------------------------------------*/

	/* METODES GET i SET --------------------------------------------------------*/
}
?>
