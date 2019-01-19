<?php
namespace procesos\model\entity;
use core;
use actividades\model\entity\Actividad;
/**
 * Fitxer amb la Classe que accedeix a la taula a_actividad_proceso
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 06/12/2018
 */
/**
 * Classe que implementa l'entitat a_actividad_proceso
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 06/12/2018
 */
class ActividadProcesoTarea Extends core\ClasePropiedades {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/**
	 * aPrimary_key de ActividadProcesoTarea
	 *
	 * @var array
	 */
	 private $aPrimary_key;

	/**
	 * aDades de ActividadProcesoTarea
	 *
	 * @var array
	 */
	 private $aDades;

	/**
	 * Id_item de ActividadProcesoTarea
	 *
	 * @var integer
	 */
	 private $iid_item;
	/**
	 * Id_tipo_proceso de ActividadProcesoTarea
	 *
	 * @var integer
	 */
	 private $iid_tipo_proceso;
	/**
	 * Id_activ de ActividadProcesoTarea
	 *
	 * @var integer
	 */
	 private $iid_activ;
	/**
	 * Id_fase de ActividadProcesoTarea
	 *
	 * @var integer
	 */
	 private $iid_fase;
	/**
	 * Id_tarea de ActividadProcesoTarea
	 *
	 * @var integer
	 */
	 private $iid_tarea;
	/**
	 * N_orden de ActividadProcesoTarea
	 *
	 * @var integer
	 */
	 private $in_orden;
	/**
	 * Completado de ActividadProcesoTarea
	 *
	 * @var boolean
	 */
	 private $bcompletado;
	/**
	 * Observ de ActividadProcesoTarea
	 *
	 * @var string
	 */
	 private $sobserv;
	/* ATRIBUTS QUE NO SÓN CAMPS------------------------------------------------- */
	/**
	 * oDbl de ActividadProcesoTarea
	 *
	 * @var object
	 */
	 protected $oDbl;
	/**
	 * NomTabla de ActividadProcesoTarea
	 *
	 * @var string
	 */
	 protected $sNomTabla;
	/* CONSTRUCTOR -------------------------------------------------------------- */

	/**
	 * Constructor de la classe.
	 * Si només necessita un valor, se li pot passar un integer.
	 * En general se li passa un array amb les claus primàries.
	 *
	 * @param integer|array iid_item
	 * 						$a_id. Un array con los nombres=>valores de las claves primarias.
	 */
	function __construct($a_id='') {
		$oDbl = $GLOBALS['oDBC'];
		if (is_array($a_id)) { 
			$this->aPrimary_key = $a_id;
			foreach($a_id as $nom_id=>$val_id) {
				if (($nom_id == 'id_item') && $val_id !== '') $this->iid_item = (int)$val_id; // evitem SQL injection fent cast a integer
			}	} else {
			if (isset($a_id) && $a_id !== '') {
				$this->iid_item = intval($a_id); // evitem SQL injection fent cast a integer
				$this->aPrimary_key = array('iid_item' => $this->iid_item);
			}
		}
		$this->setoDbl($oDbl);
		$this->setNomTabla('a_actividad_proceso');
	}

	/* METODES PUBLICS ----------------------------------------------------------*/

