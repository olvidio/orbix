<?php
namespace ubis\model\entity;

use core\ConfigGlobal;
use core\DatosCampo;
use core\Set;
use function core\is_true;

/**
 * Clase que implementa la entidad u_centros_dl
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 27/09/2010
 */
class CentroDl extends Centro
{
    /* ATRIBUTOS ----------------------------------------------------------------- */
    /**
     * Id_auto de CentroDl
     *
     * @var integer
     */
    protected $iid_auto;
    /**
     * N_buzon de CentroDl
     *
     * @var integer
     */
    protected $in_buzon;
    /**
     * Num_pi de CentroDl
     *
     * @var integer
     */
    protected $inum_pi;
    /**
     * Num_cartas de CentroDl
     *
     * @var integer
     */
    protected $inum_cartas;
    /**
     * Observ de CentroDl
     *
     * @var string
     */
    protected $sobserv;
    /**
     * Num_habit_indiv de CentroDl
     *
     * @var integer
     */
    protected $inum_habit_indiv;
    /**
     * Plazas de CentroDl
     *
     * @var integer
     */
    protected $iplazas;
    /**
     * Id_zona de CentroDl
     *
     * @var integer
     */
    protected $iid_zona;
    /**
     * sede de CentroDl
     *
     * @var boolean
     */
    protected $bsede;
    /**
     * Num_cartas_mensuales de CentroDl
     *
     * @var integer
     */
    protected $inum_cartas_mensuales;
    /* ATRIBUTOS QUE NO SON CAMPOS------------------------------------------------- */
    /* CONSTRUCTOR -------------------------------------------------------------- */

