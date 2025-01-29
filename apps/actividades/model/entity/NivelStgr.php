<?php
namespace actividades\model\entity;

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
class NivelStgr extends ClasePropiedades
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * aPrimary_key de NivelStgr
     *
     * @var array
     */
    private $aPrimary_key;

    /**
     * aDades de NivelStgr
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
     * Nivel_stgr de NivelStgr
     *
     * @var integer
     */
    private $inivel_stgr;
    /**
     * Desc_nivel de NivelStgr
     *
     * @var string
     */
    private $sdesc_nivel;
    /**
     * Desc_breve de NivelStgr
     *
     * @var string
     */
    private $sdesc_breve;
    /**
     * Orden de NivelStgr
     *
     * @var integer
     */
    private $iorden;
    /* ATRIBUTOS QUE NO SON CAMPOS------------------------------------------------- */

    /* CONSTRUCTOR -------------------------------------------------------------- */

    /**
     * Constructor de la classe.
     * Si només necessita un valor, se li pot passar un integer.
     * En general se li passa un array amb les claus primàries.
     *
     * @param integer|array inivel_stgr
     *                        $a_id. Un array con los nombres=>valores de las claves primarias.
     */
    function __construct($a_id = '')
    {
        $oDbl = $GLOBALS['oDBPC'];
        $oDbl_Select = $GLOBALS['oDBPC_Select'];
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                if (($nom_id === 'nivel_stgr') && $val_id !== '') $this->inivel_stgr = (int)$val_id;
            }
        } else {
            if (isset($a_id) && $a_id !== '') {
                $this->inivel_stgr = (integer)$a_id;
                $this->aPrimary_key = array('inivel_stgr' => $this->inivel_stgr);
            }
        }
        $this->setoDbl($oDbl);
        $this->setoDbl_Select($oDbl_Select);
        $this->setNomTabla('xa_nivel_stgr');
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
        $aDades['desc_nivel'] = $this->sdesc_nivel;
        $aDades['desc_breve'] = $this->sdesc_breve;
        $aDades['orden'] = $this->iorden;
        array_walk($aDades, 'core\poner_null');

        if ($bInsert === false) {
            //UPDATE
            $update = "
					desc_nivel               = :desc_nivel,
					desc_breve               = :desc_breve,
					orden                    = :orden";
            if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE nivel_stgr='$this->inivel_stgr'")) === false) {
                $sClauError = 'NivelStgr.update.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'NivelStgr.update.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return false;
                }
            }
        } else {
            // INSERT
            $campos = "(desc_nivel,desc_breve,orden)";
            $valores = "(:desc_nivel,:desc_breve,:orden)";
            if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === false) {
                $sClauError = 'NivelStgr.insertar.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'NivelStgr.insertar.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return false;
                }
            }
            $aDades['nivel_stgr'] = $oDbl->lastInsertId($nom_tabla . '_nivel_stgr_seq');
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
        if (isset($this->inivel_stgr)) {
            if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE nivel_stgr='$this->inivel_stgr'")) === false) {
                $sClauError = 'NivelStgr.carregar';
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
        if (($oDbl->exec("DELETE FROM $nom_tabla WHERE nivel_stgr='$this->inivel_stgr'")) === false) {
            $sClauError = 'NivelStgr.eliminar';
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
        if (array_key_exists('nivel_stgr', $aDades)) $this->setNivel_stgr($aDades['nivel_stgr']);
        if (array_key_exists('desc_nivel', $aDades)) $this->setDesc_nivel($aDades['desc_nivel']);
        if (array_key_exists('desc_breve', $aDades)) $this->setDesc_breve($aDades['desc_breve']);
        if (array_key_exists('orden', $aDades)) $this->setOrden($aDades['orden']);
    }

    /**
     * Establece a 'empty' el valor de todos los atributos
     *
     */
    function setNullAllAtributes()
    {
        $aPK = $this->getPrimary_key();
        $this->setNivel_stgr('');
        $this->setDesc_nivel('');
        $this->setDesc_breve('');
        $this->setOrden('');
        $this->setPrimary_key($aPK);
    }


    /* MÉTODOS GET y SET --------------------------------------------------------*/
    /**
     * Recupera todos los atributos de NivelStgr en un array
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
     * Recupera la clave primaria de NivelStgr en un array
     *
     * @return array aPrimary_key
     */
    function getPrimary_key()
    {
        if (!isset($this->aPrimary_key)) {
            $this->aPrimary_key = array('nivel_stgr' => $this->inivel_stgr);
        }
        return $this->aPrimary_key;
    }

    /**
     * Establece la clave primaria de NivelStgr en un array
     *
     * @return array aPrimary_key
     */
    public function setPrimary_key($a_id = '')
    {
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                if (($nom_id === 'nivel_stgr') && $val_id !== '') $this->inivel_stgr = (int)$val_id;
            }
        }
    }

    /**
     * Recupera el atributo inivel_stgr de NivelStgr
     *
     * @return integer inivel_stgr
     */
    function getNivel_stgr()
    {
        if (!isset($this->inivel_stgr) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->inivel_stgr;
    }

    /**
     * Establece el valor del atributo inivel_stgr de NivelStgr
     *
     * @param integer inivel_stgr
     */
    function setNivel_stgr($inivel_stgr)
    {
        $this->inivel_stgr = $inivel_stgr;
    }

    /**
     * Recupera el atributo sdesc_nivel de NivelStgr
     *
     * @return string sdesc_nivel
     */
    function getDesc_nivel()
    {
        if (!isset($this->sdesc_nivel) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->sdesc_nivel;
    }

    /**
     * Establece el valor del atributo sdesc_nivel de NivelStgr
     *
     * @param string sdesc_nivel='' optional
     */
    function setDesc_nivel($sdesc_nivel = '')
    {
        $this->sdesc_nivel = $sdesc_nivel;
    }

    /**
     * Recupera el atributo sdesc_breve de NivelStgr
     *
     * @return string sdesc_breve
     */
    function getDesc_breve()
    {
        if (!isset($this->sdesc_breve) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->sdesc_breve;
    }

    /**
     * Establece el valor del atributo sdesc_breve de NivelStgr
     *
     * @param string sdesc_breve='' optional
     */
    function setDesc_breve($sdesc_breve = '')
    {
        $this->sdesc_breve = $sdesc_breve;
    }

    /**
     * Recupera el atributo iorden de NivelStgr
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
     * Establece el valor del atributo iorden de NivelStgr
     *
     * @param integer iorden='' optional
     */
    function setOrden($iorden = '')
    {
        $this->iorden = $iorden;
    }
    /* MÉTODOS GET y SET D'ATRIBUTOS QUE NO SON CAMPOS -----------------------------*/

    /**
     * Retorna una col·lecció d'objectes del tipus DatosCampo
     *
     */
    function getDatosCampos()
    {
        $oNivelStgrSet = new Set();

        $oNivelStgrSet->add($this->getDatosDesc_nivel());
        $oNivelStgrSet->add($this->getDatosDesc_breve());
        $oNivelStgrSet->add($this->getDatosOrden());
        return $oNivelStgrSet->getTot();
    }


    /**
     * Recupera les propietats de l'atribut sdesc_nivel de NivelStgr
     * en una clase del tipus DatosCampo
     *
     * @return object DatosCampo
     */
    function getDatosDesc_nivel()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'desc_nivel'));
        $oDatosCampo->setEtiqueta(_("desc_nivel"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut sdesc_breve de NivelStgr
     * en una clase del tipus DatosCampo
     *
     * @return object DatosCampo
     */
    function getDatosDesc_breve()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'desc_breve'));
        $oDatosCampo->setEtiqueta(_("desc_breve"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut iorden de NivelStgr
     * en una clase del tipus DatosCampo
     *
     * @return object DatosCampo
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
