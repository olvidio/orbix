<?php
namespace asistentes\model\entity;

use actividades\model\entity\Actividad;
use cambios\model\GestorAvisoCambios;
use core\ConfigGlobal;
use core;
use personas\model\entity\Persona;

/**
 * Fitxer amb la Classe que accedeix a la taula d_asistentes_de_paso
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 11/03/2014
 */

/**
 * Clase que implementa la entidad d_asistentes_de_paso
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 11/03/2014
 */
class AsistentePub extends core\ClasePropiedades
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * aPrimary_key de AsistentePub
     *
     * @var array
     */
    protected $aPrimary_key;

    /**
     * aDades de AsistentePub
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
     * aDades de AsistentePub abans dels canvis.
     *
     * @var array
     */
    protected $aDadesActuals;

    /**
     * Id_activ de AsistentePub
     *
     * @var integer
     */
    protected $iid_activ;
    /**
     * Id_nom de AsistentePub
     *
     * @var integer
     */
    protected $iid_nom;
    /**
     * Propio de AsistentePub
     *
     * @var boolean
     */
    protected $bpropio;
    /**
     * Est_ok de AsistentePub
     *
     * @var boolean
     */
    protected $best_ok;
    /**
     * Cfi de AsistentePub
     *
     * @var boolean
     */
    protected $bcfi;
    /**
     * Cfi_con de AsistentePub
     *
     * @var integer
     */
    protected $icfi_con;
    /**
     * Falta de AsistentePub
     *
     * @var boolean
     */
    protected $bfalta;
    /**
     * Encargo de AsistentePub
     *
     * @var string
     */
    protected $sencargo;
    /**
     * dl_responsable de AsistentePub
     *
     * @var string
     */
    protected $sdl_responsable;
    /**
     * Observ de AsistentePub
     *
     * @var string
     */
    protected $sobserv;
    /**
     * Observ_est de AsistentePub
     *
     * @var string
     */
    protected $sobserv_est;
    /**
     * Plaza de AsistentePub
     *
     * @var integer
     */
    protected $iplaza;
    /**
     * Propietario de AsistentePub
     *
     * @var string
     */
    protected $spropietario;
    /**
     * id_tabla de AsistentePub
     *
     * @var string
     */
    protected $sid_tabla;
    /* ATRIBUTOS QUE NO SON CAMPOS------------------------------------------------- */


    /* Metodes -------------------------------------------------------------- */


    /**
     * Saber si puedo modificar.
     * - true para asistentes de mi dl, y para los de paso que he puesto yo
     * - false para asistentes de otra dl, y para los de paso que NO he puesto yo
     *
     * @return boolean
     */
    public function perm_modificar()
    {
        return $this->getDl_responsable() == ConfigGlobal::mi_delef();
    }

    public function buscarAsistencia($id_nom, $id_activ)
    {
        $gesAsistente = new GestorAsistente();
        $cAsistentes = $gesAsistente->getAsistentes(['id_nom' => $id_nom, 'id_activ' => $id_activ]);
        if (is_array($cAsistentes) && !empty($cAsistentes)) {
            return $cAsistentes[0];
        } else {
            return FALSE;
        }
    }

    /**
     * para saber el nombre de la clase que toca según mi dl, y la dl de la
     * actividad a la que asisto
     *
     * @param string $id_nom
     * @param string $dl dl que organiza la actividad
     * @param integer $id_tabla de la actividad
     * @param integer optional $id_activ de la actividad
     */
    public function getClaseAsistente($id_nom, $id_activ)
    {
        $msg_err = '';
        // Comprobar si ya existe la asistencia.
        if (($oAsistente = $this->buscarAsistencia($id_nom, $id_activ)) !== FALSE) {
            return $oAsistente;
        }
        // hay que averiguar si la persona es de la dl o de fuera.
        $oPersona = Persona::NewPersona($id_nom);
        if (!is_object($oPersona)) {
            $msg_err = "<br>$oPersona con id_nom: $id_nom en  " . __FILE__ . ": line " . __LINE__;
            exit($msg_err);
        }
        $obj_persona = get_class($oPersona);
        $obj_persona = str_replace("personas\\model\\entity\\", '', $obj_persona);
        // hay que averiguar si la actividad es de la dl o de fuera.
        $oActividad = new Actividad($id_activ);
        // si es de la sf quito la 'f'
        $dl = preg_replace('/f$/', '', $oActividad->getDl_org());

        if ($dl == core\ConfigGlobal::mi_delef()) {
            switch ($obj_persona) {
                case 'PersonaN':
                case 'PersonaNax':
                case 'PersonaAgd':
                case 'PersonaS':
                case 'PersonaSSSC':
                case 'PersonaDl':
                    $clase = 'asistentes\\model\\entity\\AsistenteDl';
                    break;
                case 'PersonaIn':
                    // Supongo que sólo debería modificar la dl origen.
                    //$clase = 'asistentes\\model\\entity\\AsistenteIn';
                    // $oAsistente=new asistentes\AsistenteIn(array('id_activ'=>$id_activ,'id_nom'=>$id_nom));
                    exit (_("los datos de asistencia los modifica la dl del asistente"));
                    break;
                case 'PersonaEx':
                    $clase = 'asistentes\\model\\entity\\AsistenteEx';
                    break;
            }
        } else {
            // Creo que en cualquier caso debe ser un asistente Out.
            // El Ex es solo para una persona Ex ?¿
            if ($obj_persona == 'PersonaEx') {
                $clase = 'asistentes\\model\\entity\\AsistenteEx';
            } else {
                $clase = 'asistentes\\model\\entity\\AsistenteOut';
            }
        }
        return new $clase;
    }

    /* CONSTRUCTOR -------------------------------------------------------------- */

    /**
     * Constructor de la classe.
     * Si només necessita un valor, se li pot passar un integer.
     * En general se li passa un array amb les claus primàries.
     *
     * @param integer|array iid_activ,iid_nom
     *                        $a_id. Un array con los nombres=>valores de las claves primarias.
     */
    function __construct($a_id = '')
    {
        $oDbl = $GLOBALS['oDBEP'];
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                if (($nom_id == 'id_activ') && $val_id !== '') $this->iid_activ = (int)$val_id; // evitem SQL injection fent cast a integer
                if (($nom_id == 'id_nom') && $val_id !== '') $this->iid_nom = (int)$val_id; // evitem SQL injection fent cast a integer
            }
        }
        $this->setoDbl($oDbl);
        $this->setNomTabla('d_asistentes_de_paso');
    }

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    /**
     * Desa els atributs de l'objecte a la base de dades.
     * Si no hi ha el registre, fa el insert, si hi es fa el update.
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
        $aDades = array();
        $aDades['propio'] = $this->bpropio;
        $aDades['est_ok'] = $this->best_ok;
        $aDades['cfi'] = $this->bcfi;
        $aDades['cfi_con'] = $this->icfi_con;
        $aDades['falta'] = $this->bfalta;
        $aDades['encargo'] = $this->sencargo;
        $aDades['dl_responsable'] = $this->sdl_responsable;
        $aDades['observ'] = $this->sobserv;
        $aDades['observ_est'] = $this->sobserv_est;
        $aDades['plaza'] = $this->iplaza;
        $aDades['propietario'] = $this->spropietario;
        //$aDades['id_tabla'] = $this->sid_tabla;
        array_walk($aDades, 'core\poner_null');
        //para el caso de los boolean FALSE, el pdo(+postgresql) pone string '' en vez de 0. Lo arreglo:
        if (core\is_true($aDades['propio'])) {
            $aDades['propio'] = 'true';
        } else {
            $aDades['propio'] = 'false';
        }
        if (core\is_true($aDades['est_ok'])) {
            $aDades['est_ok'] = 'true';
        } else {
            $aDades['est_ok'] = 'false';
        }
        if (core\is_true($aDades['cfi'])) {
            $aDades['cfi'] = 'true';
        } else {
            $aDades['cfi'] = 'false';
        }
        if (core\is_true($aDades['falta'])) {
            $aDades['falta'] = 'true';
        } else {
            $aDades['falta'] = 'false';
        }

        if ($bInsert === false) {
            //UPDATE
            $update = "
					propio                   = :propio,
					est_ok                   = :est_ok,
					cfi                      = :cfi,
					cfi_con                  = :cfi_con,
					falta                    = :falta,
					encargo                  = :encargo,
					dl_responsable                     = :dl_responsable,
					observ                   = :observ,
					observ_est               = :observ_est,
					plaza                    = :plaza,
					propietario              = :propietario";
            //id_tabla                 = :id_tabla";
            if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_activ='$this->iid_activ' AND id_nom=$this->iid_nom")) === false) {
                $sClauError = get_class($this) . '.update.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = get_class($this) . '.update.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return false;
                }
            }
            // Aunque no tenga el módulo de 'cambios', quizá otra dl si lo tenga.
            // Anoto el cambio si o si.
            if (empty($quiet)) {
                $oGestorCanvis = new GestorAvisoCambios();
                $shortClassName = (new \ReflectionClass($this))->getShortName();
                $oGestorCanvis->addCanvi($shortClassName, 'UPDATE', $this->iid_activ, $aDades, $this->aDadesActuals);
            }
            $this->setAllAtributes($aDades);
        } else {
            // INSERT
            array_unshift($aDades, $this->iid_activ, $this->iid_nom);
            $campos = "(id_activ,id_nom,propio,est_ok,cfi,cfi_con,falta,encargo,dl_responsable,observ,observ_est,plaza,propietario)";
            $valores = "(:id_activ,:id_nom,:propio,:est_ok,:cfi,:cfi_con,:falta,:encargo,:dl_responsable,:observ,:observ_est,:plaza,:propietario)";
            if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === false) {
                $sClauError = get_class($this) . '.insertar.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = get_class($this) . '.insertar.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return false;
                }
            }
            if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_activ='$this->iid_activ' AND id_nom=$this->iid_nom")) === false) {
                $sClauError = get_class($this) . '.carregar.Last';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            }
            $aDadesLast = $oDblSt->fetch(\PDO::FETCH_ASSOC);
            $this->aDades = $aDadesLast;
            $this->setAllAtributes($aDadesLast);
            // anotar cambio.
            // Aunque no tenga el módulo de 'cambios', quizá otra dl si lo tenga.
            // Anoto el cambio si o si.
            if (empty($quiet)) {
                $oGestorCanvis = new GestorAvisoCambios();
                $shortClassName = (new \ReflectionClass($this))->getShortName();
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
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        if (isset($this->iid_activ) && isset($this->iid_nom)) {
            if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_activ='$this->iid_activ' AND id_nom=$this->iid_nom")) === false) {
                $sClauError = get_class($this) . '.carregar';
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
        // que tenga el módulo de 'cambios'
        if (core\ConfigGlobal::is_app_installed('cambios')) {
            // per carregar les dades a $this->aDadesActuals i poder posar-les als canvis.
            $this->DBCarregar('guardar');
            // ho poso abans d'esborrar perque sino no trova cap valor. En el cas d'error s'hauria d'esborrar l'apunt.
            $oGestorCanvis = new GestorAvisoCambios();
            $shortClassName = (new \ReflectionClass($this))->getShortName();
            $oGestorCanvis->addCanvi($shortClassName, 'DELETE', $this->iid_activ, array(), $this->aDadesActuals);
        }
        if (($oDbl->exec("DELETE FROM $nom_tabla WHERE id_activ='$this->iid_activ' AND id_nom=$this->iid_nom")) === false) {
            $sClauError = get_class($this) . '.eliminar';
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
        if (array_key_exists('id_activ', $aDades)) $this->setId_activ($aDades['id_activ']);
        if (array_key_exists('id_nom', $aDades)) $this->setId_nom($aDades['id_nom']);
        if (array_key_exists('propio', $aDades)) $this->setPropio($aDades['propio']);
        if (array_key_exists('est_ok', $aDades)) $this->setEst_ok($aDades['est_ok']);
        if (array_key_exists('cfi', $aDades)) $this->setCfi($aDades['cfi']);
        if (array_key_exists('cfi_con', $aDades)) $this->setCfi_con($aDades['cfi_con']);
        if (array_key_exists('falta', $aDades)) $this->setFalta($aDades['falta']);
        if (array_key_exists('encargo', $aDades)) $this->setEncargo($aDades['encargo']);
        if (array_key_exists('dl_responsable', $aDades)) $this->setDl_responsable($aDades['dl_responsable']);
        if (array_key_exists('observ', $aDades)) $this->setObserv($aDades['observ']);
        if (array_key_exists('observ_est', $aDades)) $this->setObserv_est($aDades['observ_est']);
        if (array_key_exists('plaza', $aDades)) $this->setPlazaSinComprobar($aDades['plaza']);
        if (array_key_exists('propietario', $aDades)) $this->setPropietario($aDades['propietario']);
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
        $this->setId_activ('');
        $this->setId_nom('');
        $this->setPropio('');
        $this->setEst_ok('');
        $this->setCfi('');
        $this->setCfi_con('');
        $this->setFalta('');
        $this->setEncargo('');
        $this->setDl_responsable('');
        $this->setObserv('');
        $this->setObserv_est('');
        $this->setPlazaSinComprobar('');
        $this->setPropietario('');
        $this->setId_tabla('');
        $this->setPrimary_key($aPK);
    }

    /**
     * retorna el valor de tots els atributs
     *
     * @param array $aDades
     */
    function getAllAtributes()
    {
        $aDades = array();
        $aDades['id_activ'] = $this->iid_activ;
        $aDades['id_nom'] = $this->iid_nom;
        $aDades['propio'] = $this->bpropio;
        $aDades['est_ok'] = $this->best_ok;
        $aDades['cfi'] = $this->bcfi;
        $aDades['cfi_con'] = $this->icfi_con;
        $aDades['falta'] = $this->bfalta;
        $aDades['encargo'] = $this->sencargo;
        $aDades['dl_responsable'] = $this->sdl_responsable;
        $aDades['observ'] = $this->sobserv;
        $aDades['observ_est'] = $this->sobserv_est;
        $aDades['plaza'] = $this->iplaza;
        $aDades['propietario'] = $this->spropietario;
        $aDades['id_tabla'] = $this->sid_tabla;

        return $aDades;
    }

    /* MÉTODOS GET y SET --------------------------------------------------------*/
    /**
     * Recupera todos los atributos de AsistentePub en un array
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
     * Recupera la clave primaria de AsistentePub en un array
     *
     * @return array aPrimary_key
     */
    function getPrimary_key()
    {
        if (!isset($this->aPrimary_key)) {
            $this->aPrimary_key = array('id_activ' => $this->iid_activ, 'id_nom' => $this->iid_nom);
        }
        return $this->aPrimary_key;
    }

    /**
     * Establece la clave primaria de AsistentePub en un array
     *
     * @return array aPrimary_key
     */
    public function setPrimary_key($a_id = '')
    {
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                if (($nom_id == 'id_activ') && $val_id !== '') $this->iid_activ = (int)$val_id; // evitem SQL injection fent cast a integer
                if (($nom_id == 'id_nom') && $val_id !== '') $this->iid_nom = (int)$val_id; // evitem SQL injection fent cast a integer
            }
        }
    }

    /**
     * Recupera el atributo iid_activ de AsistentePub
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
     * Establece el valor del atributo iid_activ de AsistentePub
     *
     * @param integer iid_activ
     */
    function setId_activ($iid_activ)
    {
        $this->iid_activ = $iid_activ;
    }

    /**
     * Recupera el atributo iid_nom de AsistentePub
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
     * Establece el valor del atributo iid_nom de AsistentePub
     *
     * @param integer iid_nom
     */
    function setId_nom($iid_nom)
    {
        $this->iid_nom = $iid_nom;
    }

    /**
     * Recupera el atributo bpropio de AsistentePub
     *
     * @return boolean bpropio
     */
    function getPropio()
    {
        if (!isset($this->bpropio) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->bpropio;
    }

    /**
     * Establece el valor del atributo bpropio de AsistentePub
     *
     * @param boolean bpropio='f' optional
     */
    function setPropio($bpropio = 'f')
    {
        $this->bpropio = $bpropio;
    }

    /**
     * Recupera el atributo best_ok de AsistentePub
     *
     * @return boolean best_ok
     */
    function getEst_ok()
    {
        if (!isset($this->best_ok) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->best_ok;
    }

    /**
     * Establece el valor del atributo best_ok de AsistentePub
     *
     * @param boolean best_ok='f' optional
     */
    function setEst_ok($best_ok = 'f')
    {
        $this->best_ok = $best_ok;
    }

    /**
     * Recupera el atributo bcfi de AsistentePub
     *
     * @return boolean bcfi
     */
    function getCfi()
    {
        if (!isset($this->bcfi) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->bcfi;
    }

    /**
     * Establece el valor del atributo bcfi de AsistentePub
     *
     * @param boolean bcfi='f' optional
     */
    function setCfi($bcfi = 'f')
    {
        $this->bcfi = $bcfi;
    }

    /**
     * Recupera el atributo icfi_con de AsistentePub
     *
     * @return integer icfi_con
     */
    function getCfi_con()
    {
        if (!isset($this->icfi_con) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->icfi_con;
    }

    /**
     * Establece el valor del atributo icfi_con de AsistentePub
     *
     * @param integer icfi_con='' optional
     */
    function setCfi_con($icfi_con = '')
    {
        $this->icfi_con = $icfi_con;
    }

    /**
     * Recupera el atributo bfalta de AsistentePub
     *
     * @return boolean bfalta
     */
    function getFalta()
    {
        if (!isset($this->bfalta) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->bfalta;
    }

    /**
     * Establece el valor del atributo bfalta de AsistentePub
     *
     * @param boolean bfalta='f' optional
     */
    function setFalta($bfalta = 'f')
    {
        $this->bfalta = $bfalta;
    }

    /**
     * Recupera el atributo sencargo de AsistentePub
     *
     * @return string sencargo
     */
    function getEncargo()
    {
        if (!isset($this->sencargo) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->sencargo;
    }

    /**
     * Establece el valor del atributo sencargo de AsistentePub
     *
     * @param string sencargo='' optional
     */
    function setEncargo($sencargo = '')
    {
        $this->sencargo = $sencargo;
    }

    /**
     * Recupera el atributo sdl_responsable de AsistentePub
     *
     * @return string sdl_responsable
     */
    function getDl_responsable()
    {
        if (!isset($this->sdl_responsable) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->sdl_responsable;
    }

    /**
     * Establece el valor del atributo sdl_responsable de AsistentePub
     *
     * @param string sdl_responsable='' optional
     */
    function setDl_responsable($sdl_responsable = '')
    {
        $this->sdl_responsable = $sdl_responsable;
    }

    /**
     * Recupera el atributo sobserv de AsistentePub
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
     * Establece el valor del atributo sobserv de AsistentePub
     *
     * @param string sobserv='' optional
     */
    function setObserv($sobserv = '')
    {
        $this->sobserv = $sobserv;
    }

    /**
     * Recupera el atributo sobserv_est de AsistentePub
     *
     * @return string sobserv_est
     */
    function getObserv_est()
    {
        if (!isset($this->sobserv_est) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->sobserv_est;
    }

    /**
     * Establece el valor del atributo sobserv_est de AsistentePub
     *
     * @param string sobserv_est='' optional
     */
    function setObserv_est($sobserv_est = '')
    {
        $this->sobserv_est = $sobserv_est;
    }

    /**
     * Recupera el atributo iplaza de AsistentePub
     *
     * @return integer iplaza
     */
    function getPlaza()
    {
        if (!isset($this->iplaza) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->iplaza;
    }

    /**
     * Establece el valor del atributo iplaza de AsistentePub
     * La distingo del setPlaza, porque al requerir getPlaza -> DBcarregar entra en un bucle.
     *
     * @param integer iplaza='' optional
     */
    protected function setPlazaSinComprobar($iplaza = '')
    {
        $this->iplaza = $iplaza;
    }

    /**
     * Establece el valor del atributo iplaza de AsistentePub
     *
     * @param integer iplaza='' optional
     */
    function setPlaza($iplaza = '')
    {
        // tipos de actividad para los que no hay que comprobar la plaza
        // 132500 => agd ca sem invierno
        //$aId_tipo_activ_no = [132500,00000];
        //$oActividad = new \actividades\model\entity\Actividad($this->iid_activ);
        //$id_tipo_activ = $oActividad->getId_tipo_activ();
        //if (in_array($id_tipo_activ, $aId_tipo_activ_no)) {
        //	return $this->setPlazaSinComprobar($iplaza);
        //}

        //hacer comprobaciones de plazas disponibles...
        $plaza_actual = $this->getPlaza();

        if ($plaza_actual < Asistente::PLAZA_DENEGADA && $iplaza > Asistente::PLAZA_DENEGADA) {
            $gesActividadPlazasR = new \actividadplazas\model\GestorResumenPlazas();
            $gesActividadPlazasR->setId_activ($this->iid_activ);
            if ($gesActividadPlazasR->getLibres() > 0) {
                $this->iplaza = $iplaza;
                //debe asignarse un propietario. Sólo si es asignada o confirmada
                $rta = $gesActividadPlazasR->getPropiedadPlazaLibre();
                if ($rta['success']) {
                    $propiedad = $rta['propiedad'];
                    if (empty($propiedad)) {
                        exit (_("no debería pasar. No puede haber una plaza libre sin propietario"));
                    } else {
                        $prop = key($propiedad);
                        $this->setPropietario($prop);
                    }
                } else {
                    $err_txt = $rta['mensaje'];
                    exit ($err_txt);
                }
            } else {
                $this->iplaza = Asistente::PLAZA_PEDIDA;
            }
        } else {
            $this->iplaza = $iplaza;
        }
    }

    /**
     * Recupera el atributo spropietario de AsistentePub
     *
     * @return integer spropietario
     */
    function getPropietario()
    {
        if (!isset($this->spropietario) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->spropietario;
    }

    /**
     * Establece el valor del atributo spropietario de AsistentePub
     *
     * @param integer spropietario='' optional
     */
    function setPropietario($spropietario = '')
    {
        $this->spropietario = $spropietario;
    }

    /**
     * Recupera el atributo sid_tabla de AsistentePub
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
     * Establece el valor del atributo sid_tabla de AsistentePub
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
        $oAsistentePubSet = new core\Set();

        //$oAsistentePubSet->add($this->getDatosId_activ());
        $oAsistentePubSet->add($this->getDatosId_nom());
        $oAsistentePubSet->add($this->getDatosPropio());
        $oAsistentePubSet->add($this->getDatosEst_ok());
        $oAsistentePubSet->add($this->getDatosCfi());
        $oAsistentePubSet->add($this->getDatosCfi_con());
        $oAsistentePubSet->add($this->getDatosFalta());
        $oAsistentePubSet->add($this->getDatosEncargo());
        $oAsistentePubSet->add($this->getDatosdl_responsable());
        $oAsistentePubSet->add($this->getDatosObserv());
        $oAsistentePubSet->add($this->getDatosObserv_est());
        return $oAsistentePubSet->getTot();
    }


    /**
     * Recupera les propietats de l'atribut iid_activ de AsistentePub
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosId_activ()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'id_activ'));
        $oDatosCampo->setEtiqueta(_("id actividad"));
        return $oDatosCampo;
    }


    /**
     * Recupera les propietats de l'atribut iid_nom de AsistentePub
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosId_nom()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'id_nom'));
        $oDatosCampo->setEtiqueta(_("id nombre"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut bpropio de AsistentePub
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosPropio()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'propio'));
        $oDatosCampo->setEtiqueta(_("propio"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut best_ok de AsistentePub
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosEst_ok()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'est_ok'));
        $oDatosCampo->setEtiqueta(_("est_ok"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut bcfi de AsistentePub
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosCfi()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'cfi'));
        $oDatosCampo->setEtiqueta(_("cfi"));
        $oDatosCampo->setAviso(FALSE);
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut icfi_con de AsistentePub
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosCfi_con()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'cfi_con'));
        $oDatosCampo->setEtiqueta(_("cfi con"));
        $oDatosCampo->setAviso(FALSE);
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut bfalta de AsistentePub
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosFalta()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'falta'));
        $oDatosCampo->setEtiqueta(_("falta"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut sencargo de AsistentePub
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosEncargo()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'encargo'));
        $oDatosCampo->setEtiqueta(_("encargo"));
        $oDatosCampo->setAviso(FALSE);
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut sdl_responsable de AsistentePub
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosdl_responsable()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'dl_responsable'));
        $oDatosCampo->setEtiqueta(_("dl_responsable"));
        $oDatosCampo->setAviso(FALSE);
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut sobserv de AsistentePub
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosObserv()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'observ'));
        $oDatosCampo->setEtiqueta(_("observaciones"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut sobserv_est de AsistentePub
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosObserv_est()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'observ_est'));
        $oDatosCampo->setEtiqueta(_("observaciones estudios"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut iplaza de AsistentePub
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosPlaza()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'plaza'));
        $oDatosCampo->setEtiqueta(_("plaza"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut spropietario de AsistentePub
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosPropietario()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'propietario'));
        $oDatosCampo->setEtiqueta(_("propietario"));
        return $oDatosCampo;
    }
}

?>