    /**
     * Constructor de la classe.
     * Si només necessita un valor, se li pot passar un integer.
     * En general se li passa un array amb les claus primàries.
     *
     * @param integer|array $iid_ubi
     *                        $a_id. Un array con los nombres=>valores de las claves primarias.
     */
    function __construct($a_id = '')
    {
        if (ConfigGlobal::is_dmz()) {
            $oDbl = $GLOBALS['oDBC'];
            $oDbl_Select = $GLOBALS['oDBC_Select'];
            $this->setoDbl($oDbl);
            $this->setoDbl_Select($oDbl);
            $this->setNomTabla('cu_centros_dl');
        } else {
            $oDbl = $GLOBALS['oDB'];
            $this->setoDbl($oDbl);
            $this->setoDbl_Select($oDbl);
            $this->setNomTabla('u_centros_dl');
        }
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                $nom_id = 'i' . $nom_id; //imagino que es un integer
                if ($val_id !== '') $this->$nom_id = (integer)$val_id;
            }
        } else {
            if (isset($a_id) && $a_id !== '') {
                $this->iid_ubi = (integer)$a_id;
                $this->aPrimary_key = array('id_ubi' => $this->iid_ubi);
            }
        }
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
        $aDades['tipo_ubi'] = $this->stipo_ubi;
        $aDades['nombre_ubi'] = $this->snombre_ubi;
        $aDades['dl'] = $this->sdl;
        $aDades['pais'] = $this->spais;
        $aDades['region'] = $this->sregion;
        $aDades['status'] = $this->bstatus;
        $aDades['f_status'] = $this->df_status;
        $aDades['sv'] = $this->bsv;
        $aDades['sf'] = $this->bsf;
        $aDades['tipo_ctr'] = $this->stipo_ctr;
        $aDades['tipo_labor'] = $this->itipo_labor;
        $aDades['cdc'] = $this->bcdc;
        $aDades['id_ctr_padre'] = $this->iid_ctr_padre;
        $aDades['n_buzon'] = $this->in_buzon;
        $aDades['num_pi'] = $this->inum_pi;
        $aDades['num_cartas'] = $this->inum_cartas;
        $aDades['observ'] = $this->sobserv;
        $aDades['num_habit_indiv'] = $this->inum_habit_indiv;
        $aDades['plazas'] = $this->iplazas;
        $aDades['id_zona'] = $this->iid_zona;
        $aDades['sede'] = $this->bsede;
        $aDades['num_cartas_mensuales'] = $this->inum_cartas_mensuales;
        array_walk($aDades, 'core\poner_null');
        //para el caso de los boolean FALSE, el pdo(+postgresql) pone string '' en vez de 0. Lo arreglo:
        if (is_true($aDades['status'])) {
            $aDades['status'] = 'true';
        } else {
            $aDades['status'] = 'false';
        }
        if (is_true($aDades['sv'])) {
            $aDades['sv'] = 'true';
        } else {
            $aDades['sv'] = 'false';
        }
        if (is_true($aDades['sf'])) {
            $aDades['sf'] = 'true';
        } else {
            $aDades['sf'] = 'false';
        }
        if (is_true($aDades['cdc'])) {
            $aDades['cdc'] = 'true';
        } else {
            $aDades['cdc'] = 'false';
        }
        if (is_true($aDades['sede'])) {
            $aDades['sede'] = 'true';
        } else {
            $aDades['sede'] = 'false';
        }

        if ($bInsert === false) {
            //UPDATE
            $update = "
					tipo_ubi                 = :tipo_ubi,
					nombre_ubi               = :nombre_ubi,
					dl                       = :dl,
					pais                     = :pais,
					region                   = :region,
					status                   = :status,
					f_status                 = :f_status,
					sv                       = :sv,
					sf                       = :sf,
					tipo_ctr                 = :tipo_ctr,
					tipo_labor               = :tipo_labor,
					cdc                      = :cdc,
					id_ctr_padre             = :id_ctr_padre,
					n_buzon                  = :n_buzon,
					num_pi                   = :num_pi,
					num_cartas               = :num_cartas,
					observ                   = :observ,
					num_habit_indiv          = :num_habit_indiv,
					plazas                   = :plazas,
					id_zona                  = :id_zona,
					sede                     = :sede,
					num_cartas_mensuales     = :num_cartas_mensuales";
            //print_r($aDades);
            if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_ubi='$this->iid_ubi'")) === false) {
                $sClauError = 'CentroDl.update.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'CentroDl.update.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return false;
                }
            }
        } else {
            // INSERT
            $campos = "(tipo_ubi,nombre_ubi,dl,pais,region,status,f_status,sv,sf,tipo_ctr,tipo_labor,cdc,id_ctr_padre,n_buzon,num_pi,num_cartas,observ,num_habit_indiv,plazas,id_zona,sede,num_cartas_mensuales)";
            $valores = "(:tipo_ubi,:nombre_ubi,:dl,:pais,:region,:status,:f_status,:sv,:sf,:tipo_ctr,:tipo_labor,:cdc,:id_ctr_padre,:n_buzon,:num_pi,:num_cartas,:observ,:num_habit_indiv,:plazas,:id_zona,:sede,:num_cartas_mensuales)";
            if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === false) {
                $sClauError = 'CentroDl.insertar.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'CentroDl.insertar.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return false;
                }
            }
            $aDades['id_auto'] = $oDbl->lastInsertId('u_centros_dl_id_auto_seq');
            $aDades['id_ubi'] = $oDbl->query("SELECT id_ubi FROM $nom_tabla WHERE id_auto =" . $aDades['id_auto'])->fetchColumn();
        }
        $this->setAllAtributes($aDades);

        // Modifico la ficha en la BD-comun
        // Solo los de mi dl
        if ($this->sdl == ConfigGlobal::mi_delef()) {
            $this->copia2Comun($aDades);
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
        if (isset($this->iid_ubi)) {
            if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_ubi='$this->iid_ubi'")) === false) {
                $sClauError = 'CentroDl.carregar';
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
        if (($oDbl->exec("DELETE FROM $nom_tabla WHERE id_ubi='$this->iid_ubi'")) === false) {
            $sClauError = 'CentroDl.eliminar';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        // Modifico la ficha en la BD-comun
        $this->borraDeComun($this->iid_ubi);
        return true;
    }

    /* OTROS MÉTODOS  ----------------------------------------------------------*/
    /**
     *
     * Elimina el registre de la base de dades comun
     *
     */
    protected function borraDeComun($iid_ubi)
    {
        // para la sf (comienza por 2).
        if (substr($this->iid_ubi, 0, 1) == 2) {
            $oCentroEllas = new CentroEllas($iid_ubi);
            $oCentroEllas->DBEliminar();
        } else {
            $oCentroEllos = new CentroEllos($iid_ubi);
            $oCentroEllos->DBEliminar();
        }
    }

    protected function copia2Comun($aDades)
    {
        unset($aDades['id_auto']);
        unset($aDades['num_cartas']);
        unset($aDades['n_buzon']);
        unset($aDades['num_pi']);
        unset($aDades['num_cartas']);
        unset($aDades['observ']);
        unset($aDades['num_habit_indiv']);
        unset($aDades['plazas']);
        unset($aDades['sede']);
        unset($aDades['num_cartas_mensuales']);

        // para la sf (comienza por 2).
        if (substr($this->iid_ubi, 0, 1) == 2) {
            $oCentroEllas = new CentroEllas($this->iid_ubi);
            $oCentroEllas->setAllAtributes($aDades);
            $oCentroEllas->DBGuardar();
        } else {
            $oCentroEllos = new CentroEllos($this->iid_ubi);
            $oCentroEllos->setAllAtributes($aDades);
            $oCentroEllos->DBGuardar();
        }

    }
    /* MÉTODOS PRIVADOS ----------------------------------------------------------*/

    /**
     * Establece el valor de todos los atributos
     *
     * @param array $aDades
     */
    function setAllAtributes(array $aDades, $convert = FALSE)
    {
        //print_r($aDades);
        if (!is_array($aDades)) return;
        if (array_key_exists('id_schema', $aDades)) $this->setId_schema($aDades['id_schema']);
        if (array_key_exists('tipo_ubi', $aDades)) $this->setTipo_ubi($aDades['tipo_ubi']);
        if (array_key_exists('id_ubi', $aDades)) $this->setId_ubi($aDades['id_ubi']);
        if (array_key_exists('nombre_ubi', $aDades)) $this->setNombre_ubi($aDades['nombre_ubi']);
        if (array_key_exists('dl', $aDades)) $this->setDl($aDades['dl']);
        if (array_key_exists('pais', $aDades)) $this->setPais($aDades['pais']);
        if (array_key_exists('region', $aDades)) $this->setRegion($aDades['region']);
        if (array_key_exists('status', $aDades)) $this->setStatus($aDades['status']);
        if (array_key_exists('f_status', $aDades)) $this->setF_status($aDades['f_status'], $convert);
        if (array_key_exists('sv', $aDades)) $this->setSv($aDades['sv']);
        if (array_key_exists('sf', $aDades)) $this->setSf($aDades['sf']);
        if (array_key_exists('tipo_ctr', $aDades)) $this->setTipo_ctr($aDades['tipo_ctr']);
        if (array_key_exists('tipo_labor', $aDades)) $this->setTipo_labor($aDades['tipo_labor']);
        if (array_key_exists('cdc', $aDades)) $this->setCdc($aDades['cdc']);
        if (array_key_exists('id_ctr_padre', $aDades)) $this->setId_ctr_padre($aDades['id_ctr_padre']);
        if (array_key_exists('n_buzon', $aDades)) $this->setN_buzon($aDades['n_buzon']);
        if (array_key_exists('num_pi', $aDades)) $this->setNum_pi($aDades['num_pi']);
        if (array_key_exists('num_cartas', $aDades)) $this->setNum_cartas($aDades['num_cartas']);
        if (array_key_exists('observ', $aDades)) $this->setObserv($aDades['observ']);
        if (array_key_exists('num_habit_indiv', $aDades)) $this->setNum_habit_indiv($aDades['num_habit_indiv']);
        if (array_key_exists('plazas', $aDades)) $this->setPlazas($aDades['plazas']);
        if (array_key_exists('id_zona', $aDades)) $this->setId_zona($aDades['id_zona']);
        if (array_key_exists('sede', $aDades)) $this->setSede($aDades['sede']);
        if (array_key_exists('num_cartas_mensuales', $aDades)) $this->setNum_cartas_mensuales($aDades['num_cartas_mensuales']);
    }

    /**
     * Establece a 'empty' el valor de todos los atributos
     *
     */
    function setNullAllAtributes()
    {
        $aPK = $this->getPrimary_key();
        $this->setId_schema('');
        $this->setTipo_ubi('');
        $this->setId_ubi('');
        $this->setNombre_ubi('');
        $this->setDl('');
        $this->setPais('');
        $this->setRegion('');
        $this->setStatus('');
        $this->setF_status('');
        $this->setSv('');
        $this->setSf('');
        $this->setTipo_ctr('');
        $this->setTipo_labor('');
        $this->setCdc('');
        $this->setId_ctr_padre('');
        $this->setN_buzon('');
        $this->setNum_pi('');
        $this->setNum_cartas('');
        $this->setObserv('');
        $this->setNum_habit_indiv('');
        $this->setPlazas('');
        $this->setId_zona('');
        $this->setSede('');
        $this->setNum_cartas_mensuales('');
        $this->setPrimary_key($aPK);
    }


    /* MÉTODOS GET y SET --------------------------------------------------------*/
    /**
     * Recupera el atributo in_buzon de CentroDl
     *
     * @return integer in_buzon
     */
    function getN_buzon()
    {
        if (!isset($this->in_buzon) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->in_buzon;
    }

    /**
     * Establece el valor del atributo in_buzon de CentroDl
     *
     * @param integer in_buzon='' optional
     */
    function setN_buzon($in_buzon = '')
    {
        $this->in_buzon = $in_buzon;
    }

    /**
     * Recupera el atributo inum_pi de CentroDl
     *
     * @return integer inum_pi
     */
    function getNum_pi()
    {
        if (!isset($this->inum_pi) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->inum_pi;
    }

    /**
     * Establece el valor del atributo inum_pi de CentroDl
     *
     * @param integer inum_pi='' optional
     */
    function setNum_pi($inum_pi = '')
    {
        $this->inum_pi = $inum_pi;
    }

    /**
     * Recupera el atributo inum_cartas de CentroDl
     *
     * @return integer inum_cartas
     */
    function getNum_cartas()
    {
        if (!isset($this->inum_cartas) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->inum_cartas;
    }

    /**
     * Establece el valor del atributo inum_cartas de CentroDl
     *
     * @param integer inum_cartas='' optional
     */
    function setNum_cartas($inum_cartas = '')
    {
        $this->inum_cartas = $inum_cartas;
    }

    /**
     * Recupera el atributo sobserv de CentroDl
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
     * Establece el valor del atributo sobserv de CentroDl
     *
     * @param string sobserv='' optional
     */
    function setObserv($sobserv = '')
    {
        $this->sobserv = $sobserv;
    }

    /**
     * Recupera el atributo inum_habit_indiv de CentroDl
     *
     * @return integer inum_habit_indiv
     */
    function getNum_habit_indiv()
    {
        if (!isset($this->inum_habit_indiv) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->inum_habit_indiv;
    }

    /**
     * Establece el valor del atributo inum_habit_indiv de CentroDl
     *
     * @param integer inum_habit_indiv='' optional
     */
    function setNum_habit_indiv($inum_habit_indiv = '')
    {
        $this->inum_habit_indiv = $inum_habit_indiv;
    }

    /**
     * Recupera el atributo iplazas de CentroDl
     *
     * @return integer iplazas
     */
    function getPlazas()
    {
        if (!isset($this->iplazas) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->iplazas;
    }

    /**
     * Establece el valor del atributo iplazas de CentroDl
     *
     * @param integer iplazas='' optional
     */
    function setPlazas($iplazas = '')
    {
        $this->iplazas = $iplazas;
    }

    /**
     * Recupera el atributo iid_zona de CentroDl
     *
     * @return integer iid_zona
     */
    function getId_zona()
    {
        if (!isset($this->iid_zona) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->iid_zona;
    }

    /**
     * Establece el valor del atributo iid_zona de CentroDl
     *
     * @param integer iid_zona='' optional
     */
    function setId_zona($iid_zona = '')
    {
        $this->iid_zona = $iid_zona;
    }

    /**
     * Recupera el atributo bsede de CentroDl
     *
     * @return boolean bsede
     */
    function getSede()
    {
        if (!isset($this->bsede) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->bsede;
    }

    /**
     * Establece el valor del atributo bsede de CentroDl
     *
     * @param boolean bsede='' optional
     */
    function setSede($bsede = '')
    {
        $this->bsede = $bsede;
    }

    /**
     * Recupera el atributo inum_cartas_mensuales de CentroDl
     *
     * @return integer inum_cartas_mensuales
     */
    function getNum_cartas_mensuales()
    {
        if (!isset($this->inum_cartas_mensuales) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->inum_cartas_mensuales;
    }

    /**
     * Establece el valor del atributo inum_cartas_mensuales de CentroDl
     *
     * @param integer inum_cartas_mensuales='' optional
     */
    function setNum_cartas_mensuales($inum_cartas_mensuales = '')
    {
        $this->inum_cartas_mensuales = $inum_cartas_mensuales;
    }
    /* MÉTODOS GET y SET D'ATRIBUTOS QUE NO SON CAMPOS -----------------------------*/

    /**
     * Retorna una col·lecció d'objectes del tipus DatosCampo
     *
     */
    function getDatosCampos()
    {
        $oCentroooDlSet = new Set();

        $oCentroooDlSet->add($this->getDatosTipo_ubi());
        $oCentroooDlSet->add($this->getDatosNombre_ubi());
        $oCentroooDlSet->add($this->getDatosDl());
        $oCentroooDlSet->add($this->getDatosPais());
        $oCentroooDlSet->add($this->getDatosRegion());
        $oCentroooDlSet->add($this->getDatosStatus());
        $oCentroooDlSet->add($this->getDatosF_status());
        $oCentroooDlSet->add($this->getDatosSv());
        $oCentroooDlSet->add($this->getDatosSf());
        $oCentroooDlSet->add($this->getDatosTipo_ctr());
        $oCentroooDlSet->add($this->getDatosTipo_labor());
        $oCentroooDlSet->add($this->getDatosCdc());
        $oCentroooDlSet->add($this->getDatosId_ctr_padre());
        $oCentroooDlSet->add($this->getDatosN_buzon());
        $oCentroooDlSet->add($this->getDatosNum_pi());
        $oCentroooDlSet->add($this->getDatosNum_cartas());
        $oCentroooDlSet->add($this->getDatosObserv());
        $oCentroooDlSet->add($this->getDatosNum_habit_indiv());
        $oCentroooDlSet->add($this->getDatosPlazas());
        $oCentroooDlSet->add($this->getDatosId_zona());
        $oCentroooDlSet->add($this->getDatosNum_cartas_mensuales());
        return $oCentroooDlSet->getTot();
    }

    /**
     * Recupera les propietats de l'atribut in_buzon de CentroDl
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosN_buzon()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'n_buzon'));
        $oDatosCampo->setEtiqueta(_("número de buzón"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut inum_pi de CentroDl
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosNum_pi()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'num_pi'));
        $oDatosCampo->setEtiqueta(_("número de pi"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut inum_cartas de CentroDl
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosNum_cartas()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'num_cartas'));
        $oDatosCampo->setEtiqueta(_("número de cartas"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut sobserv de CentroDl
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosObserv()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'observ'));
        $oDatosCampo->setEtiqueta(_("observaciones"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut inum_habit_indiv de CentroDl
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosNum_habit_indiv()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'num_habit_indiv'));
        $oDatosCampo->setEtiqueta(_("num_habit_indiv"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut iplazas de CentroDl
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosPlazas()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'plazas'));
        $oDatosCampo->setEtiqueta(_("plazas"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut iid_zona de CentroDl
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosId_zona()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'id_zona'));
        $oDatosCampo->setEtiqueta(_("id_zona"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut bsede de CentroDl
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosSede()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'sede'));
        $oDatosCampo->setEtiqueta(_("sede"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut inum_cartas_mensuales de CentroDl
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosNum_cartas_mensuales()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'num_cartas_mensuales'));
        $oDatosCampo->setEtiqueta(_("número de cartas mensuales"));
        return $oDatosCampo;
    }
}

?>
