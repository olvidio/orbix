<?php

namespace devel\model\entity;

use core;

/**
 * Fitxer amb la Classe que accedeix a la taula db_idschema
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 19/06/2018
 */

/**
 * Clase que implementa la entidad db_idschema
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 19/06/2018
 */
class DbSchema extends core\ClasePropiedades
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * aPrimary_key de DbSchema
     *
     * @var array
     */
    private $aPrimary_key;

    /**
     * aDades de DbSchema
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
     * Schema de DbSchema
     *
     * @var string
     */
    private $sschema;
    /**
     * Id de DbSchema
     *
     * @var integer
     */
    private $iid;
    /* ATRIBUTOS QUE NO SON CAMPOS------------------------------------------------- */
    /**
     * oDbl de DbSchema
     *
     * @var object
     */
    protected $oDbl;
    /**
     * NomTabla de DbSchema
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
     * @param integer|array sschema
     *                        $a_id. Un array con los nombres=>valores de las claves primarias.
     */
    function __construct($a_id = '')
    {
        $oDbl = $GLOBALS['oDBPC'];
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                if (($nom_id == 'schema') && $val_id !== '') $this->sschema = (string)$val_id; // evitem SQL injection fent cast a string
            }
        } else {
            if (isset($a_id) && $a_id !== '') {
                $this->sschema = (integer)$a_id;
                $this->aPrimary_key = array('schema' => $this->sschema);
            }
        }
        $this->setoDbl($oDbl);
        $this->setNomTabla('db_idschema');
    }

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    /**
     * Cambiar nombre: al reves que lo normal, uso de clave el id
     *
     */
    public function DBCambiarNombre($old, $new)
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        //UPDATE
        $update = "UPDATE $nom_tabla SET schema='$new' WHERE schema='$old'";

        try {
            $oDbl->query($update);
        } catch (\PDOException $e) {
            $err_txt = $e->errorInfo[2];
            $this->setErrorTxt($err_txt);
            $sClauError = 'DbSchema.update.execute';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }

        return true;
    }

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
        $aDades['id'] = $this->iid;
        array_walk($aDades, 'core\poner_null');

        if ($bInsert === false) {
            //UPDATE
            $update = "
					id                       = :id";
            if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE schema='$this->sschema'")) === false) {
                $sClauError = 'DbSchema.update.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'DbSchema.update.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return false;
                }
            }
        } else {
            // INSERT
            array_unshift($aDades, $this->sschema);
            $campos = "(schema,id)";
            $valores = "(:schema,:id)";
            if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === false) {
                $sClauError = 'DbSchema.insertar.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'DbSchema.insertar.execute';
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
        if (isset($this->sschema)) {
            if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE schema='$this->sschema'")) === false) {
                $sClauError = 'DbSchema.carregar';
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
        if (($oDblSt = $oDbl->exec("DELETE FROM $nom_tabla WHERE schema='$this->sschema'")) === false) {
            $sClauError = 'DbSchema.eliminar';
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
        if (array_key_exists('schema', $aDades)) $this->setSchema($aDades['schema']);
        if (array_key_exists('id', $aDades)) $this->setId($aDades['id']);
    }

    /**
     * Establece a 'empty' el valor de todos los atributos
     *
     */
    function setNullAllAtributes()
    {
        $aPK = $this->getPrimary_key();
        $this->setSchema('');
        $this->setId('');
        $this->setPrimary_key($aPK);
    }


    /* MÉTODOS GET y SET --------------------------------------------------------*/

    /**
     * Recupera todos los atributos de DbSchema en un array
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
     * Recupera la clave primaria de DbSchema en un array
     *
     * @return array aPrimary_key
     */
    function getPrimary_key()
    {
        if (!isset($this->aPrimary_key)) {
            $this->aPrimary_key = array('schema' => $this->sschema);
        }
        return $this->aPrimary_key;
    }

    /**
     * Establece la clave primaria de DbSchema en un array
     *
     * @return array aPrimary_key
     */
    public function setPrimary_key($a_id = '')
    {
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                if (($nom_id == 'schema') && $val_id !== '') $this->sschema = $val_id;
            }
        }
    }

    /**
     * Recupera el atributo sschema de DbSchema
     *
     * @return string sschema
     */
    function getSchema()
    {
        if (!isset($this->sschema) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->sschema;
    }

    /**
     * Establece el valor del atributo sschema de DbSchema
     *
     * @param string sschema
     */
    function setSchema($sschema)
    {
        $this->sschema = $sschema;
    }

    /**
     * Recupera el atributo iid de DbSchema
     *
     * @return integer iid
     */
    function getId()
    {
        if (!isset($this->iid) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->iid;
    }

    /**
     * Establece el valor del atributo iid de DbSchema
     *
     * @param integer iid='' optional
     */
    function setId($iid = '')
    {
        $this->iid = $iid;
    }
    /* MÉTODOS GET y SET D'ATRIBUTOS QUE NO SON CAMPOS -----------------------------*/

    /**
     * Retorna una col·lecció d'objectes del tipus DatosCampo
     *
     */
    function getDatosCampos()
    {
        $oDbSchemaSet = new core\Set();

        $oDbSchemaSet->add($this->getDatosId());
        return $oDbSchemaSet->getTot();
    }


    /**
     * Recupera les propietats de l'atribut iid de DbSchema
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosId()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'id'));
        $oDatosCampo->setEtiqueta(_("id"));
        return $oDatosCampo;
    }
}
