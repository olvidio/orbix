<?php
namespace procesos\model\entity;
use actividades\model\entity\Actividad;
use actividades\model\entity\TipoDeActividad;
use core;
/**
 * GestorActividadProcesoTarea
 *
 * Classe per gestionar la llista d'objectes de la clase ActividadProcesoTarea
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 06/12/2018
 */

class GestorActividadProcesoTarea Extends core\ClaseGestor {
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
		$this->setNomTabla('a_actividad_proceso');
	}


	/* METODES PUBLICS -----------------------------------------------------------*/
	public function generarProceso($iid_activ='') {
	    $oActividad = new Actividad(array('id_activ'=>$iid_activ));
	    $iid_tipo_activ = $oActividad->getId_tipo_activ();
	    $oTipo = new TipoDeActividad(array('id_tipo_activ'=>$iid_tipo_activ));
	    
	    if ($oActividad->getDl_org() == core\ConfigGlobal::mi_dele()) {
	        $id_tipo_proceso=$oTipo->getId_tipo_proceso();
	    } else {
	        $id_tipo_proceso=$oTipo->getId_tipo_proceso_ex();
	    }
	    
	    $iid_fase = $this->generar($iid_activ,$id_tipo_proceso);
	    return $iid_fase;
	}
	public function getFaseActual($iid_activ='') {
	    if (empty($iid_activ)) return false;
	    // fase en la que se encuentra actualmente
	    $iid_fase = $this->faseActualAcabada($iid_activ);
	    if (is_numeric($iid_fase)) {
	        return $iid_fase;
	    } else {
	        if ($iid_fase == 'END') { // devuelve la última fase
	            $iid_fase = $this->faseUltima($iid_activ);
	            return $iid_fase;
	        }
	        if (empty($iid_fase) || $iid_fase == 'SIN') {
	            //echo sprintf(_('esta actividad: %s no tiene ninguna fase. Se está generando...'),$oActividad->getNom_activ());
	            echo '<br>'._('ATENCIÓN: puede que tenga que actualizar la página para que salgan todas las actividades.');
	            echo '<br>';
	            $this->generarProceso($iid_activ);
	        }
	    }
	}
	
	
	/**
	 * retorna un integer id_fase que és la última del seu proces.
	 *
	 * @param integer iid_activ
	 * @return integer
	 */
	function faseUltima($iid_activ='') {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
	    $sQry = "SELECT * FROM $nom_tabla WHERE id_activ=".$iid_activ." ORDER BY n_orden DESC LIMIT 1";
	    if (($qRs = $oDbl->query($sQry)) === false) {
	        $sClauError = 'GestorActividadProcesoTarea.faseUltima.prepare';
	        $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
	        return false;
	    }
	    if ($qRs->rowCount() == 1 ) {
	        $aDades = $qRs->fetch(\PDO::FETCH_ASSOC);
	        return $aDades['id_fase'];
	    } else {
	        return false;
	    }
	}
	/**
	 * //retorna un objecte de tipus ActividadProcesoTarea que és l'actual.
	 * retorna integer id_fase que és l'actual. No torna l'objecte per guanyar temps. Penso només ho fa servir PermActiv.
	 *		o un string: 'END' -> Totes les fases completades.
	 *					  'SIN' -> no té cap procés associat.
	 *
	 * @param integer iid_activ
	 * @return integer|string
	 */
	function faseActual($iid_activ='') {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
	    $sQry = "SELECT * FROM $nom_tabla WHERE id_activ=".$iid_activ." AND completado='f'
				ORDER BY n_orden LIMIT 1";
	    if (($qRs = $oDbl->query($sQry)) === false) {
	        $sClauError = 'GestorActividadProcesoTarea.faseActual.prepare';
	        $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
	        return false;
	    }
	    if ($qRs->rowCount() == 1 ) {
	        $aDades = $qRs->fetch(\PDO::FETCH_ASSOC);
	        
	        return $aDades['id_fase'];
	    } else { // puede ser que no exista el proceso, o que estén todas las fases completadas.
	        $sQry2 = "SELECT * FROM $nom_tabla WHERE id_activ=".$iid_activ." AND completado='t'
					ORDER BY n_orden LIMIT 1";
	        if (($qRs2 = $oDbl->query($sQry2)) === false) {
	            $sClauError = 'GestorActividadProcesoTarea.faseActual.prepare';
	            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
	            return false;
	        }
	        if ($qRs2->rowCount() == 1 ) {
	            return 'END';
	        } else {
	            return 'SIN';
	        }
	    }
	}
	
	/**
	 * //retorna un objecte de tipus ActividadProcesoTarea que és la última acabada.
	 * retorna integer id_fase que és la última acabada. No torna l'objecte per guanyar temps.
	 *		o un string: 'START' -> Totes les fases en blanc.
	 *					  'SIN' -> no té cap procés associat.
	 *
	 * @param integer iid_activ
	 * @return integer|string
	 */
	function faseActualAcabada($iid_activ='') {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
	    $sQry = "SELECT * FROM $nom_tabla WHERE id_activ=".$iid_activ." AND completado='t'
				ORDER BY n_orden DESC LIMIT 1";
	    if (($qRs = $oDbl->query($sQry)) === false) {
	        $sClauError = 'GestorActividadProcesoTarea.faseActual.prepare';
	        $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
	        return false;
	    }
	    if ($qRs->rowCount() == 1 ) {
	        $aDades = $qRs->fetch(\PDO::FETCH_ASSOC);
	        
	        return $aDades['id_fase'];
	    } else { // puede ser que no exista el proceso, o que estén todas las fases en blanco.
	        $sQry2 = "SELECT * FROM $nom_tabla WHERE id_activ=".$iid_activ." AND completado='f'
					ORDER BY n_orden LIMIT 1";
	        if (($qRs2 = $oDbl->query($sQry2)) === false) {
	            $sClauError = 'GestorActividadProcesoTarea.faseActual.prepare';
	            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
	            return false;
	        }
	        if ($qRs2->rowCount() == 1 ) {
	            return 'START';
	        } else {
	            return 'SIN';
	        }
	    }
	}
	
	/**
	 * Borra el procés per l'activitat.
	 *
	 * @param integer iid_activ
	 * @return none.
	 */
	function borrar($iid_activ='') {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
	    if (!empty($iid_activ)) {
	        $sQry = "DELETE FROM $nom_tabla WHERE id_activ=$iid_activ";
	        if (($oDblSt = $oDbl->query($sQry)) === false) {
	            $sClauError = 'GestorActividadProcesoTarea.get.prepare';
	            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
	            return false;
	        }
	    }
	}
	
	/**
	 * Genera el procés per l'activitat, segons el tipus de procés.
	 * retorna el id_fase de la primera fase. Serveix per la funció getFaseActual.
	 *
	 * @param integer iid_activ
	 * @param integer iid_tipo_proceso
	 * @return id_fase.
	 */
	function generar($iid_activ='',$iid_tipo_proceso='') {
	    $this->borrar($iid_activ);
	    $GesProceso = new GestorProceso();
	    $cProcesos = $GesProceso->getProcesos(array('id_tipo_proceso'=>$iid_tipo_proceso,'_ordre'=>'n_orden'));
	    $p=0;
	    $statusActividad = '';
	    foreach ($cProcesos as $oProceso) {
	        $p++;
	        extract($oProceso->getTot());
	        $oActividadProcesoTarea = new ActividadProcesoTarea();
	        $oActividadProcesoTarea->setId_tipo_proceso($iid_tipo_proceso);
	        $oActividadProcesoTarea->setId_activ($iid_activ);
	        $oActividadProcesoTarea->setId_fase($id_fase);
	        $oActividadProcesoTarea->setId_tarea($id_tarea);
	        $oActividadProcesoTarea->setN_orden($n_orden);
	        if ($p == 1) {
	            $oActividadProcesoTarea->setCompletado('t'); // Marco la primera fase como completado.
	            // marco el status correspondiente en la actividad. Hay que hacerlo al final para no entrar en
	            // un bucle recurente al modifiar una actividad nueva que todavía no tinen el proceso.
	            $statusActividad = $status;
	        }
	        if (($oActividadProcesoTarea->DBGuardar()) === false) {
	            echo "error: No se ha guardado el proceso: $iid_activ,$iid_tipo_proceso,$id_fase,$id_tarea,$n_orden<br>";
	            //return false;
	        }
	    }
	    if (!empty($statusActividad)) {
	        $oActividad = new Actividad($iid_activ);
	        $oActividad->DBCarregar();
	        $oActividad->setStatus($statusActividad);
	        $quiet = 1; // Para que no anote el cambio.
	        $oActividad->DBGuardar($quiet);
	    }
	    if(!empty($cProcesos[0])) {
	        return $cProcesos[0]->getId_fase();
	    } else {
	        echo "error al generar el proceso de la actividad: $iid_activ. tipo de proceso: $iid_tipo_proceso<br>";
	    }
	}
	
	/**
	 * retorna l'array d'objectes de tipus ActividadProcesoTarea
	 *
	 * @param string sQuery la query a executar.
	 * @return array Una col·lecció d'objectes de tipus ActividadProcesoTarea
	 */
	function getActividadProcesoTareasQuery($sQuery='') {
		$oDbl = $this->getoDbl();
		$oActividadProcesoTareaSet = new core\Set();
		if (($oDbl->query($sQuery)) === FALSE) {
			$sClauError = 'GestorActividadProcesoTarea.query';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return FALSE;
		}
		foreach ($oDbl->query($sQuery) as $aDades) {
			$a_pkey = array('id_item' => $aDades['id_item']);
			$oActividadProcesoTarea= new ActividadProcesoTarea($a_pkey);
			$oActividadProcesoTarea->setAllAtributes($aDades);
			$oActividadProcesoTareaSet->add($oActividadProcesoTarea);
		}
		return $oActividadProcesoTareaSet->getTot();
	}

	/**
	 * retorna l'array d'objectes de tipus ActividadProcesoTarea
	 *
	 * @param array aWhere associatiu amb els valors de les variables amb les quals farem la query
	 * @param array aOperators associatiu amb els valors dels operadors que cal aplicar a cada variable
	 * @return array Una col·lecció d'objectes de tipus ActividadProcesoTarea
	 */
	function getActividadProcesoTareas($aWhere=array(),$aOperators=array()) {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
		$oActividadProcesoTareaSet = new core\Set();
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
			$sClauError = 'GestorActividadProcesoTarea.llistar.prepare';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return FALSE;
		}
		if (($oDblSt->execute($aWhere)) === FALSE) {
			$sClauError = 'GestorActividadProcesoTarea.llistar.execute';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
			return FALSE;
		}
		foreach ($oDblSt as $aDades) {
			$a_pkey = array('id_item' => $aDades['id_item']);
			$oActividadProcesoTarea= new ActividadProcesoTarea($a_pkey);
			$oActividadProcesoTarea->setAllAtributes($aDades);
			$oActividadProcesoTareaSet->add($oActividadProcesoTarea);
		}
		return $oActividadProcesoTareaSet->getTot();
	}

	/* METODES PROTECTED --------------------------------------------------------*/

	/* METODES GET i SET --------------------------------------------------------*/
}