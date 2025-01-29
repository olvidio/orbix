<?php
namespace ubis\model\entity;

use core\ClasePropiedades;
use core\DatosCampo;
use core\Set;
use function core\is_true;

/**
 * Fitxer amb la Classe que accedeix a la taula u_cross_ubi_dir
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 11/02/2014
 */

/**
 * Clase que implementa la entidad u_cross_ubi_dir
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 11/02/2014
 */
abstract class UbixDireccion extends ClasePropiedades
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * aPrimary_key de UbixDireccion
     *
     * @var array
     */
    protected $aPrimary_key;

    /**
     * aDades de UbixDireccion
     *
     * @var array
     */
    protected $aDades;

    /**
     * bLoaded
     *
     * @var boolean
     */
    protected $bLoaded = FALSE;

    /**
     * Id_ubi de UbixDireccion
     *
     * @var integer
     */
    protected $iid_ubi;
    /**
     * Id_direccion de UbixDireccion
     *
     * @var integer
     */
    protected $iid_direccion;
    /**
     * Propietario de UbixDireccion
     *
     * @var boolean
     */
    protected $bpropietario;
    /**
     * Principal de UbixDireccion
     *
     * @var boolean
     */
    protected $bprincipal;
    /* ATRIBUTOS QUE NO SON CAMPOS------------------------------------------------- */
    /* CONSTRUCTOR -------------------------------------------------------------- */

    /**
     * Constructor de la classe.
     * Si només necessita un valor, se li pot passar un integer.
     * En general se li passa un array amb les claus primàries.
     *
     * @param integer|array iid_ubi,iid_direccion
     *                        $a_id. Un array con los nombres=>valores de las claves primarias.
     */
    function __construct($a_id = '')
    {
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
        if ($this->DBCarregar('guardar') === false) {
            $bInsert = true;
        } else {
            $bInsert = false;
        }
        $aDades = array();
        $aDades['propietario'] = $this->bpropietario;
        $aDades['principal'] = $this->bprincipal;
        array_walk($aDades, 'core\poner_null');
        //para el caso de los boolean FALSE, el pdo(+postgresql) pone string '' en vez de 0. Lo arreglo:
        if (is_true($aDades['propietario'])) {
            $aDades['propietario'] = 'true';
        } else {
            $aDades['propietario'] = 'false';
        }
        if (is_true($aDades['principal'])) {
            $aDades['principal'] = 'true';
        } else {
            $aDades['principal'] = 'false';
        }

        if ($bInsert === false) {
            //UPDATE
            $update = "
					propietario              = :propietario,
					principal         	     = :principal";
            if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_ubi='$this->iid_ubi' AND id_direccion='$this->iid_direccion'")) === false) {
                $sClauError = 'UbixDireccion.update.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'UbixDireccion.update.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return false;
                }
            }
        } else {
            // INSERT
            array_unshift($aDades, $this->iid_ubi, $this->iid_direccion);
            $campos = "(id_ubi,id_direccion,propietario,principal)";
            $valores = "(:id_ubi,:id_direccion,:propietario,:principal)";
            if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === false) {
                $sClauError = 'UbixDireccion.insertar.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'UbixDireccion.insertar.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return false;
                }
            }
        }
        $this->setAllAtributes($aDades);
        return true;
    }

    /**
     * Carga los campos de la base de datos como atributos de la clase.
     *
     */
    public function DBCarregar($que = null)
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        if (isset($this->iid_ubi) && isset($this->iid_direccion)) {
            if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_ubi='$this->iid_ubi' AND id_direccion='$this->iid_direccion'")) === false) {
                $sClauError = 'UbixDireccion.carregar';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            }
            $aDades = $oDblSt->fetch(\PDO::FETCH_ASSOC);
            // Para evitar posteriores cargas
            $this->bLoaded = TRUE;
            switch ($que) {
                case 'tot':
                    $this->aDades = $aDades;
                    break;
                case 'guardar':
                    if (!$oDblSt->rowCount()) return false;
                    break;
                default:
                    // En el caso de no existir esta fila, $aDades = FALSE:
                    if ($aDades === FALSE) {
                        $this->setNullAllAtributes();
                    } else {
                        $this->setAllAtributes($aDades);
                    }
            }
            return true;
        } else {
            return false;
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
        if (($oDblSt = $oDbl->exec("DELETE FROM $nom_tabla WHERE id_ubi='$this->iid_ubi' AND id_direccion='$this->iid_direccion'")) === false) {
            $sClauError = 'UbixDireccion.eliminar';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        return true;
    }

    /* OTROS MÉTODOS  ----------------------------------------------------------*/
    /* MÉTODOS PRIVADOS ----------------------------------------------------------*/

    /**
     * Establece el valor de todos los atributos
     *
     * @param array $aDades
     */
    function setAllAtributes(array $aDades)
    {
        if (!is_array($aDades)) return;
        if (array_key_exists('id_schema', $aDades)) $this->setId_schema($aDades['id_schema']);
        if (array_key_exists('id_ubi', $aDades)) $this->setId_ubi($aDades['id_ubi']);
        if (array_key_exists('id_direccion', $aDades)) $this->setId_direccion($aDades['id_direccion']);
        if (array_key_exists('propietario', $aDades)) $this->setPropietario($aDades['propietario']);
        if (array_key_exists('principal', $aDades)) $this->setPrincipal($aDades['principal']);
    }

    /**
     * Establece a 'empty' el valor de todos los atributos
     *
     */
    function setNullAllAtributes()
    {
        $aPK = $this->getPrimary_key();
        $this->setId_schema('');
        $this->setId_ubi('');
        $this->setId_direccion('');
        $this->setPropietario('');
        $this->setPrincipal('');
        $this->setPrimary_key($aPK);
    }


    /* MÉTODOS GET y SET --------------------------------------------------------*/
    /**
     * Recupera todos los atributos de UbixDireccion en un array
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
     * Recupera la clave primaria de UbixDireccion en un array
     *
     * @return array aPrimary_key
     */
    function getPrimary_key()
    {
        if (!isset($this->aPrimary_key)) {
            $this->aPrimary_key = array('id_ubi' => $this->iid_ubi, 'id_direccion' => $this->iid_direccion);
        }
        return $this->aPrimary_key;
    }

    /**
     * Establece la clave primaria de UbixDireccion en un array
     *
     * @return array aPrimary_key
     */
    public function setPrimary_key($a_id = '')
    {
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                if (($nom_id == 'id_ubi') && $val_id !== '') $this->iid_ubi = (int)$val_id; 
                if (($nom_id == 'id_direccion') && $val_id !== '') $this->iid_direccion = (int)$val_id; 
            }
        }
    }

    /**
     * Recupera el atributo iid_ubi de UbixDireccion
     *
     * @return integer iid_ubi
     */
    function getId_ubi()
    {
        if (!isset($this->iid_ubi) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->iid_ubi;
    }

    /**
     * Establece el valor del atributo iid_ubi de UbixDireccion
     *
     * @param integer iid_ubi
     */
    function setId_ubi($iid_ubi)
    {
        $this->iid_ubi = $iid_ubi;
    }

    /**
     * Recupera el atributo iid_direccion de UbixDireccion
     *
     * @return integer iid_direccion
     */
    function getId_direccion()
    {
        if (!isset($this->iid_direccion) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->iid_direccion;
    }

    /**
     * Establece el valor del atributo iid_direccion de UbixDireccion
     *
     * @param integer iid_direccion
     */
    function setId_direccion($iid_direccion)
    {
        $this->iid_direccion = $iid_direccion;
    }

    /**
     * Recupera el atributo bpropietario de UbixDireccion
     *
     * @return boolean bpropietario
     */
    function getPropietario()
    {
        if (!isset($this->bpropietario) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->bpropietario;
    }

    /**
     * Establece el valor del atributo bpropietario de UbixDireccion
     *
     * @param boolean bpropietario='f' optional
     */
    function setPropietario($bpropietario = 'f')
    {
        $this->bpropietario = $bpropietario;
    }

    /**
     * Recupera el atributo bprincipal de UbixDireccion
     *
     * @return boolean bprincipal
     */
    function getPrincipal()
    {
        if (!isset($this->bprincipal) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->bprincipal;
    }

    /**
     * Establece el valor del atributo bprincipal de UbixDireccion
     *
     * @param boolean bprincipal='f' optional
     */
    function setPrincipal($bprincipal = 'f')
    {
        $this->bprincipal = $bprincipal;
    }
    /* MÉTODOS GET y SET D'ATRIBUTOS QUE NO SON CAMPOS -----------------------------*/

    /**
     * Retorna una col·lecció d'objectes del tipus DatosCampo
     *
     */
    function getDatosCampos()
    {
        $oUbixDireccionSet = new Set();

        $oUbixDireccionSet->add($this->getDatosPropietario());
        $oUbixDireccionSet->add($this->getDatosPrincipal());
        return $oUbixDireccionSet->getTot();
    }


    /**
     * Recupera les propietats de l'atribut bpropietario de UbixDireccion
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosPropietario()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'propietario'));
        $oDatosCampo->setEtiqueta(_("propietario"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut bprincipal de UbixDireccion
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosPrincipal()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'principal'));
        $oDatosCampo->setEtiqueta(_("principal"));
        return $oDatosCampo;
    }
}

?>
