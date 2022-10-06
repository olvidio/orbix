<?php

namespace actividadessacd\model\entity;

use core;

/**
 * Fitxer amb la Classe que accedeix a la taula a_sacd_textos
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 26/3/2019
 */

/**
 * Clase que implementa la entidad a_sacd_textos
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 26/3/2019
 */
class AtnActivSacdTexto extends core\ClasePropiedades
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * aPrimary_key de AtnActivSacdTexto
     *
     * @var array
     */
    private $aPrimary_key;

    /**
     * aDades de AtnActivSacdTexto
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
     * Id_item de AtnActivSacdTexto
     *
     * @var integer
     */
    private $iid_item;
    /**
     * Idioma de AtnActivSacdTexto
     *
     * @var string
     */
    private $sidioma;
    /**
     * Clave de AtnActivSacdTexto
     *
     * @var string
     */
    private $sclave;
    /**
     * Texto de AtnActivSacdTexto
     *
     * @var string
     */
    private $stexto;
    /* ATRIBUTOS QUE NO SON CAMPOS------------------------------------------------- */
    /**
     * oDbl de AtnActivSacdTexto
     *
     * @var object
     */
    protected $oDbl;
    /**
     * NomTabla de AtnActivSacdTexto
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
     * @param integer|array iid_item
     *                        $a_id. Un array con los nombres=>valores de las claves primarias.
     */
    function __construct($a_id = '')
    {
        $oDbl = $GLOBALS['oDBE'];
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                if (($nom_id == 'id_item') && $val_id !== '') $this->iid_item = (int)$val_id; // evitem SQL injection fent cast a integer
            }
        } else {
            if (isset($a_id) && $a_id !== '') {
                $this->iid_item = (integer)$a_id; // evitem SQL injection fent cast a integer
                $this->aPrimary_key = array('id_item' => $this->iid_item);
            }
        }
        $this->setoDbl($oDbl);
        $this->setNomTabla('a_sacd_textos');
    }

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    /**
     * Desa els atributs de l'objecte a la base de dades.
     * Si no hi ha el registre, fa el insert, si hi es fa el update.
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
        $aDades = array();
        $aDades['idioma'] = $this->sidioma;
        $aDades['clave'] = $this->sclave;
        $aDades['texto'] = $this->stexto;
        array_walk($aDades, 'core\poner_null');

        if ($bInsert === FALSE) {
            //UPDATE
            $update = "
					idioma                   = :idioma,
					clave                    = :clave,
					texto                    = :texto";
            if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_item='$this->iid_item'")) === FALSE) {
                $sClauError = 'AtnActivSacdTexto.update.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return FALSE;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'AtnActivSacdTexto.update.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return FALSE;
                }
            }
        } else {
            // INSERT
            $campos = "(idioma,clave,texto)";
            $valores = "(:idioma,:clave,:texto)";
            if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === FALSE) {
                $sClauError = 'AtnActivSacdTexto.insertar.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return FALSE;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'AtnActivSacdTexto.insertar.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return FALSE;
                }
            }
            $this->id_item = $oDbl->lastInsertId('a_sacd_textos_id_item_seq');
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
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        if (isset($this->iid_item)) {
            if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_item='$this->iid_item'")) === FALSE) {
                $sClauError = 'AtnActivSacdTexto.carregar';
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
        if (($oDbl->exec("DELETE FROM $nom_tabla WHERE id_item='$this->iid_item'")) === FALSE) {
            $sClauError = 'AtnActivSacdTexto.eliminar';
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
    function setAllAtributes($aDades)
    {
        if (!is_array($aDades)) return;
        if (array_key_exists('id_item', $aDades)) $this->setId_item($aDades['id_item']);
        if (array_key_exists('idioma', $aDades)) $this->setIdioma($aDades['idioma']);
        if (array_key_exists('clave', $aDades)) $this->setClave($aDades['clave']);
        if (array_key_exists('texto', $aDades)) $this->setTexto($aDades['texto']);
    }

    /**
     * Establece a 'empty' el valor de todos los atributos
     *
     */
    function setNullAllAtributes()
    {
        $aPK = $this->getPrimary_key();
        $this->setId_item('');
        $this->setIdioma('');
        $this->setClave('');
        $this->setTexto('');
        $this->setPrimary_key($aPK);
    }


    /* MÉTODOS GET y SET --------------------------------------------------------*/

    /**
     * Recupera todos los atributos de AtnActivSacdTexto en un array
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
     * Recupera la clave primaria de AtnActivSacdTexto en un array
     *
     * @return array aPrimary_key
     */
    function getPrimary_key()
    {
        if (!isset($this->aPrimary_key)) {
            $this->aPrimary_key = array('id_item' => $this->iid_item);
        }
        return $this->aPrimary_key;
    }

    /**
     * Establece la clave primaria de AtnActivSacdTexto  en un array
     *
     * @return array aPrimary_key
     */
    public function setPrimary_key($a_id = '')
    {
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                if (($nom_id == 'id_item') && $val_id !== '') $this->iid_item = (int)$val_id; // evitem SQL injection fent cast a integer
            }
        }
    }

    /**
     * Recupera el atributo iid_item de AtnActivSacdTexto
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
     * Establece el valor del atributo iid_item de AtnActivSacdTexto
     *
     * @param integer iid_item
     */
    function setId_item($iid_item)
    {
        $this->iid_item = $iid_item;
    }

    /**
     * Recupera el atributo sidioma de AtnActivSacdTexto
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
     * Establece el valor del atributo sidioma de AtnActivSacdTexto
     *
     * @param string sidioma='' optional
     */
    function setIdioma($sidioma = '')
    {
        $this->sidioma = $sidioma;
    }

    /**
     * Recupera el atributo sclave de AtnActivSacdTexto
     *
     * @return string sclave
     */
    function getClave()
    {
        if (!isset($this->sclave) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->sclave;
    }

    /**
     * Establece el valor del atributo sclave de AtnActivSacdTexto
     *
     * @param string sclave='' optional
     */
    function setClave($sclave = '')
    {
        $this->sclave = $sclave;
    }

    /**
     * Recupera el atributo stexto de AtnActivSacdTexto
     *
     * @return string stexto
     */
    function getTexto()
    {
        if (!isset($this->stexto) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->stexto;
    }

    /**
     * Establece el valor del atributo stexto de AtnActivSacdTexto
     *
     * @param string stexto='' optional
     */
    function setTexto($stexto = '')
    {
        $this->stexto = $stexto;
    }
    /* MÉTODOS GET y SET D'ATRIBUTOS QUE NO SON CAMPOS -----------------------------*/

    /**
     * Retorna una col·lecció d'objectes del tipus DatosCampo
     *
     */
    function getDatosCampos()
    {
        $oAtnActivSacdTextoSet = new core\Set();

        $oAtnActivSacdTextoSet->add($this->getDatosIdioma());
        $oAtnActivSacdTextoSet->add($this->getDatosClave());
        $oAtnActivSacdTextoSet->add($this->getDatosTexto());
        return $oAtnActivSacdTextoSet->getTot();
    }


    /**
     * Recupera les propietats de l'atribut sidioma de AtnActivSacdTexto
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosIdioma()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'idioma'));
        $oDatosCampo->setEtiqueta(_("idioma"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut sclave de AtnActivSacdTexto
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosClave()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'clave'));
        $oDatosCampo->setEtiqueta(_("clave"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut stexto de AtnActivSacdTexto
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosTexto()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'texto'));
        $oDatosCampo->setEtiqueta(_("texto"));
        return $oDatosCampo;
    }
}
