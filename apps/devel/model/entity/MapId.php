<?php

namespace devel\model\entity;

use core\ClasePropiedades;
use core\DatosCampo;
use core\Set;

/**
 * Fitxer amb la Classe que accedeix a la taula map_id
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 9/3/2020
 */

/**
 * Clase que implementa la entidad map_id
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 9/3/2020
 */
class MapId extends ClasePropiedades
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * aPrimary_key de MapId
     *
     * @var array
     */
    private $aPrimary_key;

    /**
     * aDades de MapId
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
     * oPersonaNota de MapId
     *
     * @var string
     */
    private $sobjeto;
    /**
     * Id_resto de MapId
     *
     * @var integer
     */
    private $iid_resto;
    /**
     * Id_dl de MapId
     *
     * @var integer
     */
    private $iid_dl;
    /* ATRIBUTOS QUE NO SON CAMPOS------------------------------------------------- */
    /**
     * oDbl de MapId
     *
     * @var object
     */
    protected $oDbl;
    /**
     * NomTabla de MapId
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
     * @param integer|array sobjeto,iid_resto
     *                        $a_id. Un array con los nombres=>valores de las claves primarias.
     */
    function __construct($a_id = '')
    {
        $oDbl = $GLOBALS['oDBRC'];
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                if (($nom_id === 'objeto') && $val_id !== '') $this->sobjeto = (string)$val_id; // evitem SQL injection fent cast a string
                if (($nom_id === 'id_resto') && $val_id !== '') $this->iid_resto = (int)$val_id;
            }
        }
        $this->setoDbl($oDbl);
        $this->setNomTabla('map_id');
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
        $aDades['id_dl'] = $this->iid_dl;
        array_walk($aDades, 'core\poner_null');

        if ($bInsert === FALSE) {
            //UPDATE
            $update = "
					id_dl                    = :id_dl";
            if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE objeto='$this->sobjeto' AND id_resto='$this->iid_resto'")) === FALSE) {
                $sClauError = 'MapId.update.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return FALSE;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'MapId.update.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return FALSE;
                }
            }
        } else {
            // INSERT
            array_unshift($aDades, $this->sobjeto, $this->iid_resto);
            $campos = "(objeto,id_resto,id_dl)";
            $valores = "(:objeto,:id_resto,:id_dl)";
            if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === FALSE) {
                $sClauError = 'MapId.insertar.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return FALSE;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'MapId.insertar.execute';
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
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        if (isset($this->sobjeto) && isset($this->iid_resto)) {
            if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE objeto='$this->sobjeto' AND id_resto='$this->iid_resto'")) === FALSE) {
                $sClauError = 'MapId.carregar';
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
        if (($oDbl->exec("DELETE FROM $nom_tabla WHERE objeto='$this->sobjeto' AND id_resto='$this->iid_resto'")) === FALSE) {
            $sClauError = 'MapId.eliminar';
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
        if (array_key_exists('objeto', $aDades)) $this->setObjeto($aDades['objeto']);
        if (array_key_exists('id_resto', $aDades)) $this->setId_resto($aDades['id_resto']);
        if (array_key_exists('id_dl', $aDades)) $this->setId_dl($aDades['id_dl']);
    }

    /**
     * Establece a 'empty' el valor de todos los atributos
     *
     */
    function setNullAllAtributes()
    {
        $aPK = $this->getPrimary_key();
        $this->setObjeto('');
        $this->setId_resto('');
        $this->setId_dl('');
        $this->setPrimary_key($aPK);
    }

    /* MÉTODOS GET y SET --------------------------------------------------------*/

    /**
     * Recupera todos los atributos de MapId en un array
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
     * Recupera la clave primaria de MapId en un array
     *
     * @return array aPrimary_key
     */
    function getPrimary_key()
    {
        if (!isset($this->aPrimary_key)) {
            $this->aPrimary_key = array('objeto' => $this->sobjeto, 'id_resto' => $this->iid_resto);
        }
        return $this->aPrimary_key;
    }

    /**
     * Establece la clave primaria de MapId en un array
     *
     */
    public function setPrimary_key($a_id = '')
    {
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                if (($nom_id === 'objeto') && $val_id !== '') $this->sobjeto = (string)$val_id; // evitem SQL injection fent cast a string
                if (($nom_id === 'id_resto') && $val_id !== '') $this->iid_resto = (int)$val_id;
            }
        }
    }


    /**
     * Recupera el atributo sobjeto de MapId
     *
     * @return string sobjeto
     */
    function getObjeto()
    {
        if (!isset($this->sobjeto) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->sobjeto;
    }

    /**
     * Establece el valor del atributo sobjeto de MapId
     *
     * @param string sobjeto
     */
    function setObjeto($sobjeto)
    {
        $this->sobjeto = $sobjeto;
    }

    /**
     * Recupera el atributo iid_resto de MapId
     *
     * @return integer iid_resto
     */
    function getId_resto()
    {
        if (!isset($this->iid_resto) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->iid_resto;
    }

    /**
     * Establece el valor del atributo iid_resto de MapId
     *
     * @param integer iid_resto
     */
    function setId_resto($iid_resto)
    {
        $this->iid_resto = $iid_resto;
    }

    /**
     * Recupera el atributo iid_dl de MapId
     *
     * @return integer iid_dl
     */
    function getId_dl()
    {
        if (!isset($this->iid_dl) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->iid_dl;
    }

    /**
     * Establece el valor del atributo iid_dl de MapId
     *
     * @param integer iid_dl='' optional
     */
    function setId_dl($iid_dl = '')
    {
        $this->iid_dl = $iid_dl;
    }
    /* MÉTODOS GET y SET D'ATRIBUTOS QUE NO SON CAMPOS -----------------------------*/

    /**
     * Retorna una col·lecció d'objectes del tipus DatosCampo
     *
     */
    function getDatosCampos()
    {
        $oMapIdSet = new Set();

        $oMapIdSet->add($this->getDatosId_dl());
        return $oMapIdSet->getTot();
    }


    /**
     * Recupera les propietats de l'atribut iid_dl de MapId
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosId_dl()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'id_dl'));
        $oDatosCampo->setEtiqueta(_("id_dl"));
        return $oDatosCampo;
    }
}
