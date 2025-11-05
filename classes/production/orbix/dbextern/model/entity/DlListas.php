<?php

namespace dbextern\model\entity;

use core\ClasePropiedades;
use core\DatosCampo;
use core\Set;

/**
 * Para las dl que están en la DBU
 *
 * @author dani
 *
 */
class DlListas extends ClasePropiedades
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /*
    Dl vachar(5)
    nombre_dl varchar(30)
    numero_dl tinyinteger
    abr_r varchar(10)
    numero_r tinyinteger
     */

    /**
     * aPrimary_key de Listas
     *
     * @var array
     */
    private $aPrimary_key;

    /**
     * aDades de Listas
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
     * Numero_dl de Listas
     *
     * @var integer
     */
    private $iNumero_dl;
    /**
     * Numero_r de Listas
     *
     * @var integer
     */
    private $iNumero_r;
    /**
     * Dl de Listas
     *
     * @var string
     */
    private $sDl;
    /**
     * Nombre_dl de Listas
     *
     * @var string
     */
    private $sNombre_dl;
    /**
     * Abr_r de Listas
     *
     * @var string
     */
    private $sAbr_r;

    /* ATRIBUTOS QUE NO SON CAMPOS------------------------------------------------- */


    /* CONSTRUCTOR -------------------------------------------------------------- */

    /**
     * Constructor de la classe.
     * Si només necessita un valor, se li pot passar un integer.
     * En general se li passa un array amb les claus primàries.
     *
     * @param integer|array sDl
     *                        $a_id. Un array con los nombres=>valores de las claves primarias.
     */
    function __construct($a_id = '')
    {
        if (!empty($GLOBALS['oDBListas']) && $GLOBALS['oDBListas'] === 'error') {
            exit(_("no se puede conectar con la base de datos de Listas"));
        }
        $oDbl = $GLOBALS['oDBListas'];
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                if (($nom_id === 'dl') && $val_id !== '') $this->sDl = (string)$val_id;
            }
        } else {
            if (isset($a_id) && $a_id !== '') {
                $this->sDl = $a_id;
                $this->aPrimary_key = array('dl' => $this->sDl);
            }
        }
        $this->setoDbl($oDbl);
        $this->setNomTabla('dbo.q_Aux_Dl');
    }

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/


    /**
     * Guarda los atributos de la clase en la base de datos.
     * Si no existe el registro, hace el insert; Si existe hace el update.
     *
     */
    public function DBGuardar()
    {
        //$oDbl = $this->getoDbl();
        //$nom_tabla = $this->getNomTabla();
        return false;
    }

    /**
     * Carga los campos de la base de datos como atributos de la clase.
     *
     */
    public function DBCarregar($que = null)
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        if (isset($this->sDl)) {
            if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE dl='$this->sDl'")) === false) {
                $sClauError = 'Listas.carregar';
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
        //$oDbl = $this->getoDbl();
        //$nom_tabla = $this->getNomTabla();
        return FALSE;
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
        if (array_key_exists('dl', $aDades)) $this->setDl($aDades['dl']);
        if (array_key_exists('nombre_dl', $aDades)) $this->setNombre_dl($aDades['nombre_dl']);
        if (array_key_exists('numero_dl', $aDades)) $this->setNumero_dl($aDades['numero_dl']);
        if (array_key_exists('abr_r', $aDades)) $this->setAbr_r($aDades['abr_r']);
        if (array_key_exists('numero_r', $aDades)) $this->setNumero_r($aDades['numero_r']);
    }

    /**
     * Establece a 'empty' el valor de todos los atributos
     *
     */
    function setNullAllAtributes()
    {
        $aPK = $this->getPrimary_key();
        $this->setDl('');
        $this->setNombre_dl('');
        $this->setNumero_dl('');
        $this->setAbr_r('');
        $this->setNumero_r('');
        $this->setPrimary_key($aPK);
    }


    /* MÉTODOS GET y SET --------------------------------------------------------*/
    /**
     * Recupera todos los atributos de Listas en un array
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
     * Recupera la clave primaria de Listas en un array
     *
     * @return array aPrimary_key
     */
    function getPrimary_key()
    {
        if (!isset($this->aPrimary_key)) {
            $this->aPrimary_key = array('dl' => $this->sDl);
        }
        return $this->aPrimary_key;
    }

    /**
     * Establece la clave primaria de Listas en un array
     *
     * @return array aPrimary_key
     */
    public function setPrimary_key($a_id = '')
    {
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                if (($nom_id === 'dl') && $val_id !== '') $this->sDl = (string)$val_id;
            }
        }
    }

    /**
     * Recupera el atributo sDl de Listas
     *
     * @return string sDl
     */
    function getDl()
    {
        if (!isset($this->sDl) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->sDl;
    }

    /**
     * Establece el valor del atributo sDl de Listas
     *
     * @param string sDl
     */
    function setDl($sDl)
    {
        $this->sDl = $sDl;
    }

    /**
     * Recupera el atributo sNombre_dl de Listas
     *
     * @return string sNombre_dl
     */
    function getNombre_dl()
    {
        if (!isset($this->sNombre_dl) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->sNombre_dl;
    }

    /**
     * Establece el valor del atributo sNombre_dl de Listas
     *
     * @param string sNombre_dl
     */
    function setNombre_dl($sNombre_dl)
    {
        $this->sNombre_dl = $sNombre_dl;
    }

    /**
     * Recupera el atributo sAbr_r de Listas
     *
     * @return string sAbr_r
     */
    function getAbr_r()
    {
        if (!isset($this->sAbr_r) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->sAbr_r;
    }

    /**
     * Establece el valor del atributo sAbr_r de Listas
     *
     * @param string sAbr_r
     */
    function setAbr_r($sAbr_r)
    {
        $this->sAbr_r = $sAbr_r;
    }

    /**
     * Recupera el atributo iNumero_dl de Listas
     *
     * @return string iNumero_dl
     */
    function getNumero_dl()
    {
        if (!isset($this->iNumero_dl) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->iNumero_dl;
    }

    /**
     * Establece el valor del atributo iNumero_dl de Listas
     *
     * @param string iNumero_dl
     */
    function setNumero_dl($iNumero_dl)
    {
        $this->iNumero_dl = $iNumero_dl;
    }

    /**
     * Recupera el atributo iNumero_r de Listas
     *
     * @return string iNumero_r
     */
    function getNumero_r()
    {
        if (!isset($this->iNumero_r) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->iNumero_r;
    }

    /**
     * Establece el valor del atributo iNumero_r de Listas
     *
     * @param string iNumero_r
     */
    function setNumero_r($iNumero_r)
    {
        $this->iNumero_r = $iNumero_r;
    }

    /* MÉTODOS GET y SET D'ATRIBUTOS QUE NO SON CAMPOS -----------------------------*/

    /**
     * Retorna una col·lecció d'objectes del tipus DatosCampo
     *
     */
    function getDatosCampos()
    {
        $oListasSet = new Set();

        $oListasSet->add($this->getDatosDl());
        $oListasSet->add($this->getDatosNombre_dl());
        $oListasSet->add($this->getDatoiNumero_dl());
        $oListasSet->add($this->getDatosAbr_r());
        $oListasSet->add($this->getDatoiNumero_r());
        return $oListasSet->getTot();
    }

    /**
     * Recupera les propietats de l'atribut sDl de Listas
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosDl()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'dl'));
        $oDatosCampo->setEtiqueta(_("dl"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut sNombre_dl de Listas
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosNombre_dl()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'nombre_dl'));
        $oDatosCampo->setEtiqueta(_("Nombre dl"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut iNumero_dl de Listas
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatoiNumero_dl()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'numero_dl'));
        $oDatosCampo->setEtiqueta(_("Número dl"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut sAbr_r de Listas
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosAbr_r()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'abr_r'));
        $oDatosCampo->setEtiqueta(_("Abreviatura región"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut iNumero_r de Listas
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatoiNumero_r()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'numero_r'));
        $oDatosCampo->setEtiqueta(_("Número región"));
        return $oDatosCampo;
    }
}