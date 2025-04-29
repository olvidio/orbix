<?php
namespace actividadcargos\model\entity;

use core\ClasePropiedades;
use core\DatosCampo;
use core\Set;
use function core\is_true;

/**
 * Fitxer amb la Classe que accedeix a la taula xd_orden_cargo
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 19/11/2014
 */

/**
 * Clase que implementa la entidad xd_orden_cargo
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 19/11/2014
 */
class Cargo extends ClasePropiedades
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * aPrimary_key de cargo
     *
     * @var array
     */
    private $aPrimary_key;

    /**
     * aDades de cargo
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
     * Id_cargo de cargo
     *
     * @var integer
     */
    private $iid_cargo;
    /**
     * Cargo de cargo
     *
     * @var string
     */
    private $scargo;
    /**
     * Orden_cargo de cargo
     *
     * @var integer
     */
    private $iorden_cargo;
    /**
     * Sf de cargo
     *
     * @var boolean
     */
    private $bsf;
    /**
     * Sv de cargo
     *
     * @var boolean
     */
    private $bsv;
    /**
     * Tipo_cargo de cargo
     *
     * @var string
     */
    private $stipo_cargo;
    /* ATRIBUTOS QUE NO SON CAMPOS------------------------------------------------- */
    /**
     * oDbl de cargo
     *
     * @var object
     */
    protected $oDbl;
    /**
     * NomTabla de cargo
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
     * @param integer|array iid_cargo
     *                        $a_id. Un array con los nombres=>valores de las claves primarias.
     */
    function __construct($a_id = '')
    {
        $oDbl = $GLOBALS['oDBPC'];
        $oDbl_Select = $GLOBALS['oDBPC_Select'];
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                if (($nom_id === 'id_cargo') && $val_id !== '') $this->iid_cargo = (int)$val_id;
            }
        } else {
            if (isset($a_id) && $a_id !== '') {
                $this->iid_cargo = (integer)$a_id; 
                $this->aPrimary_key = array('id_cargo' => $this->iid_cargo);
            }
        }
        $this->setoDbl($oDbl);
        $this->setoDbl_Select($oDbl_Select);
        $this->setNomTabla('xd_orden_cargo');
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
        $aDades = [];
        $aDades['cargo'] = $this->scargo;
        $aDades['orden_cargo'] = $this->iorden_cargo;
        $aDades['sf'] = $this->bsf;
        $aDades['sv'] = $this->bsv;
        $aDades['tipo_cargo'] = $this->stipo_cargo;
        array_walk($aDades, 'core\poner_null');
        //para el caso de los boolean FALSE, el pdo(+postgresql) pone string '' en vez de 0. Lo arreglo:
        if (is_true($aDades['sf'])) {
            $aDades['sf'] = 'true';
        } else {
            $aDades['sf'] = 'false';
        }
        if (is_true($aDades['sv'])) {
            $aDades['sv'] = 'true';
        } else {
            $aDades['sv'] = 'false';
        }

        if ($bInsert === false) {
            //UPDATE
            $update = "
					cargo                    = :cargo,
					orden_cargo              = :orden_cargo,
					sf                       = :sf,
					sv                       = :sv,
					tipo_cargo               = :tipo_cargo";
            if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_cargo='$this->iid_cargo'")) === false) {
                $sClauError = 'cargo.update.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'cargo.update.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return false;
                }
            }
        } else {
            // INSERT
            $campos = "(cargo,orden_cargo,sf,sv,tipo_cargo)";
            $valores = "(:cargo,:orden_cargo,:sf,:sv,:tipo_cargo)";
            if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === false) {
                $sClauError = 'cargo.insertar.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'cargo.insertar.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return false;
                }
            }
            $this->iid_cargo = $oDbl->lastInsertId('xd_orden_cargo_id_cargo_seq');
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
        if (isset($this->iid_cargo)) {
            if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_cargo='$this->iid_cargo'")) === false) {
                $sClauError = 'cargo.carregar';
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
        if (($oDbl->exec("DELETE FROM $nom_tabla WHERE id_cargo='$this->iid_cargo'")) === false) {
            $sClauError = 'cargo.eliminar';
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
        if (array_key_exists('id_cargo', $aDades)) $this->setId_cargo($aDades['id_cargo']);
        if (array_key_exists('cargo', $aDades)) $this->setCargo($aDades['cargo']);
        if (array_key_exists('orden_cargo', $aDades)) $this->setOrden_cargo($aDades['orden_cargo']);
        if (array_key_exists('sf', $aDades)) $this->setSf($aDades['sf']);
        if (array_key_exists('sv', $aDades)) $this->setSv($aDades['sv']);
        if (array_key_exists('tipo_cargo', $aDades)) $this->setTipo_cargo($aDades['tipo_cargo']);
    }

    /**
     * Establece a 'empty' el valor de todos los atributos
     *
     */
    function setNullAllAtributes()
    {
        $aPK = $this->getPrimary_key();
        $this->setId_cargo('');
        $this->setCargo('');
        $this->setOrden_cargo('');
        $this->setSf('');
        $this->setSv('');
        $this->setTipo_cargo('');
        $this->setPrimary_key($aPK);
    }


    /* MÉTODOS GET y SET --------------------------------------------------------*/

    /**
     * Recupera todos los atributos de cargo en un array
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
     * Recupera la clave primaria de cargo en un array
     *
     * @return array aPrimary_key
     */
    function getPrimary_key()
    {
        if (!isset($this->aPrimary_key)) {
            $this->aPrimary_key = array('id_cargo' => $this->iid_cargo);
        }
        return $this->aPrimary_key;
    }

    /**
     * Establece la clave primaria de cargo en un array
     *
     * @return array aPrimary_key
     */
    public function setPrimary_key($a_id = '')
    {
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                if (($nom_id === 'id_cargo') && $val_id !== '') $this->iid_cargo = (int)$val_id;
            }
        }
    }

    /**
     * Recupera el atributo iid_cargo de cargo
     *
     * @return integer iid_cargo
     */
    function getId_cargo()
    {
        if (!isset($this->iid_cargo) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->iid_cargo;
    }

    /**
     * Establece el valor del atributo iid_cargo de cargo
     *
     * @param integer iid_cargo
     */
    function setId_cargo($iid_cargo)
    {
        $this->iid_cargo = $iid_cargo;
    }

    /**
     * Recupera el atributo scargo de cargo
     *
     * @return string scargo
     */
    function getCargo()
    {
        if (!isset($this->scargo) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->scargo;
    }

    /**
     * Establece el valor del atributo scargo de cargo
     *
     * @param string scargo='' optional
     */
    function setCargo($scargo = '')
    {
        $this->scargo = $scargo;
    }

    /**
     * Recupera el atributo iorden_cargo de cargo
     *
     * @return integer iorden_cargo
     */
    function getOrden_cargo()
    {
        if (!isset($this->iorden_cargo) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->iorden_cargo;
    }

    /**
     * Establece el valor del atributo iorden_cargo de cargo
     *
     * @param integer iorden_cargo='' optional
     */
    function setOrden_cargo($iorden_cargo = '')
    {
        $this->iorden_cargo = $iorden_cargo;
    }

    /**
     * Recupera el atributo bsf de cargo
     *
     * @return boolean bsf
     */
    function getSf()
    {
        if (!isset($this->bsf) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->bsf;
    }

    /**
     * Establece el valor del atributo bsf de cargo
     *
     * @param boolean bsf='f' optional
     */
    function setSf($bsf = 'f')
    {
        $this->bsf = $bsf;
    }

    /**
     * Recupera el atributo bsv de cargo
     *
     * @return boolean bsv
     */
    function getSv()
    {
        if (!isset($this->bsv) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->bsv;
    }

    /**
     * Establece el valor del atributo bsv de cargo
     *
     * @param boolean bsv='f' optional
     */
    function setSv($bsv = 'f')
    {
        $this->bsv = $bsv;
    }

    /**
     * Recupera el atributo stipo_cargo de cargo
     *
     * @return string stipo_cargo
     */
    function getTipo_cargo()
    {
        if (!isset($this->stipo_cargo) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->stipo_cargo;
    }

    /**
     * Establece el valor del atributo stipo_cargo de cargo
     *
     * @param string stipo_cargo='' optional
     */
    function setTipo_cargo($stipo_cargo = '')
    {
        $this->stipo_cargo = $stipo_cargo;
    }
    /* MÉTODOS GET y SET D'ATRIBUTOS QUE NO SON CAMPOS -----------------------------*/

    /**
     * Retorna una col·lecció d'objectes del tipus DatosCampo
     *
     */
    function getDatosCampos()
    {
        $ocargoSet = new Set();

        $ocargoSet->add($this->getDatosCargo());
        $ocargoSet->add($this->getDatosOrden_cargo());
        $ocargoSet->add($this->getDatosSf());
        $ocargoSet->add($this->getDatosSv());
        $ocargoSet->add($this->getDatosTipo_cargo());
        return $ocargoSet->getTot();
    }


    /**
     * Recupera les propietats de l'atribut scargo de cargo
     * en una clase del tipus DatosCampo
     *
     * @return object DatosCampo
     */
    function getDatosCargo()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'cargo'));
        $oDatosCampo->setEtiqueta(_("cargo"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut iorden_cargo de cargo
     * en una clase del tipus DatosCampo
     *
     * @return object DatosCampo
     */
    function getDatosOrden_cargo()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'orden_cargo'));
        $oDatosCampo->setEtiqueta(_("orden cargo"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut bsf de cargo
     * en una clase del tipus DatosCampo
     *
     * @return object DatosCampo
     */
    function getDatosSf()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'sf'));
        $oDatosCampo->setEtiqueta(_("sf"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut bsv de cargo
     * en una clase del tipus DatosCampo
     *
     * @return object DatosCampo
     */
    function getDatosSv()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'sv'));
        $oDatosCampo->setEtiqueta(_("sv"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut stipo_cargo de cargo
     * en una clase del tipus DatosCampo
     *
     * @return object DatosCampo
     */
    function getDatosTipo_cargo()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'tipo_cargo'));
        $oDatosCampo->setEtiqueta(_("tipo de cargo"));
        return $oDatosCampo;
    }
}

?>
