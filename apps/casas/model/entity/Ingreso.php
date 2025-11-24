<?php

namespace casas\model\entity;

use core\ClasePropiedades;
use core\DatosCampo;
use core\Set;

/**
 * Fitxer amb la Classe que accedeix a la taula da_ingresos_dl
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 26/6/2019
 */

/**
 * Clase que implementa la entidad da_ingresos_dl
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 26/6/2019
 */
class Ingreso extends ClasePropiedades
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * aPrimary_key de Ingreso
     *
     * @var array
     */
    private $aPrimary_key;

    /**
     * aDades de Ingreso
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
     * Id_activ de Ingreso
     *
     * @var integer
     */
    private $iid_activ;
    /**
     * Ingresos de Ingreso
     *
     * @var float
     */
    private $iingresos;
    /**
     * Num_asistentes de Ingreso
     *
     * @var integer
     */
    private $inum_asistentes;
    /**
     * Ingresos_previstos de Ingreso
     *
     * @var float
     */
    private $iingresos_previstos;
    /**
     * Num_asistentes_previstos de Ingreso
     *
     * @var integer
     */
    private $inum_asistentes_previstos;
    /**
     * Observ de Ingreso
     *
     * @var string
     */
    private $sobserv;
    /* ATRIBUTOS QUE NO SON CAMPOS------------------------------------------------- */
    /**
     * oDbl de Ingreso
     *
     * @var object
     */
    protected $oDbl;
    /**
     * NomTabla de Ingreso
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
     * @param integer|array iid_activ
     *                        $a_id. Un array con los nombres=>valores de las claves primarias.
     */
    function __construct($a_id = '')
    {
        $oDbl = $GLOBALS['oDBC'];
        $oDbl_Select = $GLOBALS['oDBC_Select'];
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                if (($nom_id == 'id_activ') && $val_id !== '') $this->iid_activ = (int)$val_id; 
            }
        } else {
            if (isset($a_id) && $a_id !== '') {
                $this->iid_activ = (integer)$a_id; 
                $this->aPrimary_key = array('id_activ' => $this->iid_activ);
            }
        }
        $this->setoDbl($oDbl);
        $this->setoDbl_Select($oDbl_Select);
        $this->setNomTabla('da_ingresos_dl');
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
        $aDades['ingresos'] = $this->iingresos;
        $aDades['num_asistentes'] = $this->inum_asistentes;
        $aDades['ingresos_previstos'] = $this->iingresos_previstos;
        $aDades['num_asistentes_previstos'] = $this->inum_asistentes_previstos;
        $aDades['observ'] = $this->sobserv;
        array_walk($aDades, 'core\poner_null');

        if ($bInsert === FALSE) {
            //UPDATE
            $update = "
					ingresos                 = :ingresos,
					num_asistentes           = :num_asistentes,
					ingresos_previstos       = :ingresos_previstos,
					num_asistentes_previstos = :num_asistentes_previstos,
					observ                   = :observ";
            if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_activ='$this->iid_activ'")) === FALSE) {
                $sClauError = 'Ingreso.update.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return FALSE;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'Ingreso.update.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return FALSE;
                }
            }
        } else {
            // INSERT
            array_unshift($aDades, $this->iid_activ);
            $campos = "(id_activ,ingresos,num_asistentes,ingresos_previstos,num_asistentes_previstos,observ)";
            $valores = "(:id_activ,:ingresos,:num_asistentes,:ingresos_previstos,:num_asistentes_previstos,:observ)";
            if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === FALSE) {
                $sClauError = 'Ingreso.insertar.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return FALSE;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'Ingreso.insertar.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return FALSE;
                }
            }
        }
        $this->setAllAttributes($aDades);
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
        if (isset($this->iid_activ)) {
            if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_activ='$this->iid_activ'")) === FALSE) {
                $sClauError = 'Ingreso.carregar';
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
                        $this->setAllAttributes($aDades);
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
        if (($oDbl->exec("DELETE FROM $nom_tabla WHERE id_activ='$this->iid_activ'")) === FALSE) {
            $sClauError = 'Ingreso.eliminar';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return FALSE;
        }
        return TRUE;
    }

    /* OTROS MÉTODOS  ----------------------------------------------------------*/
    /* MÉTODOS PRIVADOS ----------------------------------------------------------*/

    /**
     * Establece el valor de todos los atributos
     *
     * @param array $aDades
     */
    function setAllAttributes(array $aDades)
    {
        if (!is_array($aDades)) return;
        if (array_key_exists('id_activ', $aDades)) $this->setId_activ($aDades['id_activ']);
        if (array_key_exists('ingresos', $aDades)) $this->setIngresos($aDades['ingresos']);
        if (array_key_exists('num_asistentes', $aDades)) $this->setNum_asistentes($aDades['num_asistentes']);
        if (array_key_exists('ingresos_previstos', $aDades)) $this->setIngresos_previstos($aDades['ingresos_previstos']);
        if (array_key_exists('num_asistentes_previstos', $aDades)) $this->setNum_asistentes_previstos($aDades['num_asistentes_previstos']);
        if (array_key_exists('observ', $aDades)) $this->setObserv($aDades['observ']);
    }

    /**
     * Establece a 'empty' el valor de todos los atributos
     *
     */
    function setNullAllAtributes()
    {
        $aPK = $this->getPrimary_key();
        $this->setId_activ('');
        $this->setIngresos('');
        $this->setNum_asistentes('');
        $this->setIngresos_previstos('');
        $this->setNum_asistentes_previstos('');
        $this->setObserv('');
        $this->setPrimary_key($aPK);
    }


    /* MÉTODOS GET y SET --------------------------------------------------------*/

    /**
     * Recupera todos los atributos de Ingreso en un array
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
     * Recupera la clave primaria de Ingreso en un array
     *
     * @return array aPrimary_key
     */
    function getPrimary_key()
    {
        if (!isset($this->aPrimary_key)) {
            $this->aPrimary_key = array('id_activ' => $this->iid_activ);
        }
        return $this->aPrimary_key;
    }

    /**
     * Establece la clave primaria de Ingreso en un array
     *
     * @return array aPrimary_key
     */
    public function setPrimary_key($a_id = '')
    {
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                if (($nom_id == 'id_activ') && $val_id !== '') $this->iid_activ = (int)$val_id; 
            }
        }
    }

    /**
     * Recupera el atributo iid_activ de Ingreso
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
     * Establece el valor del atributo iid_activ de Ingreso
     *
     * @param integer iid_activ
     */
    function setId_activ($iid_activ)
    {
        $this->iid_activ = $iid_activ;
    }

    /**
     * Recupera el atributo iingresos de Ingreso
     *
     * @return float iingresos
     */
    function getIngresos(): float
    {
        if (!isset($this->iingresos) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->iingresos;
    }

    /**
     * Establece el valor del atributo iingresos de Ingreso
     *
     * @param float iingresos='' optional
     */
    function setIngresos($iingresos = 0)
    {
        $this->iingresos = (float)$iingresos;
    }

    /**
     * Recupera el atributo inum_asistentes de Ingreso
     *
     * @return integer|null inum_asistentes
     */
    function getNum_asistentes(): ?int
    {
        if (!isset($this->inum_asistentes) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return (int)$this->inum_asistentes;
    }

    /**
     * Establece el valor del atributo inum_asistentes de Ingreso
     *
     * @param integer inum_asistentes='' optional
     */
    function setNum_asistentes($inum_asistentes = '')
    {
        $this->inum_asistentes = $inum_asistentes;
    }

    /**
     * Recupera el atributo iingresos_previstos de Ingreso
     *
     * @return float iingresos_previstos
     */
    public function getIngresos_previstos(): float
    {
        if (!isset($this->iingresos_previstos) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->iingresos_previstos;
    }

    /**
     * Establece el valor del atributo iingresos_previstos de Ingreso
     *
     * @param float iingresos_previstos='' optional
     */
    function setIngresos_previstos($iingresos_previstos = 0)
    {
        $this->iingresos_previstos = (float) $iingresos_previstos;
    }

    /**
     * Recupera el atributo inum_asistentes_previstos de Ingreso
     *
     * @return integer inum_asistentes_previstos
     */
    public function getNum_asistentes_previstos(): int
    {
        if (!isset($this->inum_asistentes_previstos) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return (int)$this->inum_asistentes_previstos;
    }

    /**
     * Establece el valor del atributo inum_asistentes_previstos de Ingreso
     *
     * @param integer inum_asistentes_previstos='' optional
     */
    function setNum_asistentes_previstos($inum_asistentes_previstos = '')
    {
        $this->inum_asistentes_previstos = (int)$inum_asistentes_previstos;
    }

    /**
     * Recupera el atributo sobserv de Ingreso
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
     * Establece el valor del atributo sobserv de Ingreso
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
        $oIngresoSet = new Set();

        $oIngresoSet->add($this->getDatosIngresos());
        $oIngresoSet->add($this->getDatosNum_asistentes());
        $oIngresoSet->add($this->getDatosIngresos_previstos());
        $oIngresoSet->add($this->getDatosNum_asistentes_previstos());
        $oIngresoSet->add($this->getDatosObserv());
        return $oIngresoSet->getTot();
    }


    /**
     * Recupera les propietats de l'atribut iingresos de Ingreso
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosIngresos()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'ingresos'));
        $oDatosCampo->setEtiqueta(_("ingresos"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut inum_asistentes de Ingreso
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosNum_asistentes()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'num_asistentes'));
        $oDatosCampo->setEtiqueta(_("num_asistentes"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut iingresos_previstos de Ingreso
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosIngresos_previstos()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'ingresos_previstos'));
        $oDatosCampo->setEtiqueta(_("ingresos_previstos"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut inum_asistentes_previstos de Ingreso
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosNum_asistentes_previstos()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'num_asistentes_previstos'));
        $oDatosCampo->setEtiqueta(_("num_asistentes_previstos"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut sobserv de Ingreso
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosObserv()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'observ'));
        $oDatosCampo->setEtiqueta(_("observ"));
        return $oDatosCampo;
    }
}
