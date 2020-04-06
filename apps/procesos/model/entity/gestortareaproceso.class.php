<?php
namespace procesos\model\entity;
use core;
/**
 * GestorTareaProceso
 *
 * Classe per gestionar la llista d'objectes de la clase TareaProceso
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 07/12/2018
 */

class GestorTareaProceso Extends core\ClaseGestor {
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
		$this->setNomTabla('a_tareas_proceso');
	}


	/* METODES PUBLICS -----------------------------------------------------------*/
	
	/**
	 * retorna array de fase#traea => fase_previa, tarea_previa.
	 *
	 * @param integer iid_tipo_proceso tipus de procés.
	 * @return array $aFases . $aFases[$fase_tarea] = ['id_fase' => $id_fase_previa, 'id_tarea' => $id_tarea_previa]; 
	 */
	public function getArrayFasesDependientes($iid_tipo_proceso) {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
	    $sQuery = "SELECT * FROM $nom_tabla 
                    WHERE id_tipo_proceso = $iid_tipo_proceso
		            ORDER BY n_orden DESC;
                    ";
	    
	    if (($oDbl->query($sQuery)) === false) {
	        $sClauError = 'GestorTareaProceso.query';
	        $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
	        return false;
	    }
        $aFases = [];
	    foreach ($oDbl->query($sQuery) as $aDades) {
	        $id_fase = $aDades['id_fase'];
	        $id_tarea = $aDades['id_tarea'];
	        // tarea puede ser empty en vez de 0:
	        $id_tarea = empty($id_tarea)? 0 : $id_tarea;
	        $fase_tarea = $id_fase.'#'.$id_tarea;
	        
	        $id_fase_previa = $aDades['id_fase_previa'];
	        $id_tarea_previa = $aDades['id_tarea_previa'];
	        // tarea puede ser empty en vez de 0:
	        $id_tarea_previa = empty($id_tarea_previa)? 0 : $id_tarea_previa;
	
	        $aFases[$fase_tarea] = ['id_fase' => $id_fase_previa, 'id_tarea' => $id_tarea_previa]; 
	    }
	    return $aFases;
	}
	
	/**
	 * retorna un array
	 *
	 * @param integer iid_tipo_proceso tipus de procés.
	 * @param integer id_fase.
	 * @param integer id_tarea.
	 * @param integer $f   nº de fila
	 * @return array  $aFases = [$f => "$id_fase#$id_tarea"];
	 */
	public function getListaFasesDependientes($iid_tipo_proceso,$id_fase,$id_tarea=0,$f=0) {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
	    $sQuery = "SELECT * FROM $nom_tabla 
                    WHERE id_tipo_proceso = $iid_tipo_proceso
		            ORDER BY n_orden DESC
                    ";
	    
	    if (($oDbl->query($sQuery)) === false) {
	        $sClauError = 'GestorTareaProceso.query';
	        $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
	        return false;
	    }
        $aFases = [$f => "$id_fase#$id_tarea"];
	    foreach ($oDbl->query($sQuery) as $aDades) {
	        $id_fase_i = $aDades['id_fase'];
	        $id_tarea_i = $aDades['id_tarea'];
	        // tarea puede ser empty en vez de 0:
	        $id_tarea_i = empty($id_tarea_i)? 0 : $id_tarea_i;
	        
	        if ($id_fase == $id_fase_i && $id_tarea == $id_tarea_i) {
                
                $id_fase_previa = $aDades['id_fase_previa'];
                $id_tarea_previa = $aDades['id_tarea_previa'];
                if (!empty($id_fase_previa)) {
                    // si no hay tarea previa, vale cualquier fase previa,
                    // pero busco la primera en orden
                    if (empty($id_tarea_previa)) {
                        $aWhereFP = ['id_tipo_proceso' => $iid_tipo_proceso,
                                      'id_fase' => $id_fase_previa,
                                      '_ordre' => 'n_orden',
                                    ];
                    } else {
                        $aWhereFP = ['id_tipo_proceso' => $iid_tipo_proceso,
                                      'id_fase' => $id_fase_previa,
                                      'id_tarea' => $id_tarea_previa,
                                      '_ordre' => 'n_orden',
                                    ];
                    }
                    $cTareas = $this->getTareasProceso($aWhereFP);
                    $oTareaProceso = $cTareas[0];
                    $id_fase_j = $oTareaProceso->getId_fase();
                    $id_tarea_j = $oTareaProceso->getId_tarea();
                    
                    $f++;
                    $aF2 = $this->getListaFasesDependientes($iid_tipo_proceso, $id_fase_j,$id_tarea_j,$f);
                    $aFases = $aFases + $aF2;
                }
                return $aFases;
	        }
	    }
	}
	
	/**
	 * retorna la primera fase del status.
	 *
	 * @param integer iid_tipo_proceso tipus de procés.
	 * @param integer status
	 * @return integer id_fase.
	 */
	public function getFirstFaseStatus($iid_tipo_proceso,$status) {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
	    $sQuery = "SELECT * FROM $nom_tabla 
                    WHERE id_tipo_proceso = $iid_tipo_proceso AND status = $status 
                    ORDER BY n_orden
                    LIMIT 1";
	    
	    if (($oDbl->query($sQuery)) === false) {
	        $sClauError = 'GestorTareaProceso.query';
	        $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
	        return false;
	    }
	    $id_fase = '';
	    foreach ($oDbl->query($sQuery) as $aDades) {
	        $id_fase = $aDades['id_fase'];
	    }
	    return $id_fase;
	}
	
	/**
	 * retorna la ultima fase del status.
	 *
	 * @param integer iid_tipo_proceso tipus de procés.
	 * @param integer status
	 * @return integer id_fase.
	 */
	public function getLastFaseStatus($iid_tipo_proceso,$status) {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
	    $sQuery = "SELECT * FROM $nom_tabla 
                    WHERE id_tipo_proceso = $iid_tipo_proceso AND status = $status 
                    ORDER BY n_orden DESC
                    LIMIT 1";
	    
	    if (($oDbl->query($sQuery)) === false) {
	        $sClauError = 'GestorTareaProceso.query';
	        $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
	        return false;
	    }
	    $id_fase = '';
	    foreach ($oDbl->query($sQuery) as $aDades) {
	        $id_fase = $aDades['id_fase'];
	    }
	    return $id_fase;
	}
	
	/**
	 * retorna integer de la id_fase anterior
	 *
	 * @param integer id_item la fase tarea.
	 * @return integer id_true o false si hi ha un error
	 */
	public function getStatusFaseAnterior($iid_item) {
		$nom_tabla = $this->getNomTabla();
	    $oActual = new TareaProceso(array('id_item'=>$iid_item));
	    $iid_tipo_proceso = $oActual->getId_tipo_proceso();
	    $in_orden = $oActual->getN_orden();
	    // buscar el anterior
	    $sQry="SELECT id_item FROM $nom_tabla
				WHERE id_tipo_proceso=$iid_tipo_proceso AND n_orden < $in_orden
				ORDER BY n_orden DESC LIMIT 1";
	    $ColeccionProcesos=$this->getTareasProcesosQuery($sQry);
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
	public function setTareasProcesosOrden($iid_item,$sque) {
		$nom_tabla = $this->getNomTabla();
	    $oActual = new TareaProceso(array('id_item'=>$iid_item));
	    $iid_tipo_proceso = $oActual->getId_tipo_proceso();
	    $in_orden = $oActual->getN_orden();
	    switch ($sque) {
	        case "up":
	            // buscar el anterior
	            $sQry="SELECT id_item FROM $nom_tabla
						WHERE id_tipo_proceso=$iid_tipo_proceso AND n_orden < $in_orden
						ORDER BY n_orden DESC LIMIT 1";
	            $ColeccionProcesos=$this->getTareasProcesosQuery($sQry);
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
	            $ColeccionProcesos=$this->getTareasProcesosQuery($sQry);
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
	function zzgetFasesProcesoOrdenadas($iid_tipo_proceso='') {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
	    if (empty($iid_tipo_proceso)) return array();
	    $sQuery = "SELECT * FROM $nom_tabla WHERE id_tipo_proceso = $iid_tipo_proceso ORDER BY n_orden";
	    
	    if (($oDbl->query($sQuery)) === false) {
	        $sClauError = 'GestorTareaProceso.query';
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
	 * retorna l'array d'objectes de tipus TareaProceso
	 *
	 * @param string sQuery la query a executar.
	 * @return array Una col·lecció d'objectes de tipus TareaProceso
	 */
	function getTareasProcesosQuery($sQuery='') {
		$oDbl = $this->getoDbl();
		$oTareaProcesoSet = new core\Set();
		if (($oDbl->query($sQuery)) === FALSE) {
			$sClauError = 'GestorTareaProceso.query';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return FALSE;
		}
		foreach ($oDbl->query($sQuery) as $aDades) {
			$a_pkey = array('id_item' => $aDades['id_item']);
			$oTareaProceso= new TareaProceso($a_pkey);
			$oTareaProceso->setAllAtributes($aDades);
			$oTareaProcesoSet->add($oTareaProceso);
		}
		return $oTareaProcesoSet->getTot();
	}

	/**
	 * retorna l'array d'objectes de tipus TareaProceso
	 *
	 * @param array aWhere associatiu amb els valors de les variables amb les quals farem la query
	 * @param array aOperators associatiu amb els valors dels operadors que cal aplicar a cada variable
	 * @return array Una col·lecció d'objectes de tipus TareaProceso
	 */
	function getTareasProceso($aWhere=array(),$aOperators=array()) {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
		$oTareaProcesoSet = new core\Set();
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
			$sClauError = 'GestorTareaProceso.llistar.prepare';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return FALSE;
		}
		if (($oDblSt->execute($aWhere)) === FALSE) {
			$sClauError = 'GestorTareaProceso.llistar.execute';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
			return FALSE;
		}
		foreach ($oDblSt as $aDades) {
			$a_pkey = array('id_item' => $aDades['id_item']);
			$oTareaProceso= new TareaProceso($a_pkey);
			$oTareaProceso->setAllAtributes($aDades);
			$oTareaProcesoSet->add($oTareaProceso);
		}
		return $oTareaProcesoSet->getTot();
	}

	/* METODES PROTECTED --------------------------------------------------------*/

	/* METODES GET i SET --------------------------------------------------------*/
}
