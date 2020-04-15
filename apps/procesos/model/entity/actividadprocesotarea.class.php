<?php
namespace procesos\model\entity;
use actividades\model\entity\Actividad;
use actividades\model\entity\ActividadAll;
use cambios\model\gestorAvisoCambios;
use core\ConfigGlobal;
use function core\is_true;
use core;
use menus\model\PermisoMenu;
/**
 * Fitxer amb la Classe que accedeix a la taula a_actividad_proceso_(sf/sv)
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 06/12/2018
 */
/**
 * Classe que implementa l'entitat a_actividad_proceso_(sf/sv)
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
     * aDades de ActividadProcesoTarea abans dels canvis.
     *
     * @var array
     */
    private $aDadesActuals;
    
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
    
    
    
    /**
     * force de ActividadProcesoTarea
     * para forzar a cambiar fases dependientes
     *
     * @var boolean
     */
    protected $bForce = FALSE;
    /**
     * Per les funcions de mirar i marcar dependientes.
     * 
     * @var array
     */
    protected $aFasesTareasEncadenadas;
    /**
     * Para cada fase, las fases que dependen de ella.
     * 
     * @var array
     */
    protected $aFasesPosteriores;
    /**
     * para cada fase, las fases de las que depende.
     * 
     * @var array
     */
    protected $aFasesPrevias;
    /**
     * Per les funcions de mirar i marcar dependientes.
     * 
     * @var array
     */
    protected $aFasesEstado;

    /**
     * Per les funcions de mirar i marcar dependientes.
     * 
     * @var array
     */
    protected $aOpcionesOficinas;
    /**
     * Per evitar referencias circulars.
     * 
     * @var array
     */
    protected $aStack = [];
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

    public function setSfsv ($isfsv='') {
        if (empty($isfsv)) {
            if (ConfigGlobal::mi_sfsv() == 1) {
                $this->setNomTabla('a_actividad_proceso_sv');
            } else {
                $this->setNomTabla('a_actividad_proceso_sf');
            }
        } else {
            if ($isfsv == 1) {
                $this->setNomTabla('a_actividad_proceso_sv');
            } else {
                $this->setNomTabla('a_actividad_proceso_sf');
            }
        }
    }
    
    
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
            }
        } else {
            if (isset($a_id) && $a_id !== '') {
                $this->iid_item = intval($a_id); // evitem SQL injection fent cast a integer
                $this->aPrimary_key = array('id_item' => $this->iid_item);
            }
        }
        $this->setoDbl($oDbl);
        $this->setSfsv();
        $this->cargar_permisos();
    }
    
    /* METODES PUBLICS ----------------------------------------------------------*/
    
    /**
     * Desa els atributs de l'objecte a la base de dades.
     * Si no hi ha el registre, fa el insert, si hi es fa el update.
     *
     *@param bool optional $quiet : true per que no apunti els canvis. 0 (per defecte) apunta els canvis.
     */
    public function DBGuardar($quiet=0) {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        if ($this->DBCarregar('guardar') === FALSE) { $bInsert=TRUE; } else { $bInsert=FALSE; }
        $aDades=array();
        $aDades['id_tipo_proceso'] = $this->iid_tipo_proceso;
        $aDades['id_activ'] = $this->iid_activ;
        $aDades['id_fase'] = $this->iid_fase;
        $aDades['id_tarea'] = $this->iid_tarea;
        $aDades['completado'] = $this->bcompletado;
        $aDades['observ'] = $this->sobserv;
        array_walk($aDades, 'core\poner_null');
        //para el caso de los boolean FALSE, el pdo(+postgresql) pone string '' en vez de 0. Lo arreglo:
        if ( core\is_true($aDades['completado']) ) { $aDades['completado']='true'; } else { $aDades['completado']='false'; }
        
        if ($bInsert === FALSE) {
            // comprobar si hay que cambiar el estado (status) de la actividad.
            // en caso de completar la fase. Si se quita el 'completado' habría que buscar la fase anterior para saber que status corresponde.
            $permitido = TRUE;
            $oActividad = new Actividad($this->iid_activ);
            $statusActividad = $oActividad->getStatus();
            $GesTareaProcesos = new GestorTareaProceso();
            $cTareasProceso = $GesTareaProcesos->getTareasProceso(['id_tipo_proceso'=>$this->iid_tipo_proceso,'id_fase'=>$this->iid_fase,'id_tarea'=>$this->iid_tarea]);
            // sólo debería haber uno
            if (!empty($cTareasProceso)) {
                $oTareaProceso = $cTareasProceso[0];
            } else {
                $msg_err = sprintf(_("error: La fase del proceso tipo: %s, fase: %s, tarea: %s"),$this->iid_tipo_proceso,$this->iid_fase,$this->iid_tarea);
                exit($msg_err);
            }
            $fase_tarea = $this->iid_fase.'#'.$this->iid_tarea;
            // comprobar que tengo permiso
            if (!$this->tiene_permiso($fase_tarea)) {
                // No se puede marcar por alguna razón.
                echo _("No tiene permiso para marcar o desmarcar esta fase");
                exit();
            }
            $this->cargarFases();
            if ( is_true($aDades['completado']) ) {
                $statusProceso = $oTareaProceso->getStatus();
                $this->marcar($fase_tarea);
            } else {
                $this->desmarcar($fase_tarea);
                $statusProceso = $GesTareaProcesos->getStatusProceso($this->iid_tipo_proceso,$this->aFasesEstado);
            }
            if ($statusProceso != $statusActividad) { // cambiar el status de la actividad.
                // OJO si la actividad no es de la dl, no puedo cambiarla.
                $dl_org = $oActividad->getDl_org();
                $id_tabla = $oActividad->getId_tabla();
                // Sólo dre puede aprobar (pasar de proyecto a actual) las actividades
                // ojo marcha atrás tampoco debería poderse.
                if ( ($statusProceso == ActividadAll::STATUS_ACTUAL && $statusActividad < ActividadAll::STATUS_ACTUAL) 
                    OR ($statusActividad == ActividadAll::STATUS_ACTUAL && $statusProceso < ActividadAll::STATUS_ACTUAL) ) {
                    // para dl y dlf:
                    $dl_org_no_f = preg_replace('/(\.*)f$/', '\1', $dl_org);
                    if ($dl_org == core\ConfigGlobal::mi_delef() OR $dl_org_no_f == core\ConfigGlobal::mi_dele()) {
                        if ( $_SESSION['oPerm']->have_perm_oficina('des') ) {
                            $oActividad->setStatus($statusProceso);
                            $oActividad->DBGuardar();
                            // además debería marcar como completado la fase correspondiente del proceso de la sf.
                            $this->marcarFaseEnSf($statusProceso,$statusActividad);
                        } else {
                            echo _("no se puede cambiar el status de la actividad a 'actual', porque debe hacerlo dre");
                            $permitido = FALSE;
                        }
                    } else { // para el resto.
                        if ($id_tabla == 'dl') {
                            // No se puede modificar una actividad de otra dl
                            echo sprintf(_("no se puede modificar el status de una actividad de otra dl (%s)"),$dl_org);
                            $permitido = FALSE;
                        } else {
                            $oActividad->setStatus($statusProceso);
                            $oActividad->DBGuardar();
                        }
                    }
                } else {
                    if ($dl_org == core\ConfigGlobal::mi_delef()) {
                        $oActividad->setStatus($statusProceso);
                        $oActividad->DBGuardar();
                    } else {
                        if ($id_tabla == 'dl') {
                            // No se puede modificar una actividad de otra dl
                            echo sprintf(_("no se puede modificar el status de una actividad de otra dl (%s)"),$dl_org);
                            //$permitido = FALSE;
                        } else {
                            $oActividad->setStatus($statusProceso);
                            $oActividad->DBGuardar();
                        }
                    }
                }
            }
            //UPDATE
            if ($permitido) {
                $update="
                        id_tipo_proceso          = :id_tipo_proceso,
                        id_activ                 = :id_activ,
                        id_fase                  = :id_fase,
                        id_tarea                 = :id_tarea,
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
                        // Dejar la actividad como estaba
                        $oActividad->setStatus($statusActividad);
                        $oActividad->DBGuardar();
                        return FALSE;
                    } elseif (core\ConfigGlobal::is_app_installed('cambios')) {
                        if (empty($quiet)) {
                            $oGestorCanvis = new gestorAvisoCambios();
                            $shortClassName = (new \ReflectionClass($this))->getShortName();
                            $oGestorCanvis->addCanvi($shortClassName, 'FASE', $this->iid_activ, $aDades, $this->aDadesActuals);
                        }
                    }
                }
                $this->setAllAtributes($aDades);
            }
        } else {
            // INSERT
            $campos="(id_tipo_proceso,id_activ,id_fase,id_tarea,completado,observ)";
            $valores="(:id_tipo_proceso,:id_activ,:id_fase,:id_tarea,:completado,:observ)";
            if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === FALSE) {
                $sClauError = 'ActividadProcesoTarea.insertar.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return FALSE;
            } else {
                try {
                	$oDblSt->execute($aDades);
                }
                catch ( \PDOException $e) {
                	$err_txt=$e->errorInfo[2];
                	$this->setErrorTxt($err_txt);
                    $sClauError = 'ActividadProcesoTarea.insertar.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return FALSE;
                }
            }
            $nomSeq = $this->sNomTabla."_id_item_seq";
            $id_item = $oDbl->lastInsertId($nomSeq);
            if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_item=$id_item")) === false) {
                $sClauError = 'ActividadProcesoTarea.carregar.Last';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            }
            $aDadesLast = $oDblSt->fetch(\PDO::FETCH_ASSOC);
            $this->aDades=$aDadesLast;
            $this->setAllAtributes($aDadesLast);
            // anotar cambio.
            if (empty($quiet)) {
                $oGestorCanvis = new gestorAvisoCambios();
                $shortClassName = (new \ReflectionClass($this))->getShortName();
                $oGestorCanvis->addCanvi($shortClassName, 'FASE', $aDadesLast['id_activ'], $this->aDades, array());
            }
        }
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
                    $this->aDadesActuals=$aDades;
                    break;
                default:
                    // En el caso de no existir esta fila, $aDades = FALSE:
                    if ($aDades === FALSE) {
                        $this->setNullAllAtributes();
                    } else {
                        $this->setAllAtributes($aDades);
                    }
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
            if (core\ConfigGlobal::is_app_installed('cambios')) {
                $oGestorCanvis = new gestorAvisoCambios();
                $shortClassName = (new \ReflectionClass($this))->getShortName();
                $oGestorCanvis->addCanvi($shortClassName, 'FASE', $this->iid_activ, array(), $this->aDadesActuals);
            }
            
            if (($oDbl->exec("DELETE FROM $nom_tabla WHERE id_item='$this->iid_item'")) === FALSE) {
                $sClauError = 'ActividadProcesoTarea.eliminar';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return FALSE;
            }
            return TRUE;
        }
    }

    /**
     * Creo esta nueva función para poder guardar sin volver a repetir el proceso de mirar si hay que cambiar el
     * estado de la actividad. Sólo sirve para hacer UPDATE de completada.
     *
     */
    public function DBMarcar() {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $this->DBCarregar('guardar');
        
        $aDades=array();
        $aDades['id_tipo_proceso'] = $this->iid_tipo_proceso;
        $aDades['id_activ'] = $this->iid_activ;
        $aDades['id_fase'] = $this->iid_fase;
        $aDades['id_tarea'] = $this->iid_tarea;
        $aDades['completado'] = $this->bcompletado;
        $aDades['observ'] = $this->sobserv;
        array_walk($aDades, 'core\poner_null');
        //para el caso de los boolean FALSE, el pdo(+postgresql) pone string '' en vez de 0. Lo arreglo:
        if ( core\is_true($aDades['completado']) ) { $aDades['completado']='true'; } else { $aDades['completado']='false'; }
        
        //UPDATE
        $update="
                id_tipo_proceso          = :id_tipo_proceso,
                id_activ                 = :id_activ,
                id_fase                  = :id_fase,
                id_tarea                 = :id_tarea,
                completado               = :completado,
                observ                   = :observ";
        if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_item='$this->iid_item'")) === FALSE) {
            $sClauError = 'ActividadProcesoTarea.update.prepare';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return FALSE;
        } else {
            try {
            	$oDblSt->execute($aDades);
            }
            catch ( \PDOException $e) {
            	$err_txt=$e->errorInfo[2];
            	$this->setErrorTxt($err_txt);
                $sClauError = 'ActividadProcesoTarea.update.execute';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                return FALSE;
            }
        }
        $this->setAllAtributes($aDades);
    }
    
    /* METODES ALTRES  ----------------------------------------------------------*/
    
    private function desmarcar($fase_tarea) {
        // comprobar si hay dependencias insatisfechas
        $rta = $this->comprobar_dependientes($fase_tarea);
        if ($rta['marcada'] === FALSE) {
            // No se puede marcar por alguna razón.
            echo $rta['mensaje'];
            exit();
        }

        $id_fase = strtok($fase_tarea, '#');
        $id_tarea = strtok('#');
        
        $aWhere = [
            'id_activ' => $this->iid_activ,
            'id_fase' => $id_fase,
            'id_tarea' => $id_tarea,
        ];
        $gesActividadProcesoTarea = new GestorActividadProcesoTarea();
        $cActividadProcesoTarea = $gesActividadProcesoTarea->getActividadProcesoTareas($aWhere);
        $oActividadProcesoTarea = $cActividadProcesoTarea[0];
        
        $oActividadProcesoTarea->DBCarregar();
        $oActividadProcesoTarea->SetCompletado('f');
        $oActividadProcesoTarea->DBMarcar();
        // Hay que cambiarlo en el array, porque sino no se actualiza:
        $this->aFasesEstado[$fase_tarea] = FALSE;
    }
    
    private function marcar($fase_tarea) {
        // Cuando un proceso está mal y se da el caso de referencias circulares en las dependecias,
        // se emple una variable global para poder detectar cuando se está intentando marcar una fase por segunda vez.
        if (in_array($fase_tarea, $this->aStack)) {
            $msg = _("Hay un error en el diseño del proceso: referencias circulares.");
            exit($msg);
        }
        $this->aStack[] = $fase_tarea;
        // comprobar si hay dependencias insatisfechas
        $rta = $this->comprobar_dependencia($fase_tarea);
        if ($rta['marcada'] === FALSE) {
            // No se puede marcar por alguna razón.
            echo $rta['mensaje'];
            exit();
        }
                
        $id_fase = strtok($fase_tarea, '#');
        $id_tarea = strtok('#');
        
        $aWhere = [
            'id_activ' => $this->iid_activ,
            'id_fase' => $id_fase,
            'id_tarea' => $id_tarea,
        ];
        $gesActividadProcesoTarea = new GestorActividadProcesoTarea();
        $cActividadProcesoTarea = $gesActividadProcesoTarea->getActividadProcesoTareas($aWhere);
        $oActividadProcesoTarea = $cActividadProcesoTarea[0];
        
        $oActividadProcesoTarea->DBCarregar();
        $oActividadProcesoTarea->SetCompletado('t');
        $oActividadProcesoTarea->DBMarcar();
        // Hay que cambiarlo en el array, porque sino no se actualiza:
        $this->aFasesEstado[$fase_tarea] = TRUE;
    }
    
    private function comprobar_dependencia($fase_tarea) {
        $msg = '';
        $bMarcada = TRUE;
        // Si no tiene ninguna fase previa, devuelve directamente TRUE.
        if (array_key_exists($fase_tarea, $this->aFasesPrevias)) {
            // comprobar el estado de cada una:
            $aFasesPrevias = $this->aFasesPrevias[$fase_tarea];
            foreach ($aFasesPrevias as $fase_tarea_previa => $mensaje) {
                if (!$this->is_completa($fase_tarea_previa)) {
                    // Si es forzado, solo me aseguro de tener permiso.
                    if ($this->isForce()) {
                        if ($this->tiene_permiso($fase_tarea_previa)) {
                            $this->marcar($fase_tarea_previa);
                            continue;
                        }
                    }
                    $msg .= empty($mensaje)? $this->getMensaje($fase_tarea_previa,'marcar') : $mensaje;
                    $bMarcada = FALSE;
                }
            }
        }
        return ['marcada' => $bMarcada, 'mensaje' => $msg];
    }
    
    public function getMensaje($fase_tarea,$para) {
        $id_fase = strtok($fase_tarea, '#');
        
        $oFase = new ActividadFase($id_fase);
        $descFase = $oFase->getDesc_fase();
        switch($para) {
            case 'marcar':
                $mensaje = sprintf (_("No tienen completada la fase: %s"),$descFase);
                break;
            case 'desmarcar':
                $mensaje = sprintf (_("La fase: %s está marcada, y depende de esta."),$descFase);
                break;
        }
        return $mensaje;
    }
    private function is_completa($fase_tarea) {
        $completado = empty($this->aFasesEstado[$fase_tarea])? FALSE : $this->aFasesEstado[$fase_tarea];
        return $completado;
    }
        
    private function tiene_permiso($fase_tarea) {
        $id_fase = strtok($fase_tarea, '#');
        $id_tarea = strtok('#');
        
        $aWhere = [
                    'id_tipo_proceso'  => $this->iid_tipo_proceso,
                    'id_fase'          => $id_fase,
                    'id_tarea'         => $id_tarea,
        ];
        $gesTareaProceso = new GestorTareaProceso();
        $cTareaProceso = $gesTareaProceso->getTareasProceso($aWhere);
        if (empty($cTareaProceso)) {
            // la fase de la que depende no está en el proceso
            $msg = sprintf(_("Proceso mal diseñado. La fase %s, con tarea %s no está en el proceso"),$id_fase,$id_tarea);                
            exit ($msg);
        }
        $oTareaProceso = $cTareaProceso[0];
        $of_responsable_txt = $oTareaProceso->getOf_responsable_txt();
        if (empty($of_responsable_txt)) {
            return TRUE; 
        }
        if ($_SESSION['oPerm']->have_perm_oficina($of_responsable_txt)) {
            return TRUE; 
        } else {
            return FALSE;
        }
    }
    
    private function cargarFases() {
        $gesTareaProceso = new GestorTareaProceso();
        $this->aFasesPrevias = $gesTareaProceso->arbolPrevio($this->iid_tipo_proceso);   
        $this->aFasesPosteriores = $gesTareaProceso->getArrayFasesDependientes($this->iid_tipo_proceso);   
        
        $gesActividadProcesoTareas = new GestorActividadProcesoTarea();
        $this->aFasesEstado = $gesActividadProcesoTareas->getListaFaseEstado($this->iid_activ);
    }
    
    private function cargar_permisos() {
        //para crear un desplegable de oficinas. Uso los de los menus
        $oPermMenus = new PermisoMenu;
        $this->aOpcionesOficinas = $oPermMenus->lista_array();
    }
    
    private function comprobar_dependientes($fase_tarea) {
        $msg = '';
        $bMarcada = TRUE;
 
        $this->aFasesTareasEncadenadas = [];
        $this->agregar_dependientes($fase_tarea);

        foreach ($this->aFasesTareasEncadenadas as $fase_tarea_anterior) {
            $completado = FALSE;
            if (array_key_exists($fase_tarea_anterior, $this->aFasesEstado)) {
                $completado = $this->aFasesEstado[$fase_tarea_anterior];
                if (is_true($completado)) {
                    // Si es forzado, solo me aseguro de tener permiso.
                    if ($this->isForce()) {
                        if ($this->tiene_permiso($fase_tarea_anterior)) {
                            $this->desmarcar($fase_tarea_anterior);
                            continue;
                        }
                    }
                    $msg .= empty($mensaje)? $this->getMensaje($fase_tarea_anterior,'desmarcar') : $mensaje;
                    $bMarcada = FALSE;
                }
            }
        }
        return ['marcada' => $bMarcada, 'mensaje' => $msg];
    }
    
    private function agregar_dependientes($fase_tarea_org) {
        // buscar id_fase_org en array
        $b = $this->dependientes_de($fase_tarea_org);
        $a = $this->aFasesTareasEncadenadas;
        $this->aFasesTareasEncadenadas = array_unique(array_merge($a,$b));
        
        if (!empty($b)) {
            foreach ($b as $fase_tarea) {
                $this->agregar_dependientes($fase_tarea);
            }
        }
        return [];
    }
    private function dependientes_de($fase_tarea_org) {
        // buscar id_fase_org en array
        $b = [];
        foreach ($this->aFasesPosteriores as $fase_tarea => $aaFase_tarea_previa) {
            foreach ($aaFase_tarea_previa as $aFase_tarea_previa) {
                foreach ($aFase_tarea_previa as $fase_tarea_previa => $mensaje) {
                    if ($fase_tarea_org == $fase_tarea_previa) {
                        $b[] = $fase_tarea;
                    }
                }
            }
        }
        return $b;
    }

    private function marcarFaseEnSf($statusProceso,$statusActividad) {
        $gesActividadProcesoTareas = new GestorActividadProcesoTarea();
        // buscar el id_tipo_proceso para esta actividad de la otra sección
        if (core\ConfigGlobal::mi_sfsv() == 1) {
            $gesActividadProcesoTareas->setNomTabla('a_actividad_proceso_sf');
        } else {
            $gesActividadProcesoTareas->setNomTabla('a_actividad_proceso_sv');
        }
        
        $cActividadProcesoTareas = $gesActividadProcesoTareas->getActividadProcesoTareas(['id_activ' => $this->iid_activ]);
        
        // Puede ser queel proceso no exista (para sfsv=2):
        if (empty($cActividadProcesoTareas)) {
            $gesActividadProcesoTareas->generarProceso($this->iid_activ, 2);
        }
        
        /* Para no andar buscando que fase corresponde a status, finalmente he decidido
         * que las id_fase para el cambio de status son fijas, e iguales al status de la actividad.
         */
        if ( $statusActividad == 1 && $statusProceso == 2 ) {
            $id_fase = ActividadFase::FASE_APROBADA;
            $aWhere = ['id_activ' => $this->iid_activ, 'id_fase' => $id_fase];
            $cActividadProcesoTareas = $gesActividadProcesoTareas->getActividadProcesoTareas($aWhere);
            if (!empty($cActividadProcesoTareas)) {
                $oActividadPorcesoTarea = $cActividadProcesoTareas[0];
                $oActividadPorcesoTarea->setCompletado('t');
                $oActividadPorcesoTarea->DBGuardar();
            }
        }
        if ( $statusActividad > $statusProceso ) {
            $id_fase = ActividadFase::FASE_APROBADA;
            $aWhere = ['id_activ' => $this->iid_activ, 'id_fase' => $id_fase];
            $cActividadProcesoTareas = $gesActividadProcesoTareas->getActividadProcesoTareas($aWhere);
            if (!empty($cActividadProcesoTareas)) {
                $oActividadPorcesoTarea = $cActividadProcesoTareas[0];
                $oActividadPorcesoTarea->setCompletado('f');
                $oActividadPorcesoTarea->DBGuardar();
            }
        }
    }
    
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
        if (array_key_exists('completado',$aDades)) $this->setCompletado($aDades['completado']);
        if (array_key_exists('observ',$aDades)) $this->setObserv($aDades['observ']);
    }
    
    /**
     * Estableix a empty el valor de tots els atributs
     *
     */
    function setNullAllAtributes() {
        $aPK = $this->getPrimary_key();
        $this->setId_item('');
        $this->setId_tipo_proceso('');
        $this->setId_activ('');
        $this->setId_fase('');
        $this->setId_tarea('');
        $this->setCompletado('');
        $this->setObserv('');
        $this->setPrimary_key($aPK);
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
     * Estableix las claus primàries de ActividadProcesoTarea en un array
     *
     * @return array aPrimary_key
     */
    public function setPrimary_key($a_id='') {
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach($a_id as $nom_id=>$val_id) {
                if (($nom_id == 'id_item') && $val_id !== '') $this->iid_item = (int)$val_id; // evitem SQL injection fent cast a integer
            }
        }
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

    /**
     * @return boolean
     */
    public function isForce()
    {
        return $this->bForce;
    }

    /**
     * @param boolean $bForce
     */
    public function setForce($bForce)
    {
        if (is_true($bForce)) {
            $this->bForce = TRUE;
        } else {
            $this->bForce = FALSE;
        }
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
