<?php

namespace permisos\model\entity;

use core\ClasePropiedades;
use core\DatosCampo;
use core\Set;
use devel\model\AppDB;
use function core\is_true;

/**
 * Fitxer amb la Classe que accedeix a la taula m0_mods_installed_dl
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 15/12/2014
 */

/**
 * Clase que implementa la entidad m0_mods_installed_dl
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 15/12/2014
 */
class ModuloInstalado extends ClasePropiedades
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * aPrimary_key de ModuloInstalado
     *
     * @var array
     */
    private $aPrimary_key;

    /**
     * aDades de ModuloInstalado
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
     * Id_schema de ModuloInstalado
     *
     * @var integer
     */
    private $iid_schema;
    /**
     * Id_mod de ModuloInstalado
     *
     * @var integer
     */
    private $iid_mod;
    /**
     * Status de ModuloInstalado
     *
     * @var boolean
     */
    private $bstatus;
    /**
     * Param de ModuloInstalado
     *
     * @var string
     */
    private $sparam;
    /* ATRIBUTOS QUE NO SON CAMPOS------------------------------------------------- */
    /**
     * oDbl de ModuloInstalado
     *
     * @var object
     */
    protected $oDbl;
    /**
     * NomTabla de ModuloInstalado
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
     * @param integer|array iid_mod
     *                        $a_id. Un array con los nombres=>valores de las claves primarias.
     */
    function __construct($a_id = '')
    {
        $oDbl = $GLOBALS['oDBE'];
        $oDbl_Select = $GLOBALS['oDBE_Select'];
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                if (($nom_id === 'id_mod') && $val_id !== '') $this->iid_mod = (int)$val_id;
            }
        } else {
            if (isset($a_id) && $a_id !== '') {
                $this->iid_mod = (integer)$a_id; 
                $this->aPrimary_key = array('id_mod' => $this->iid_mod);
            }
        }
        $this->setoDbl($oDbl);
        $this->setoDbl_Select($oDbl_Select);
        $this->setNomTabla('m0_mods_installed_dl');
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
        $aPrevDades = $this->aDades;
        $aDades = array();
        $aDades['status'] = $this->bstatus;
        $aDades['param'] = $this->sparam;
        array_walk($aDades, 'core\poner_null');
        //para el caso de los boolean FALSE, el pdo(+postgresql) pone string '' en vez de 0. Lo arreglo:
        if (is_true($aDades['status'])) {
            $aDades['status'] = 'true';
        } else {
            $aDades['status'] = 'false';
        }

        if ($bInsert === false) {
            //UPDATE
            $update = "
					status                   = :status,
					param                    = :param";
            if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_mod='$this->iid_mod'")) === false) {
                $sClauError = 'ModuloInstalado.update.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'ModuloInstalado.update.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return false;
                }
            }
            /*
             * Activar la creación de tablas necesarias para la app.
             */
            if ($aPrevDades['status'] === FALSE && is_true($aDades['status'] )) {
                $oAppDB = new AppDB($this->iid_mod);
                $oAppDB->createTables();
            }
        } else {
            // INSERT
            array_unshift($aDades, $this->iid_mod);
            $campos = "(id_mod,status,param)";
            $valores = "(:id_mod,:status,:param)";
            if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === false) {
                $sClauError = 'ModuloInstalado.insertar.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'ModuloInstalado.insertar.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return false;
                }
            }
            /*
             * Activar la creación de tablas necesarias para la app.
             * las creo siempre, aunque no esté activo. (17/6/2020)
             */
            //if (is_true($aDades['status'])) {
            $oAppDB = new AppDB($this->iid_mod);
            $oAppDB->createTables();
            //}
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
        if (isset($this->iid_mod)) {
            if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_mod='$this->iid_mod'")) === false) {
                $sClauError = 'ModuloInstalado.carregar';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            }
            $aDades = $oDblSt->fetch(\PDO::FETCH_ASSOC);
            // Para evitar posteriores cargas
            $this->bLoaded = TRUE;
            $this->aDades = $aDades;
            switch ($que) {
                case 'tot':
                    //$this->aDades=$aDades;
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
        if (($oDbl->exec("DELETE FROM $nom_tabla WHERE id_mod='$this->iid_mod'")) === false) {
            $sClauError = 'ModuloInstalado.eliminar';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        /*
         * Eliminar las tablas necesarias para la app.
         */
        $oAppDB = new AppDB($this->iid_mod);
        $oAppDB->dropTables();

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
        if (array_key_exists('id_mod', $aDades)) $this->setId_mod($aDades['id_mod']);
        if (array_key_exists('status', $aDades)) $this->setStatus($aDades['status']);
        if (array_key_exists('param', $aDades)) $this->setParam($aDades['param']);
    }

    /**
     * Establece a 'empty' el valor de todos los atributos
     *
     */
    function setNullAllAtributes()
    {
        $aPK = $this->getPrimary_key();
        $this->setId_schema('');
        $this->setId_mod('');
        $this->setStatus('');
        $this->setParam('');
        $this->setPrimary_key($aPK);
    }


    /* MÉTODOS GET y SET --------------------------------------------------------*/

    /**
     * Recupera todos los atributos de ModuloInstalado en un array
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
     * Recupera la clave primaria de ModuloInstalado en un array
     *
     * @return array aPrimary_key
     */
    function getPrimary_key()
    {
        if (!isset($this->aPrimary_key)) {
            $this->aPrimary_key = array('id_mod' => $this->iid_mod);
        }
        return $this->aPrimary_key;
    }

    /**
     * Establece la clave primaria de ModuloInstalado en un array
     *
     * @return array aPrimary_key
     */
    public function setPrimary_key($a_id = '')
    {
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                if (($nom_id === 'id_mod') && $val_id !== '') $this->iid_mod = (int)$val_id;
            }
        }
    }

    /**
     * Recupera el atributo iid_mod de ModuloInstalado
     *
     * @return integer iid_mod
     */
    function getId_mod()
    {
        if (!isset($this->iid_mod) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->iid_mod;
    }

    /**
     * Establece el valor del atributo iid_mod de ModuloInstalado
     *
     * @param integer iid_mod
     */
    function setId_mod($iid_mod)
    {
        $this->iid_mod = $iid_mod;
    }

    /**
     * Recupera el atributo bstatus de ModuloInstalado
     *
     * @return boolean bstatus
     */
    function getStatus()
    {
        if (!isset($this->bstatus) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->bstatus;
    }

    /**
     * Establece el valor del atributo bstatus de ModuloInstalado
     *
     * @param boolean bstatus='f' optional
     */
    function setStatus($bstatus = 'f')
    {
        $this->bstatus = $bstatus;
    }

    /**
     * Recupera el atributo sparam de ModuloInstalado
     *
     * @return string sparam
     */
    function getParam()
    {
        if (!isset($this->sparam) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->sparam;
    }

    /**
     * Establece el valor del atributo sparam de ModuloInstalado
     *
     * @param string sparam='' optional
     */
    function setParam($sparam = '')
    {
        $this->sparam = $sparam;
    }
    /* MÉTODOS GET y SET D'ATRIBUTOS QUE NO SON CAMPOS -----------------------------*/

    /**
     * Retorna una col·lecció d'objectes del tipus DatosCampo
     *
     */
    function getDatosCampos()
    {
        $oModuloInstaladoSet = new Set();

        $oModuloInstaladoSet->add($this->getDatosId_mod());
        $oModuloInstaladoSet->add($this->getDatosStatus());
        $oModuloInstaladoSet->add($this->getDatosParam());
        return $oModuloInstaladoSet->getTot();
    }

    function getDatosId_mod()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'id_mod'));
        $oDatosCampo->setEtiqueta(_("nombre"));
        $oDatosCampo->setTipo('opciones');
        $oDatosCampo->setArgument('devel\model\entity\Modulo');
        $oDatosCampo->setArgument2('getNom'); // método para obtener el valor a mostrar del objeto relacionado.
        $oDatosCampo->setArgument3('getListaModulos');
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut bstatus de ModuloInstalado
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosStatus()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'status'));
        $oDatosCampo->setEtiqueta(_("activo"));
        $oDatosCampo->setTipo('check');
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut sparam de ModuloInstalado
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosParam()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'param'));
        $oDatosCampo->setEtiqueta(_("parametros"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument('50');
        return $oDatosCampo;
    }
}
