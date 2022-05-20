<?php
namespace documentos\model\entity;
use core;
use web\Desplegable;
/**
 * GestorEquipaje
 *
 * Classe per gestionar la llista d'objectes de la clase Equipaje
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 28/4/2022
 */

class GestorEquipaje Extends core\ClaseGestor {
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
		$this->setNomTabla('doc_equipajes');
	}


	/* METODES PUBLICS -----------------------------------------------------------*/

	/**
	 * retorna un array
	 * Els equipatges coincidents
	 *
	 * @param id_equipaje
	 * @return array Una Llista
	 */
	function getEquipajesCoincidentes($id_equipaje) {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
		$oEquipaje = new Equipaje($id_equipaje);
		$f_ini = $oEquipaje->getF_ini();
		$f_fin = $oEquipaje->getF_fin();
		$sQuery="SELECT id_equipaje,nom_equipaje FROM $nom_tabla
							WHERE (f_ini BETWEEN '$f_ini' AND '$f_fin')
									OR (f_fin BETWEEN '$f_ini' AND '$f_fin')
							ORDER BY nom_equipaje";
		if ($oDbl->query($sQuery) === false) {
			$sClauError = 'GestorTipoDoc.lista';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return false;
		}
		$aOpciones=array();
		foreach ($oDbl->query($sQuery) as $aClave) {
			$aOpciones[]=$aClave[0];
		}
		return $aOpciones;
	}
	/**
	 * retorna un objecte del tipus Desplegable
	 * Els posibles equipatges
	 *
	 * @param f_ini date una data a partir de la qual
	 * @return array Una Llista
	 */
	function getListaEquipajes($f_ini='') {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
		$where = '';
		if (!empty($f_ini)) $where = "WHERE f_ini > '$f_ini'";
		$sQuery="SELECT id_equipaje,nom_equipaje FROM $nom_tabla
							$where
							ORDER BY f_ini,nom_equipaje";
		if (($oDbl->query($sQuery)) === false) {
			$sClauError = 'GestorTipoDoc.lista';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return false;
		}
		$aOpciones=array();
		foreach ($oDbl->query($sQuery) as $aClave) {
			$clave=$aClave[0];
			$val=$aClave[1];
			$aOpciones[$clave]=$val;
		}
		return new Desplegable('',$aOpciones,'',true);
	}

	/**
	 * retorna l'array d'objectes de tipus Equipaje
	 *
	 * @param string sQuery la query a executar.
	 * @return array Una col·lecció d'objectes de tipus Equipaje
	 */
	function getEquipajesQuery($sQuery='') {
		$oDbl = $this->getoDbl();
		$oEquipajeSet = new core\Set();
		if (($oDbl->query($sQuery)) === FALSE) {
			$sClauError = 'GestorEquipaje.query';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return FALSE;
		}
		foreach ($oDbl->query($sQuery) as $aDades) {
			$a_pkey = array('id_equipaje' => $aDades['id_equipaje']);
			$oEquipaje= new Equipaje($a_pkey);
			$oEquipaje->setAllAtributes($aDades);
			$oEquipajeSet->add($oEquipaje);
		}
		return $oEquipajeSet->getTot();
	}

	/**
	 * retorna l'array d'objectes de tipus Equipaje
	 *
	 * @param array aWhere associatiu amb els valors de les variables amb les quals farem la query
	 * @param array aOperators associatiu amb els valors dels operadors que cal aplicar a cada variable
	 * @return array Una col·lecció d'objectes de tipus Equipaje
	 */
	function getEquipajes($aWhere=array(),$aOperators=array()) {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
		$oEquipajeSet = new core\Set();
		$oCondicion = new core\Condicion();
		$aCondi = array();
		foreach ($aWhere as $camp => $val) {
			if ($camp == '_ordre') continue;
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
		if ($sLimit === FALSE) return;
		$sOrdre = '';
		if (isset($aWhere['_ordre']) && $aWhere['_ordre']!='') $sOrdre = ' ORDER BY '.$aWhere['_ordre'];
		if (isset($aWhere['_ordre'])) unset($aWhere['_ordre']);
		$sQry = "SELECT * FROM $nom_tabla ".$sCondi.$sOrdre.$sLimit;
		if (($oDblSt = $oDbl->prepare($sQry)) === FALSE) {
			$sClauError = 'GestorEquipaje.llistar.prepare';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return FALSE;
		}
		if (($oDblSt->execute($aWhere)) === FALSE) {
			$sClauError = 'GestorEquipaje.llistar.execute';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
			return FALSE;
		}
		foreach ($oDblSt as $aDades) {
			$a_pkey = array('id_equipaje' => $aDades['id_equipaje']);
			$oEquipaje= new Equipaje($a_pkey);
			$oEquipaje->setAllAtributes($aDades);
			$oEquipajeSet->add($oEquipaje);
		}
		return $oEquipajeSet->getTot();
	}

	/* METODES PROTECTED --------------------------------------------------------*/

	/* METODES GET i SET --------------------------------------------------------*/
}
