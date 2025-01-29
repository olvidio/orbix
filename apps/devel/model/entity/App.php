<?php
namespace devel\model\entity;

use core\ClasePropiedades;
use core\DatosCampo;
use core\Set;

/**
 * Fitxer amb la Classe que accedeix a la taula m0_apps
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 15/12/2014
 */

/**
 * Clase que implementa la entidad m0_apps
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 15/12/2014
 */
class App extends ClasePropiedades
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * aPrimary_key de App
     *
     * @var array
     */
    private $aPrimary_key;

    /**
     * aDades de App
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
     * Id_app de App
     *
     * @var integer
     */
    private $iid_app;
    /**
     * Nom de App
     *
     * @var string
     */
    private $snom;
    /**
     * Db_prefix de App
     *
     * @var string
     */
    private $sdb_prefix;
    /* ATRIBUTOS QUE NO SON CAMPOS------------------------------------------------- */
    /**
     * oDbl de App
     *
     * @var object
     */
    protected $oDbl;
    /**
     * NomTabla de App
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
     * @param integer|array iid_app
     *                        $a_id. Un array con los nombres=>valores de las claves primarias.
     */
    function __construct($a_id = '')
    {
        $oDbl = $GLOBALS['oDBPC'];
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                if (($nom_id === 'id_app') && $val_id !== '') $this->iid_app = (int)$val_id;
            }
        } else {
            if (isset($a_id) && $a_id !== '') {
                $this->iid_app = (integer)$a_id;
                $this->aPrimary_key = array('id_app' => $this->iid_app);
            }
        }
        $this->setoDbl($oDbl);
        $this->setNomTabla('m0_apps');
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
        $aDades['nom'] = $this->snom;
        $aDades['db_prefix'] = $this->sdb_prefix;
        array_walk($aDades, 'core\poner_null');

        if ($bInsert === false) {
            //UPDATE
            $update = "
					nom                      = :nom,
					db_prefix                = :db_prefix";
            if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_app='$this->iid_app'")) === false) {
                $sClauError = 'App.update.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'App.update.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return false;
                }
            }
        } else {
            // INSERT
            $campos = "(nom,db_prefix)";
            $valores = "(:nom,:db_prefix)";
            if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === false) {
                $sClauError = 'App.insertar.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'App.insertar.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return false;
                }
            }
            $this->iid_app = $oDbl->lastInsertId('m0_apps_id_app_seq');
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
        if (isset($this->iid_app)) {
            if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_app='$this->iid_app'")) === false) {
                $sClauError = 'App.carregar';
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
        if (($oDblSt = $oDbl->exec("DELETE FROM $nom_tabla WHERE id_app='$this->iid_app'")) === false) {
            $sClauError = 'App.eliminar';
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
        if (array_key_exists('id_app', $aDades)) $this->setId_app($aDades['id_app']);
        if (array_key_exists('nom', $aDades)) $this->setNom($aDades['nom']);
        if (array_key_exists('db_prefix', $aDades)) $this->setDb_prefix($aDades['db_prefix']);
    }

    /**
     * Establece a 'empty' el valor de todos los atributos
     *
     */
    function setNullAllAtributes()
    {
        $aPK = $this->getPrimary_key();
        $this->setId_schema('');
        $this->setId_app('');
        $this->setNom('');
        $this->setDb_prefix('');
        $this->setPrimary_key($aPK);
    }


    /* MÉTODOS GET y SET --------------------------------------------------------*/

    /**
     * Recupera todos los atributos de App en un array
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
     * Recupera la clave primaria de App en un array
     *
     * @return array aPrimary_key
     */
    function getPrimary_key()
    {
        if (!isset($this->aPrimary_key)) {
            $this->aPrimary_key = array('id_app' => $this->iid_app);
        }
        return $this->aPrimary_key;
    }

    /**
     * Establece la clave primaria de App en un array
     *
     * @return array aPrimary_key
     */
    public function setPrimary_key($a_id = '')
    {
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                if (($nom_id === 'id_app') && $val_id !== '') $this->iid_app = (int)$val_id;
            }
        }
    }

    /**
     * Recupera el atributo iid_app de App
     *
     * @return integer iid_app
     */
    function getId_app()
    {
        if (!isset($this->iid_app) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->iid_app;
    }

    /**
     * Establece el valor del atributo iid_app de App
     *
     * @param integer iid_app
     */
    function setId_app($iid_app)
    {
        $this->iid_app = $iid_app;
    }

    /**
     * Recupera el atributo snom de App
     *
     * @return string snom
     */
    function getNom()
    {
        if (!isset($this->snom) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->snom;
    }

    /**
     * Establece el valor del atributo snom de App
     *
     * @param string snom='' optional
     */
    function setNom($snom = '')
    {
        $this->snom = $snom;
    }

    /**
     * Recupera el atributo sdb_prefix de App
     *
     * @return string sdb_prefix
     */
    function getDb_prefix()
    {
        if (!isset($this->sdb_prefix) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->sdb_prefix;
    }

    /**
     * Establece el valor del atributo sdb_prefix de App
     *
     * @param string sdb_prefix='' optional
     */
    function setDb_prefix($sdb_prefix = '')
    {
        $this->sdb_prefix = $sdb_prefix;
    }
    /* MÉTODOS GET y SET D'ATRIBUTOS QUE NO SON CAMPOS -----------------------------*/

    /**
     * Retorna una col·lecció d'objectes del tipus DatosCampo
     *
     */
    function getDatosCampos()
    {
        $oAppSet = new Set();

        $oAppSet->add($this->getDatosNom());
        $oAppSet->add($this->getDatosDb_prefix());
        return $oAppSet->getTot();
    }


    /**
     * Recupera les propietats de l'atribut snom de App
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosNom()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'nom'));
        $oDatosCampo->setEtiqueta(_("nombre"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(30);
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut sdb_prefix de App
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosDb_prefix()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'db_prefix'));
        $oDatosCampo->setEtiqueta(_("db prefix"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(30);
        return $oDatosCampo;
    }
}

?>
