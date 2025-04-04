<?php
namespace usuarios\model\entity;

use core\ClasePropiedades;
use core\DatosCampo;
use core\Set;
use function core\is_true;

/**
 * Fitxer amb la Classe que accedeix a la taula x_locales
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 26/11/2014
 */

/**
 * Clase que implementa la entidad x_locales
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 26/11/2014
 */
class Local extends ClasePropiedades
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * aPrimary_key de Local
     *
     * @var array
     */
    private $aPrimary_key;

    /**
     * aDades de Local
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
     * Id_locale de Local
     *
     * @var string
     */
    private $sid_locale;
    /**
     * Nom Locale de Local
     *
     * @var string
     */
    private $snom_locale;
    /**
     * Idioma de Local
     *
     * @var string
     */
    private $sidioma;
    /**
     * Nom_idioma de Local
     *
     * @var string
     */
    private $snom_idioma;
    /**
     * Activo de Local
     *
     * @var boolean
     */
    private $bactivo;
    /* ATRIBUTOS QUE NO SON CAMPOS------------------------------------------------- */
    /**
     * oDbl de Local
     *
     * @var object
     */
    protected $oDbl;
    /**
     * NomTabla de Local
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
     * @param integer|array sid_locale
     *                        $a_id. Un array con los nombres=>valores de las claves primarias.
     */
    function __construct($a_id = '')
    {
        $oDbl = $GLOBALS['oDBPC'];
        $oDbl_Select = $GLOBALS['oDBPC_Select'];
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                if (($nom_id === 'id_locale') && $val_id !== '') $this->sid_locale = (string)$val_id; // evitem SQL injection fent cast a string
            }
        } else {
            if (isset($a_id) && $a_id !== '') {
                $this->sid_locale = (string)$a_id;
                $this->aPrimary_key = array('id_locale' => $this->sid_locale);
            }
        }
        $this->setoDbl($oDbl);
        $this->setoDbl_Select($oDbl_Select);
        $this->setNomTabla('x_locales');
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
        $aDades['nom_locale'] = $this->snom_locale;
        $aDades['idioma'] = $this->sidioma;
        $aDades['nom_idioma'] = $this->snom_idioma;
        $aDades['activo'] = $this->bactivo;
        array_walk($aDades, 'core\poner_null');
        //para el caso de los boolean FALSE, el pdo(+postgresql) pone string '' en vez de 0. Lo arreglo:
        if (is_true($aDades['activo'])) {
            $aDades['activo'] = 'true';
        } else {
            $aDades['activo'] = 'false';
        }

        if ($bInsert === false) {
            //UPDATE
            $update = "
					nom_locale               = :nom_locale,
					idioma                   = :idioma,
					nom_idioma               = :nom_idioma,
					activo                   = :activo";
            if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_locale='$this->sid_locale'")) === false) {
                $sClauError = 'Local.update.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'Local.update.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return false;
                }
            }
        } else {
            // INSERT
            array_unshift($aDades, $this->sid_locale);
            $campos = "(id_locale,nom_locale,idioma,nom_idioma,activo)";
            $valores = "(:id_locale,:nom_locale,:idioma,:nom_idioma,:activo)";
            if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === false) {
                $sClauError = 'Local.insertar.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'Local.insertar.execute';
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
        if (isset($this->sid_locale)) {
            if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_locale='$this->sid_locale'")) === false) {
                $sClauError = 'Local.carregar';
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
        if (($oDblSt = $oDbl->exec("DELETE FROM $nom_tabla WHERE id_locale='$this->sid_locale'")) === false) {
            $sClauError = 'Local.eliminar';
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
        if (array_key_exists('id_locale', $aDades)) $this->setId_locale($aDades['id_locale']);
        if (array_key_exists('nom_locale', $aDades)) $this->setNom_Locale($aDades['nom_locale']);
        if (array_key_exists('idioma', $aDades)) $this->setIdioma($aDades['idioma']);
        if (array_key_exists('nom_idioma', $aDades)) $this->setNom_idioma($aDades['nom_idioma']);
        if (array_key_exists('activo', $aDades)) $this->setActivo($aDades['activo']);
    }

    /**
     * Establece a 'empty' el valor de todos los atributos
     *
     */
    function setNullAllAtributes()
    {
        $aPK = $this->getPrimary_key();
        $this->setId_locale('');
        $this->setNom_Locale('');
        $this->setIdioma('');
        $this->setNom_idioma('');
        $this->setActivo('');
        $this->setPrimary_key($aPK);
    }


    /* MÉTODOS GET y SET --------------------------------------------------------*/

    /**
     * Recupera todos los atributos de Local en un array
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
     * Recupera la clave primaria de Local en un array
     *
     * @return array aPrimary_key
     */
    function getPrimary_key()
    {
        if (!isset($this->aPrimary_key)) {
            $this->aPrimary_key = array('id_locale' => $this->sid_locale);
        }
        return $this->aPrimary_key;
    }

    /**
     * Establece la clave primaria de Local en un array
     *
     * @return array aPrimary_key
     */
    public function setPrimary_key($a_id = '')
    {
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                if (($nom_id === 'id_locale') && $val_id !== '') $this->sid_locale = $val_id;
            }
        }
    }

    /**
     * Recupera el atributo sid_locale de Local
     *
     * @return string sid_locale
     */
    function getId_locale()
    {
        if (!isset($this->sid_locale) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->sid_locale;
    }

    /**
     * Establece el valor del atributo sid_locale de Local
     *
     * @param string sid_locale
     */
    function setId_locale($sid_locale)
    {
        $this->sid_locale = $sid_locale;
    }

    /**
     * Recupera el atributo snom_locale de Local
     *
     * @return string snom_locale
     */
    function getNom_Locale()
    {
        if (!isset($this->snom_locale) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->snom_locale;
    }

    /**
     * Establece el valor del atributo snom_locale de Local
     *
     * @param string snom_locale='' optional
     */
    function setNom_Locale($snom_locale = '')
    {
        $this->snom_locale = $snom_locale;
    }

    /**
     * Recupera el atributo sidioma de Local
     *
     * @return string sidioma
     */
    function getIdioma()
    {
        if (!isset($this->sidioma) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->sidioma;
    }

    /**
     * Establece el valor del atributo sidioma de Local
     *
     * @param string sidioma='' optional
     */
    function setIdioma($sidioma = '')
    {
        $this->sidioma = $sidioma;
    }

    /**
     * Recupera el atributo snom_idioma de Local
     *
     * @return string snom_idioma
     */
    function getNom_idioma()
    {
        if (!isset($this->snom_idioma) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->snom_idioma;
    }

    /**
     * Establece el valor del atributo snom_idioma de Local
     *
     * @param string snom_idioma='' optional
     */
    function setNom_idioma($snom_idioma = '')
    {
        $this->snom_idioma = $snom_idioma;
    }

    /**
     * Recupera el atributo bactivo de Local
     *
     * @return boolean bactivo
     */
    function getActivo()
    {
        if (!isset($this->bactivo) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->bactivo;
    }

    /**
     * Establece el valor del atributo bactivo de Local
     *
     * @param boolean bactivo='f' optional
     */
    function setActivo($bactivo = 'f')
    {
        $this->bactivo = $bactivo;
    }
    /* MÉTODOS GET y SET D'ATRIBUTOS QUE NO SON CAMPOS -----------------------------*/

    /**
     * Retorna una col·lecció d'objectes del tipus DatosCampo
     *
     */
    function getDatosCampos()
    {
        $oLocalSet = new Set();

        $oLocalSet->add($this->getDatosNom_Locale());
        $oLocalSet->add($this->getDatosIdioma());
        $oLocalSet->add($this->getDatosNom_idioma());
        $oLocalSet->add($this->getDatosActivo());
        return $oLocalSet->getTot();
    }


    /**
     * Recupera les propietats de l'atribut snom_locale de Local
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosNom_Locale()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'nom_locale'));
        $oDatosCampo->setEtiqueta(_("nom_locale"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument('50');
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut sidioma de Local
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosIdioma()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'idioma'));
        $oDatosCampo->setEtiqueta(_("idioma"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument('50');
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut snom_idioma de Local
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosNom_idioma()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'nom_idioma'));
        $oDatosCampo->setEtiqueta(_("nombre idioma"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument('50');
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut bactivo de Local
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosActivo()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'activo'));
        $oDatosCampo->setEtiqueta(_("activo"));
        $oDatosCampo->setTipo('check');
        return $oDatosCampo;
    }
}

?>
