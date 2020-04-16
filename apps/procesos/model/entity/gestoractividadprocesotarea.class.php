<?php
namespace procesos\model\entity;

use actividades\model\entity\Actividad;
use actividades\model\entity\ActividadEx;
use actividades\model\entity\TipoDeActividad;
use core\ClaseGestor;
use core\Condicion;
use core\ConfigGlobal;
use core\Set;
use function core\is_true;
use ubis\model\entity\Casa;
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

class GestorActividadProcesoTarea Extends ClaseGestor {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/* CONSTRUCTOR -------------------------------------------------------------- */

	/**
	 * Només per aquest cas sobreescric la funció per fer-la publica.
	 * estableix el valor de l'atribut sNomTabla de Grupo
	 *
	 * @param string sNomTabla
	 */
	public function setNomTabla($sNomTabla) {
		$this->sNomTabla = $sNomTabla;
	}
	
	/**
	 * Constructor de la classe.
	 *
	 * @return $gestor
	 *
	 */
	function __construct() {
		$oDbl = $GLOBALS['oDBC'];
		$this->setoDbl($oDbl);
		if (ConfigGlobal::mi_sfsv() == 1) {
		    $this->setNomTabla('a_actividad_proceso_sv');
		} else {
		    $this->setNomTabla('a_actividad_proceso_sf');
		}
	}


	/* METODES PUBLICS -----------------------------------------------------------*/
	public function borrarFaseTareaInexistente($id_tipo_proceso,$id_fase,$id_tarea) {
        $oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
		$nom_tabla_procesos = 'a_tareas_proceso'; 
		
		$temp_table = "tmp_borrar";
		$sQuery = "CREATE TEMPORARY TABLE $temp_table AS ";
		$sQuery .= "SELECT a.id_activ,a.id_fase,id_tarea
                    FROM $nom_tabla a LEFT JOIN $nom_tabla_procesos p USING (id_tipo_proceso,id_fase,id_tarea)
                    WHERE id_tipo_proceso=$id_tipo_proceso AND p.id_fase IS NULL";

		if (($oDblSt = $oDbl->query($sQuery)) === FALSE) {
	        $sClauError = 'GestorActividadProcesoTarea.fasesCompletadas.prepare';
	        $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
	        return false;
	    }

		// Borrar:
		$sQry_INSERT = "DELETE FROM $nom_tabla a
                        USING $temp_table t
                        WHERE  a.id_activ = t.id_activ
                            AND a.id_fase = t.id_fase
                            AND a.id_tarea = t.id_tarea
                       ";
	       
		if (($oDblSt = $oDbl->query($sQry_INSERT)) === FALSE) {
	        $sClauError = 'GestorActividadProcesoTarea.fasesCompletadas.prepare';
	        $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
	        return false;
	    }
	    
        
	}
	public function añadirFaseTarea($id_tipo_proceso,$id_fase,$id_tarea) {
        $oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
		
		$temp_table = "tmp_proceso_".$id_fase."_".$id_tarea;
		$sQuery = "CREATE TEMPORARY TABLE $temp_table AS ";
	    $sQuery .= "(SELECT DISTINCT id_activ FROM $nom_tabla WHERE id_tipo_proceso=$id_tipo_proceso)";
	    $sQuery .= " EXCEPT "; 
        $sQuery .= "(SELECT DISTINCT id_activ FROM $nom_tabla 
                    WHERE id_tipo_proceso=$id_tipo_proceso AND id_fase=$id_fase AND id_tarea=$id_tarea)";
        
		if (($oDblSt = $oDbl->query($sQuery)) === FALSE) {
	        $sClauError = 'GestorActividadProcesoTarea.fasesCompletadas.prepare';
	        $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
	        return false;
	    }

		// Añadir fase:
		$sQry_INSERT = "INSERT INTO $nom_tabla (id_tipo_proceso,id_activ,id_fase,id_tarea)    
                        SELECT $id_tipo_proceso, id_activ, $id_fase, $id_tarea FROM $temp_table";
	       
