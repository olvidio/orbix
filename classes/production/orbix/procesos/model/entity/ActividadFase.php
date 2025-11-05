<?php

namespace procesos\model\entity;

use core\ClasePropiedades;
use core\ConfigGlobal;
use core\DatosCampo;
use core\Set;
use function core\is_true;

/**
 * Fitxer amb la Classe que accedeix a la taula a_fases
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 07/12/2018
 */

/**
 * Clase que implementa la entidad a_fases
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 07/12/2018
 */
class ActividadFase extends ClasePropiedades
{

    // Fases constants.
    const FASE_PROYECTO = 1; // Proyecto.
    const FASE_APROBADA = 2; // Actual.
    const FASE_TERMINADA = 3; // Terminada.
    const FASE_OK_SACD = 5; // ok_atn_sacd.

    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * aPrimary_key de ActividadFase
     *
     * @var array
     */
    private $aPrimary_key;

    /**
     * aDades de ActividadFase
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
     * Id_fase de ActividadFase
     *
     * @var integer
     */
    private $iid_fase;
    /**
     * Desc_fase de ActividadFase
     *
     * @var string
     */
    private $sdesc_fase;
    /**
     * Sf de ActividadFase
     *
     * @var boolean
     */
    private $bsf;
    /**
     * Sv de ActividadFase
     *
     * @var boolean
     */
    private $bsv;
    /* ATRIBUTOS QUE NO SON CAMPOS------------------------------------------------- */
    /**
     * oDbl de ActividadFase
     *
     * @var object
     */
    protected $oDbl;
    /**
     * NomTabla de ActividadFase
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
     * @param integer|array iid_fase
     *                        $a_id. Un array con los nombres=>valores de las claves primarias.
     */
    function __construct($a_id = '')
    {
        $oDbl = $GLOBALS['oDBC'];
        $oDbl_Select = $GLOBALS['oDBC_Select'];
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                if (($nom_id === 'id_fase') && $val_id !== '') $this->iid_fase = (int)$val_id;
            }
        } else {
            if (isset($a_id) && $a_id !== '') {
                $this->iid_fase = (integer)$a_id;
                $this->aPrimary_key = array('id_fase' => $this->iid_fase);
            }
        }
        $this->setoDbl($oDbl);
        $this->setoDbl_Select($oDbl_Select);
        $this->setNomTabla('a_fases');
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
        if ($this->DBCarregar('guardar') === FALSE) {
            $bInsert = TRUE;
        } else {
            $bInsert = FALSE;
        }
        $aDades = [];
        $aDades['desc_fase'] = $this->sdesc_fase;
        $aDades['sf'] = $this->bsf;
        $aDades['sv'] = $this->bsv;
        array_walk($aDades, 'core\poner_null');
        //para el caso de los boolean FALSE, el pdo(+postgresql) pone string '' en vez de 0. Lo arreglo:
        if (is_true($aDades['sf'])) {
            $aDades['sf'] = 'true';
        } else {
            $aDades['sf'] = 'false';
        }
        if (is_true($aDades['sv'])) {
            $aDades['sv'] = 'true';
        } else {
            $aDades['sv'] = 'false';
        }

        if ($bInsert === FALSE) {
            //UPDATE
            $update = "
					desc_fase                = :desc_fase,
					sf                       = :sf,
					sv                       = :sv";
            if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_fase='$this->iid_fase'")) === FALSE) {
                $sClauError = 'ActividadFase.update.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return FALSE;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'ActividadFase.update.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return FALSE;
                }
            }
        } else {
            // INSERT
            $campos = "(desc_fase,sf,sv)";
            $valores = "(:desc_fase,:sf,:sv)";
            if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === FALSE) {
                $sClauError = 'ActividadFase.insertar.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return FALSE;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'ActividadFase.insertar.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return FALSE;
                }
            }
            $this->iid_fase = $oDbl->lastInsertId('a_fases_id_fase_seq');
        }
        $this->setAllAtributes($aDades);
        return TRUE;
    }

    /**
     * Carga los campos de la base de datos como atributos de la clase.
     *
     */
    public function DBCarregar($que = null)
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        if (isset($this->iid_fase)) {
            if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_fase='$this->iid_fase'")) === FALSE) {
                $sClauError = 'ActividadFase.carregar';
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
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        if ($this->iid_fase < 10) {
            $msg = sprintf(_("no se puede eliminar la fase %s"), $this->iid_fase);
            $msg .= "\n";
            echo $msg;
            return FALSE;
        } else {
            // Sólo puedo eliminar si la otra sección no tiene nada.
            if ((ConfigGlobal::mi_sfsv() == 1 && $this->getSf())
                || (ConfigGlobal::mi_sfsv() == 2 && $this->getSv())) {
                $msg = _("no se puede eliminar, lo usa la otra sección") . "\n";
                echo $msg;
                return FALSE;
            } else {
                if (($oDbl->exec("DELETE FROM $nom_tabla WHERE id_fase='$this->iid_fase'")) === FALSE) {
                    $sClauError = 'ActividadFase.eliminar';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                    return FALSE;
                }
                return TRUE;
            }
        }
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
        if (array_key_exists('id_fase', $aDades)) $this->setId_fase($aDades['id_fase']);
        if (array_key_exists('desc_fase', $aDades)) $this->setDesc_fase($aDades['desc_fase']);
        if (array_key_exists('sf', $aDades)) $this->setSf($aDades['sf'], TRUE);
        if (array_key_exists('sv', $aDades)) $this->setSv($aDades['sv'], TRUE);
    }

    /**
     * Establece a 'empty' el valor de todos los atributos
     *
     */
    function setNullAllAtributes()
    {
        $aPK = $this->getPrimary_key();
        $this->setId_fase('');
        $this->setDesc_fase('');
        $this->setSf('');
        $this->setSv('');
        $this->setPrimary_key($aPK);
    }


    /* MÉTODOS GET y SET --------------------------------------------------------*/

    /**
     * Recupera todos los atributos de ActividadFase en un array
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
     * Recupera la clave primaria de ActividadFase en un array
     *
     * @return array aPrimary_key
     */
    function getPrimary_key()
    {
        if (!isset($this->aPrimary_key)) {
            $this->aPrimary_key = array('id_fase' => $this->iid_fase);
        }
        return $this->aPrimary_key;
    }

    /**
     * Establece la clave primaria de ActividadFase en un array
     *
     * @return array aPrimary_key
     */
    public function setPrimary_key($a_id = '')
    {
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                if (($nom_id === 'id_fase') && $val_id !== '') $this->iid_fase = (int)$val_id;
            }
        }
    }

    /**
     * Recupera el atributo iid_fase de ActividadFase
     *
     * @return integer iid_fase
     */
    function getId_fase()
    {
        if (!isset($this->iid_fase) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->iid_fase;
    }

    /**
     * Establece el valor del atributo iid_fase de ActividadFase
     *
     * @param integer iid_fase
     */
    function setId_fase($iid_fase)
    {
        $this->iid_fase = $iid_fase;
    }

    /**
     * Recupera el atributo sdesc_fase de ActividadFase
     *
     * @return string sdesc_fase
     */
    function getDesc_fase()
    {
        if (!isset($this->sdesc_fase) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->sdesc_fase;
    }

    /**
     * Establece el valor del atributo sdesc_fase de ActividadFase
     *
     * @param string sdesc_fase='' optional
     */
    function setDesc_fase($sdesc_fase = '')
    {
        $this->sdesc_fase = $sdesc_fase;
    }

    /**
     * Recupera el atributo bsf de ActividadFase
     *
     * @return boolean bsf
     */
    function getSf()
    {
        if (!isset($this->bsf) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->bsf;
    }

    /**
     * Establece el valor del atributo bsf de ActividadFase
     *
     * @param boolean bsf='f' optional
     * @param boolean fromDB='f' optional, Para cuando se usa para cargar desde la DB
     */
    function setSf($bsf = 'f', $fromDB = FALSE)
    {
        if ($fromDB) {
            $this->bsf = $bsf;
        } else {
            // sólo dejo cambiar si soy sf
            if (ConfigGlobal::mi_sfsv() == 2) {
                $this->bsf = $bsf;
            }
        }
    }

    /**
     * Recupera el atributo bsv de ActividadFase
     *
     * @return boolean bsv
     */
    function getSv()
    {
        if (!isset($this->bsv) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->bsv;
    }

    /**
     * Establece el valor del atributo bsv de ActividadFase
     *
     * @param boolean bsv='f' optional
     * @param boolean fromDB='f' optional, Para cuando se usa para cargar desde la DB
     */
    function setSv($bsv = 'f', $fromDB = FALSE)
    {
        if ($fromDB) {
            $this->bsv = $bsv;
        } else {
            // sólo dejo cambiar si soy sv
            if (ConfigGlobal::mi_sfsv() == 1) {
                $this->bsv = $bsv;
            }
        }
    }
    /* MÉTODOS GET y SET D'ATRIBUTOS QUE NO SON CAMPOS -----------------------------*/

    /**
     * Retorna una col·lecció d'objectes del tipus DatosCampo
     *
     */
    function getDatosCampos()
    {
        $oActividadFaseSet = new Set();

        $oActividadFaseSet->add($this->getDatosDesc_fase());
        $oActividadFaseSet->add($this->getDatosSf());
        $oActividadFaseSet->add($this->getDatosSv());
        return $oActividadFaseSet->getTot();
    }

    /**
     * Recupera les propietats de l'atribut sdesc_fase de ActividadFase
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosDesc_fase()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'desc_fase'));
        $oDatosCampo->setEtiqueta(_("descripción"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument('30');
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut bsf de ActividadFase
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosSf()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'sf'));
        $oDatosCampo->setEtiqueta(_("sf"));
        $oDatosCampo->setTipo('check');
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut bsv de ActividadFase
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosSv()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'sv'));
        $oDatosCampo->setEtiqueta(_("sv"));
        $oDatosCampo->setTipo('check');
        return $oDatosCampo;
    }
}
