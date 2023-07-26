<?php

namespace procesos\model\entity;

use core;
use stdClass;

/**
 * Fitxer amb la Classe que accedeix a la taula aux_usuarios_perm
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 02/01/2019
 */

/**
 * Clase que implementa la entidad aux_usuarios_perm
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 02/01/2019
 */
class PermUsuarioActividad extends core\ClasePropiedades
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * aPrimary_key de PermUsuarioActividad
     *
     * @var array
     */
    private $aPrimary_key;

    /**
     * aDades de PermUsuarioActividad
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
     * Id_item de PermUsuarioActividad
     *
     * @var integer
     */
    private $iid_item;
    /**
     * Id_usuario de PermUsuarioActividad
     *
     * @var integer
     */
    private $iid_usuario;
    /**
     * Dl_propia de PermUsuarioActividad
     *
     * @var boolean
     */
    private $bdl_propia;
    /**
     * Id_tipo_activ_txt de PermUsuarioActividad
     *
     * @var string
     */
    private $sid_tipo_activ_txt;
    /**
     * Fase_ref de PermUsuarioActividad
     *
     * @var integer
     */
    private $ifase_ref;
    /**
     * Afecta_a de PermUsuarioActividad
     *
     * @var integer
     */
    private $iafecta_a;
    /**
     * PermOn de PermUsuarioActividad
     *
     * @var integer
     */
    private $iperm_on;
    /**
     * PermOn de PermUsuarioActividad
     *
     * @var integer
     */
    private $iperm_off;
    /* ATRIBUTOS QUE NO SON CAMPOS------------------------------------------------- */
    /**
     * oDbl de PermUsuarioActividad
     *
     * @var object
     */
    protected $oDbl;
    /**
     * NomTabla de PermUsuarioActividad
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
        $oDbl = $GLOBALS['oDBE'];
        $oDbl_Select = $GLOBALS['oDBE_Select'];
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                if (($nom_id == 'id_item') && $val_id !== '') $this->iid_item = (int)$val_id; 
            }
        } else {
            if (isset($a_id) && $a_id !== '') {
                $this->iid_item = (integer)$a_id; 
                $this->aPrimary_key = array('id_item' => $this->iid_item);
            }
        }
        $this->setoDbl($oDbl);
        $this->setoDbl_Select($oDbl_Select);
        $this->setNomTabla('aux_usuarios_perm');
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
        $aDades['id_usuario'] = $this->iid_usuario;
        $aDades['dl_propia'] = $this->bdl_propia;
        $aDades['id_tipo_activ_txt'] = $this->sid_tipo_activ_txt;
        $aDades['fase_ref'] = $this->ifase_ref;
        $aDades['afecta_a'] = $this->iafecta_a;
        $aDades['perm_on'] = $this->iperm_on;
        $aDades['perm_off'] = $this->iperm_off;
        array_walk($aDades, 'core\poner_null');
        //para el caso de los boolean FALSE, el pdo(+postgresql) pone string '' en vez de 0. Lo arreglo:
        if (core\is_true($aDades['dl_propia'])) {
            $aDades['dl_propia'] = 'true';
        } else {
            $aDades['dl_propia'] = 'false';
        }

        if ($bInsert === FALSE) {
            //UPDATE
            $update = "
					id_usuario               = :id_usuario,
					dl_propia                = :dl_propia,
					id_tipo_activ_txt        = :id_tipo_activ_txt,
					fase_ref                 = :fase_ref,
					afecta_a                 = :afecta_a,
					perm_on                  = :perm_on,
					perm_off                 = :perm_off";
            if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_item='$this->iid_item'")) === FALSE) {
                $sClauError = 'PermUsuarioActividad.update.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return FALSE;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'PermUsuarioActividad.update.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return FALSE;
                }
            }
        } else {
            // INSERT
            $campos = "(id_usuario,dl_propia,id_tipo_activ_txt,fase_ref,afecta_a,perm_on,perm_off)";
            $valores = "(:id_usuario,:dl_propia,:id_tipo_activ_txt,:fase_ref,:afecta_a,:perm_on,:perm_off)";
            if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === FALSE) {
                $sClauError = 'PermUsuarioActividad.insertar.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return FALSE;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'PermUsuarioActividad.insertar.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return FALSE;
                }
            }
            $this->id_item = $oDbl->lastInsertId('aux_usuarios_perm_id_item_seq');
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
        if (isset($this->iid_item)) {
            if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_item='$this->iid_item'")) === FALSE) {
                $sClauError = 'PermUsuarioActividad.carregar';
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
            $sClauError = 'PermUsuarioActividad.eliminar';
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
        if (array_key_exists('id_usuario', $aDades)) $this->setId_usuario($aDades['id_usuario']);
        if (array_key_exists('dl_propia', $aDades)) $this->setDl_propia($aDades['dl_propia']);
        if (array_key_exists('id_tipo_activ_txt', $aDades)) $this->setId_tipo_activ_txt($aDades['id_tipo_activ_txt']);
        if (array_key_exists('fase_ref', $aDades)) $this->setFase_ref($aDades['fase_ref']);
        if (array_key_exists('afecta_a', $aDades)) $this->setAfecta_a($aDades['afecta_a']);
        if (array_key_exists('perm_on', $aDades)) $this->setPerm_on($aDades['perm_on']);
        if (array_key_exists('perm_off', $aDades)) $this->setPerm_off($aDades['perm_off']);
    }

    /**
     * Establece a 'empty' el valor de todos los atributos
     *
     */
    function setNullAllAtributes()
    {
        $aPK = $this->getPrimary_key();
        $this->setId_item('');
        $this->setId_usuario('');
        $this->setDl_propia('');
        $this->setId_tipo_activ_txt('');
        $this->setFase_ref('');
        $this->setAfecta_a('');
        $this->setPerm_on('');
        $this->setPerm_off('');
        $this->setPrimary_key($aPK);
    }


    /* MÉTODOS GET y SET --------------------------------------------------------*/

    /**
     * Recupera todos los atributos de PermUsuarioActividad en un array
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
     * Recupera la clave primaria de PermUsuarioActividad en un array
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
     * Establece la clave primaria de PermUsuarioActividad en un array
     *
     * @return array aPrimary_key
     */
    public function setPrimary_key($a_id = '')
    {
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                if (($nom_id == 'id_item') && $val_id !== '') $this->iid_item = (int)$val_id; 
            }
        }
    }

    /**
     * Recupera el atributo iid_item de PermUsuarioActividad
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
     * Establece el valor del atributo iid_item de PermUsuarioActividad
     *
     * @param integer iid_item
     */
    function setId_item($iid_item)
    {
        $this->iid_item = $iid_item;
    }

    /**
     * Recupera el atributo iid_usuario de PermUsuarioActividad
     *
     * @return integer iid_usuario
     */
    function getId_usuario()
    {
        if (!isset($this->iid_usuario) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->iid_usuario;
    }

    /**
     * Establece el valor del atributo iid_usuario de PermUsuarioActividad
     *
     * @param integer iid_usuario='' optional
     */
    function setId_usuario($iid_usuario = '')
    {
        $this->iid_usuario = $iid_usuario;
    }

    /**
     * Recupera el atributo bdl_propia de PermUsuarioActividad
     *
     * @return boolean bdl_propia
     */
    function getDl_propia()
    {
        if (!isset($this->bdl_propia) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->bdl_propia;
    }

    /**
     * Establece el valor del atributo bdl_propia de PermUsuarioActividad
     *
     * @param boolean bdl_propia='f' optional
     */
    function setDl_propia($bdl_propia = 'f')
    {
        $this->bdl_propia = $bdl_propia;
    }

    /**
     * Recupera el atributo sid_tipo_activ_txt de PermUsuarioActividad
     *
     * @return string sid_tipo_activ_txt
     */
    function getId_tipo_activ_txt()
    {
        if (!isset($this->sid_tipo_activ_txt) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->sid_tipo_activ_txt;
    }

    /**
     * Establece el valor del atributo sid_tipo_activ_txt de PermUsuarioActividad
     *
     * @param string sid_tipo_activ_txt='' optional
     */
    function setId_tipo_activ_txt($sid_tipo_activ_txt = '')
    {
        $this->sid_tipo_activ_txt = $sid_tipo_activ_txt;
    }

    /**
     * Recupera el atributo ifase_ref de PermUsuarioActividad
     *
     * @return integer ifase_ref
     */
    function getFase_ref()
    {
        if (!isset($this->ifase_ref) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->ifase_ref;
    }

    /**
     * Establece el valor del atributo ifase_ref de PermUsuarioActividad
     *
     * @param integer ifase_ref='' optional
     */
    function setFase_ref($ifase_ref = '')
    {
        $this->ifase_ref = $ifase_ref;
    }

    /**
     * Recupera el atributo iafecta_a de PermUsuarioActividad
     *
     * @return integer iafecta_a
     */
    function getAfecta_a()
    {
        if (!isset($this->iafecta_a) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->iafecta_a;
    }

    /**
     * Establece el valor del atributo iafecta_a de PermUsuarioActividad
     *
     * @param integer iafecta_a='' optional
     */
    function setAfecta_a($iafecta_a = '')
    {
        $this->iafecta_a = $iafecta_a;
    }

    /**
     * recupera l'atribut iperm_on de permusuarioactividad
     *
     * @return integer iperm_on
     */
    function getPerm_on()
    {
        if (!isset($this->iperm_on) && !$this->bLoaded) {
            $this->dbcarregar();
        }
        return $this->iperm_on;
    }

    /**
     * Establece el valor del atributo iperm_on de permusuarioactividad
     *
     * @param integer iperm_on='' optional
     */
    function setPerm_on($iperm_on = '')
    {
        $this->iperm_on = $iperm_on;
    }

    /**
     * recupera l'atribut iperm_off de permusuarioactividad
     *
     * @return integer iperm_off
     */
    function getPerm_off()
    {
        if (!isset($this->iperm_off) && !$this->bLoaded) {
            $this->dbcarregar();
        }
        return $this->iperm_off;
    }

    /**
     * Establece el valor del atributo iperm_off de permusuarioactividad
     *
     * @param integer iperm_off='' optional
     */
    function setPerm_off($iperm_off = '')
    {
        $this->iperm_off = $iperm_off;
    }
    /* MÉTODOS GET y SET D'ATRIBUTOS QUE NO SON CAMPOS -----------------------------*/

    /**
     * Retorna una col·lecció d'objectes del tipus DatosCampo
     *
     */
    function getDatosCampos()
    {
        $oPermUsuarioActividadSet = new core\Set();

        $oPermUsuarioActividadSet->add($this->getDatosId_usuario());
        $oPermUsuarioActividadSet->add($this->getDatosDl_propia());
        $oPermUsuarioActividadSet->add($this->getDatosId_tipo_activ_txt());
        $oPermUsuarioActividadSet->add($this->getDatosFase_ref());
        $oPermUsuarioActividadSet->add($this->getDatosAfecta_a());
        $oPermUsuarioActividadSet->add($this->getDatosPerm_on());
        $oPermUsuarioActividadSet->add($this->getDatosPerm_off());
        return $oPermUsuarioActividadSet->getTot();
    }

    /**
     * Recupera les propietats de l'atribut iid_usuario de PermUsuarioActividad
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosId_usuario()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'id_usuario'));
        $oDatosCampo->setEtiqueta(_("id_usuario"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut bdl_propia de PermUsuarioActividad
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosDl_propia()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'dl_propia'));
        $oDatosCampo->setEtiqueta(_("dl_propia"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut sid_tipo_activ_txt de PermUsuarioActividad
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosId_tipo_activ_txt()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'id_tipo_activ_txt'));
        $oDatosCampo->setEtiqueta(_("id_tipo_activ_txt"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut ifase_ref de PermUsuarioActividad
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosFase_ref()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'fase_ref'));
        $oDatosCampo->setEtiqueta(_("Fase de referencia"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut iafecta_a de PermUsuarioActividad
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosAfecta_a()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'afecta_a'));
        $oDatosCampo->setEtiqueta(_("afecta_a"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut iperm_on de PermUsuarioActividad
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosPerm_on()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'perm_on'));
        $oDatosCampo->setEtiqueta(_("perm_on"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut iperm_off de PermUsuarioActividad
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosPerm_off()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'perm_off'));
        $oDatosCampo->setEtiqueta(_("perm_off"));
        return $oDatosCampo;
    }
}
