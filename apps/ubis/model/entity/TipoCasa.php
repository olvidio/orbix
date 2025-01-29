<?php
namespace ubis\model\entity;

use core\ClasePropiedades;
use core\DatosCampo;
use core\Set;

/**
 * Clase que implementa la entidad xu_tipo_casa
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 01/10/2010
 */
class TipoCasa extends ClasePropiedades
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * aPrimary_key de TipoCasa
     *
     * @var array
     */
    private $aPrimary_key;

    /**
     * aDades de TipoCasa
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
     * Tipo_casa de TipoCasa
     *
     * @var string
     */
    private $stipo_casa;
    /**
     * Nombre_tipo_casa de TipoCasa
     *
     * @var string
     */
    private $snombre_tipo_casa;
    /* ATRIBUTOS QUE NO SON CAMPOS------------------------------------------------- */
    /* CONSTRUCTOR -------------------------------------------------------------- */

    /**
     * Constructor de la classe.
     * Si només necessita un valor, se li pot passar un integer.
     * En general se li passa un array amb les claus primàries.
     *
     * @param integer|array stipo_casa
     *                        $a_id. Un array con los nombres=>valores de las claves primarias.
     */
    function __construct($a_id = '')
    {
        $oDbl = $GLOBALS['oDBPC'];
        $oDbl_Select = $GLOBALS['oDBPC_Select'];
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                if (($nom_id === 'tipo_casa') && $val_id !== '') $this->stipo_casa = (string)$val_id;
            }
        } else {
            if (isset($a_id) && $a_id !== '') {
                $this->stipo_casa = (integer)$a_id; 
                $this->aPrimary_key = array('tipo_casa' => $this->stipo_casa);
            }
        }
        $this->setoDbl($oDbl);
        $this->setoDbl_Select($oDbl_Select);
        $this->setNomTabla('xu_tipo_casa');
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
        $aDades['nombre_tipo_casa'] = $this->snombre_tipo_casa;
        array_walk($aDades, 'core\poner_null');

        if ($bInsert === false) {
            //UPDATE
            $update = "
					nombre_tipo_casa         = :nombre_tipo_casa";
            if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE tipo_casa='$this->stipo_casa'")) === false) {
                $sClauError = 'TipoCasa.update.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'TipoCasa.update.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return false;
                }
            }
        } else {
            // INSERT
            array_unshift($aDades, $this->stipo_casa);
            $campos = "(tipo_casa,nombre_tipo_casa)";
            $valores = "(:tipo_casa,:nombre_tipo_casa)";
            if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === false) {
                $sClauError = 'TipoCasa.insertar.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'TipoCasa.insertar.execute';
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
        if (isset($this->stipo_casa)) {
            if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE tipo_casa='$this->stipo_casa'")) === false) {
                $sClauError = 'TipoCasa.carregar';
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
        if (($oDblSt = $oDbl->exec("DELETE FROM $nom_tabla WHERE tipo_casa='$this->stipo_casa'")) === false) {
            $sClauError = 'TipoCasa.eliminar';
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
        if (array_key_exists('tipo_casa', $aDades)) $this->setTipo_casa($aDades['tipo_casa']);
        if (array_key_exists('nombre_tipo_casa', $aDades)) $this->setNombre_tipo_casa($aDades['nombre_tipo_casa']);
    }

    /**
     * Establece a 'empty' el valor de todos los atributos
     *
     */
    function setNullAllAtributes()
    {
        $aPK = $this->getPrimary_key();
        $this->setTipo_casa('');
        $this->setNombre_tipo_casa('');
        $this->setPrimary_key($aPK);
    }


    /* MÉTODOS GET y SET --------------------------------------------------------*/
    /**
     * Recupera todos los atributos de TipoCasa en un array
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
     * Recupera la clave primaria de TipoCasa en un array
     *
     * @return array aPrimary_key
     */
    function getPrimary_key()
    {
        if (!isset($this->aPrimary_key)) {
            $this->aPrimary_key = array('tipo_casa' => $this->stipo_casa);
        }
        return $this->aPrimary_key;
    }

    /**
     * Establece la clave primaria de TipoCasa en un array
     *
     * @return array aPrimary_key
     */
    public function setPrimary_key($a_id = '')
    {
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                if (($nom_id == 'tipo_casa') && $val_id !== '') $this->stipo_casa = $val_id;
            }
        }
    }

    /**
     * Recupera el atributo stipo_casa de TipoCasa
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
     * Establece el valor del atributo stipo_casa de TipoCasa
     *
     * @param string stipo_casa
     */
    function setTipo_casa($stipo_casa)
    {
        $this->stipo_casa = $stipo_casa;
    }

    /**
     * Recupera el atributo snombre_tipo_casa de TipoCasa
     *
     * @return string snombre_tipo_casa
     */
    function getNombre_tipo_casa()
    {
        if (!isset($this->snombre_tipo_casa) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->snombre_tipo_casa;
    }

    /**
     * Establece el valor del atributo snombre_tipo_casa de TipoCasa
     *
     * @param string snombre_tipo_casa='' optional
     */
    function setNombre_tipo_casa($snombre_tipo_casa = '')
    {
        $this->snombre_tipo_casa = $snombre_tipo_casa;
    }
    /* MÉTODOS GET y SET D'ATRIBUTOS QUE NO SON CAMPOS -----------------------------*/

    /**
     * Retorna una col·lecció d'objectes del tipus DatosCampo
     *
     */
    function getDatosCampos()
    {
        $oTipoDeCasaSet = new Set();

        $oTipoDeCasaSet->add($this->getDatosTipo_casa());
        $oTipoDeCasaSet->add($this->getDatosNombre_tipo_casa());
        return $oTipoDeCasaSet->getTot();
    }


    /**
     * Recupera les propietats de l'atribut stipo_casa de TipoDeCasa
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosTipo_casa()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'tipo_casa'));
        $oDatosCampo->setEtiqueta(_("tipo de casa"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(6);
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut snombre_tipo_casa de TipoDeCasa
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosNombre_tipo_casa()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'nombre_tipo_casa'));
        $oDatosCampo->setEtiqueta(_("nombre del tipo de casa"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(30);
        return $oDatosCampo;
    }
}

?>
