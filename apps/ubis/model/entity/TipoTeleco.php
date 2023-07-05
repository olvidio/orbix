<?php
namespace ubis\model\entity;

use core;

/**
 * Clase que implementa la entidad xd_tipo_teleco
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 01/10/2010
 */
class TipoTeleco extends core\ClasePropiedades
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * aPrimary_key de TipoTeleco
     *
     * @var array
     */
    private $aPrimary_key;

    /**
     * aDades de TipoTeleco
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
     * Tipo_teleco de TipoTeleco
     *
     * @var string
     */
    private $stipo_teleco;
    /**
     * Nombre_teleco de TipoTeleco
     *
     * @var string
     */
    private $snombre_teleco;
    /**
     * Ubi de TipoTeleco
     *
     * @var boolean
     */
    private $bubi;
    /**
     * Persona de TipoTeleco
     *
     * @var boolean
     */
    private $bpersona;
    /* ATRIBUTOS QUE NO SON CAMPOS------------------------------------------------- */
    /* CONSTRUCTOR -------------------------------------------------------------- */

    /**
     * Constructor de la classe.
     * Si només necessita un valor, se li pot passar un integer.
     * En general se li passa un array amb les claus primàries.
     *
     * @param integer|array stipo_teleco
     *                        $a_id. Un array con los nombres=>valores de las claves primarias.
     */
    function __construct($a_id = '')
    {
        $oDbl = $GLOBALS['oDBPC'];
        $oDbl_Select = $GLOBALS['oDBPC_Select'];
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                $nom_id = 'i' . $nom_id; //imagino que es un integer
                if ($val_id !== '') $this->$nom_id = (integer)$val_id; 
            }
        } else {
            if (isset($a_id) && $a_id !== '') {
                $this->stipo_teleco = $a_id;
                $this->aPrimary_key = array('tipo_teleco' => $this->stipo_teleco);
            }
        }
        $this->setoDbl($oDbl);
        $this->setoDbl_Select($oDbl_Select);
        $this->setNomTabla('xd_tipo_teleco');
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
        $aDades['nombre_teleco'] = $this->snombre_teleco;
        $aDades['ubi'] = $this->bubi;
        $aDades['persona'] = $this->bpersona;
        array_walk($aDades, 'core\poner_null');
        //para el caso de los boolean FALSE, el pdo(+postgresql) pone string '' en vez de 0. Lo arreglo:
        if (core\is_true($aDades['ubi'])) {
            $aDades['ubi'] = 'true';
        } else {
            $aDades['ubi'] = 'false';
        }
        if (core\is_true($aDades['persona'])) {
            $aDades['persona'] = 'true';
        } else {
            $aDades['persona'] = 'false';
        }

        if ($bInsert === false) {
            //UPDATE
            $update = "
					nombre_teleco            = :nombre_teleco,
					ubi                      = :ubi,
					persona                  = :persona";
            if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE tipo_teleco='$this->stipo_teleco'")) === false) {
                $sClauError = 'TipoTeleco.update.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'TipoTeleco.update.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return false;
                }
            }
        } else {
            // INSERT
            array_unshift($aDades, $this->stipo_teleco);
            $campos = "(tipo_teleco,nombre_teleco,ubi,persona)";
            $valores = "(:tipo_teleco,:nombre_teleco,:ubi,:persona)";
            if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === false) {
                $sClauError = 'TipoTeleco.insertar.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'TipoTeleco.insertar.execute';
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
        if (isset($this->stipo_teleco)) {
            if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE tipo_teleco='$this->stipo_teleco'")) === false) {
                $sClauError = 'TipoTeleco.carregar';
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
        if (($oDblSt = $oDbl->exec("DELETE FROM $nom_tabla WHERE tipo_teleco='$this->stipo_teleco'")) === false) {
            $sClauError = 'TipoTeleco.eliminar';
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
    function setAllAtributes($aDades)
    {
        if (!is_array($aDades)) return;
        if (array_key_exists('tipo_teleco', $aDades)) $this->setTipo_teleco($aDades['tipo_teleco']);
        if (array_key_exists('nombre_teleco', $aDades)) $this->setNombre_teleco($aDades['nombre_teleco']);
        if (array_key_exists('ubi', $aDades)) $this->setUbi($aDades['ubi']);
        if (array_key_exists('persona', $aDades)) $this->setPersona($aDades['persona']);
    }

    /**
     * Establece a 'empty' el valor de todos los atributos
     *
     */
    function setNullAllAtributes()
    {
        $aPK = $this->getPrimary_key();
        $this->setTipo_teleco('');
        $this->setNombre_teleco('');
        $this->setUbi('');
        $this->setPersona('');
        $this->setPrimary_key($aPK);
    }


    /* MÉTODOS GET y SET --------------------------------------------------------*/
    /**
     * Recupera todos los atributos de TipoTeleco en un array
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
     * Recupera la clave primaria de TipoTeleco en un array
     *
     * @return array aPrimary_key
     */
    function getPrimary_key()
    {
        if (!isset($this->aPrimary_key)) {
            $this->aPrimary_key = array('tipo_teleco' => $this->stipo_teleco);
        }
        return $this->aPrimary_key;
    }

    /**
     * Establece la clave primaria de TipoTeleco en un array
     *
     * @return array aPrimary_key
     */
    public function setPrimary_key($a_id = '')
    {
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                if (($nom_id == 'tipo_teleco') && $val_id !== '') $this->stipo_teleco = $val_id;
            }
        }
    }

    /**
     * Recupera el atributo stipo_teleco de TipoTeleco
     *
     * @return string stipo_teleco
     */
    function getTipo_teleco()
    {
        if (!isset($this->stipo_teleco) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->stipo_teleco;
    }

    /**
     * Establece el valor del atributo stipo_teleco de TipoTeleco
     *
     * @param string stipo_teleco
     */
    function setTipo_teleco($stipo_teleco)
    {
        $this->stipo_teleco = $stipo_teleco;
    }

    /**
     * Recupera el atributo snombre_teleco de TipoTeleco
     *
     * @return string snombre_teleco
     */
    function getNombre_teleco()
    {
        if (!isset($this->snombre_teleco) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->snombre_teleco;
    }

    /**
     * Establece el valor del atributo snombre_teleco de TipoTeleco
     *
     * @param string snombre_teleco
     */
    function setNombre_teleco($snombre_teleco)
    {
        $this->snombre_teleco = $snombre_teleco;
    }

    /**
     * Recupera el atributo bubi de TipoTeleco
     *
     * @return boolean bubi
     */
    function getUbi()
    {
        if (!isset($this->bubi) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->bubi;
    }

    /**
     * Establece el valor del atributo bubi de TipoTeleco
     *
     * @param boolean bubi='f' optional
     */
    function setUbi($bubi = 'f')
    {
        $this->bubi = $bubi;
    }

    /**
     * Recupera el atributo bpersona de TipoTeleco
     *
     * @return boolean bpersona
     */
    function getPersona()
    {
        if (!isset($this->bpersona) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->bpersona;
    }

    /**
     * Establece el valor del atributo bpersona de TipoTeleco
     *
     * @param boolean bpersona='f' optional
     */
    function setPersona($bpersona = 'f')
    {
        $this->bpersona = $bpersona;
    }
    /* MÉTODOS GET y SET D'ATRIBUTOS QUE NO SON CAMPOS -----------------------------*/

    /**
     * Retorna una col·lecció d'objectes del tipus DatosCampo
     *
     */
    function getDatosCampos()
    {
        $oTipoTelecoSet = new core\Set();

        $oTipoTelecoSet->add($this->getDatosUbi());
        $oTipoTelecoSet->add($this->getDatosPersona());
        return $oTipoTelecoSet->getTot();
    }


    /**
     * Recupera les propietats de l'atribut bubi de TipoTeleco
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosUbi()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'ubi'));
        $oDatosCampo->setEtiqueta(_("ubi"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut bpersona de TipoTeleco
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosPersona()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'persona'));
        $oDatosCampo->setEtiqueta(_("persona"));
        return $oDatosCampo;
    }
}

?>
