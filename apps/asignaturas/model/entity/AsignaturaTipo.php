<?php
namespace asignaturas\model\entity;

use core\ClasePropiedades;
use core\DatosCampo;
use core\Set;

/**
 * Clase que implementa la entidad $nom_tabla
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 01/12/2010
 */
class AsignaturaTipo extends ClasePropiedades
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * aPrimary_key de AsignaturaTipo
     *
     * @var array
     */
    private $aPrimary_key;

    /**
     * aDades de AsignaturaTipo
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
     * Id_tipo de AsignaturaTipo
     *
     * @var integer
     */
    private $iid_tipo;
    /**
     * Tipo_asignatura de AsignaturaTipo
     *
     * @var string
     */
    private $stipo_asignatura;
    /**
     * Tipo_breve de AsignaturaTipo
     *
     * @var string
     */
    private $stipo_breve;
    /**
     * Año de AsignaturaTipo
     *
     * @var string
     */
    private $saño;
    /**
     * Tipo_latin de AsignaturaTipo
     *
     * @var string
     */
    private $stipo_latin;
    /* ATRIBUTOS QUE NO SON CAMPOS------------------------------------------------- */
    /* CONSTRUCTOR -------------------------------------------------------------- */

    /**
     * Constructor de la classe.
     * Si només necessita un valor, se li pot passar un integer.
     * En general se li passa un array amb les claus primàries.
     *
     * @param integer|array iid_tipo
     *                        $a_id. Un array con los nombres=>valores de las claves primarias.
     */
    function __construct($a_id = '')
    {
        $oDbl = $GLOBALS['oDBPC'];
        $oDbl_Select = $GLOBALS['oDBPC_Select'];
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                if (($nom_id === 'id_tipo') && $val_id !== '') $this->iid_tipo = (int)$val_id;
            }
        } else {
            if (isset($a_id) && $a_id !== '') {
                $this->iid_tipo = (integer)$a_id;
                $this->aPrimary_key = array('id_tipo' => $this->iid_tipo);
            }
        }
        $this->setoDbl($oDbl);
        $this->setoDbl_Select($oDbl_Select);
        $this->setNomTabla('xa_tipo_asig');
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
        $aDades['tipo_asignatura'] = $this->stipo_asignatura;
        $aDades['tipo_breve'] = $this->stipo_breve;
        $aDades['año'] = $this->saño;
        $aDades['tipo_latin'] = $this->stipo_latin;
        array_walk($aDades, 'core\poner_null');

        if ($bInsert === false) {
            //UPDATE
            $update = "
					tipo_asignatura          = :tipo_asignatura,
					tipo_breve               = :tipo_breve,
					año                     = :año,
					tipo_latin               = :tipo_latin";
            if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_tipo='$this->iid_tipo'")) === false) {
                $sClauError = 'AsignaturaTipo.update.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'AsignaturaTipo.update.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return false;
                }
            }
        } else {
            // INSERT
            array_unshift($aDades, $this->iid_tipo);
            $campos = "(id_tipo,tipo_asignatura,tipo_breve,año,tipo_latin)";
            $valores = "(:id_tipo,:tipo_asignatura,:tipo_breve,:año,:tipo_latin)";
            if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === false) {
                $sClauError = 'AsignaturaTipo.insertar.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'AsignaturaTipo.insertar.execute';
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
        if (isset($this->iid_tipo)) {
            if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_tipo='$this->iid_tipo'")) === false) {
                $sClauError = 'AsignaturaTipo.carregar';
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
        if (($oDblSt = $oDbl->exec("DELETE FROM $nom_tabla WHERE id_tipo='$this->iid_tipo'")) === false) {
            $sClauError = 'AsignaturaTipo.eliminar';
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
        if (array_key_exists('id_tipo', $aDades)) $this->setId_tipo($aDades['id_tipo']);
        if (array_key_exists('tipo_asignatura', $aDades)) $this->setTipo_asignatura($aDades['tipo_asignatura']);
        if (array_key_exists('tipo_breve', $aDades)) $this->setTipo_breve($aDades['tipo_breve']);
        if (array_key_exists('año', $aDades)) $this->setAño($aDades['año']);
        if (array_key_exists('tipo_latin', $aDades)) $this->setTipo_latin($aDades['tipo_latin']);
    }

    /**
     * Establece a 'empty' el valor de todos los atributos
     *
     */
    function setNullAllAtributes()
    {
        $aPK = $this->getPrimary_key();
        $this->setId_schema('');
        $this->setId_tipo('');
        $this->setTipo_asignatura('');
        $this->setTipo_breve('');
        if (array_key_exists('año', $this->aDades)) $this->setAño($this->aDades['año']);
        $this->setTipo_latin('');
        $this->setPrimary_key($aPK);
    }


    /* MÉTODOS GET y SET --------------------------------------------------------*/
    /**
     * Recupera todos los atributos de AsignaturaTipo en un array
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
     * Recupera la clave primaria de AsignaturaTipo en un array
     *
     * @return array aPrimary_key
     */
    function getPrimary_key()
    {
        if (!isset($this->aPrimary_key)) {
            $this->aPrimary_key = array('id_tipo' => $this->iid_tipo);
        }
        return $this->aPrimary_key;
    }

    /**
     * Establece la clave primaria de AsignaturaTipo en un array
     *
     * @return array aPrimary_key
     */
    public function setPrimary_key($a_id = '')
    {
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                if (($nom_id === 'id_tipo') && $val_id !== '') $this->iid_tipo = (int)$val_id;
            }
        }
    }

    /**
     * Recupera el atributo iid_tipo de AsignaturaTipo
     *
     * @return integer iid_tipo
     */
    function getId_tipo()
    {
        if (!isset($this->iid_tipo) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->iid_tipo;
    }

    /**
     * Establece el valor del atributo iid_tipo de AsignaturaTipo
     *
     * @param integer iid_tipo
     */
    function setId_tipo($iid_tipo)
    {
        $this->iid_tipo = $iid_tipo;
    }

    /**
     * Recupera el atributo stipo_asignatura de AsignaturaTipo
     *
     * @return string stipo_asignatura
     */
    function getTipo_asignatura()
    {
        if (!isset($this->stipo_asignatura) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->stipo_asignatura;
    }

    /**
     * Establece el valor del atributo stipo_asignatura de AsignaturaTipo
     *
     * @param string stipo_asignatura='' optional
     */
    function setTipo_asignatura($stipo_asignatura = '')
    {
        $this->stipo_asignatura = $stipo_asignatura;
    }

    /**
     * Recupera el atributo stipo_breve de AsignaturaTipo
     *
     * @return string stipo_breve
     */
    function getTipo_breve()
    {
        if (!isset($this->stipo_breve) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->stipo_breve;
    }

    /**
     * Establece el valor del atributo stipo_breve de AsignaturaTipo
     *
     * @param string stipo_breve='' optional
     */
    function setTipo_breve($stipo_breve = '')
    {
        $this->stipo_breve = $stipo_breve;
    }

    /**
     * Recupera el atributo saño de AsignaturaTipo
     *
     * @return string saño
     */
    function getAño()
    {
        if (!isset($this->saño)) {
            $this->DBCarregar();
        }
        return $this->saño;
    }

    /**
     * Establece el valor del atributo saño de AsignaturaTipo
     *
     * @param string saño='' optional
     */
    function setAño($saño = '')
    {
        $this->saño = $saño;
    }

    /**
     * Recupera el atributo stipo_latin de AsignaturaTipo
     *
     * @return string stipo_latin
     */
    function getTipo_latin()
    {
        if (!isset($this->stipo_latin) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->stipo_latin;
    }

    /**
     * Establece el valor del atributo stipo_latin de AsignaturaTipo
     *
     * @param string stipo_latin='' optional
     */
    function setTipo_latin($stipo_latin = '')
    {
        $this->stipo_latin = $stipo_latin;
    }
    /* MÉTODOS GET y SET D'ATRIBUTOS QUE NO SON CAMPOS -----------------------------*/

    /**
     * Retorna una col·lecció d'objectes del tipus DatosCampo
     *
     */
    function getDatosCampos()
    {
        $oAsignaturaTipoSet = new Set();

        $oAsignaturaTipoSet->add($this->getDatosTipo_asignatura());
        $oAsignaturaTipoSet->add($this->getDatosTipo_breve());
        $oAsignaturaTipoSet->add($this->getDatosAño());
        $oAsignaturaTipoSet->add($this->getDatosTipo_latin());
        return $oAsignaturaTipoSet->getTot();
    }


    /**
     * Recupera les propietats de l'atribut stipo_asignatura de AsignaturaTipo
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosTipo_asignatura()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'tipo_asignatura'));
        $oDatosCampo->setEtiqueta(_("tipo de asignatura"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut stipo_breve de AsignaturaTipo
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosTipo_breve()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'tipo_breve'));
        $oDatosCampo->setEtiqueta(_("tipo breve"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut saño de AsignaturaTipo
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosAño()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'año'));
        $oDatosCampo->setEtiqueta(_("año"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut stipo_latin de AsignaturaTipo
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosTipo_latin()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'tipo_latin'));
        $oDatosCampo->setEtiqueta(_("tipo_latin"));
        return $oDatosCampo;
    }
}

?>
