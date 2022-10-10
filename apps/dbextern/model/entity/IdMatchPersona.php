<?php

namespace dbextern\model\entity;

use core;

/**
 * Fitxer amb la Classe que accedeix a la taula conv_id_personas
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 28/02/2017
 */

/**
 * Clase que implementa la entidad conv_id_personas
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 28/02/2017
 */
class IdMatchPersona extends core\ClasePropiedades
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * aPrimary_key de IdMatchPersona
     *
     * @var array
     */
    private $aPrimary_key;

    /**
     * aDades de IdMatchPersona
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
     * Id_listas de IdMatchPersona
     *
     * @var integer
     */
    private $iid_listas;
    /**
     * Id_orbix de IdMatchPersona
     *
     * @var integer
     */
    private $iid_orbix;
    /**
     * Id_tabla de IdMatchPersona
     *
     * @var string
     */
    private $sid_tabla;
    /* ATRIBUTOS QUE NO SON CAMPOS------------------------------------------------- */
    /**
     * oDbl de IdMatchPersona
     *
     * @var object
     */
    protected $oDbl;
    /**
     * NomTabla de IdMatchPersona
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
     * @param integer|array iid_listas
     *                        $a_id. Un array con los nombres=>valores de las claves primarias.
     */
    function __construct($a_id = '')
    {
        $oDbl = $GLOBALS['oDBP'];
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                if (($nom_id == 'id_listas') && $val_id !== '') $this->iid_listas = (int)$val_id; 
            }
        } else {
            if (isset($a_id) && $a_id !== '') {
                $this->iid_listas = (integer)$a_id; 
                $this->aPrimary_key = array('id_listas' => $this->iid_listas);
            }
        }
        $this->setoDbl($oDbl);
        $this->setNomTabla('conv_id_personas');
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
        $aDades['id_orbix'] = $this->iid_orbix;
        $aDades['id_tabla'] = $this->sid_tabla;
        array_walk($aDades, 'core\poner_null');

        if ($bInsert === false) {
            //UPDATE
            $update = "
					id_orbix                 = :id_orbix,
					id_tabla                 = :id_tabla";
            if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_listas='$this->iid_listas'")) === false) {
                $sClauError = 'IdMatchPersona.update.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'IdMatchPersona.update.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return false;
                }
            }
        } else {
            // INSERT
            array_unshift($aDades, $this->iid_listas);
            $campos = "(id_listas,id_orbix,id_tabla)";
            $valores = "(:id_listas,:id_orbix,:id_tabla)";
            if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === false) {
                $sClauError = 'IdMatchPersona.insertar.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'IdMatchPersona.insertar.execute';
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
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        if (isset($this->iid_listas)) {
            if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_listas='$this->iid_listas'")) === false) {
                $sClauError = 'IdMatchPersona.carregar';
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
        if (($oDblSt = $oDbl->exec("DELETE FROM $nom_tabla WHERE id_listas='$this->iid_listas'")) === false) {
            $sClauError = 'IdMatchPersona.eliminar';
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
        if (array_key_exists('id_schema', $aDades)) $this->setId_schema($aDades['id_schema']);
        if (array_key_exists('id_listas', $aDades)) $this->setId_listas($aDades['id_listas']);
        if (array_key_exists('id_orbix', $aDades)) $this->setId_orbix($aDades['id_orbix']);
        if (array_key_exists('id_tabla', $aDades)) $this->setId_tabla($aDades['id_tabla']);
    }

    /**
     * Establece a 'empty' el valor de todos los atributos
     *
     */
    function setNullAllAtributes()
    {
        $aPK = $this->getPrimary_key();
        $this->setId_schema('');
        $this->setId_listas('');
        $this->setId_orbix('');
        $this->setId_tabla('');
        $this->setPrimary_key($aPK);
    }


    /* MÉTODOS GET y SET --------------------------------------------------------*/

    /**
     * Recupera todos los atributos de IdMatchPersona en un array
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
     * Recupera la clave primaria de IdMatchPersona en un array
     *
     * @return array aPrimary_key
     */
    function getPrimary_key()
    {
        if (!isset($this->aPrimary_key)) {
            $this->aPrimary_key = array('id_listas' => $this->iid_listas);
        }
        return $this->aPrimary_key;
    }

    /**
     * Establece la clave primaria de IdMatchPersona en un array
     *
     * @return array aPrimary_key
     */
    public function setPrimary_key($a_id = '')
    {
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                if (($nom_id == 'id_listas') && $val_id !== '') $this->iid_listas = (int)$val_id; 
            }
        }
    }

    /**
     * Recupera el atributo iid_listas de IdMatchPersona
     *
     * @return integer iid_listas
     */
    function getId_listas()
    {
        if (!isset($this->iid_listas) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->iid_listas;
    }

    /**
     * Establece el valor del atributo iid_listas de IdMatchPersona
     *
     * @param integer iid_listas
     */
    function setId_listas($iid_listas)
    {
        $this->iid_listas = $iid_listas;
    }

    /**
     * Recupera el atributo iid_orbix de IdMatchPersona
     *
     * @return integer iid_orbix
     */
    function getId_orbix()
    {
        if (!isset($this->iid_orbix) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->iid_orbix;
    }

    /**
     * Establece el valor del atributo iid_orbix de IdMatchPersona
     *
     * @param integer iid_orbix='' optional
     */
    function setId_orbix($iid_orbix = '')
    {
        $this->iid_orbix = $iid_orbix;
    }

    /**
     * Recupera el atributo sid_tabla de IdMatchPersona
     *
     * @return string sid_tabla
     */
    function getId_tabla()
    {
        if (!isset($this->sid_tabla) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->sid_tabla;
    }

    /**
     * Establece el valor del atributo sid_tabla de IdMatchPersona
     *
     * @param string sid_tabla='' optional
     */
    function setId_tabla($sid_tabla = '')
    {
        $this->sid_tabla = $sid_tabla;
    }
    /* MÉTODOS GET y SET D'ATRIBUTOS QUE NO SON CAMPOS -----------------------------*/

    /**
     * Retorna una col·lecció d'objectes del tipus DatosCampo
     *
     */
    function getDatosCampos()
    {
        $oIdMatchPersonaSet = new core\Set();

        $oIdMatchPersonaSet->add($this->getDatosId_orbix());
        $oIdMatchPersonaSet->add($this->getDatosId_tabla());
        return $oIdMatchPersonaSet->getTot();
    }


    /**
     * Recupera les propietats de l'atribut iid_orbix de IdMatchPersona
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosId_orbix()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'id_orbix'));
        $oDatosCampo->setEtiqueta(_("id_orbix"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut sid_tabla de IdMatchPersona
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosId_tabla()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'id_tabla'));
        $oDatosCampo->setEtiqueta(_("id_tabla"));
        return $oDatosCampo;
    }
}

?>