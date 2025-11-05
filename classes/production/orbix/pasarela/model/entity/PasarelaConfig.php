<?php

namespace pasarela\model\entity;

use core\ClasePropiedades;
use core\DatosCampo;
use core\Set;
use stdClass;

/**
 * Fitxer amb la Classe que accedeix a la taula pasarela_dl
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 21/9/2022
 */

/**
 * Clase que implementa la entidad pasarela_dl
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 21/9/2022
 */
class PasarelaConfig extends ClasePropiedades
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * aPrimary_key de PasarelaConfig
     *
     * @var array
     */
    private $aPrimary_key;

    /**
     * aDades de PasarelaConfig
     *
     * @var array
     */
    private $aDades;

    /**
     * bLoaded de PasarelaConfig
     *
     * @var boolean
     */
    private $bLoaded = FALSE;

    /**
     * Id_schema de PasarelaConfig
     *
     * @var integer
     */
    private $iid_schema;

    /**
     * Nom_parametro de PasarelaConfig
     *
     * @var string
     */
    private $snom_parametro;
    /**
     * Valor de PasarelaConfig
     *
     * @var object JSON
     */
    private $json_valor;
    /* ATRIBUTOS QUE NO SON CAMPOS------------------------------------------------- */
    /**
     * oDbl de PasarelaConfig
     *
     * @var object
     */
    protected $oDbl;
    /**
     * NomTabla de PasarelaConfig
     *
     * @var string
     */
    protected $sNomTabla;
    /* CONSTRUCTOR -------------------------------------------------------------- */

    /**
     * Constructor de la classe.
     * Si només necessita un valor, se li pot passar un string.
     * En general se li passa un array amb les claus primàries.
     *
     * @param string|array snom_parametro
     *                        $a_id. Un array con los nombres=>valores de las claves primarias.
     */
    function __construct($a_id = '')
    {
        $oDbl = $GLOBALS['oDBC'];
        $oDbl_Select = $GLOBALS['oDBC_Select'];
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                if (($nom_id === 'nom_parametro') && $val_id !== '') $this->snom_parametro = (string)$val_id; // evitem SQL injection fent cast a string
            }
        } else {
            if (isset($a_id) && $a_id !== '') {
                $this->snom_parametro = (string)$a_id; // evitem SQL injection fent cast a string
                $this->aPrimary_key = array('snom_parametro' => $this->snom_parametro);
            }
        }
        $this->setoDbl($oDbl);
        $this->setoDbl_Select($oDbl_Select);
        $this->setNomTabla('pasarela_dl');
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
        if ($this->DBCarregar('guardar') === FALSE) {
            $bInsert = TRUE;
        } else {
            $bInsert = FALSE;
        }
        $aDades = [];
        $aDades['json_valor'] = $this->json_valor;
        array_walk($aDades, 'core\poner_null');

        if ($bInsert === FALSE) {
            //UPDATE
            $update = "
					json_valor            = :json_valor";
            if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE nom_parametro='$this->snom_parametro'")) === FALSE) {
                $sClauError = 'PasarelaConfig.update.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return FALSE;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'PasarelaConfig.update.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return FALSE;
                }
            }
        } else {
            // INSERT
            array_unshift($aDades, $this->snom_parametro);
            $campos = "(nom_parametro,json_valor)";
            $valores = "(:nom_parametro,:json_valor)";
            if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === FALSE) {
                $sClauError = 'PasarelaConfig.insertar.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return FALSE;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'PasarelaConfig.insertar.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return FALSE;
                }
            }
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
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        if (isset($this->snom_parametro)) {
            if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE nom_parametro='$this->snom_parametro'")) === FALSE) {
                $sClauError = 'PasarelaConfig.carregar';
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
        if (($oDbl->exec("DELETE FROM $nom_tabla WHERE nom_parametro='$this->snom_parametro'")) === FALSE) {
            $sClauError = 'PasarelaConfig.eliminar';
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
    function setAllAtributes(array $aDades)
    {
        if (!is_array($aDades)) return;
        if (array_key_exists('id_schema', $aDades)) $this->setId_schema($aDades['id_schema']);
        if (array_key_exists('nom_parametro', $aDades)) $this->setNom_parametro($aDades['nom_parametro']);
        if (array_key_exists('json_valor', $aDades)) $this->setJson_valor($aDades['json_valor']);
    }

    /**
     * Establece a 'empty' el valor de todos los atributos
     *
     */
    function setNullAllAtributes()
    {
        $aPK = $this->getPrimary_key();
        $this->setId_schema('');
        $this->setNom_parametro('');
        $this->setJson_valor('');
        $this->setPrimary_key($aPK);
    }

    /* MÉTODOS GET y SET --------------------------------------------------------*/

    /**
     * Recupera todos los atributos de PasarelaConfig en un array
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
     * Recupera la clave primaria de PasarelaConfig en un array
     *
     * @return array aPrimary_key
     */
    function getPrimary_key()
    {
        if (!isset($this->aPrimary_key)) {
            $this->aPrimary_key = array('nom_parametro' => $this->snom_parametro);
        }
        return $this->aPrimary_key;
    }

    /**
     * Establece la clave primaria de PasarelaConfig en un array
     *
     */
    public function setPrimary_key($a_id = '')
    {
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                if (($nom_id === 'nom_parametro') && $val_id !== '') $this->snom_parametro = (string)$val_id; // evitem SQL injection fent cast a string
            }
        } else {
            if (isset($a_id) && $a_id !== '') {
                $this->snom_parametro = (string)$a_id; // evitem SQL injection fent cast a string
                $this->aPrimary_key = array('snom_parametro' => $this->snom_parametro);
            }
        }
    }


    /**
     * Recupera el atributo snom_parametro de PasarelaConfig
     *
     * @return string snom_parametro
     */
    function getNom_parametro()
    {
        if (!isset($this->snom_parametro) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->snom_parametro;
    }

    /**
     * Establece el valor del atributo snom_parametro de PasarelaConfig
     *
     * @param string snom_parametro
     */
    function setNom_parametro($snom_parametro)
    {
        $this->snom_parametro = $snom_parametro;
    }

    /**
     * Recupera el atributo json_valor de PasarelaConfig json_prot_ref de EscritoDB
     *
     * @param boolean $bArray si hay que devolver un array en vez de un objeto.
     * @return object JSON json_valor
     */
    function getJson_valor(bool $bArray = FALSE)
    {
        if (!isset($this->json_valor) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        $oJSON = json_decode($this->json_valor, $bArray);
        if (empty($oJSON) || $oJSON === '[]') {
            if ($bArray) {
                $oJSON = [];
            } else {
                $oJSON = new stdClass;
            }
        }
        return $oJSON;
    }

    /**
     * Establece el valor del atributo json_valor de PasarelaConfig
     *
     * @param object JSON json_valor
     * @param boolean $db =FALSE optional. Para determinar la variable que se le pasa es ya un objeto json,
     *  o es una variable de php hay que convertirlo. En la base de datos ya es json.
     */
    function setJson_valor($oJSON, bool $db = FALSE)
    {
        if ($db === FALSE) {
            $json = json_encode($oJSON);
        } else {
            $json = $oJSON;
        }
        $this->json_valor = $json;
    }
    /* MÉTODOS GET y SET D'ATRIBUTOS QUE NO SON CAMPOS -----------------------------*/

    /**
     * Retorna una col·lecció d'objectes del tipus DatosCampo
     *
     */
    function getDatosCampos()
    {
        $oPasarelaConfigSet = new Set();

        $oPasarelaConfigSet->add($this->getDatosValor());
        return $oPasarelaConfigSet->getTot();
    }


    /**
     * Recupera les propietats de l'atribut json_valor de PasarelaConfig
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosValor()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'json_valor'));
        $oDatosCampo->setEtiqueta(_("valor"));
        return $oDatosCampo;
    }
}
