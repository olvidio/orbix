<?php
namespace notas\model\entity;

use core\ClasePropiedades;
use core\DatosCampo;
use core\Set;

/**
 * Fitxer amb la Classe que accedeix a la taula e_actas_tribunal_dl
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 07/04/2014
 */

/**
 * Clase que implementa la entidad e_actas_tribunal_dl
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 07/04/2014
 */
class ActaTribunal extends ClasePropiedades
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * aPrimary_key de ActaTribunalDl
     *
     * @var array
     */
    protected $aPrimary_key;

    /**
     * Id_schema de ActaTribunalDl
     *
     * @var integer
     */
    protected $iid_schema;

    /**
     * aDades de ActaTribunalDl
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
     * Acta de ActaTribunalDl
     *
     * @var string
     */
    protected $sacta;
    /**
     * Examinador de ActaTribunalDl
     *
     * @var string
     */
    protected $sexaminador;
    /**
     * Orden de ActaTribunalDl
     *
     * @var integer
     */
    protected $iorden;
    /**
     * Id_item de ActaTribunalDl
     *
     * @var integer
     */
    protected $iid_item;
    /* ATRIBUTOS QUE NO SON CAMPOS------------------------------------------------- */
    /**
     * oDbl de ActaTribunalDl
     *
     * @var object
     */
    protected $oDbl;
    /**
     * NomTabla de ActaTribunalDl
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
     * @param integer|array iid_schema -> importamte para los esquemas region del stgr.
     *                        iid_item
     *                        $a_id. Un array con los nombres=>valores de las claves primarias.
     */
    function __construct($a_id = '')
    {
        $oDbl = $GLOBALS['oDBP'];
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                if (($nom_id === 'id_schema') && $val_id !== '') {
                    $this->iid_schema = (int)$val_id;
                }
                if (($nom_id === 'id_item') && $val_id !== '') {
                    $this->iid_item = (int)$val_id;
                }
            }
        } else {
            return FALSE;
        }
        $this->setoDbl($oDbl);
        $this->setNomTabla('e_actas_tribunal');
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
        $aDades['acta'] = $this->sacta;
        $aDades['examinador'] = $this->sexaminador;
        $aDades['orden'] = $this->iorden;
        array_walk($aDades, 'core\poner_null');

        if ($bInsert === false) {
            //UPDATE
            $update = "
					acta                     = :acta,
					examinador               = :examinador,
					orden                    = :orden";
            if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_schema = $this->iid_schema AND id_item=$this->iid_item")) === false) {
                $sClauError = 'ActaTribunalDl.update.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'ActaTribunalDl.update.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return false;
                }
            }
        } else {
            // INSERT
            array_unshift($aDades, $this->iid_item);
            $campos = "(acta,examinador,orden)";
            $valores = "(:acta,:examinador,:orden)";
            if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === false) {
                $sClauError = 'ActaTribunalDl.insertar.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'ActaTribunalDl.insertar.execute';
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
        $rta = FALSE;
        $nom_tabla = $this->getNomTabla();
        if (isset($this->iid_item)) {
            if (($oDblSt = $this->oDbl->query("SELECT * FROM $nom_tabla WHERE id_schema = $this->iid_schema AND id_item=$this->iid_item")) === false) {
                $sClauError = 'ActaTribunalDl.carregar';
                $_SESSION['oGestorErrores']->addErrorAppLastError($this->oDbl, $sClauError, __LINE__, __FILE__);
                $rta = FALSE;
            }
            $aDades_local = $oDblSt->fetch(\PDO::FETCH_ASSOC);
            // Para evitar posteriores cargas
            $this->bLoaded = TRUE;
            switch ($que) {
                case 'tot':
                    $this->aDades = $aDades_local;
                    break;
                case 'guardar':
                    if (!$oDblSt->rowCount()) {
                        $rta = FALSE;
                    }
                    break;
                default:
                    // En el caso de no existir esta fila, $aDades = FALSE:
                    if ($aDades_local === FALSE) {
                        $this->setNullAllAtributes();
                    } else {
                        $this->setAllAtributes($aDades_local);
                    }
            }
            $rta = TRUE;
        }

        return $rta;
    }

    /**
     * Elimina la fila de la base de datos que corresponde a la clase.
     *
     */
    public function DBEliminar()
    {
        $nom_tabla = $this->getNomTabla();
        if (($this->oDbl->exec("DELETE FROM $nom_tabla WHERE  id_schema = $this->iid_schema AND id_item=$this->iid_item")) === false) {
            $sClauError = 'ActaTribunalDl.eliminar';
            $_SESSION['oGestorErrores']->addErrorAppLastError($this->oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        return true;
    }

    /**
     * Para hacer publico el método setNombre_tabla
     *
     * @param string $nom_tabla
     */
    public function cambiarTabla(string $nom_tabla)
    {
        $this->setNomTabla($nom_tabla);
    }

    /**
     * Para hacer publico el método setO
     *
     * @param string $nom_tabla
     */
    public function cambiarDB($oDbl)
    {
        $this->setoDbl($oDbl);
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
        if (!is_array($aDades)) {
            return;
        }
        if (array_key_exists('id_schema', $aDades)) {
            $this->setId_schema($aDades['id_schema']);
        }
        if (array_key_exists('acta', $aDades)) {
            $this->setActa($aDades['acta']);
        }
        if (array_key_exists('examinador', $aDades)) {
            $this->setExaminador($aDades['examinador']);
        }
        if (array_key_exists('orden', $aDades)) {
            $this->setOrden($aDades['orden']);
        }
        if (array_key_exists('id_item', $aDades)) {
            $this->setId_item($aDades['id_item']);
        }
    }

    /**
     * Establece a 'empty' el valor de todos los atributos
     *
     */
    function setNullAllAtributes()
    {
        $aPK = $this->getPrimary_key();
        $this->setId_schema('');
        $this->setActa('');
        $this->setExaminador('');
        $this->setOrden('');
        $this->setId_item('');
        $this->setPrimary_key($aPK);
    }


    /* MÉTODOS GET y SET --------------------------------------------------------*/

    /**
     * Recupera todos los atributos de ActaTribunalDl en un array
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
     * Recupera la clave primaria de ActaTribunalDl en un array
     *
     * @return array aPrimary_key
     */
    function getPrimary_key()
    {
        if (!isset($this->aPrimary_key)) {
            $this->aPrimary_key = ['id_schema' => $this->iid_schema,
                'id_item' => $this->iid_item,
            ];
        }
        return $this->aPrimary_key;
    }

    /**
     * Establece la clave primaria de ActaTribunalDl en un array
     *
     * @return array aPrimary_key
     */
    public function setPrimary_key($a_id = '')
    {
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                if (($nom_id === 'id_schema') && $val_id !== '') {
                    $this->iid_schema = (int)$val_id;
                }
                if (($nom_id === 'id_item') && $val_id !== '') {
                    $this->iid_item = (int)$val_id;
                }
            }
        }
    }

    /**
     * Recupera el atributo sacta de ActaTribunalDl
     *
     * @return string sacta
     */
    function getActa()
    {
        if (!isset($this->sacta) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->sacta;
    }

    /**
     * Establece el valor del atributo sacta de ActaTribunalDl
     *
     * @param string sacta='' optional
     */
    function setActa($sacta = '')
    {
        $this->sacta = $sacta;
    }

    /**
     * Recupera el atributo sexaminador de ActaTribunalDl
     *
     * @return string sexaminador
     */
    function getExaminador()
    {
        if (!isset($this->sexaminador) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->sexaminador;
    }

    /**
     * Establece el valor del atributo sexaminador de ActaTribunalDl
     *
     * @param string sexaminador='' optional
     */
    function setExaminador($sexaminador = '')
    {
        $this->sexaminador = $sexaminador;
    }

    /**
     * Recupera el atributo iorden de ActaTribunalDl
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
     * Establece el valor del atributo iorden de ActaTribunalDl
     *
     * @param integer iorden='' optional
     */
    function setOrden($iorden = '')
    {
        $this->iorden = $iorden;
    }

    /**
     * Recupera el atributo iid_item de ActaTribunalDl
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
     * Establece el valor del atributo iid_item de ActaTribunalDl
     *
     * @param integer iid_item
     */
    function setId_item($iid_item)
    {
        $this->iid_item = $iid_item;
    }
    /* MÉTODOS GET y SET D'ATRIBUTOS QUE NO SON CAMPOS -----------------------------*/

    /**
     * Retorna una col·lecció d'objectes del tipus DatosCampo
     *
     */
    function getDatosCampos()
    {
        $oActaTribunalDlSet = new Set();

        $oActaTribunalDlSet->add($this->getDatosActa());
        $oActaTribunalDlSet->add($this->getDatosExaminador());
        $oActaTribunalDlSet->add($this->getDatosOrden());
        return $oActaTribunalDlSet->getTot();
    }


    /**
     * Recupera les propietats de l'atribut sacta de ActaTribunalDl
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosActa()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'acta'));
        $oDatosCampo->setEtiqueta(_("acta"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut sexaminador de ActaTribunalDl
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosExaminador()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'examinador'));
        $oDatosCampo->setEtiqueta(_("examinador"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut iorden de ActaTribunalDl
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosOrden()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'orden'));
        $oDatosCampo->setEtiqueta(_("orden"));
        return $oDatosCampo;
    }
}

?>
