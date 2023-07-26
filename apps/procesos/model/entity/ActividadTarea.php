<?php

namespace procesos\model\entity;

use core;

/**
 * Fitxer amb la Classe que accedeix a la taula a_tareas
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 07/12/2018
 */

/**
 * Clase que implementa la entidad a_tareas
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 07/12/2018
 */
class ActividadTarea extends core\ClasePropiedades
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * aPrimary_key de ActividadTarea
     *
     * @var array
     */
    private $aPrimary_key;

    /**
     * aDades de ActividadTarea
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
     * Id_fase de ActividadTarea
     *
     * @var integer
     */
    private $iid_fase;
    /**
     * Id_tarea de ActividadTarea
     *
     * @var integer
     */
    private $iid_tarea;
    /**
     * Desc_tarea de ActividadTarea
     *
     * @var string
     */
    private $sdesc_tarea;
    /* ATRIBUTOS QUE NO SON CAMPOS------------------------------------------------- */
    /**
     * oDbl de ActividadTarea
     *
     * @var object
     */
    protected $oDbl;
    /**
     * NomTabla de ActividadTarea
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
     * @param integer|array iid_tarea
     *                        $a_id. Un array con los nombres=>valores de las claves primarias.
     */
    function __construct($a_id = '')
    {
        $oDbl = $GLOBALS['oDBC'];
        $oDbl_Select = $GLOBALS['oDBC_Select'];
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                if (($nom_id == 'id_tarea') && $val_id !== '') $this->iid_tarea = (int)$val_id; 
            }
        } else {
            if (isset($a_id) && $a_id !== '') {
                $this->iid_tarea = (integer)$a_id; 
                $this->aPrimary_key = array('id_tarea' => $this->iid_tarea);
            }
        }
        $this->setoDbl($oDbl);
        $this->setoDbl_Select($oDbl_Select);
        $this->setNomTabla('a_tareas');
    }

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    /**
     * Guarda los atributos de la clase en la base de datos.
     * Si no existe el registro, hace el insert; Si existe hace el update.
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
        $aDades['id_fase'] = $this->iid_fase;
        $aDades['desc_tarea'] = $this->sdesc_tarea;
        array_walk($aDades, 'core\poner_null');

        if ($bInsert === FALSE) {
            //UPDATE
            $update = "
					id_fase                  = :id_fase,
					desc_tarea               = :desc_tarea";
            if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_tarea='$this->iid_tarea'")) === FALSE) {
                $sClauError = 'ActividadTarea.update.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return FALSE;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'ActividadTarea.update.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return FALSE;
                }
            }
        } else {
            // INSERT
            $campos = "(id_fase,desc_tarea)";
            $valores = "(:id_fase,:desc_tarea)";
            if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === FALSE) {
                $sClauError = 'ActividadTarea.insertar.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return FALSE;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'ActividadTarea.insertar.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return FALSE;
                }
            }
            $this->id_tarea = $oDbl->lastInsertId('a_tareas_id_tarea_seq');
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
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        if (isset($this->iid_tarea)) {
            if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_tarea='$this->iid_tarea'")) === FALSE) {
                $sClauError = 'ActividadTarea.carregar';
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
        if (empty($this->iid_tarea)) {
            $msg = _("no se puede eliminar la tarea 0") . "\n";
            echo $msg;
            return FALSE;
        } else {
            if (($oDbl->exec("DELETE FROM $nom_tabla WHERE id_tarea='$this->iid_tarea'")) === FALSE) {
                $sClauError = 'ActividadTarea.eliminar';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return FALSE;
            }
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
        if (array_key_exists('id_fase', $aDades)) $this->setId_fase($aDades['id_fase']);
        if (array_key_exists('id_tarea', $aDades)) $this->setId_tarea($aDades['id_tarea']);
        if (array_key_exists('desc_tarea', $aDades)) $this->setDesc_tarea($aDades['desc_tarea']);
    }

    /**
     * Establece a 'empty' el valor de todos los atributos
     *
     */
    function setNullAllAtributes()
    {
        $aPK = $this->getPrimary_key();
        $this->setId_fase('');
        $this->setId_tarea('');
        $this->setDesc_tarea('');
        $this->setPrimary_key($aPK);
    }


    /* MÉTODOS GET y SET --------------------------------------------------------*/

    /**
     * Recupera todos los atributos de ActividadTarea en un array
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
     * Recupera la clave primaria de ActividadTarea en un array
     *
     * @return array aPrimary_key
     */
    function getPrimary_key()
    {
        if (!isset($this->aPrimary_key)) {
            $this->aPrimary_key = array('id_tarea' => $this->iid_tarea);
        }
        return $this->aPrimary_key;
    }

    /**
     * Establece la clave primaria de ActividadTarea en un array
     *
     * @return array aPrimary_key
     */
    public function setPrimary_key($a_id = '')
    {
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                if (($nom_id == 'id_tarea') && $val_id !== '') $this->iid_tarea = (int)$val_id; 
            }
        }
    }

    /**
     * Recupera el atributo iid_fase de ActividadTarea
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
     * Establece el valor del atributo iid_fase de ActividadTarea
     *
     * @param integer iid_fase='' optional
     */
    function setId_fase($iid_fase = '')
    {
        $this->iid_fase = $iid_fase;
    }

    /**
     * Recupera el atributo iid_tarea de ActividadTarea
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
     * Establece el valor del atributo iid_tarea de ActividadTarea
     *
     * @param integer iid_tarea
     */
    function setId_tarea($iid_tarea)
    {
        $this->iid_tarea = $iid_tarea;
    }

    /**
     * Recupera el atributo sdesc_tarea de ActividadTarea
     *
     * @return string sdesc_tarea
     */
    function getDesc_tarea()
    {
        if (!isset($this->sdesc_tarea) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->sdesc_tarea;
    }

    /**
     * Establece el valor del atributo sdesc_tarea de ActividadTarea
     *
     * @param string sdesc_tarea='' optional
     */
    function setDesc_tarea($sdesc_tarea = '')
    {
        $this->sdesc_tarea = $sdesc_tarea;
    }
    /* MÉTODOS GET y SET D'ATRIBUTOS QUE NO SON CAMPOS -----------------------------*/

    /**
     * Retorna una col·lecció d'objectes del tipus DatosCampo
     *
     */
    function getDatosCampos()
    {
        $oActividadTareaSet = new core\Set();

        $oActividadTareaSet->add($this->getDatosId_fase());
        $oActividadTareaSet->add($this->getDatosDesc_tarea());
        return $oActividadTareaSet->getTot();
    }


    /**
     * Recupera les propietats de l'atribut iid_fase de ActividadTarea
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosId_fase()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'id_fase'));
        $oDatosCampo->setEtiqueta(_("fase a la que pertenece"));
        $oDatosCampo->setTipo('opciones');
        $oDatosCampo->setArgument('procesos\model\entity\ActividadFase'); // nombre del objeto relacionado
        $oDatosCampo->setArgument2('getDesc_fase'); // método para obtener el valor a mostrar del objeto relacionado.
        $oDatosCampo->setArgument3('getListaActividadFases'); // método con que crear la lista de opciones del Gestor objeto relacionado.
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut sdesc_tarea de ActividadTarea
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosDesc_tarea()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'desc_tarea'));
        $oDatosCampo->setEtiqueta(_("descripción de la tarea"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument('30');
        return $oDatosCampo;
    }
}
