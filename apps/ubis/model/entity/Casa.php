<?php
namespace ubis\model\entity;

use core\DatosCampo;
use core\Set;
use function core\is_true;

/**
 * Clase que implementa la entidad u_cdc
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 27/09/2010
 */
class Casa extends UbiGlobal
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * Tipo_casa de Casa
     *
     * @var string
     */
    protected $stipo_casa;

    /**
     * Plazas de Casa
     *
     * @var integer
     */
    protected $iplazas;
    /**
     * Plazas_min de Casa
     *
     * @var integer
     */
    protected $iplazas_min;
    /**
     * Num_sacd de Casa
     *
     * @var integer
     */
    protected $inum_sacd;
    /**
     * Biblioteca de Casa
     *
     * @var string
     */
    protected $sbiblioteca;
    /**
     * Observ de Casa
     *
     * @var string
     */
    protected $sobserv;
    /* ATRIBUTOS QUE NO SON CAMPOS------------------------------------------------- */
    /* CONSTRUCTOR -------------------------------------------------------------- */

    /**
     * Constructor de la classe.
     * Si només necessita un valor, se li pot passar un integer.
     * En general se li passa un array amb les claus primàries.
     *
     * @param integer|array iid_ubi
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
                $this->iid_ubi = (integer)$a_id; 
                $this->aPrimary_key = array('id_ubi' => $this->iid_ubi);
            }
        }
        $this->setoDbl($oDbl);
        $this->setoDbl_Select($oDbl_Select);
        $this->setNomTabla('u_cdc');
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
        $aDades['tipo_casa'] = $this->stipo_casa;
        $aDades['plazas'] = $this->iplazas;
        $aDades['plazas_min'] = $this->iplazas_min;
        $aDades['num_sacd'] = $this->inum_sacd;
        $aDades['biblioteca'] = $this->sbiblioteca;
        $aDades['observ'] = $this->sobserv;
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
					tipo_casa                = :tipo_casa,
					plazas                   = :plazas,
					plazas_min               = :plazas_min,
					num_sacd                 = :num_sacd,
					biblioteca               = :biblioteca,
					observ                   = :observ";
            if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_ubi='$this->iid_ubi'")) === false) {
                $sClauError = 'Casa.update.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'Casa.update.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return false;
                }
            }
        } else {
            // INSERT
            array_unshift($aDades, $this->iid_ubi);
            $campos = "(tipo_ubi,id_ubi,nombre_ubi,dl,pais,region,status,f_status,sv,sf,tipo_casa,plazas,plazas_min,num_sacd,biblioteca,observ)";
            $valores = "(:tipo_ubi,:id_ubi,:nombre_ubi,:dl,:pais,:region,:status,:f_status,:sv,:sf,:tipo_casa,:plazas,:plazas_min,:num_sacd,:biblioteca,:observ)";
            if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === false) {
                $sClauError = 'Casa.insertar.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'Casa.insertar.execute';
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
        if (isset($this->iid_ubi)) {
            if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_ubi='$this->iid_ubi'")) === false) {
                $sClauError = 'Casa.carregar';
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
        if (($oDblSt = $oDbl->exec("DELETE FROM $nom_tabla WHERE id_ubi='$this->iid_ubi'")) === false) {
            $sClauError = 'Casa.eliminar';
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
    function setAllAtributes(array $aDades, $convert = FALSE)
    {
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
        if (array_key_exists('tipo_casa', $aDades)) $this->setTipo_casa($aDades['tipo_casa']);
        if (array_key_exists('plazas', $aDades)) $this->setPlazas($aDades['plazas']);
        if (array_key_exists('plazas_min', $aDades)) $this->setPlazas_min($aDades['plazas_min']);
        if (array_key_exists('num_sacd', $aDades)) $this->setNum_sacd($aDades['num_sacd']);
        if (array_key_exists('biblioteca', $aDades)) $this->setBiblioteca($aDades['biblioteca']);
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
        $this->setTipo_casa('');
        $this->setPlazas('');
        $this->setPlazas_min('');
        $this->setNum_sacd('');
        $this->setBiblioteca('');
        $this->setObserv('');
        $this->setPrimary_key($aPK);
    }


    /* MÉTODOS GET y SET --------------------------------------------------------*/

    /**
     * Recupera el atributo stipo_casa de Casa
     *
     * @return string stipo_casa
     */
    function getTipo_casa()
    {
        if (!isset($this->stipo_casa) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->stipo_casa;
    }

    /**
     * Establece el valor del atributo stipo_casa de Casa
     *
     * @param string stipo_casa='' optional
     */
    function setTipo_casa($stipo_casa = '')
    {
        $this->stipo_casa = $stipo_casa;
    }

    /**
     * Recupera el atributo iplazas de Casa
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
     * Establece el valor del atributo iplazas de Casa
     *
     * @param integer iplazas='' optional
     */
    function setPlazas($iplazas = '')
    {
        $this->iplazas = $iplazas;
    }

    /**
     * Recupera el atributo iplazas_min de Casa
     *
     * @return integer iplazas_min
     */
    function getPlazas_min()
    {
        if (!isset($this->iplazas_min) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->iplazas_min;
    }

    /**
     * Establece el valor del atributo iplazas_min de Casa
     *
     * @param integer iplazas_min='' optional
     */
    function setPlazas_min($iplazas_min = '')
    {
        $this->iplazas_min = $iplazas_min;
    }

    /**
     * Recupera el atributo inum_sacd de Casa
     *
     * @return integer inum_sacd
     */
    function getNum_sacd()
    {
        if (!isset($this->inum_sacd) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->inum_sacd;
    }

    /**
     * Establece el valor del atributo inum_sacd de Casa
     *
     * @param integer inum_sacd='' optional
     */
    function setNum_sacd($inum_sacd = '')
    {
        $this->inum_sacd = $inum_sacd;
    }

    /**
     * Recupera el atributo sbiblioteca de Casa
     *
     * @return string sbiblioteca
     */
    function getBiblioteca()
    {
        if (!isset($this->sbiblioteca) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->sbiblioteca;
    }

    /**
     * Establece el valor del atributo sbiblioteca de Casa
     *
     * @param string sbiblioteca='' optional
     */
    function setBiblioteca($sbiblioteca = '')
    {
        $this->sbiblioteca = $sbiblioteca;
    }

    /**
     * Recupera el atributo sobserv de Casa
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
     * Establece el valor del atributo sobserv de Casa
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
        $oUbiSet = new Set();

        $oUbiSet->add($this->getDatosTipo_ubi());
        $oUbiSet->add($this->getDatosNombre_ubi());
        $oUbiSet->add($this->getDatosDl());
        $oUbiSet->add($this->getDatosPais());
        $oUbiSet->add($this->getDatosRegion());
        $oUbiSet->add($this->getDatosStatus());
        $oUbiSet->add($this->getDatosF_status());
        $oUbiSet->add($this->getDatosSv());
        $oUbiSet->add($this->getDatosSf());
        $oUbiSet->add($this->getDatosTipo_casa());
        $oUbiSet->add($this->getDatosPlazas());
        $oUbiSet->add($this->getDatosPlazas_min());
        $oUbiSet->add($this->getDatosNum_sacd());
        $oUbiSet->add($this->getDatosBiblioteca());
        $oUbiSet->add($this->getDatosObserv());
        return $oUbiSet->getTot();
    }

    /**
     * Recupera les propietats de l'atribut stipo_casa de Casa
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosTipo_casa()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'tipo_casa'));
        $oDatosCampo->setEtiqueta(_("tipo de casa"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut iplazas de Casa
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
     * Recupera les propietats de l'atribut iplazas_min de Casa
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosPlazas_min()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'plazas_min'));
        $oDatosCampo->setEtiqueta(_("plazas mínimo"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut inum_sacd de Casa
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosNum_sacd()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'num_sacd'));
        $oDatosCampo->setEtiqueta(_("num sacd"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut sbiblioteca de Casa
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosBiblioteca()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'biblioteca'));
        $oDatosCampo->setEtiqueta(_("biblioteca casa"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut sobserv de Casa
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
}

?>
