<?php

namespace config\model\entity;

use core\ClasePropiedades;
use core\DatosCampo;
use core\Set;

/**
 * Fitxer amb la Classe que accedeix a la taula x_config_schema
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 7/5/2019
 */

/**
 * Clase que implementa la entidad x_config_schema
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 7/5/2019
 */
class ConfigSchema extends ClasePropiedades
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * aPrimary_key de ConfigSchema
     *
     * @var array
     */
    private $aPrimary_key;

    /**
     * aDades de ConfigSchema
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
     * Parametro de ConfigSchema
     *
     * @var string
     */
    private $sparametro;
    /**
     * Valor de ConfigSchema
     *
     * @var string
     */
    private $svalor;
    /* ATRIBUTOS QUE NO SON CAMPOS------------------------------------------------- */
    /**
     * oDbl de ConfigSchema
     *
     * @var object
     */
    protected $oDbl;
    /**
     * NomTabla de ConfigSchema
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
     * @param integer|array sparametro
     *                        $a_id. Un array con los nombres=>valores de las claves primarias.
     */
    function __construct($a_id = '')
    {
        $oDbl = $GLOBALS['oDBC'];
        $oDbl_Select = $GLOBALS['oDBC_Select'];
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                if (($nom_id === 'parametro') && $val_id !== '') $this->sparametro = (string)$val_id; // evitem SQL injection fent cast a string
            }
        } else {
            if (isset($a_id) && $a_id !== '') {
                $this->sparametro = $a_id;
                $this->aPrimary_key = array('parametro' => $this->sparametro);
            }
        }
        $this->setoDbl($oDbl);
        $this->setoDbl_Select($oDbl_Select);
        $this->setNomTabla('x_config_schema');
    }

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    /**
     * Guarda los atributos de la clase en la base de datos.
     *
     * @return boolean
     */
    public function DBGuardar()
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->DBCargar('guardar') === FALSE;
        if ($bInsert) {
            $aDades = [];
            $aDades['parametro'] = $this->sparametro;
            $aDades['valor'] = $this->svalor;
            if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla (parametro, valor) VALUES (:parametro, :valor)")) === FALSE) {
                $sClauError = 'ConfigSchema.insertar.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return FALSE;
            }
            if (($oDblSt->execute($aDades)) === FALSE) {
                $sClauError = 'ConfigSchema.insertar.execute';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                return FALSE;
            }
        } else {
            $aDades = [];
            $aDades['valor'] = $this->svalor;
            $aDadesWhere = [];
            $aDadesWhere['parametro'] = $this->sparametro;
            $sUpdate = '';
            foreach ($aDades as $nom => $val) {
                $sUpdate .= "$nom = :$nom, ";
            }
            $sUpdate = substr($sUpdate, 0, -2);
            $sWhere = '';
            foreach ($aDadesWhere as $nom => $val) {
                $sWhere .= "$nom = :$nom AND ";
            }
            $sWhere = substr($sWhere, 0, -5);
            if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $sUpdate WHERE $sWhere")) === FALSE) {
                $sClauError = 'ConfigSchema.update.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return FALSE;
            }
            if (($oDblSt->execute(array_merge($aDades, $aDadesWhere))) === FALSE) {
                $sClauError = 'ConfigSchema.update.execute';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                return FALSE;
            }
        }
        $this->setAllAtributes($aDades);
        return TRUE;
    }

    /**
     * carga los campos de la tabla en los atributos de la clase
     * en un array
     *
     * Si oDbl es false, es que no se encontrado el registro en la base de datos.
     *
     * @param string $que = '' optional . 'guardar' si es para un guardado
     * @return array|boolean
     */
    public function DBCargar(string $que = '')
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        if ($que === 'guardar') {
            if (($oDblSt = $oDbl->prepare("SELECT * FROM $nom_tabla WHERE parametro='$this->sparametro'")) === FALSE) {
                $sClauError = 'ConfigSchema.carregar.guard';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return FALSE;
            }
            if (($oDblSt->execute()) === FALSE) {
                $sClauError = 'ConfigSchema.carregar.execute';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                return FALSE;
            }
            $aDades = $oDblSt->fetchAll();
            $this->aDades = $aDades[0];
            if ($oDblSt->rowCount() === 0) {
                return FALSE;
            }
        } else {
            if (($oDblSt = $oDbl->prepare("SELECT * FROM $nom_tabla WHERE parametro='$this->sparametro'")) === FALSE) {
                $sClauError = 'ConfigSchema.carregar.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return FALSE;
            }
            if (($oDblSt->execute()) === FALSE) {
                $sClauError = 'ConfigSchema.carregar.execute';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                return FALSE;
            }
            $aDades = $oDblSt->fetchAll();
            $this->aDades = $aDades[0] ?? [];
            if ($oDblSt->rowCount() === 0) {
                return FALSE;
            }
        }
        $this->setAllAtributes($this->aDades);
        $this->bLoaded = TRUE;
        return $this->aDades;
    }

    /**
     * Establece el valor de todos los atributos
     * del objeto de una sola vez
     *
     * @param array $aDades
     * @return void
     */
    public function setAllAtributes(array $aDades)
    {
        if (!array_key_exists('parametro', $aDades)) {
            return;
        }
        $this->setParametro($aDades['parametro']);
        $this->setValor($aDades['valor']);
    }

    /**
     * @function getDatosCampos
     *
     * @return array|false
     */
    public function getDatosCampos()
    {
        $oDatosCampos = new Set();
        $oDatosCampos->add($this->getDatosParametro());
        $oDatosCampos->add($this->getDatosValor());
        return $oDatosCampos->getTot();
    }

    /**
     * Recupera les propietats de l'atribut sparametro de ConfigSchema
     * en una classe DatosCampo o també en un array.
     *
     * @return DatosCampo|array
     */
    public function getDatosParametro()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'parametro'));
        $oDatosCampo->setEtiqueta(_("parametro"));
        $oDatosCampo->setTipo("texto");
        $oDatosCampo->setArgument(255);
        $oDatosCampo->setArgument2(0);
        $oDatosCampo->setMenubool(FALSE);
        $oDatosCampo->setLista(FALSE);
        $oDatosCampo->setAyuda("Sugiere poner un nombre con varias palabras separadas por '_'\npara una mejor comprensión");
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut svalor de ConfigSchema
     * en una classe DatosCampo o també en un array.
     *
     * @return DatosCampo|array
     */
    public function getDatosValor()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'valor'));
        $oDatosCampo->setEtiqueta(_("valor"));
        $oDatosCampo->setTipo("texto");
        $oDatosCampo->setArgument(255);
        $oDatosCampo->setArgument2(0);
        $oDatosCampo->setMenubool(FALSE);
        $oDatosCampo->setLista(FALSE);
        $oDatosCampo->setAyuda("Para varios posibles valores, separarlos por una coma: ',' ");
        return $oDatosCampo;
    }

    /* MÉTODOS PRIVADOS ----------------------------------------------------------*/

    /* MÉTODOS GET y SET --------------------------------------------------------*/

    /**
     * Recupera el valor de l'atribut sparametro de ConfigSchema
     *
     * @return string
     */
    public function getParametro()
    {
        if (!isset($this->sparametro) && !$this->bLoaded) {
            $this->DBCargar();
        }
        return $this->sparametro;
    }

    /**
     * Estableix el valor de l'atribut sparametro de ConfigSchema
     *
     * @param string $sparametro
     */
    public function setParametro($sparametro)
    {
        $this->sparametro = $sparametro;
    }

    /**
     * Recupera el valor de l'atribut svalor de ConfigSchema
     *
     * @return string
     */
    public function getValor()
    {
        if (!isset($this->svalor) && !$this->bLoaded) {
            $this->DBCargar();
        }
        return $this->svalor;
    }

    /**
     * Estableix el valor de l'atribut svalor de ConfigSchema
     *
     * @param string $svalor
     */
    public function setValor($svalor)
    {
        $this->svalor = $svalor;
    }
}
