<?php
namespace procesos\model\entity;
use core;
/**
 * GestorProceso
 *
 * Classe per gestionar la llista d'objectes de la clase Proceso
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 07/12/2018
 */

class GestorProceso Extends core\ClaseGestor {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/* CONSTRUCTOR -------------------------------------------------------------- */


	/**
	 * Constructor de la classe.
	 *
	 * @return $gestor
	 *
	 */
	function __construct() {
		$oDbl = $GLOBALS['oDBC'];
		$this->setoDbl($oDbl);
		$this->setNomTabla('a_procesos');
	}


	/* METODES PUBLICS -----------------------------------------------------------*/
	/**
	 * retorna intege de la id_fase anterior
	 *
	 * @param integer id_item la fase tarea.
	 * @return integer id_true o false si hi ha un error
	 */
	function getStatusFaseAnterior($iid_item) {
		$nom_tabla = $this->getNomTabla();
	    $oActual = new Proceso(array('id_item'=>$iid_item));
	    $iid_tipo_proceso = $oActual->getId_tipo_proceso();
	    $in_orden = $oActual->getN_orden();
	    // buscar el anterior
	    $sQry="SELECT id_item FROM $nom_tabla
				WHERE id_tipo_proceso=$iid_tipo_proceso AND n_orden < $in_orden
				ORDER BY n_orden DESC LIMIT 1";
	    $ColeccionProcesos=$this->getProcesosQuery($sQry);
	    if (count($ColeccionProcesos) > 0) {
	        $oAnterior=$ColeccionProcesos[0];
	        return $oAnterior->getStatus();
	    } //ja està el primer
	}
	
	/**
	 * retorna res
	 * fa el canvi d'ordre
	 *
	 * @param string +/- avançar o retrocedir una posició.
	 * @param integer id_item la fase tarea en concret que s'ha de modificar.
	 * @return true o false si hi ha un error
	 */
	function setProcesosOrden($iid_item,$sque) {
		$nom_tabla = $this->getNomTabla();
	    $oActual = new Proceso(array('id_item'=>$iid_item));
	    $iid_tipo_proceso = $oActual->getId_tipo_proceso();
	    $in_orden = $oActual->getN_orden();
	    switch ($sque) {
	        case "up":
	            // buscar el anterior
	            $sQry="SELECT id_item FROM $nom_tabla
						WHERE id_tipo_proceso=$iid_tipo_proceso AND n_orden < $in_orden
						ORDER BY n_orden DESC LIMIT 1";
	            $ColeccionProcesos=$this->getProcesosQuery($sQry);
	            if (count($ColeccionProcesos) > 0) {
	                $oAnterior=$ColeccionProcesos[0];
	                $oActual->setN_orden($oAnterior->getN_orden());
	                $oActual->DBGuardar();
	                $oAnterior->setN_orden($in_orden);
	                $oAnterior->DBGuardar();
	            } //ja està el primer
	            break;
	        case "down":
	            // buscar el siguiente
	            $sQry="SELECT id_item FROM $nom_tabla
						WHERE id_tipo_proceso=$iid_tipo_proceso AND n_orden > $in_orden
						ORDER BY n_orden ASC LIMIT 1";
	            $ColeccionProcesos=$this->getProcesosQuery($sQry);
	            if (count($ColeccionProcesos) > 0) {
	                $oNext=$ColeccionProcesos[0];
	                $oActual->setN_orden($oNext->getN_orden());
	                $oActual->DBGuardar();
	                $oNext->setN_orden($in_orden);
	                $oNext->DBGuardar();
	            } //ja està l'últim
	            break;
	    }
	}
	
	/**
	 * retorna l'array amb la lista de fases ordenadas. Per utilitzar a 'a_fases_gestor.class'
	 *
	 * @param integer iid_tipo_proceso tipus de procés.
	 * @return array Una llista de fases.
	 */
	function getFasesProcesoOrdenadas($iid_tipo_proceso='') {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
	    if (empty($iid_tipo_proceso)) return array();
	    $oDbl = $GLOBALS['oDBA'];
	    $sQuery = "SELECT * FROM $nom_tabla WHERE id_tipo_proceso = $iid_tipo_proceso ORDER BY n_orden";
	    
	    if (($oDbl->query($sQuery)) === false) {
	        $sClauError = 'GestorProceso.query';
	        $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
	        return false;
	    }
	    $aFases = [];
	    foreach ($oDbl->query($sQuery) as $aDades) {
	        $aFases[$aDades['id_item']] = $aDades['id_fase'];
	    }
	    return $aFases;
	}
	
	/**
	 * retorna l'array d'objectes de tipus Proceso
	 *
	 * @param string sQuery la query a executar.
	 * @return array Una col·lecció d'objectes de tipus Proceso
	 */
	function getProcesosQuery($sQuery='') {
		$oDbl = $this->getoDbl();
		$oProcesoSet = new core\Set();
		if (($oDbl->query($sQuery)) === FALSE) {
			$sClauError = 'GestorProceso.query';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return FALSE;
		}
		foreach ($oDbl->query($sQuery) as $aDades) {
			$a_pkey = array('id_item' => $aDades['id_item']);
			$oProceso= new Proceso($a_pkey);
			$oProceso->setAllAtributes($aDades);
			$oProcesoSet->add($oProceso);
		}
		return $oProcesoSet->getTot();
	}

	/**
	 * retorna l'array d'objectes de tipus Proceso
	 *
	 * @param array aWhere associatiu amb els valors de les variables amb les quals farem la query
	 * @param array aOperators associatiu amb els valors dels operadors que cal aplicar a cada variable
	 * @return array Una col·lecció d'objectes de tipus Proceso
	 */
	function getProcesos($aWhere=array(),$aOperators=array()) {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
		$oProcesoSet = new core\Set();
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
			$sClauError = 'GestorProceso.llistar.prepare';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return FALSE;
		}
		if (($oDblSt->execute($aWhere)) === FALSE) {
			$sClauError = 'GestorProceso.llistar.execute';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
			return FALSE;
		}
		foreach ($oDblSt as $aDades) {
			$a_pkey = array('id_item' => $aDades['id_item']);
			$oProceso= new Proceso($a_pkey);
			$oProceso->setAllAtributes($aDades);
			$oProcesoSet->add($oProceso);
		}
		return $oProcesoSet->getTot();
	}

	/* METODES PROTECTED --------------------------------------------------------*/

	/* METODES GET i SET --------------------------------------------------------*/
}
