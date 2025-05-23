<?php
namespace ubis\model\entity;

use core\ConfigGlobal;
use core\DatosCampo;
use core\Set;
use function core\is_true;

/**
 * Clase que implementa la entidad u_centros
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 27/09/2010
 */
class Centro extends UbiGlobal
{
    /* ATRIBUTOS ----------------------------------------------------------------- */
    /**
     * Tipo_ctr de Centro
     *
     * @var string
     */
    protected $stipo_ctr;
    /**
     * Tipo_labor de Centro
     *
     * @var integer
     */
    protected $itipo_labor;
    /**
     * Cdc de Centro
     *
     * @var boolean
     */
    protected $bcdc;
    /**
     * Id_ctr_padre de Centro
     *
     * @var integer
     */
    protected $iid_ctr_padre;
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
        if (ConfigGlobal::is_dmz()) {
            $oDbl = $GLOBALS['oDBEP'];
            $oDbl_Select = $GLOBALS['oDBEP_Select'];
            $this->setoDbl($oDbl);
            $this->setoDbl_Select($oDbl);
            $this->setNomTabla('u_centros');
        } else {
            $oDbl = $GLOBALS['oDBP'];
            $this->setoDbl($oDbl);
            $this->setoDbl_Select($oDbl);
            $this->setNomTabla('u_centros');
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
					cdc                      = :cdc";
            if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_ubi='$this->iid_ubi'")) === false) {
                $sClauError = 'Centro.update.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'Centro.update.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return false;
                }
            }
        } else {
            // INSERT
            array_unshift($aDades, $this->iid_ubi);
            $campos = "(tipo_ubi,id_ubi,nombre_ubi,dl,pais,region,status,f_status,sv,sf,tipo_ctr,tipo_labor,cdc)";
            $valores = "(:tipo_ubi,:id_ubi,:nombre_ubi,:dl,:pais,:region,:status,:f_status,:sv,:sf,:tipo_ctr,:tipo_labor,:cdc)";
            if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === false) {
                $sClauError = 'Centro.insertar.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'Centro.insertar.execute';
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
                $sClauError = 'Centro.carregar';
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
            $sClauError = 'Centro.eliminar';
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
        if (array_key_exists('tipo_ctr', $aDades)) $this->setTipo_ctr($aDades['tipo_ctr']);
        if (array_key_exists('tipo_labor', $aDades)) $this->setTipo_labor($aDades['tipo_labor']);
        if (array_key_exists('cdc', $aDades)) $this->setCdc($aDades['cdc']);
        if (array_key_exists('id_ctr_padre', $aDades)) $this->setId_ctr_padre($aDades['id_ctr_padre']);
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
        $this->setPrimary_key($aPK);
    }


    /* MÉTODOS GET y SET --------------------------------------------------------*/
    /**
     * Recupera el atributo stipo_ctr de Centro
     *
     * @return string stipo_ctr
     */
    function getTipo_ctr()
    {
        if (!isset($this->stipo_ctr) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->stipo_ctr;
    }

    /**
     * Establece el valor del atributo stipo_ctr de Centro
     *
     * @param string stipo_ctr='' optional
     */
    function setTipo_ctr($stipo_ctr = '')
    {
        $this->stipo_ctr = $stipo_ctr;
    }

    /**
     * Recupera el atributo itipo_labor de Centro
     *
     * @return integer itipo_labor
     */
    function getTipo_labor()
    {
        if (!isset($this->itipo_labor) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->itipo_labor;
    }

    /**
     * Establece el valor del atributo itipo_labor de Centro
     *
     * @param integer itipo_labor='' optional
     */
    function setTipo_labor($itipo_labor = '')
    {
        $this->itipo_labor = $itipo_labor;
    }

    /**
     * Recupera el atributo bcdc de Centro
     *
     * @return boolean bcdc
     */
    function getCdc()
    {
        if (!isset($this->bcdc) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->bcdc;
    }

    /**
     * Establece el valor del atributo bcdc de Centro
     *
     * @param boolean bcdc='f' optional
     */
    function setCdc($bcdc = 'f')
    {
        $this->bcdc = $bcdc;
    }

    /**
     * Recupera el atributo iid_ctr_padre de Centro
     *
     * @return integer iid_ctr_padre
     */
    function getId_ctr_padre()
    {
        if (!isset($this->iid_ctr_padre) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->iid_ctr_padre;
    }

    /**
     * Establece el valor del atributo iid_ctr_padre de Centro
     *
     * @param integer iid_ctr_padre='' optional
     */
    function setId_ctr_padre($iid_ctr_padre = '')
    {
        $this->iid_ctr_padre = $iid_ctr_padre;
    }
    /* MÉTODOS GET y SET D'ATRIBUTOS QUE NO SON CAMPOS -----------------------------*/

    /**
     * Retorna una col·lecció d'objectes del tipus DatosCampo
     *
     */
    function getDatosCampos()
    {
        $oCentroSet = new Set();

        $oCentroSet->add($this->getDatosTipo_ubi());
        $oCentroSet->add($this->getDatosNombre_ubi());
        $oCentroSet->add($this->getDatosDl());
        $oCentroSet->add($this->getDatosPais());
        $oCentroSet->add($this->getDatosRegion());
        $oCentroSet->add($this->getDatosStatus());
        $oCentroSet->add($this->getDatosF_status());
        $oCentroSet->add($this->getDatosSv());
        $oCentroSet->add($this->getDatosSf());
        $oCentroSet->add($this->getDatosTipo_ctr());
        $oCentroSet->add($this->getDatosTipo_labor());
        $oCentroSet->add($this->getDatosCdc());
        $oCentroSet->add($this->getDatosId_ctr_padre());
        return $oCentroSet->getTot();
    }

    /**
     * Recupera les propietats de l'atribut stipo_ctr de CentroooDl
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosTipo_ctr()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'tipo_ctr'));
        $oDatosCampo->setEtiqueta(_("tipo de ctr"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut itipo_labor de CentroooDl
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosTipo_labor()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'tipo_labor'));
        $oDatosCampo->setEtiqueta(_("tipo de labor"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut bcdc de CentroooDl
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosCdc()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'cdc'));
        $oDatosCampo->setEtiqueta(_("cdc"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut iid_ctr_padre de CentroooDl
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosId_ctr_padre()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'id_ctr_padre'));
        $oDatosCampo->setEtiqueta(_("id_ctr_padre"));
        return $oDatosCampo;
    }
}
