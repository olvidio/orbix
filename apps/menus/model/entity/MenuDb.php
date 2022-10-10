<?php
namespace menus\model\entity;

use core;

/**
 * Fitxer amb la Classe que accedeix a la taula $nom_tabla
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 15/01/2014
 */

/**
 * Clase que implementa la entidad $nom_tabla
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 15/01/2014
 */
class MenuDb extends core\ClasePropiedades
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * aPrimary_key de MenuDb
     *
     * @var array
     */
    private $aPrimary_key;

    /**
     * aDades de MenuDb
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
     * Id_menu de MenuDb
     *
     * @var integer
     */
    private $iid_menu;
    /**
     * Orden de MenuDb
     *
     * @var integer
     */
    private $iorden;
    /**
     * Menu de MenuDb
     *
     * @var string
     */
    private $smenu;
    /**
     * Parametros de MenuDb
     *
     * @var string
     */
    private $sparametros;
    /**
     * Id_metamenu de MenuDb
     *
     * @var integer
     */
    private $iid_metamenu;
    /**
     * Menu_perm de MenuDb
     *
     * @var integer
     */
    private $imenu_perm;
    /**
     * Id_grupmenu de MenuDb
     *
     * @var integer
     */
    private $iid_grupmenu;
    /**
     * Ok de MenuDb
     *
     * @var boolean
     */
    private $bok;
    /* ATRIBUTOS QUE NO SON CAMPOS------------------------------------------------- */

    /* CONSTRUCTOR -------------------------------------------------------------- */

    /**
     * Constructor de la classe.
     * Si només necessita un valor, se li pot passar un integer.
     * En general se li passa un array amb les claus primàries.
     *
     * @param integer|array iid_menu
     *                        $a_id. Un array con los nombres=>valores de las claves primarias.
     */
    function __construct($a_id = '')
    {
        $oDbl = $GLOBALS['oDBE'];
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                if (($nom_id == 'id_menu') && $val_id !== '') $this->iid_menu = (int)$val_id; 
            }
        } else {
            if (isset($a_id) && $a_id !== '') {
                $this->iid_menu = (integer)$a_id; 
                $this->aPrimary_key = array('id_menu' => $this->iid_menu);
            }
        }
        $this->setoDbl($oDbl);
        $this->setNomTabla('aux_menus');
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
        $aDades['orden'] = $this->iorden;
        $aDades['menu'] = $this->smenu;
        $aDades['parametros'] = $this->sparametros;
        $aDades['id_metamenu'] = $this->iid_metamenu;
        $aDades['menu_perm'] = $this->imenu_perm;
        $aDades['id_grupmenu'] = $this->iid_grupmenu;
        $aDades['ok'] = $this->bok;
        array_walk($aDades, 'core\poner_null');
        //para el caso de los boolean FALSE, el pdo(+postgresql) pone string '' en vez de 0. Lo arreglo:
        if (core\is_true($aDades['ok'])) {
            $aDades['ok'] = 'true';
        } else {
            $aDades['ok'] = 'false';
        }

        if ($bInsert === false) {
            //UPDATE
            $update = "
					orden                    = :orden,
					menu                     = :menu,
					parametros               = :parametros,
					id_metamenu              = :id_metamenu,
					menu_perm                = :menu_perm,
					id_grupmenu              = :id_grupmenu,
					ok 			             = :ok";
            if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_menu='$this->iid_menu'")) === false) {
                $sClauError = 'MenuDb.update.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'MenuDb.update.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return false;
                }
            }
        } else {
            // INSERT
            $campos = "(orden,menu,parametros,id_metamenu,menu_perm,id_grupmenu,ok)";
            $valores = "(:orden,:menu,:parametros,:id_metamenu,:menu_perm,:id_grupmenu,:ok)";
            if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === false) {
                $sClauError = 'MenuDb.insertar.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'MenuDb.insertar.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return false;
                }
            }
            $this->id_menu = $oDbl->lastInsertId($nom_tabla . '_id_menu_seq');
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
        if (isset($this->iid_menu)) {
            if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_menu='$this->iid_menu'")) === false) {
                $sClauError = 'MenuDb.carregar';
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
        if (($oDblSt = $oDbl->exec("DELETE FROM $nom_tabla WHERE id_menu='$this->iid_menu'")) === false) {
            $sClauError = 'MenuDb.eliminar';
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
        if (array_key_exists('id_menu', $aDades)) $this->setId_menu($aDades['id_menu']);
        if (array_key_exists('orden', $aDades)) $this->setOrden($aDades['orden']);
        if (array_key_exists('menu', $aDades)) $this->setMenu($aDades['menu']);
        if (array_key_exists('parametros', $aDades)) $this->setParametros($aDades['parametros']);
        if (array_key_exists('id_metamenu', $aDades)) $this->setId_metamenu($aDades['id_metamenu']);
        if (array_key_exists('menu_perm', $aDades)) $this->setMenu_perm($aDades['menu_perm']);
        if (array_key_exists('id_grupmenu', $aDades)) $this->setId_grupmenu($aDades['id_grupmenu']);
        if (array_key_exists('ok', $aDades)) $this->setOk($aDades['ok']);
    }

    /**
     * Establece a 'empty' el valor de todos los atributos
     *
     */
    function setNullAllAtributes()
    {
        $aPK = $this->getPrimary_key();
        $this->setId_schema('');
        $this->setId_menu('');
        $this->setOrden('');
        $this->setMenu('');
        $this->setParametros('');
        $this->setId_metamenu('');
        $this->setMenu_perm('');
        $this->setId_grupmenu('');
        $this->setOk('');
        $this->setPrimary_key($aPK);
    }


    /* MÉTODOS GET y SET --------------------------------------------------------*/
    /**
     * Recupera todos los atributos de MenuDb en un array
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
     * Recupera la clave primaria de MenuDb en un array
     *
     * @return array aPrimary_key
     */
    function getPrimary_key()
    {
        if (!isset($this->aPrimary_key)) {
            $this->aPrimary_key = array('id_menu' => $this->iid_menu);
        }
        return $this->aPrimary_key;
    }

    /**
     * Establece la clave primaria de MenuDb en un array
     *
     * @return array aPrimary_key
     */
    public function setPrimary_key($a_id = '')
    {
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                if (($nom_id == 'id_menu') && $val_id !== '') $this->iid_menu = (int)$val_id; 
            }
        }
    }

    /**
     * Recupera el atributo iid_menu de MenuDb
     *
     * @return integer iid_menu
     */
    function getId_menu()
    {
        if (!isset($this->iid_menu) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->iid_menu;
    }

    /**
     * Establece el valor del atributo iid_menu de MenuDb
     *
     * @param integer iid_menu
     */
    function setId_menu($iid_menu)
    {
        $this->iid_menu = $iid_menu;
    }

    /**
     * Recupera el atributo iorden de MenuDb
     *
     * @return integer iorden
     */
    function getOrden()
    {
        if (!isset($this->iorden) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->iorden;
    }

    /**
     * Establece el valor del atributo iorden de MenuDb
     *
     * @param integer iorden='' optional
     */
    function setOrden($iorden = '')
    {
        $this->iorden = $iorden;
    }

    /**
     * Recupera el atributo smenu de MenuDb
     *
     * @return string smenu
     */
    function getMenu()
    {
        if (!isset($this->smenu) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->smenu;
    }

    /**
     * Establece el valor del atributo smenu de MenuDb
     *
     * @param string smenu='' optional
     */
    function setMenu($smenu = '')
    {
        $this->smenu = $smenu;
    }

    /**
     * Recupera el atributo sparametros de MenuDb
     *
     * @return string sparametros
     */
    function getParametros()
    {
        if (!isset($this->sparametros) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->sparametros;
    }

    /**
     * Establece el valor del atributo sparametros de MenuDb
     *
     * @param string sparametros='' optional
     */
    function setParametros($sparametros = '')
    {
        $this->sparametros = $sparametros;
    }

    /**
     * Recupera el atributo iid_metamenu de MenuDb
     *
     * @return integer iid_metamenu
     */
    function getId_metamenu()
    {
        if (!isset($this->iid_metamenu) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->iid_metamenu;
    }

    /**
     * Establece el valor del atributo iid_metamenu de MenuDb
     *
     * @param integer iid_metamenu='' optional
     */
    function setId_metamenu($iid_metamenu = '')
    {
        $this->iid_metamenu = $iid_metamenu;
    }

    /**
     * Recupera el atributo imenu_perm de MenuDb
     *
     * @return integer imenu_perm
     */
    function getMenu_perm()
    {
        if (!isset($this->imenu_perm) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->imenu_perm;
    }

    /**
     * Establece el valor del atributo imenu_perm de MenuDb
     *
     * @param integer imenu_perm='' optional
     */
    function setMenu_perm($imenu_perm = '')
    {
        $this->imenu_perm = $imenu_perm;
    }

    /**
     * Recupera el atributo iid_grupmenu de MenuDb
     *
     * @return integer iid_grupmenu
     */
    function getId_grupmenu()
    {
        if (!isset($this->iid_grupmenu) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->iid_grupmenu;
    }

    /**
     * Establece el valor del atributo iid_grupmenu de MenuDb
     *
     * @param integer iid_grupmenu='' optional
     */
    function setId_grupmenu($iid_grupmenu = '')
    {
        $this->iid_grupmenu = $iid_grupmenu;
    }

    /**
     * Recupera el atributo bok de MenuDb
     *
     * @return boolean bok
     */
    function getOk()
    {
        if (!isset($this->bok) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->bok;
    }

    /**
     * Establece el valor del atributo bok de MenuDb
     *
     * @param boolean bok='f' optional
     */
    function setOk($bok = 'f')
    {
        $this->bok = $bok;
    }

    /* MÉTODOS GET y SET D'ATRIBUTOS QUE NO SON CAMPOS -----------------------------*/

    /**
     * Retorna una col·lecció d'objectes del tipus DatosCampo
     *
     */
    function getDatosCampos()
    {
        $oMenuDbSet = new core\Set();

        $oMenuDbSet->add($this->getDatosOrden());
        $oMenuDbSet->add($this->getDatosMenu());
        $oMenuDbSet->add($this->getDatosParametros());
        $oMenuDbSet->add($this->getDatosId_metamenu());
        $oMenuDbSet->add($this->getDatosMenu_perm());
        $oMenuDbSet->add($this->getDatosId_grupmenu());
        return $oMenuDbSet->getTot();
    }


    /**
     * Recupera les propietats de l'atribut iorden de MenuDb
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosOrden()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'orden'));
        $oDatosCampo->setEtiqueta(_("orden"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut smenu de MenuDb
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosMenu()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'menu'));
        $oDatosCampo->setEtiqueta(_("menú"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut sparametros de MenuDb
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosParametros()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'parametros'));
        $oDatosCampo->setEtiqueta(_("parametros"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut iid_metamenu de MenuDb
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosId_metamenu()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'id_metamenu'));
        $oDatosCampo->setEtiqueta(_("id_metamenu"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut imenu_perm de MenuDb
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosMenu_perm()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'menu_perm'));
        $oDatosCampo->setEtiqueta(_("menu_perm"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut iid_grupmenu de MenuDb
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosId_grupmenu()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'id_grupmenu'));
        $oDatosCampo->setEtiqueta(_("id_grupmenu"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut bok de MenuDb
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosOk()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'ok'));
        $oDatosCampo->setEtiqueta(_("ok"));
        return $oDatosCampo;
    }

}

?>
