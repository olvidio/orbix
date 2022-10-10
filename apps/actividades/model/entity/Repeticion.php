<?php
namespace actividades\model\entity;

use core;

/**
 * Fitxer amb la Classe que accedeix a la taula $nom_tabla
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 04/02/2011
 */

/**
 * Clase que implementa la entidad $nom_tabla
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 04/02/2011
 */
class Repeticion extends core\ClasePropiedades
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * aPrimary_key de Repeticion
     *
     * @var array
     */
    private $aPrimary_key;

    /**
     * aDades de Repeticion
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
     * Id_repeticion de Repeticion
     *
     * @var integer
     */
    private $iid_repeticion;
    /**
     * Repeticion de Repeticion
     *
     * @var string
     */
    private $srepeticion;
    /**
     * Temporada de Repeticion
     *
     * @var string
     */
    private $stemporada;
    /**
     * Tipo de Repeticion
     *
     * @var integer
     */
    private $itipo;
    /* ATRIBUTOS QUE NO SON CAMPOS------------------------------------------------- */

    /* CONSTRUCTOR -------------------------------------------------------------- */

    /**
     * Constructor de la classe.
     * Si només necessita un valor, se li pot passar un integer.
     * En general se li passa un array amb les claus primàries.
     *
     * @param integer|array iid_repeticion
     *                        $a_id. Un array con los nombres=>valores de las claves primarias.
     */
    function __construct($a_id = '')
    {
        $oDbl = $GLOBALS['oDBPC'];
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                if (($nom_id == 'id_repeticion') && $val_id !== '') $this->iid_repeticion = (int)$val_id; 
            }
        } else {
            if (isset($a_id) && $a_id !== '') {
                $this->iid_repeticion = (integer)$a_id; 
                $this->aPrimary_key = array('id_repeticion' => $this->iid_repeticion);
            }
        }
        $this->setoDbl($oDbl);
        $this->setNomTabla('xa_tipo_repeticion');
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
        $aDades['repeticion'] = $this->srepeticion;
        $aDades['temporada'] = $this->stemporada;
        $aDades['tipo'] = $this->itipo;
        array_walk($aDades, 'core\poner_null');

        if ($bInsert === false) {
            //UPDATE
            $update = "
					repeticion               = :repeticion,
					temporada                = :temporada,
					tipo                = :tipo";
            if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_repeticion='$this->iid_repeticion'")) === false) {
                $sClauError = 'Repeticion.update.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'Repeticion.update.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return false;
                }
            }
        } else {
            // INSERT
            $campos = "(repeticion,temporada,tipo)";
            $valores = "(:repeticion,:temporada,:tipo)";
            if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === false) {
                $sClauError = 'Repeticion.insertar.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'Repeticion.insertar.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return false;
                }
            }
            $aDades['id_repeticion'] = $oDbl->lastInsertId($nom_tabla . '_id_repeticion_seq');
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
        if (isset($this->iid_repeticion)) {
            if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_repeticion='$this->iid_repeticion'")) === false) {
                $sClauError = 'Repeticion.carregar';
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
        if (($oDbl->exec("DELETE FROM $nom_tabla WHERE id_repeticion='$this->iid_repeticion'")) === false) {
            $sClauError = 'Repeticion.eliminar';
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
        if (array_key_exists('id_repeticion', $aDades)) $this->setId_repeticion($aDades['id_repeticion']);
        if (array_key_exists('repeticion', $aDades)) $this->setRepeticion($aDades['repeticion']);
        if (array_key_exists('temporada', $aDades)) $this->setTemporada($aDades['temporada']);
        if (array_key_exists('tipo', $aDades)) $this->setTipo($aDades['tipo']);
    }

    /**
     * Establece a 'empty' el valor de todos los atributos
     *
     */
    function setNullAllAtributes()
    {
        $aPK = $this->getPrimary_key();
        $this->setId_repeticion('');
        $this->setRepeticion('');
        $this->setTemporada('');
        $this->setTipo('');
        $this->setPrimary_key($aPK);
    }


    /* MÉTODOS GET y SET --------------------------------------------------------*/
    /**
     * Recupera todos los atributos de Repeticion en un array
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
     * Recupera la clave primaria de Repeticion en un array
     *
     * @return array aPrimary_key
     */
    function getPrimary_key()
    {
        if (!isset($this->aPrimary_key)) {
            $this->aPrimary_key = array('id_repeticion' => $this->iid_repeticion);
        }
        return $this->aPrimary_key;
    }

    /**
     * Establece la clave primaria de ARepeticion en un array
     *
     * @return array aPrimary_key
     */
    public function setPrimary_key($a_id = '')
    {
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                if (($nom_id == 'id_repeticion') && $val_id !== '') $this->iid_repeticion = (int)$val_id; 
            }
        }
    }

    /**
     * Recupera el atributo iid_repeticion de Repeticion
     *
     * @return integer iid_repeticion
     */
    function getId_repeticion()
    {
        if (!isset($this->iid_repeticion) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->iid_repeticion;
    }

    /**
     * Establece el valor del atributo iid_repeticion de Repeticion
     *
     * @param integer iid_repeticion
     */
    function setId_repeticion($iid_repeticion)
    {
        $this->iid_repeticion = $iid_repeticion;
    }

    /**
     * Recupera el atributo srepeticion de Repeticion
     *
     * @return string srepeticion
     */
    function getRepeticion()
    {
        if (!isset($this->srepeticion) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->srepeticion;
    }

    /**
     * Establece el valor del atributo srepeticion de Repeticion
     *
     * @param string srepeticion='' optional
     */
    function setRepeticion($srepeticion = '')
    {
        $this->srepeticion = $srepeticion;
    }

    /**
     * Recupera el atributo stemporada de Repeticion
     *
     * @return string stemporada
     */
    function getTemporada()
    {
        if (!isset($this->stemporada) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->stemporada;
    }

    /**
     * Establece el valor del atributo stemporada de Repeticion
     *
     * @param string stemporada='' optional
     */
    function setTemporada($stemporada = '')
    {
        $this->stemporada = $stemporada;
    }

    /**
     * Recupera el atributo itipo de Repeticion
     *
     * @return string itipo
     */
    function getTipo()
    {
        if (!isset($this->itipo) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->itipo;
    }

    /**
     * Establece el valor del atributo itipo de Repeticion
     *
     * @param string itipo='' optional
     */
    function setTipo($itipo = '')
    {
        $this->itipo = $itipo;
    }
    /* MÉTODOS GET y SET D'ATRIBUTOS QUE NO SON CAMPOS -----------------------------*/

    /**
     * Retorna una col·lecció d'objectes del tipus DatosCampo
     *
     */
    function getDatosCampos()
    {
        $oRepeticionSet = new core\Set();

        $oRepeticionSet->add($this->getDatosRepeticion());
        $oRepeticionSet->add($this->getDatosTemporada());
        $oRepeticionSet->add($this->getDatosTipo());
        return $oRepeticionSet->getTot();
    }


    /**
     * Recupera les propietats de l'atribut srepeticion de Repeticion
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosRepeticion()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'repeticion'));
        $oDatosCampo->setEtiqueta(_("repetición"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(30);
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut stemporada de Repeticion
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosTemporada()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'temporada'));
        $oDatosCampo->setEtiqueta(_("temporada"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(1);
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut stipo de Repeticion
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosTipo()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'tipo'));
        $oDatosCampo->setEtiqueta(_("tipo"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(1);
        return $oDatosCampo;
    }
}

?>
