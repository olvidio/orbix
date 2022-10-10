<?php

namespace usuarios\model\entity;

use core;

/**
 * Fitxer amb la Classe que accedeix a la taula aux_usuarios_ctr_perm
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 20/5/2019
 */

/**
 * Clase que implementa la entidad aux_usuarios_ctr_perm
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 20/5/2019
 */
class PermUsuarioCentro extends core\ClasePropiedades
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * aPrimary_key de PermUsuarioCentro
     *
     * @var array
     */
    private $aPrimary_key;

    /**
     * aDades de PermUsuarioCentro
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
     * Id_item de PermUsuarioCentro
     *
     * @var integer
     */
    private $iid_item;
    /**
     * Id_usuario de PermUsuarioCentro
     *
     * @var integer
     */
    private $iid_usuario;
    /**
     * Id_ctr de PermUsuarioCentro
     *
     * @var integer
     */
    private $iid_ctr;
    /**
     * Perm_ctr de PermUsuarioCentro
     *
     * @var integer
     */
    private $iperm_ctr;
    /* ATRIBUTOS QUE NO SON CAMPOS------------------------------------------------- */
    /**
     * oDbl de PermUsuarioCentro
     *
     * @var object
     */
    protected $oDbl;
    /**
     * NomTabla de PermUsuarioCentro
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
        $this->setNomTabla('aux_usuarios_ctr_perm');
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
        $aDades['id_ctr'] = $this->iid_ctr;
        $aDades['perm_ctr'] = $this->iperm_ctr;
        array_walk($aDades, 'core\poner_null');

        if ($bInsert === FALSE) {
            //UPDATE
            $update = "
					id_usuario               = :id_usuario,
					id_ctr                   = :id_ctr,
					perm_ctr                 = :perm_ctr";
            if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_item='$this->iid_item'")) === FALSE) {
                $sClauError = 'PermUsuarioCentro.update.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return FALSE;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'PermUsuarioCentro.update.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return FALSE;
                }
            }
        } else {
            // INSERT
            $campos = "(id_usuario,id_ctr,perm_ctr)";
            $valores = "(:id_usuario,:id_ctr,:perm_ctr)";
            if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === FALSE) {
                $sClauError = 'PermUsuarioCentro.insertar.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return FALSE;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'PermUsuarioCentro.insertar.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return FALSE;
                }
            }
            $this->id_item = $oDbl->lastInsertId('aux_usuarios_ctr_perm_id_item_seq');
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
                $sClauError = 'PermUsuarioCentro.carregar';
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
            $sClauError = 'PermUsuarioCentro.eliminar';
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
        if (array_key_exists('id_ctr', $aDades)) $this->setId_ctr($aDades['id_ctr']);
        if (array_key_exists('perm_ctr', $aDades)) $this->setPerm_ctr($aDades['perm_ctr']);
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
        $this->setId_ctr('');
        $this->setPerm_ctr('');
        $this->setPrimary_key($aPK);
    }


    /* MÉTODOS GET y SET --------------------------------------------------------*/

    /**
     * Recupera todos los atributos de PermUsuarioCentro en un array
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
     * Recupera la clave primaria de PermUsuarioCentro en un array
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
     * Establece la clave primaria de PermUsuarioCentro en un array
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
     * Recupera el atributo iid_item de PermUsuarioCentro
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
     * Establece el valor del atributo iid_item de PermUsuarioCentro
     *
     * @param integer iid_item
     */
    function setId_item($iid_item)
    {
        $this->iid_item = $iid_item;
    }

    /**
     * Recupera el atributo iid_usuario de PermUsuarioCentro
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
     * Establece el valor del atributo iid_usuario de PermUsuarioCentro
     *
     * @param integer iid_usuario='' optional
     */
    function setId_usuario($iid_usuario = '')
    {
        $this->iid_usuario = $iid_usuario;
    }

    /**
     * Recupera el atributo iid_ctr de PermUsuarioCentro
     *
     * @return integer iid_ctr
     */
    function getId_ctr()
    {
        if (!isset($this->iid_ctr) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->iid_ctr;
    }

    /**
     * Establece el valor del atributo iid_ctr de PermUsuarioCentro
     *
     * @param integer iid_ctr='' optional
     */
    function setId_ctr($iid_ctr = '')
    {
        $this->iid_ctr = $iid_ctr;
    }

    /**
     * Recupera el atributo iperm_ctr de PermUsuarioCentro
     *
     * @return integer iperm_ctr
     */
    function getPerm_ctr()
    {
        if (!isset($this->iperm_ctr) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->iperm_ctr;
    }

    /**
     * Establece el valor del atributo iperm_ctr de PermUsuarioCentro
     *
     * @param integer iperm_ctr='' optional
     */
    function setPerm_ctr($iperm_ctr = '')
    {
        $this->iperm_ctr = $iperm_ctr;
    }
    /* MÉTODOS GET y SET D'ATRIBUTOS QUE NO SON CAMPOS -----------------------------*/

    /**
     * Retorna una col·lecció d'objectes del tipus DatosCampo
     *
     */
    function getDatosCampos()
    {
        $oPermUsuarioCentroSet = new core\Set();

        $oPermUsuarioCentroSet->add($this->getDatosId_usuario());
        $oPermUsuarioCentroSet->add($this->getDatosId_ctr());
        $oPermUsuarioCentroSet->add($this->getDatosPerm_ctr());
        return $oPermUsuarioCentroSet->getTot();
    }


    /**
     * Recupera les propietats de l'atribut iid_usuario de PermUsuarioCentro
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
     * Recupera les propietats de l'atribut iid_ctr de PermUsuarioCentro
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosId_ctr()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'id_ctr'));
        $oDatosCampo->setEtiqueta(_("id_ctr"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut iperm_ctr de PermUsuarioCentro
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosPerm_ctr()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'perm_ctr'));
        $oDatosCampo->setEtiqueta(_("perm_ctr"));
        return $oDatosCampo;
    }
}