	/**
	 * Desa els atributs de l'objecte a la base de dades.
	 * Si no hi ha el registre, fa el insert, si hi es fa el update.
	 *
	 */
	public function DBGuardar() {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
		if ($this->DBCarregar('guardar') === FALSE) { $bInsert=TRUE; } else { $bInsert=FALSE; }
		$aDades=array();
		$aDades['id_tipo_proceso'] = $this->iid_tipo_proceso;
		$aDades['id_activ'] = $this->iid_activ;
		$aDades['id_fase'] = $this->iid_fase;
		$aDades['id_tarea'] = $this->iid_tarea;
		$aDades['n_orden'] = $this->in_orden;
		$aDades['completado'] = $this->bcompletado;
		$aDades['observ'] = $this->sobserv;
		array_walk($aDades, 'core\poner_null');
		//para el caso de los boolean FALSE, el pdo(+postgresql) pone string '' en vez de 0. Lo arreglo:
		$aDades['completado'] = ($aDades['completado'] === 't')? 'true' : $aDades['completado'];
		if ( filter_var( $aDades['completado'], FILTER_VALIDATE_BOOLEAN)) { $aDades['completado']='t'; } else { $aDades['completado']='f'; }

		if ($bInsert === FALSE) {
			//UPDATE
			$update="
					id_tipo_proceso          = :id_tipo_proceso,
					id_activ                 = :id_activ,
					id_fase                  = :id_fase,
					id_tarea                 = :id_tarea,
					n_orden                  = :n_orden,
					completado               = :completado,
					observ                   = :observ";
			if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_item='$this->iid_item'")) === FALSE) {
				$sClauError = 'ActividadProcesoTarea.update.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return FALSE;
			} else {
				if ($oDblSt->execute($aDades) === FALSE) {
					$sClauError = 'ActividadProcesoTarea.update.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return FALSE;
				} elseif (core\ConfigGlobal::is_app_installed('procesos')) {
				    if (core\ConfigGlobal::is_app_installed('avisos')) {
                        if (empty($quiet)) {
                            $oGestorCanvis = new GestorCanvis();
                            $oGestorCanvis->addCanvi('a_actividad_proceso', 'FASE', $this->iid_activ, $aDades, $this->aDadesActuals);
                        }
				    }
				    // comprobar si hay que cambiar el estado (status) de la actividad.
				    // en caso de completar la fase. Si se quita el 'completado' habría que buscar la fase anterior para saber que status corresponde.
				    $oActividad = new Actividad($this->iid_activ);
				    $statusActividad = $oActividad->getStatus();
				    $oProceso = new Proceso(array('id_tipo_proceso'=>$this->iid_tipo_proceso,'id_fase'=>$this->iid_fase,'id_tarea'=>$this->iid_tarea));
				    if ($aDades['completado'] == 't') {
				        $statusProceso = $oProceso->getStatus();
				    } else {
				        $itemProceso = $oProceso->getId_item();
				        $GesProcesos = new GestorProceso();
				        $statusProceso = $GesProcesos->getStatusFaseAnterior($itemProceso);
				    }
				    if ($statusProceso != $statusActividad) { // cambiar el status de la actividad.
				        // OJO si la actividad no es de la dl, no puedo cambiarla.
				        $dl_org = $oActividad->getDl_org();
				        $id_tabla = $oActividad->getId_tabla();
				        if ($dl_org == core\ConfigGlobal::mi_dele()) {
                            $oActividad->setStatus($statusProceso);
                            $oActividad->DBGuardar();
				        } else {
				            if ($id_tabla == 'dl') {
				                //$oActividad = new ActividadPub($a_pkey);
				                // No se puede eliminar una actividad de otra dl
				                echo _("no se puede modificar el status de una actividad de otra dl");
				                //return false;
				            } else {
                                $oActividad->setStatus($statusProceso);
                                $oActividad->DBGuardar();
				            }
				        }
				    }
				}
			}
		} else {
			// INSERT
			$campos="(id_tipo_proceso,id_activ,id_fase,id_tarea,n_orden,completado,observ)";
			$valores="(:id_tipo_proceso,:id_activ,:id_fase,:id_tarea,:n_orden,:completado,:observ)";		
			if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === FALSE) {
				$sClauError = 'ActividadProcesoTarea.insertar.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return FALSE;
			} else {
				if ($oDblSt->execute($aDades) === FALSE) {
					$sClauError = 'ActividadProcesoTarea.insertar.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return FALSE;
				}
			}
			$this->id_item = $oDbl->lastInsertId('a_actividad_proceso_id_item_seq');
		}
		$this->setAllAtributes($aDades);
		return TRUE;
	}

	/**
	 * Carrega els camps de la base de dades com atributs de l'objecte.
	 *
	 */
	public function DBCarregar($que=null) {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
		if (isset($this->iid_item)) {
			if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_item='$this->iid_item'")) === FALSE) {
				$sClauError = 'ActividadProcesoTarea.carregar';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return FALSE;
			}
			$aDades = $oDblSt->fetch(\PDO::FETCH_ASSOC);
			switch ($que) {
				case 'tot':
					$this->aDades=$aDades;
					break;
				case 'guardar':
					if (!$oDblSt->rowCount()) return FALSE;
					break;
				default:
					$this->setAllAtributes($aDades);
			}
			return TRUE;
		} else {
		   	return FALSE;
		}
	}

	/**
	 * Elimina el registre de la base de dades corresponent a l'objecte.
	 *
	 */
	public function DBEliminar() {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
		if ($this->DBCarregar('guardar') === false) {
		    // Si no existeix no cal eliminar-el.
		    return false;
		} else {
		    // ho poso abans d'esborrar perque sino no trova cap valor. En el cas d'error s'hauria d'esborrar l'apunt.
            if (core\ConfigGlobal::is_app_installed('avisos')) {
                $oGestorCanvis = new GestorCanvis();
                $oGestorCanvis->addCanvi('a_actividad_proceso', 'FASE', $this->iid_activ, array(), $this->aDadesActuals);
            }

            if (($oDbl->exec("DELETE FROM $nom_tabla WHERE id_item='$this->iid_item'")) === FALSE) {
                $sClauError = 'ActividadProcesoTarea.eliminar';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return FALSE;
            }
            return TRUE;
		}
	}
	
	/* METODES ALTRES  ----------------------------------------------------------*/
	/* METODES PRIVATS ----------------------------------------------------------*/

	/**
	 * Estableix el valor de tots els atributs
	 *
	 * @param array $aDades
	 */
	function setAllAtributes($aDades) {
		if (!is_array($aDades)) return;
		if (array_key_exists('id_item',$aDades)) $this->setId_item($aDades['id_item']);
		if (array_key_exists('id_tipo_proceso',$aDades)) $this->setId_tipo_proceso($aDades['id_tipo_proceso']);
		if (array_key_exists('id_activ',$aDades)) $this->setId_activ($aDades['id_activ']);
		if (array_key_exists('id_fase',$aDades)) $this->setId_fase($aDades['id_fase']);
		if (array_key_exists('id_tarea',$aDades)) $this->setId_tarea($aDades['id_tarea']);
		if (array_key_exists('n_orden',$aDades)) $this->setN_orden($aDades['n_orden']);
		if (array_key_exists('completado',$aDades)) $this->setCompletado($aDades['completado']);
		if (array_key_exists('observ',$aDades)) $this->setObserv($aDades['observ']);
	}

	/* METODES GET i SET --------------------------------------------------------*/

	/**
	 * Recupera tots els atributs de ActividadProcesoTarea en un array
	 *
	 * @return array aDades
	 */
	function getTot() {
		if (!is_array($this->aDades)) {
			$this->DBCarregar('tot');
		}
		return $this->aDades;
	}

	/**
	 * Recupera las claus primàries de ActividadProcesoTarea en un array
	 *
	 * @return array aPrimary_key
	 */
	function getPrimary_key() {
		if (!isset($this->aPrimary_key )) {
			$this->aPrimary_key = array('id_item' => $this->iid_item);
		}
		return $this->aPrimary_key;
	}

	/**
	 * Recupera l'atribut iid_item de ActividadProcesoTarea
	 *
	 * @return integer iid_item
	 */
	function getId_item() {
		if (!isset($this->iid_item)) {
			$this->DBCarregar();
		}
		return $this->iid_item;
	}
	/**
	 * estableix el valor de l'atribut iid_item de ActividadProcesoTarea
	 *
	 * @param integer iid_item
	 */
	function setId_item($iid_item) {
		$this->iid_item = $iid_item;
	}
	/**
	 * Recupera l'atribut iid_tipo_proceso de ActividadProcesoTarea
	 *
	 * @return integer iid_tipo_proceso
	 */
	function getId_tipo_proceso() {
		if (!isset($this->iid_tipo_proceso)) {
			$this->DBCarregar();
		}
		return $this->iid_tipo_proceso;
	}
	/**
	 * estableix el valor de l'atribut iid_tipo_proceso de ActividadProcesoTarea
	 *
	 * @param integer iid_tipo_proceso='' optional
	 */
	function setId_tipo_proceso($iid_tipo_proceso='') {
		$this->iid_tipo_proceso = $iid_tipo_proceso;
	}
	/**
	 * Recupera l'atribut iid_activ de ActividadProcesoTarea
	 *
	 * @return integer iid_activ
	 */
	function getId_activ() {
		if (!isset($this->iid_activ)) {
			$this->DBCarregar();
		}
		return $this->iid_activ;
	}
	/**
	 * estableix el valor de l'atribut iid_activ de ActividadProcesoTarea
	 *
	 * @param integer iid_activ='' optional
	 */
	function setId_activ($iid_activ='') {
		$this->iid_activ = $iid_activ;
	}
	/**
	 * Recupera l'atribut iid_fase de ActividadProcesoTarea
	 *
	 * @return integer iid_fase
	 */
	function getId_fase() {
		if (!isset($this->iid_fase)) {
			$this->DBCarregar();
		}
		return $this->iid_fase;
	}
	/**
	 * estableix el valor de l'atribut iid_fase de ActividadProcesoTarea
	 *
	 * @param integer iid_fase='' optional
	 */
	function setId_fase($iid_fase='') {
		$this->iid_fase = $iid_fase;
	}
	/**
	 * Recupera l'atribut iid_tarea de ActividadProcesoTarea
	 *
	 * @return integer iid_tarea
	 */
	function getId_tarea() {
		if (!isset($this->iid_tarea)) {
			$this->DBCarregar();
		}
		return $this->iid_tarea;
	}
	/**
	 * estableix el valor de l'atribut iid_tarea de ActividadProcesoTarea
	 *
	 * @param integer iid_tarea='' optional
	 */
	function setId_tarea($iid_tarea='') {
		$this->iid_tarea = $iid_tarea;
	}
	/**
	 * Recupera l'atribut in_orden de ActividadProcesoTarea
	 *
	 * @return integer in_orden
	 */
	function getN_orden() {
		if (!isset($this->in_orden)) {
			$this->DBCarregar();
		}
		if (empty($this->in_orden)) printf(_('No debería ser 0. En %s, linea %s'), __FILE__,__LINE__);
		return $this->in_orden;
	}
	/**
	 * estableix el valor de l'atribut in_orden de ActividadProcesoTarea
	 *
	 * @param integer in_orden='' optional
	 */
	function setN_orden($in_orden='') {
		$this->in_orden = $in_orden;
	}
	/**
	 * Recupera l'atribut bcompletado de ActividadProcesoTarea
	 *
	 * @return boolean bcompletado
	 */
	function getCompletado() {
		if (!isset($this->bcompletado)) {
			$this->DBCarregar();
		}
		return $this->bcompletado;
	}
	/**
	 * estableix el valor de l'atribut bcompletado de ActividadProcesoTarea
	 *
	 * @param boolean bcompletado='f' optional
	 */
	function setCompletado($bcompletado='f') {
		$this->bcompletado = $bcompletado;
	}
	/**
	 * Recupera l'atribut sobserv de ActividadProcesoTarea
	 *
	 * @return string sobserv
	 */
	function getObserv() {
		if (!isset($this->sobserv)) {
			$this->DBCarregar();
		}
		return $this->sobserv;
	}
	/**
	 * estableix el valor de l'atribut sobserv de ActividadProcesoTarea
	 *
	 * @param string sobserv='' optional
	 */
	function setObserv($sobserv='') {
		$this->sobserv = $sobserv;
	}
	/* METODES GET i SET D'ATRIBUTS QUE NO SÓN CAMPS -----------------------------*/

	/**
	 * Retorna una col·lecció d'objectes del tipus DatosCampo
	 *
	 */
	function getDatosCampos() {
		$oActividadProcesoTareaSet = new core\Set();

		$oActividadProcesoTareaSet->add($this->getDatosId_tipo_proceso());
		$oActividadProcesoTareaSet->add($this->getDatosId_activ());
		$oActividadProcesoTareaSet->add($this->getDatosId_fase());
		$oActividadProcesoTareaSet->add($this->getDatosId_tarea());
		$oActividadProcesoTareaSet->add($this->getDatosN_orden());
		$oActividadProcesoTareaSet->add($this->getDatosCompletado());
		$oActividadProcesoTareaSet->add($this->getDatosObserv());
		return $oActividadProcesoTareaSet->getTot();
	}



	/**
	 * Recupera les propietats de l'atribut iid_tipo_proceso de ActividadProcesoTarea
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosId_tipo_proceso() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'id_tipo_proceso'));
		$oDatosCampo->setEtiqueta(_("id_tipo_proceso"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut iid_activ de ActividadProcesoTarea
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosId_activ() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'id_activ'));
		$oDatosCampo->setEtiqueta(_("id_activ"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut iid_fase de ActividadProcesoTarea
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosId_fase() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'id_fase'));
		$oDatosCampo->setEtiqueta(_("id_fase"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut iid_tarea de ActividadProcesoTarea
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosId_tarea() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'id_tarea'));
		$oDatosCampo->setEtiqueta(_("id_tarea"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut in_orden de ActividadProcesoTarea
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosN_orden() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'n_orden'));
		$oDatosCampo->setEtiqueta(_("n_orden"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut bcompletado de ActividadProcesoTarea
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosCompletado() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'completado'));
		$oDatosCampo->setEtiqueta(_("completado"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut sobserv de ActividadProcesoTarea
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosObserv() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'observ'));
		$oDatosCampo->setEtiqueta(_("observ"));
		return $oDatosCampo;
	}
}
