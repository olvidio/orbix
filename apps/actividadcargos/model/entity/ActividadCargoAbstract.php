<?php

namespace actividadcargos\model\entity;

use cambios\model\GestorAvisoCambios;
use core\ClasePropiedades;
use core\ConfigGlobal;
use core\DatosCampo;
use core\Set;
use ReflectionClass;
use function core\is_true;

/**
 * Fitxer amb la Classe que accedeix a la taula d_cargos_activ_dl
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 19/11/2014
 */

/**
 * Clase que implementa la entidad d_cargos_activ_dl
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 19/11/2014
 */
abstract class ActividadCargoAbstract extends ClasePropiedades
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * aPrimary_key de ActividadCargo
     *
     * @var array
     */
    protected $aPrimary_key;

    /**
     * aDades de ActividadCargo
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
     * aDades de ActividadCargo abans dels canvis.
     *
     * @var array
     */
    protected $aDadesActuals;

    /**
     * Id_schema de ActividadCargo
     *
     * @var integer
     */
    protected $iid_schema;
    /**
     * Id_item de ActividadCargo
     *
     * @var integer
     */
    protected $iid_item;
    /**
     * Id_activ de ActividadCargo
     *
     * @var integer
     */
    protected $iid_activ;
    /**
     * Id_cargo de ActividadCargo
     *
     * @var integer
     */
    protected $iid_cargo;
    /**
     * Id_nom de ActividadCargo
     *
     * @var integer
     */
    protected $iid_nom;
    /**
     * Puede_agd de ActividadCargo
     *
     * @var boolean
     */
    protected $bpuede_agd;
    /**
     * Observ de ActividadCargo
     *
     * @var string
     */
    protected $sobserv;
    /* ATRIBUTOS QUE NO SON CAMPOS------------------------------------------------- */
    /**
     * oDbl de ActividadCargo
     *
     * @var object
     */
    protected $oDbl;
    /**
     * NomTabla de ActividadCargo
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
        $this->setoDbl($oDbl);
        $this->setoDbl_Select($oDbl_Select);
        $this->setNomTabla('d_cargos_activ_dl');

        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                //if (($nom_id == 'id_item') && $val_id !== '') $this->iid_item = (int)$val_id;
                $this->$nom_id = (int)$val_id;
            }
        } else {
            if (isset($a_id) && $a_id !== '') {
                $this->iid_item = (integer)$a_id;
                $this->aPrimary_key = array('id_item' => $this->iid_item);
            }
        }
    }

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    /**
     * Guarda los atributos de la clase en la base de datos.
     * Si no existe el registro, hace el insert; Si existe hace el update.
     *
     * @param bool optional $quiet : true per que no apunti els canvis. 0 (per defecte) apunta els canvis.
     */
    public function DBGuardar($quiet = 0)
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        if ($this->DBCarregar('guardar') === false) {
            $bInsert = true;
        } else {
            $bInsert = false;
        }
        $aDades = [];
        //$aDades['id_schema'] = $this->iid_schema;
        $aDades['id_activ'] = $this->iid_activ;
        $aDades['id_cargo'] = $this->iid_cargo;
        $aDades['id_nom'] = $this->iid_nom;
        $aDades['puede_agd'] = $this->bpuede_agd;
        $aDades['observ'] = $this->sobserv;
        array_walk($aDades, 'core\poner_null');
        //para el caso de los boolean FALSE, el pdo(+postgresql) pone string '' en vez de 0. Lo arreglo:
        if (is_true($aDades['puede_agd'])) {
            $aDades['puede_agd'] = 'true';
        } else {
            $aDades['puede_agd'] = 'false';
        }

        if ($bInsert === false) {
            //UPDATE
            $update = "
					id_activ                 = :id_activ,
					id_cargo                 = :id_cargo,
					id_nom                   = :id_nom,
					puede_agd                = :puede_agd,
					observ                   = :observ";
            if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_item=$this->iid_item ")) === false) {
                $sClauError = 'ActividadCargo.update.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'ActividadCargo.update.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return false;
                }
            }
            // Anoto el cambio
            if (empty($quiet) && ConfigGlobal::is_app_installed('cambios')) {
                $oGestorCanvis = new GestorAvisoCambios();
                $shortClassName = (new ReflectionClass($this))->getShortName();
                $oGestorCanvis->addCanvi($shortClassName, 'UPDATE', $this->iid_activ, $aDades, $this->aDadesActuals);
            }
            $this->setAllAtributes($aDades);
        } else {
            // INSERT
            $campos = "(id_activ,id_cargo,id_nom,puede_agd,observ)";
            $valores = "(:id_activ,:id_cargo,:id_nom,:puede_agd,:observ)";
            if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === false) {
                $sClauError = 'ActividadCargo.insertar.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'ActividadCargo.insertar.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return false;
                }
            }
            $id_item = $oDbl->lastInsertId('d_cargos_activ_dl_id_item_seq');
            if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_item=$id_item")) === false) {
                $sClauError = 'ActividadCargo.carregar.Last';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            }
            $aDadesLast = $oDblSt->fetch(\PDO::FETCH_ASSOC);
            $this->aDades = $aDadesLast;
            $this->setAllAtributes($aDadesLast);
            // Anoto el cambio
            if (empty($quiet) && ConfigGlobal::is_app_installed('cambios')) {
                $oGestorCanvis = new GestorAvisoCambios();
                $shortClassName = (new ReflectionClass($this))->getShortName();
                $oGestorCanvis->addCanvi($shortClassName, 'INSERT', $aDadesLast['id_activ'], $this->aDades, array());
            }
        }
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
        if (isset($this->iid_item)) {
            // necesario mirar el esquema en el caso de consultar las vistas de union para regiones stgr
            $cond_schema = !empty($this->iid_schema) ? " AND id_schema=$this->iid_schema" : '';
            if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_item=$this->iid_item $cond_schema")) === false) {
                $sClauError = 'ActividadCargo.carregar';
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
                    $this->aDadesActuals = $aDades;
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
        } elseif (!empty($this->aPrimary_key)) {
            if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla
                    WHERE id_activ=$this->iid_activ AND id_cargo=$this->iid_cargo")) === FALSE) {
                $sClauError = 'ActividadCargo.carregar';
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
                    $this->aDadesActuals = $aDades;
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
        $this->DBCarregar();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        // que tenga el módulo de 'cambios'
        if (ConfigGlobal::is_app_installed('cambios')) {
            // per carregar les dades a $this->aDadesActuals i poder posar-les als canvis.
            $this->DBCarregar('guardar');
            // ho poso abans d'esborrar perque sino no trova cap valor. En el cas d'error s'hauria d'esborrar l'apunt.
            $oGestorCanvis = new GestorAvisoCambios();
            $shortClassName = (new ReflectionClass($this))->getShortName();
            $oGestorCanvis->addCanvi($shortClassName, 'DELETE', $this->iid_activ, [], $this->aDadesActuals);
        }
        if (($oDbl->exec("DELETE FROM $nom_tabla WHERE id_item=$this->iid_item")) === false) {
            $sClauError = 'ActividadCargo.eliminar';
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
        if (array_key_exists('id_item', $aDades)) $this->setId_item($aDades['id_item']);
        if (array_key_exists('id_activ', $aDades)) $this->setId_activ($aDades['id_activ']);
        if (array_key_exists('id_cargo', $aDades)) $this->setId_cargo($aDades['id_cargo']);
        if (array_key_exists('id_nom', $aDades)) $this->setId_nom($aDades['id_nom']);
        if (array_key_exists('puede_agd', $aDades)) $this->setPuede_agd($aDades['puede_agd']);
        if (array_key_exists('observ', $aDades)) $this->setObserv($aDades['observ']);
    }

    /**
     * Establece a 'empty' el valor de todos los atributos
     *
     */
    function setNullAllAtributes()
    {
        $aPK = $this->getPrimary_key();
        $this->setId_schema('');
        $this->setId_item('');
        $this->setId_activ('');
        $this->setId_cargo('');
        $this->setId_nom('');
        $this->setPuede_agd('');
        $this->setObserv('');
        $this->setPrimary_key($aPK);
    }

    /**
     * retorna el valor de tots els atributs
     *
     * @param array $aDades
     */
    function getAllAtributes()
    {
        $aDades = [];
        $aDades['id_schema'] = $this->iid_schema;
        $aDades['id_item'] = $this->iid_item;
        $aDades['id_activ'] = $this->iid_activ;
        $aDades['id_cargo'] = $this->iid_cargo;
        $aDades['id_nom'] = $this->iid_nom;
        $aDades['puede_agd'] = $this->bpuede_agd;
        $aDades['observ'] = $this->sobserv;

        return $aDades;
    }
    /* MÉTODOS GET y SET --------------------------------------------------------*/

    /**
     * Recupera todos los atributos de ActividadCargo en un array
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
     * Recupera la clave primaria de ActividadCargo en un array
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
     * Establece la clave primaria de ActividadCargo en un array
     *
     * @return array aPrimary_key
     */
    public function setPrimary_key($a_id = '')
    {
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                if (($nom_id === 'id_item') && $val_id !== '') $this->iid_item = (int)$val_id;
            }
        }
    }

    /**
     * Recupera el atributo iid_item de ActividadCargo
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
     * Establece el valor del atributo iid_item de ActividadCargo
     *
     * @param integer iid_item
     */
    function setId_item($iid_item)
    {
        $this->iid_item = $iid_item;
    }

    /**
     * Recupera el atributo iid_activ de ActividadCargo
     *
     * @return integer iid_activ
     */
    function getId_activ()
    {
        if (!isset($this->iid_activ) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->iid_activ;
    }

    /**
     * Establece el valor del atributo iid_activ de ActividadCargo
     *
     * @param integer iid_activ
     */
    function setId_activ($iid_activ)
    {
        $this->iid_activ = $iid_activ;
    }

    /**
     * Recupera el atributo iid_cargo de ActividadCargo
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
     * Establece el valor del atributo iid_cargo de ActividadCargo
     *
     * @param integer iid_cargo
     */
    function setId_cargo($iid_cargo)
    {
        // si es sacd, cambio a la clase ActividadCargoSacd
        if ($iid_cargo == 35) {

        }
        $this->iid_cargo = $iid_cargo;
    }

    /**
     * Recupera el atributo iid_nom de ActividadCargo
     *
     * @return integer iid_nom
     */
    function getId_nom()
    {
        if (!isset($this->iid_nom) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->iid_nom;
    }

    /**
     * Establece el valor del atributo iid_nom de ActividadCargo
     *
     * @param integer iid_nom='' optional
     */
    function setId_nom($iid_nom = '')
    {
        $this->iid_nom = $iid_nom;
    }

    /**
     * Recupera el atributo bpuede_agd de ActividadCargo
     *
     * @return boolean bpuede_agd
     */
    function getPuede_agd()
    {
        if (!isset($this->bpuede_agd) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->bpuede_agd;
    }

    /**
     * Establece el valor del atributo bpuede_agd de ActividadCargo
     *
     * @param boolean bpuede_agd='f' optional
     */
    function setPuede_agd($bpuede_agd = 'f')
    {
        $this->bpuede_agd = $bpuede_agd;
    }

    /**
     * Recupera el atributo sobserv de ActividadCargo
     *
     * @return string sobserv
     */
    function getObserv()
    {
        if (!isset($this->sobserv) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->sobserv;
    }

    /**
     * Establece el valor del atributo sobserv de ActividadCargo
     *
     * @param string sobserv='' optional
     */
    function setObserv($sobserv = '')
    {
        $this->sobserv = $sobserv;
    }
    /* MÉTODOS GET y SET D'ATRIBUTOS QUE NO SON CAMPOS -----------------------------*/

    /**
     * Retorna una col·lecció d'objectes del tipus DatosCampo
     *
     */
    function getDatosCampos()
    {
        $oActividadCargoSet = new Set();

        $oActividadCargoSet->add($this->getDatosId_schema());
        $oActividadCargoSet->add($this->getDatosId_nom());
        $oActividadCargoSet->add($this->getDatosPuede_agd());
        $oActividadCargoSet->add($this->getDatosObserv());
        return $oActividadCargoSet->getTot();
    }


    /**
     * Recupera les propietats de l'atribut iid_schema de ActividadCargo
     * en una clase del tipus DatosCampo
     *
     * @return object DatosCampo
     */
    function getDatosId_schema()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'id_schema'));
        $oDatosCampo->setEtiqueta(_("id_schema"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut iid_nom de ActividadCargo
     * en una clase del tipus DatosCampo
     *
     * @return object DatosCampo
     */
    function getDatosId_nom()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'id_nom'));
        $oDatosCampo->setEtiqueta(_("id_nom"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut bpuede_agd de ActividadCargo
     * en una clase del tipus DatosCampo
     *
     * @return object DatosCampo
     */
    function getDatosPuede_agd()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'puede_agd'));
        $oDatosCampo->setEtiqueta(_("¿Puede ser agd?"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut sobserv de ActividadCargo
     * en una clase del tipus DatosCampo
     *
     * @return object DatosCampo
     */
    function getDatosObserv()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'observ'));
        $oDatosCampo->setEtiqueta(_("observaciones"));
        return $oDatosCampo;
    }
}