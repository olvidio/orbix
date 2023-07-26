<?php
namespace ubis\model\entity;

use core;

/**
 * Clase que implementa la entidad xu_tipo_ctr
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 01/10/2010
 */
class TipoCentro extends core\ClasePropiedades
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * aPrimary_key de TipoCentro
     *
     * @var array
     */
    private $aPrimary_key;

    /**
     * aDades de TipoCentro
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
     * Tipo_ctr de TipoCentro
     *
     * @var string
     */
    private $stipo_ctr;
    /**
     * Nombre_tipo_ctr de TipoCentro
     *
     * @var string
     */
    private $snombre_tipo_ctr;
    /* ATRIBUTOS QUE NO SON CAMPOS------------------------------------------------- */
    /* CONSTRUCTOR -------------------------------------------------------------- */

    /**
     * Constructor de la classe.
     * Si només necessita un valor, se li pot passar un integer.
     * En general se li passa un array amb les claus primàries.
     *
     * @param integer|array stipo_ctr
     *                        $a_id. Un array con los nombres=>valores de las claves primarias.
     */
    function __construct($a_id = '')
    {
        $oDbl = $GLOBALS['oDBPC'];
        $oDbl_Select = $GLOBALS['oDBPC_Select'];
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                if (($nom_id === 'tipo_ctr') && $val_id !== '') $this->stipo_ctr = (string)$val_id;
            }
        }
        $this->setoDbl($oDbl);
        $this->setoDbl_Select($oDbl_Select);
        $this->setNomTabla('xu_tipo_ctr');
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
        $aDades['nombre_tipo_ctr'] = $this->snombre_tipo_ctr;
        array_walk($aDades, 'core\poner_null');

        if ($bInsert === false) {
            //UPDATE
            $update = "
					nombre_tipo_ctr          = :nombre_tipo_ctr";
            if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE tipo_ctr='$this->stipo_ctr'")) === false) {
                $sClauError = 'TipoCentro.update.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'TipoCentro.update.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return false;
                }
            }
        } else {
            // INSERT
            array_unshift($aDades, $this->stipo_ctr);
            $campos = "(tipo_ctr,nombre_tipo_ctr)";
            $valores = "(:tipo_ctr,:nombre_tipo_ctr)";
            if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === false) {
                $sClauError = 'TipoCentro.insertar.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'TipoCentro.insertar.execute';
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
        if (isset($this->stipo_ctr)) {
            if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE tipo_ctr='$this->stipo_ctr'")) === false) {
                $sClauError = 'TipoCentro.carregar';
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
        if (($oDblSt = $oDbl->exec("DELETE FROM $nom_tabla WHERE tipo_ctr='$this->stipo_ctr'")) === false) {
            $sClauError = 'TipoCentro.eliminar';
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
        if (array_key_exists('tipo_ctr', $aDades)) $this->setTipo_ctr($aDades['tipo_ctr']);
        if (array_key_exists('nombre_tipo_ctr', $aDades)) $this->setNombre_tipo_ctr($aDades['nombre_tipo_ctr']);
    }

    /**
     * Establece a 'empty' el valor de todos los atributos
     *
     */
    function setNullAllAtributes()
    {
        $aPK = $this->getPrimary_key();
        $this->setTipo_ctr('');
        $this->setNombre_tipo_ctr('');
        $this->setPrimary_key($aPK);
    }


    /* MÉTODOS GET y SET --------------------------------------------------------*/

    /**
     * Recupera todos los atributos de TipoCentro en un array
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
     * Recupera la clave primaria de TipoCentro en un array
     *
     * @return array aPrimary_key
     */
    function getPrimary_key()
    {
        if (!isset($this->aPrimary_key)) {
            $this->aPrimary_key = array('tipo_ctr' => $this->stipo_ctr);
        }
        return $this->aPrimary_key;
    }

    /**
     * Establece la clave primaria de TipoCentro en un array
     *
     * @return array aPrimary_key
     */
    public function setPrimary_key($a_id = '')
    {
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                if (($nom_id == 'tipo_ctr') && $val_id !== '') $this->stipo_ctr = $val_id;
            }
        }
    }

    /**
     * Recupera el atributo stipo_ctr de TipoCentro
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
     * Establece el valor del atributo stipo_ctr de TipoCentro
     *
     * @param string stipo_ctr
     */
    function setTipo_ctr($stipo_ctr)
    {
        $this->stipo_ctr = $stipo_ctr;
    }

    /**
     * Recupera el atributo snombre_tipo_ctr de TipoCentro
     *
     * @return string snombre_tipo_ctr
     */
    function getNombre_tipo_ctr()
    {
        if (!isset($this->snombre_tipo_ctr) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->snombre_tipo_ctr;
    }

    /**
     * Establece el valor del atributo snombre_tipo_ctr de TipoCentro
     *
     * @param string snombre_tipo_ctr='' optional
     */
    function setNombre_tipo_ctr($snombre_tipo_ctr = '')
    {
        $this->snombre_tipo_ctr = $snombre_tipo_ctr;
    }
    /* MÉTODOS GET y SET D'ATRIBUTOS QUE NO SON CAMPOS -----------------------------*/

    /**
     * Retorna una col·lecció d'objectes del tipus DatosCampo
     *
     */
    function getDatosCampos()
    {
        $oTipoCentroSet = new core\Set();

        $oTipoCentroSet->add($this->getDatosTipo_ctr());
        $oTipoCentroSet->add($this->getDatosNombre_tipo_ctr());
        return $oTipoCentroSet->getTot();
    }

    /**
     * Recupera les propietats de l'atribut stipo_ctr de TipoCentro
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosTipo_ctr()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'tipo_ctr'));
        $oDatosCampo->setEtiqueta(_("tipo de centro"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(6);
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut snombre_tipo_ctr de TipoCentro
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosNombre_tipo_ctr()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'nombre_tipo_ctr'));
        $oDatosCampo->setEtiqueta(_("nombre de tipo centro"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(30);
        return $oDatosCampo;
    }
}

?>
