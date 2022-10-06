<?php

namespace procesos\model\entity;

use actividades\model\entity\ActividadAll;
use core;
use stdClass;
use menus\model\PermisoMenu;

/**
 * Fitxer amb la Classe que accedeix a la taula a_tareas_proceso
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 07/12/2018
 */

/**
 * Clase que implementa la entidad a_tareas_proceso
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 07/12/2018
 */
class TareaProceso extends core\ClasePropiedades
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * aPrimary_key de TareaProceso
     *
     * @var array
     */
    private $aPrimary_key;

    /**
     * aDades de TareaProceso
     *
     * @var array
     */
    private $aDades;

    /**
     * bLoaded
     *
     * @var boolean
     */
    private $bLoaded = FALSE;

    /**
     * Id_item de TareaProceso
     *
     * @var integer
     */
    private $iid_item;
    /**
     * Id_tipo_proceso de TareaProceso
     *
     * @var integer
     */
    private $iid_tipo_proceso;
    /**
     * Id_fase de TareaProceso
     *
     * @var integer
     */
    private $iid_fase;
    /**
     * Id_tarea de TareaProceso
     *
     * @var integer
     */
    private $iid_tarea;
    /**
     * Status de TareaProceso
     *
     * @var integer
     */
    private $istatus;
    /**
     * Id_of_responsable de TareaProceso
     *
     * @var integer
     */
    private $iid_of_responsable;
    /**
     * Id_fase_previa de TareaProceso
     *
     * @var object JSON
     */
    private $json_fases_previas;
    /* ATRIBUTOS QUE NO SON CAMPOS------------------------------------------------- */
    /**
     * oDbl de TareaProceso
     *
     * @var object
     */
    protected $oDbl;
    /**
     * NomTabla de TareaProceso
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
     *                        $a_id. Un array con los nombres=>valores de las claves primarias.
     */
    function __construct($a_id = '')
    {
        $oDbl = $GLOBALS['oDBC'];
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                //if (($nom_id == 'id_item') && $val_id !== '') $this->iid_item = (int)$val_id; // evitem SQL injection fent cast a integer
                $this->$nom_id = (int)$val_id; // evitem SQL injection fent cast a integer
            }
        } else {
            if (isset($a_id) && $a_id !== '') {
                $this->iid_item = (integer)$a_id; // evitem SQL injection fent cast a integer
                $this->aPrimary_key = array('id_item' => $this->iid_item);
            }
        }
        $this->setoDbl($oDbl);
        $this->setNomTabla('a_tareas_proceso');
    }

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    /**
     * Desa els atributs de l'objecte a la base de dades.
     * Si no hi ha el registre, fa el insert, si hi es fa el update.
     *
     */
    public function DBGuardar()
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        if ($this->DBCarregar('guardar') === FALSE) {
            $bInsert = TRUE;
        } else {
            $bInsert = FALSE;
        }
        $aDades = array();
        $aDades['id_tipo_proceso'] = $this->iid_tipo_proceso;
        $aDades['id_fase'] = $this->iid_fase;
        $aDades['id_tarea'] = $this->iid_tarea;
        $aDades['status'] = $this->istatus;
        $aDades['id_of_responsable'] = $this->iid_of_responsable;
        $aDades['json_fases_previas'] = $this->json_fases_previas;
        array_walk($aDades, 'core\poner_null');

        if ($bInsert === FALSE) {
            //UPDATE
            $update = "
					id_tipo_proceso          = :id_tipo_proceso,
					id_fase                  = :id_fase,
					id_tarea                 = :id_tarea,
					status                   = :status,
					id_of_responsable        = :id_of_responsable,
					json_fases_previas       = :json_fases_previas";
            if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_item='$this->iid_item'")) === FALSE) {
                $sClauError = 'TareaProceso.update.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return FALSE;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'TareaProceso.update.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return FALSE;
                }
            }
        } else {
            // INSERT
            $campos = "(id_tipo_proceso,id_fase,id_tarea,status,id_of_responsable,json_fases_previas)";
            $valores = "(:id_tipo_proceso,:id_fase,:id_tarea,:status,:id_of_responsable,:json_fases_previas)";
            if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === FALSE) {
                $sClauError = 'TareaProceso.insertar.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return FALSE;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'TareaProceso.insertar.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return FALSE;
                }
            }
            $this->id_item = $oDbl->lastInsertId('a_tareas_proceso_id_item_seq');
        }
        $this->setAllAtributes($aDades);
        return TRUE;
    }

    /**
     * Carga los campos de la base de datos como atributos de la clase.
     *
     */
    public function DBCarregar($que = null)
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        if (isset($this->iid_item)) {
            if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_item='$this->iid_item'")) === FALSE) {
                $sClauError = 'TareaProceso.carregar';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return FALSE;
            }
            $aDades = $oDblSt->fetch(\PDO::FETCH_ASSOC);
            // Para evitar posteriores cargas
            $this->bLoaded = TRUE;
            switch ($que) {
                case 'tot':
                    $this->aDades = $aDades;
                    break;
                case 'guardar':
                    if (!$oDblSt->rowCount()) return FALSE;
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
        } elseif (!empty($this->aPrimary_key)) {
            if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla 
                    WHERE id_tipo_proceso=$this->iid_tipo_proceso AND id_fase=$this->iid_fase AND id_tarea=$this->iid_tarea")) === FALSE) {
                $sClauError = 'TareaProceso.carregar';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return FALSE;
            }
            $aDades = $oDblSt->fetch(\PDO::FETCH_ASSOC);
            // Para evitar posteriores cargas
            $this->bLoaded = TRUE;
            switch ($que) {
                case 'tot':
                    $this->aDades = $aDades;
                    break;
                case 'guardar':
                    if (!$oDblSt->rowCount()) return FALSE;
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
     * Elimina la fila de la base de datos que corresponde a la clase.
     *
     */
    public function DBEliminar()
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        if (($oDbl->exec("DELETE FROM $nom_tabla WHERE id_item='$this->iid_item'")) === FALSE) {
            $sClauError = 'TareaProceso.eliminar';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return FALSE;
        }
        return TRUE;
    }

    /* OTROS MÉTODOS  ----------------------------------------------------------*/
    /* MÉTODOS PRIVADOS ----------------------------------------------------------*/

    /**
     * Establece el valor de todos los atributos
     *
     * @param array $aDades
     */
    function setAllAtributes($aDades)
    {
        if (!is_array($aDades)) return;
        if (array_key_exists('id_item', $aDades)) $this->setId_item($aDades['id_item']);
        if (array_key_exists('id_tipo_proceso', $aDades)) $this->setId_tipo_proceso($aDades['id_tipo_proceso']);
        if (array_key_exists('id_fase', $aDades)) $this->setId_fase($aDades['id_fase']);
        if (array_key_exists('id_tarea', $aDades)) $this->setId_tarea($aDades['id_tarea']);
        if (array_key_exists('status', $aDades)) $this->setStatus($aDades['status']);
        if (array_key_exists('id_of_responsable', $aDades)) $this->setId_of_responsable($aDades['id_of_responsable']);
        if (array_key_exists('json_fases_previas', $aDades)) $this->setJson_fases_previas($aDades['json_fases_previas']);
    }

    /**
     * Establece a 'empty' el valor de todos los atributos
     *
     */
    function setNullAllAtributes()
    {
        $aPK = $this->getPrimary_key();
        $this->setId_item('');
        $this->setId_tipo_proceso('');
        $this->setId_fase('');
        $this->setId_tarea('');
        $this->setStatus('');
        $this->setId_of_responsable('');
        $this->setJson_fases_previas('');
        $this->setPrimary_key($aPK);
    }


    /* MÉTODOS GET y SET --------------------------------------------------------*/

    /**
     * Recupera todos los atributos de TareaProceso en un array
     *
     * @return array aDades
     */
    function getTot()
    {
        if (!is_array($this->aDades)) {
            $this->DBCarregar('tot');
        }
        return $this->aDades;
    }

    /**
     * Recupera la clave primaria de TareaProceso en un array
     *
     * @return array aPrimary_key
     */
    function getPrimary_key()
    {
        if (!isset($this->aPrimary_key)) {
            $this->aPrimary_key = array('id_item' => $this->iid_item);
        }
        return $this->aPrimary_key;
    }

    /**
     * Establece la clave primaria de TareaProceso en un array
     *
     * @return array aPrimary_key
     */
    public function setPrimary_key($a_id = '')
    {
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                if (($nom_id == 'id_item') && $val_id !== '') $this->iid_item = (int)$val_id; // evitem SQL injection fent cast a integer
            }
        }
    }

    /**
     * Recupera el atributo iid_item de TareaProceso
     *
     * @return integer iid_item
     */
    function getId_item()
    {
        if (!isset($this->iid_item) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->iid_item;
    }

    /**
     * Establece el valor del atributo iid_item de TareaProceso
     *
     * @param integer iid_item
     */
    function setId_item($iid_item)
    {
        $this->iid_item = $iid_item;
    }

    /**
     * Recupera el atributo iid_tipo_proceso de TareaProceso
     *
     * @return integer iid_tipo_proceso
     */
    function getId_tipo_proceso()
    {
        if (!isset($this->iid_tipo_proceso) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->iid_tipo_proceso;
    }

    /**
     * Establece el valor del atributo iid_tipo_proceso de TareaProceso
     *
     * @param integer iid_tipo_proceso='' optional
     */
    function setId_tipo_proceso($iid_tipo_proceso = '')
    {
        $this->iid_tipo_proceso = $iid_tipo_proceso;
    }

    /**
     * Recupera el atributo iid_fase de TareaProceso
     *
     * @return integer iid_fase
     */
    function getId_fase()
    {
        if (!isset($this->iid_fase) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->iid_fase;
    }

    /**
     * Establece el valor del atributo iid_fase de TareaProceso
     *
     * @param integer iid_fase='' optional
     */
    function setId_fase($iid_fase = '')
    {
        $this->iid_fase = $iid_fase;
    }

    /**
     * Recupera el atributo iid_tarea de TareaProceso
     *
     * @return integer iid_tarea
     */
    function getId_tarea()
    {
        if (!isset($this->iid_tarea) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->iid_tarea;
    }

    /**
     * Establece el valor del atributo iid_tarea de TareaProceso
     *
     * @param integer iid_tarea='' optional
     */
    function setId_tarea($iid_tarea = '')
    {
        $this->iid_tarea = $iid_tarea;
    }

    /**
     * Recupera el atributo istatus de TareaProceso
     *
     * @return integer istatus
     */
    function getStatus()
    {
        if (!isset($this->istatus) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->istatus;
    }

    /**
     * Establece el valor del atributo istatus de TareaProceso
     *
     * @param integer istatus='' optional
     */
    function setStatus($istatus = '')
    {
        $this->istatus = $istatus;
    }

    /**
     * Recupera el atributo iid_of_responsable de TareaProceso
     *
     * @return string iid_of_responsable
     */
    function getId_of_responsable()
    {
        if (!isset($this->iid_of_responsable) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->iid_of_responsable;
    }

    /**
     * Establece el valor del atributo iid_of_responsable de TareaProceso
     *
     * @param string iid_of_responsable='' optional
     */
    function setId_of_responsable($iid_of_responsable = '')
    {
        $this->iid_of_responsable = $iid_of_responsable;
    }

    /**
     * Recupera el atributo json_fases_previas de TareaProceso
     *
     * @param boolean $bArray si hay que devolver un array en vez de un objeto.
     * @return object $oFases
     */
    function getJson_fases_previas($bArray = FALSE)
    {
        if (!isset($this->json_fases_previas) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        $oFases = json_decode(json_decode($this->json_fases_previas), $bArray);
        if (empty($oFases) || $oFases == '[]') {
            if ($bArray) {
                $oFases = [];
            } else {
                $oFases = new stdClass;
            }
        }
        return $oFases;
    }

    /**
     * Establece el valor del atributo json_fases_previas de TareaProceso
     *
     * @param object $oFases
     */
    function setJson_fases_previas($oFases)
    {
        $json_fases_previas = json_encode($oFases);
        $this->json_fases_previas = $json_fases_previas;
    }

    /* MÉTODOS GET y SET D'ATRIBUTOS QUE NO SON CAMPOS -----------------------------*/

    /**
     * Recupera el atributo iid_of_responsable de TareaProceso en texte
     *
     * @return string of_responsable_txt
     */
    function getOf_responsable_txt()
    {
        // para crear el array id_oficina => oficina_txt. Uso los de los menus
        $oPermMenus = new PermisoMenu;
        $aOpcionesOficinas = $oPermMenus->lista_array();
        $id_of_responsable = $this->getId_of_responsable();
        $of_responsable_txt = empty($aOpcionesOficinas[$id_of_responsable]) ? '' : $aOpcionesOficinas[$id_of_responsable];
        return $of_responsable_txt;
    }

    /**
     * Retorna una col·lecció d'objectes del tipus DatosCampo
     *
     */
    function getDatosCampos()
    {
        $oTareaProcesoSet = new core\Set();

        $oTareaProcesoSet->add($this->getDatosId_tipo_proceso());
        $oTareaProcesoSet->add($this->getDatosId_fase());
        $oTareaProcesoSet->add($this->getDatosId_tarea());
        $oTareaProcesoSet->add($this->getDatosStatus());
        $oTareaProcesoSet->add($this->getDatosId_of_responsable());
        $oTareaProcesoSet->add($this->getDatosId_fase_previa());
        return $oTareaProcesoSet->getTot();
    }


    /**
     * Recupera les propietats de l'atribut iid_tipo_proceso de TareaProceso
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosId_tipo_proceso()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'id_tipo_proceso'));
        $oDatosCampo->setEtiqueta(_("tipo de proceso"));
        $oDatosCampo->setTipo('opciones');
        $oDatosCampo->setArgument('ProcesoTipo'); // nombre del objeto relacionado
        $oDatosCampo->setArgument2('getNom_proceso'); // método para obtener el valor a mostrar del objeto relacionado.
        $oDatosCampo->setArgument3('getListaProcesoTipos'); // método con que crear la lista de opciones del Gestor objeto relacionado.
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut iid_fase de TareaProceso
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosId_fase()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'id_fase'));
        $oDatosCampo->setEtiqueta(_("fase"));
        $oDatosCampo->setTipo('opciones');
        $oDatosCampo->setArgument('ActividadFase'); // nombre del objeto relacionado
        $oDatosCampo->setArgument2('getDesc_fase'); // método para obtener el valor a mostrar del objeto relacionado.
        $oDatosCampo->setArgument3('getListaActividadFases'); // método con que crear la lista de opciones del Gestor objeto relacionado.
        $oDatosCampo->setAccion('id_tarea'); // campo que hay que actualizar al cambiar este.
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut iid_tarea de TareaProceso
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosId_tarea()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'id_tarea'));
        $oDatosCampo->setEtiqueta(_("tarea"));
        $oDatosCampo->setTipo('depende');
        $oDatosCampo->setArgument('ActividadTarea'); // nombre del objeto relacionado para ver en listados.
        $oDatosCampo->setArgument2('getDesc_tarea'); // método para obtener el valor a mostrar del objeto relacionado.
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut istatus de TareaProceso
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosStatus()
    {
        $oActividad = new ActividadAll();
        $a_status = $oActividad->getArrayStatus();
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'status'));
        $oDatosCampo->setEtiqueta(_("status"));
        $oDatosCampo->setTipo('array');
        $oDatosCampo->setLista($a_status);

        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut iid_of_responsable de TareaProceso
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosId_of_responsable()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'id_of_responsable'));
        $oDatosCampo->setEtiqueta(_("oficina responsable"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument('7');
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut json_fases_previas de TareaProceso
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosId_fase_previa()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'id_fase_previa'));
        $oDatosCampo->setEtiqueta(_("fase previa"));
        $oDatosCampo->setTipo('opciones');
        $oDatosCampo->setArgument('ActividadFase'); // nombre del objeto relacionado
        $oDatosCampo->setArgument2('getDesc_fase'); // método para obtener el valor a mostrar del objeto relacionado.
        $oDatosCampo->setArgument3('getListaActividadFases'); // método con que crear la lista de opciones del Gestor objeto relacionado.
        $oDatosCampo->setAccion('id_tarea_previa'); // campo que hay que actualizar al cambiar este.
        return $oDatosCampo;
    }
}