		if (($oDblSt = $oDbl->query($sQry_INSERT)) === FALSE) {
	        $sClauError = 'GestorActividadProcesoTarea.fasesCompletadas.prepare';
	        $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
	        return false;
	    }
	}
	
	/**
	 * retorna un array amb les fases i el seu estat.
	 * 
	 * @param integer $iid_activ
	 * @return array $aFasesEstado = [ id_fase => $completado ]
	 */
	public function getListaFaseEstado($iid_activ) {
        $oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
	    $sQuery = "SELECT * FROM $nom_tabla WHERE id_activ=$iid_activ
                ";
		if (($oDblSt = $oDbl->query($sQuery)) === FALSE) {
	        $sClauError = 'GestorActividadProcesoTarea.fasesCompletadas.prepare';
	        $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
	        return false;
	    }

	    $aFasesEstado = [];
		foreach ($oDblSt as $aDades) {
		    $id_fase = $aDades['id_fase'];
		    $id_tarea = $aDades['id_tarea'];
		    $completado = $aDades['completado'];
            $f = "$id_fase#$id_tarea";
		    $aFasesEstado[$f] = $completado;
	    }
	    return $aFasesEstado;
	}
	
	public function zzmarcarFasesAnteriores($iid_activ, $iid_fase) {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();

	    $sQry = "UPDATE $nom_tabla SET completado='t'
                FROM ( SELECT n_orden FROM $nom_tabla
                    WHERE id_activ=$iid_activ AND id_fase=$iid_fase
                ) AS OrdenFase
                WHERE $nom_tabla.id_activ=$iid_activ AND $nom_tabla.n_orden < OrdenFase.n_orden 
                ";
	    if ($oDbl->query($sQry) === false) {
	        $sClauError = 'GestorActividadProcesoTarea.faseCompletada.prepare';
	        $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
	        return FALSE;
	    }
        return TRUE;
	}
	
	public function zzborrarFasesSiguientes($iid_activ, $iid_fase) {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();

	    $sQry = "UPDATE $nom_tabla SET completado='f'
                FROM ( SELECT n_orden FROM $nom_tabla
                    WHERE id_activ=$iid_activ AND id_fase=$iid_fase
                ) AS OrdenFase
                WHERE $nom_tabla.id_activ=$iid_activ AND $nom_tabla.n_orden > OrdenFase.n_orden 
                ";
	    if ($oDbl->query($sQry) === false) {
	        $sClauError = 'GestorActividadProcesoTarea.faseCompletada.prepare';
	        $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
	        return FALSE;
	    }
        return TRUE;
	}
	
	/**
	 * Devueleve el estado de la fase ("ok atn sacd" que es la 1) 
	 * o FALSE si falla.
	 * 
	 * @param $iid_activ
	 * @return 't'|'f'|FALSE 
	 */
	public function getSacdAprobado($iid_activ){
        $oDbl = $this->getoDbl();
        // Mirar el proceso de la sv
        $this->setNomTabla('a_actividad_proceso_sv');
        // La fase ok sacd es la 5. Por definición
        $id_fase_atn_sacd = ActividadFase::FASE_OK_SACD;
        $nom_tabla = $this->getNomTabla();
        
        $sQry = "SELECT completado FROM $nom_tabla WHERE id_activ=".$iid_activ." AND id_fase=$id_fase_atn_sacd ";
        if (($qRs = $oDbl->query($sQry)) === false) {
            $sClauError = 'GestorActividadProcesoTarea.getSacdAprobado.prepare';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        if ($qRs->rowCount() == 1 ) {
            $aDades = $qRs->fetch(\PDO::FETCH_ASSOC);
            return $aDades['completado'];
        }
        return FALSE;
	}
	
	/**
	 * En gerneral genera los dos porcesos, para sv y sf.
	 * Si se le pasa el parametro isfsv, sólo genera el proceso correspondiente.
	 * 
	 * @param string $iid_activ
	 * @param integer $isfsv
	 * @param boolean $force para forzar a borrar el proceso y generarlo de nuevo
	 * @return boolean|\procesos\model\entity\id_fase.
	 */
	public function generarProceso($iid_activ='',$isfsv='',$force=FALSE) {
	    // Si se genera al crear una actividad Ex. El objeto Actividad no la encuentra
	    // porque todavía no se ha importado (y no está en su grupo de actividades).
	    // Para evitar errores accedo directamente a los datos sin esperar a importarla,
	    // En principio la dl que la crea es porque va a importarla...
	    if ($iid_activ < 0 ) {
	       $oActividad = new ActividadEx(array('id_activ'=>$iid_activ));
	    } else {
	       $oActividad = new Actividad(array('id_activ'=>$iid_activ));
	    }
	    $iid_tipo_activ = $oActividad->getId_tipo_activ();
	    $oTipo = new TipoDeActividad(array('id_tipo_activ'=>$iid_tipo_activ));
	   
	    // Creo que cuando pasa es que no existe la actividad (pero se tiene el id_activ)
	    if (empty($oActividad) OR empty($iid_tipo_activ)) {
            echo sprintf(_("La actividad: %s ya no existe"),$iid_activ);
            return TRUE;
	    }
	    $dl_org = $oActividad->getDl_org();
        $dl_org_no_f = preg_replace('/(\.*)f$/', '\1', $dl_org);
        
        if (empty($isfsv)) {
            $a_sfsv = [1,2];
            $isfsv = ConfigGlobal::mi_sfsv();
        } else {
            $a_sfsv = [$isfsv];
        }
        $iid_fase = [];
        foreach ($a_sfsv as $sfsv) {
            if ($sfsv == 1) {
                $this->setNomTabla('a_actividad_proceso_sv');
            } else {
                $this->setNomTabla('a_actividad_proceso_sf');
            }
            if ($dl_org_no_f == ConfigGlobal::mi_dele()) {
                $id_tipo_proceso=$oTipo->getId_tipo_proceso($sfsv);
            } else {
                // NO se genera si:
                // - es una actividad de otra dl,
                // - y de la otra sección
                // - y no se hace en una casa de la dl.
                if ($isfsv != $sfsv) {
                    $id_ubi = $oActividad->getId_ubi();
                    $oUbi = new Casa($id_ubi);
                    $dl_casa = $oUbi->getDl();
                    if ($dl_casa != ConfigGlobal::mi_dele()) {
                        continue;
                    }
                }
                $id_tipo_proceso=$oTipo->getId_tipo_proceso_ex($sfsv);
            }
            if (empty($id_tipo_proceso)) {
                echo sprintf(_("No tiene definido el proceso para este tipo de actividad: %s de sv/sf: %s"),$iid_tipo_activ,$sfsv);
                return TRUE;
            }
            // Asegurar que no existe, a veces al hacerlo para las dos secciones, una lo tiene y otra no:
            // >> Cuando se hace manual, es porque se quiere regenerar y hay que forzar:
            if ($force === FALSE) {
                $cActividadProcesoTarea = $this->getActividadProcesoTareas(['id_activ' => $iid_activ]);
                if (empty($cActividadProcesoTarea)) {
                    $iid_fase[$sfsv] = $this->generar($iid_activ,$id_tipo_proceso,$sfsv);
                } else {
                    $iid_fase[$sfsv] = $cActividadProcesoTarea[0]->getId_fase();
                }
            } else {
                $iid_fase[$sfsv] = $this->generar($iid_activ,$id_tipo_proceso,$sfsv);
            }
        }

        // devuelve la fase del proceso propio
	    return $iid_fase[$isfsv];
	}
	
	public function zzgetFaseActual($iid_activ='') {
	    if (empty($iid_activ)) return false;
	    // fase en la que se encuentra actualmente
	    $iid_fase = $this->getFaseActualAcabada($iid_activ);
	    if (is_numeric($iid_fase)) {
	        return $iid_fase;
	    } else {
	        if ($iid_fase === 'START') { // devuelve la primera
	            $iid_fase = $this->getFasePrimera($iid_activ);
	            return $iid_fase;
	        }
	        if ($iid_fase === 'END') { // devuelve la última fase
	            $iid_fase = $this->getFaseUltima($iid_activ);
	            return $iid_fase;
	        }
	        if (empty($iid_fase) || $iid_fase === 'SIN') {
	            //echo sprintf(_("esta actividad: %s no tiene ninguna fase. Se está generando..."),$oActividad->getNom_activ());
	            //echo '<br>'._("ATENCIÓN: puede que tenga que actualizar la página para que salgan todas las actividades.");
	            //echo '<br>';
	            return $this->generarProceso($iid_activ);
	        }
	    }
	}
	
	/**
	 * retorna un array amb les fases completades.
	 *
	 * @param integer iid_activ
	 * @return array
	 */
	function getFasesCompletadas($iid_activ='') {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
		// No puedo hacer la consulta con WHERE completado='t',
		// porque hay que distinguirlo de si existe el proceso o no, y hay que crearlo.
	    //$sQuery = "SELECT * FROM $nom_tabla WHERE id_activ=$iid_activ
        //        AND completado='t' ";
	    $sQuery = "SELECT * FROM $nom_tabla WHERE id_activ=$iid_activ ";
		if (($oDblSt = $oDbl->query($sQuery)) === FALSE) {
	        $sClauError = 'GestorActividadProcesoTarea.fasesCompletadas.prepare';
	        $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
	        return false;
	    }
	    if ($oDblSt->rowCount() > 0 ) {
            $aFasesCompletadas = [];
            foreach ($oDblSt as $aDades) {
                if (is_true($aDades['completado'])) {
                    $aFasesCompletadas[] = $aDades['id_fase'];
                }
            }
            return $aFasesCompletadas;
	    } else {
            // no existe el proceso:
            $id_fase_primera = $this->generarProceso($iid_activ);
            ////return $this->getFasesCompletadas($iid_activ);	        
            return [$id_fase_primera]; 
	    }
	}
	/**
	 * retorna si té la fase completada o no.
	 *
	 * @param integer iid_activ
	 * @param integer iid_fase
	 * @return bool
	 */
	public function faseCompletada($iid_activ,$iid_fase) {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
		// No puedo hacer la consulta con WHERE completado='t',
		// porque hay que distinguirlo de si existe el proceso o no, y hay que crearlo.
	    $sQry = "SELECT * FROM $nom_tabla WHERE id_activ=$iid_activ AND id_fase=$iid_fase ";
	    if (($oDblSt = $oDbl->query($sQry)) === false) {
	        $sClauError = 'GestorActividadProcesoTarea.faseCompletada.prepare';
	        $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
	        return false;
	    }
	    if ($oDblSt->rowCount() == 1 ) {
            // aunque realmente solo debería existir un fila
            foreach ($oDblSt as $aDades) {
                if (is_true($aDades['completado'])) {
                    return TRUE;
                } else {
                    return FALSE;
                }
            }
	    } else {
            // no existe el proceso:
            $this->generarProceso($iid_activ);
	        return FALSE;
	    }
	}
	
	public function zzgetFaseAnteriorCompletada($iid_activ,$id_fase) {
	    $a_fases_proceso = $this->getFasesCompletadas($iid_activ);
	    
	    $id_fase_anterior = '';
	    while (current($a_fases_proceso) !== $id_fase) {
	        $id_fase_anterior = current($a_fases_proceso);
	        next($a_fases_proceso);
	    }
	    return $id_fase_anterior;

	}

	/**
	 * retorna un integer id_fase que és la primera del seu proces.
	 *
	 * @param integer iid_activ
	 * @return integer
	 */
	private function zzgetFasePrimera($iid_activ='') {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
	    $sQry = "SELECT * FROM $nom_tabla WHERE id_activ=".$iid_activ;
	    if (($qRs = $oDbl->query($sQry)) === false) {
	        $sClauError = 'GestorActividadProcesoTarea.getFasePrimera.prepare';
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
	 * retorna un integer id_fase que és la última del seu proces.
	 *
	 * @param integer iid_activ
	 * @return integer
	 */
	private function zzgetFaseUltima($iid_activ='') {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
	    $sQry = "SELECT * FROM $nom_tabla WHERE id_activ=".$iid_activ;
	    if (($qRs = $oDbl->query($sQry)) === false) {
	        $sClauError = 'GestorActividadProcesoTarea.getFaseUltima.prepare';
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
	function zzfaseActual($iid_activ='') {
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
	public function zzgetFaseActualAcabada($iid_activ='') {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
	    $sQry = "SELECT * FROM $nom_tabla WHERE id_activ=".$iid_activ." AND completado='t'
				ORDER BY n_orden DESC LIMIT 1";
	    if (($qRs = $oDbl->query($sQry)) === false) {
	        $sClauError = 'GestorActividadProcesoTarea.getFaseActual.prepare';
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
	            $sClauError = 'GestorActividadProcesoTarea.getFaseActual.prepare';
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
	private function borrar($iid_activ='') {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
	    if (!empty($iid_activ)) {
	        $sQry = "DELETE FROM $nom_tabla WHERE id_activ=$iid_activ";
	        if (($oDbl->query($sQry)) === false) {
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
	private function generar($iid_activ='',$iid_tipo_proceso='',$isfsv='') {
	    $this->borrar($iid_activ);
	    $aWhere = [
	        'id_tipo_proceso'=>$iid_tipo_proceso,
	        '_ordre' => '(json_fases_previas::json->0)::text DESC'
	    ];
	    $GesTareaProceso = new GestorTareaProceso();
	    $cTareasProceso = $GesTareaProceso->getTareasProceso($aWhere);
	    $p=0;
	    $statusActividad = '';
	    foreach ($cTareasProceso as $oTareaProceso) {
	        $p++;
	        $id_fase = $oTareaProceso->getId_fase();
	        $id_tarea = $oTareaProceso->getId_tarea();
	        $status = $oTareaProceso->getStatus();
	        $oActividadProcesoTarea = new ActividadProcesoTarea();
            $oActividadProcesoTarea->setSfsv($isfsv);
	        $oActividadProcesoTarea->setId_tipo_proceso($iid_tipo_proceso);
	        $oActividadProcesoTarea->setId_activ($iid_activ);
	        $oActividadProcesoTarea->setId_fase($id_fase);
	        $oActividadProcesoTarea->setId_tarea($id_tarea);
	        if ($p == 1) {
	            $oActividadProcesoTarea->setCompletado('t'); // Marco la primera fase como completado.
	            // marco el status correspondiente en la actividad. Hay que hacerlo al final para no entrar en
	            // un bucle recurente al modifiar una actividad nueva que todavía no tinen el proceso.
	            $statusActividad = $status;
	        }
	        if (($oActividadProcesoTarea->DBGuardar()) === false) {
	            echo "error: No se ha guardado el proceso: $iid_activ,$iid_tipo_proceso,$id_fase,$id_tarea<br>";
	            //return false;
	        }
	    }
	    if (!empty($statusActividad)) {
	        $oActividad = new Actividad($iid_activ);
	        $oActividad->DBCarregar();
	        $nom_activ = $oActividad->getNom_activ();
	        $dl_org = $oActividad->getDl_org();
            $dl_org_no_f = preg_replace('/(\.*)f$/', '\1', $dl_org);
            // El status solo se puede guardar si la actividad es de la propia dl (o des desde sv).
            if ($dl_org_no_f == ConfigGlobal::mi_delef() && $_SESSION['oPerm']->have_perm_oficina('des')) {
                $oActividad->setStatus($statusActividad);
                $quiet = 1; // Para que no anote el cambio.
                $oActividad->DBGuardar($quiet);
	        }
	    }
	    if(!empty($cTareasProceso[0])) {
	        return $cTareasProceso[0]->getId_fase();
	    } else {
	        $oProcesoTipo = new ProcesoTipo($iid_tipo_proceso);
	        $nom_proceso = empty($oProcesoTipo->getNom_proceso())? $iid_tipo_proceso : $oProcesoTipo->getNom_proceso();
	        $nom_activ = empty($nom_activ)? $iid_activ : $nom_activ;
	        
	        $msg = sprintf(_("error al generar el proceso de la actividad: '%s'. Tipo de proceso: '%s' para sf/sv: %s."), $iid_activ,$nom_proceso,$isfsv);
            $msg .= "\n";
            $msg .= _("Probablemente no esté defindo el proceso");
            $msg .= "\n";
            $msg .= "<br>";
            echo $msg;
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
		$oActividadProcesoTareaSet = new Set();
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
		$oActividadProcesoTareaSet = new Set();
		$oCondicion = new Condicion();
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
			// OJO hay que cambiar la tabla si estoy modificando la sf
            $oActividadProcesoTarea->setNomTabla($this->sNomTabla);
			
			$oActividadProcesoTarea->setAllAtributes($aDades);
			$oActividadProcesoTareaSet->add($oActividadProcesoTarea);
		}
		return $oActividadProcesoTareaSet->getTot();
	}

	/* METODES PROTECTED --------------------------------------------------------*/

	/* METODES GET i SET --------------------------------------------------------*/
}
