<?php

namespace actividadtarifas\model\entity;

use core\ClasePropiedades;
use core\DatosCampo;
use core\Set;

/**
 * Fitxer amb la Classe que accedeix a la taula xa_tipo_activ_tarifa
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 08/11/2018
 */

/**
 * Clase que implementa la entidad xa_tipo_activ_tarifa
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 08/11/2018
 */
class TipoActivTarifa extends ClasePropiedades
{

    /* CONST -------------------------------------------------------------- */

    // serie
    const S_GENERAL = 1;
    const S_ESTUDIANTE = 2;

    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * aPrimary_key de TipoActivTarifa
     *
     * @var array
     */
    private $aPrimary_key;

    /**
     * aDades de TipoActivTarifa
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
     * Id_schema de TipoActivTarifa
     *
     * @var integer
     */
    private $iid_schema;
    /**
     * Id_item de TipoActivTarifa
     *
     * @var integer
     */
    private $iid_item;
    /**
     * Id_tarifa de TipoActivTarifa
     *
     * @var integer
     */
    private $iid_tarifa;
    /**
     * Id_tipo_activ de TipoActivTarifa
     *
     * @var integer
     */
    private $iid_tipo_activ;
    /**
     * Id_serie de TipoActivTarifa
     *
     * @var integer
     */
    private $iid_serie;
    /* ATRIBUTOS QUE NO SON CAMPOS------------------------------------------------- */
    /**
     * oDbl de TipoActivTarifa
     *
     * @var object
     */
    protected $oDbl;
    /**
     * NomTabla de TipoActivTarifa
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
        $oDbl = $GLOBALS['oDBC'];
        $oDbl_Select = $GLOBALS['oDBC_Select'];
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                if (($nom_id == 'id_item') && $val_id !== '') $this->iid_item = (int)$val_id; 
            }
        } else {
            if (isset($a_id) && $a_id !== '') {
                $this->iid_item = (integer)$a_id; 
                $this->aPrimary_key = array('id_item' => $this->iid_item);
            }
        }
        $this->setoDbl($oDbl);
        $this->setoDbl_Select($oDbl_Select);
        $this->setNomTabla('xa_tipo_activ_tarifa');
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
        $aDades = [];
        $aDades['id_tarifa'] = $this->iid_tarifa;
        $aDades['id_tipo_activ'] = $this->iid_tipo_activ;
        $aDades['id_serie'] = $this->iid_serie;
        array_walk($aDades, 'core\poner_null');

        if ($bInsert === false) {
            //UPDATE
            $update = "
					id_tarifa                = :id_tarifa,
					id_tipo_activ            = :id_tipo_activ,
					id_serie                 = :id_serie";
            if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_item='$this->iid_item'")) === false) {
                $sClauError = 'TipoActivTarifa.update.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'TipoActivTarifa.update.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return false;
                }
            }
        } else {
            // INSERT
            $campos = "(id_tarifa,id_tipo_activ,id_serie)";
            $valores = "(:id_tarifa,:id_tipo_activ,:id_serie)";
            if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === false) {
                $sClauError = 'TipoActivTarifa.insertar.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'TipoActivTarifa.insertar.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return false;
                }
            }
            $this->iid_item = $oDbl->lastInsertId('xa_tipo_activ_tarifa_id_item_seq');
        }
        $this->setAllAttributes($aDades);
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
        if (isset($this->iid_item)) {
            if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_item='$this->iid_item'")) === false) {
                $sClauError = 'TipoActivTarifa.carregar';
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
                        $this->setAllAttributes($aDades);
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
        if (($oDbl->exec("DELETE FROM $nom_tabla WHERE id_item='$this->iid_item'")) === false) {
            $sClauError = 'TipoActivTarifa.eliminar';
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
    function setAllAttributes(array $aDades)
    {
        if (!is_array($aDades)) return;
        if (array_key_exists('id_item', $aDades)) $this->setId_item($aDades['id_item']);
        if (array_key_exists('id_tarifa', $aDades)) $this->setId_tarifa($aDades['id_tarifa']);
        if (array_key_exists('id_tipo_activ', $aDades)) $this->setId_tipo_activ($aDades['id_tipo_activ']);
        if (array_key_exists('id_serie', $aDades)) $this->setId_serie($aDades['id_serie']);
    }

    /**
     * Establece a 'empty' el valor de todos los atributos
     *
     */
    function setNullAllAtributes()
    {
        $aPK = $this->getPrimary_key();
        $this->setId_item('');
        $this->setId_tarifa('');
        $this->setId_tipo_activ('');
        $this->setId_serie('');
        $this->setPrimary_key($aPK);
    }


    /* MÉTODOS GET y SET --------------------------------------------------------*/

    /**
     * Recupera todos los atributos de TipoActivTarifa en un array
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
     * Recupera la clave primaria de TipoActivTarifa en un array
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
     * Establece la clave primaria de TipoActivTarifa en un array
     *
     * @return array aPrimary_key
     */
    public function setPrimary_key($a_id = '')
    {
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                if (($nom_id == 'id_item') && $val_id !== '') $this->iid_item = (int)$val_id; 
            }
        }
    }

    /**
     * Recupera el atributo iid_schema de TipoActivTarifa
     *
     * @return integer iid_schema
     */
    function getId_schema()
    {
        if (!isset($this->iid_schema) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->iid_schema;
    }

    /**
     * Establece el valor del atributo iid_schema de TipoActivTarifa
     *
     * @param integer iid_schema='' optional
     */
    function setId_schema($iid_schema = '')
    {
        $this->iid_schema = $iid_schema;
    }

    /**
     * Recupera el atributo iid_item de TipoActivTarifa
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
     * Establece el valor del atributo iid_item de TipoActivTarifa
     *
     * @param integer iid_item
     */
    function setId_item($iid_item)
    {
        $this->iid_item = $iid_item;
    }

    /**
     * Recupera el atributo iid_tarifa de TipoActivTarifa
     *
     * @return integer iid_tarifa
     */
    function getId_tarifa()
    {
        if (!isset($this->iid_tarifa) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->iid_tarifa;
    }

    /**
     * Establece el valor del atributo iid_tarifa de TipoActivTarifa
     *
     * @param integer iid_tarifa='' optional
     */
    function setId_tarifa($iid_tarifa = '')
    {
        $this->iid_tarifa = $iid_tarifa;
    }

    /**
     * Recupera el atributo iid_tipo_activ de TipoActivTarifa
     *
     * @return integer iid_tipo_activ
     */
    function getId_tipo_activ()
    {
        if (!isset($this->iid_tipo_activ) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->iid_tipo_activ;
    }

    /**
     * Establece el valor del atributo iid_tipo_activ de TipoActivTarifa
     *
     * @param integer iid_tipo_activ='' optional
     */
    function setId_tipo_activ($iid_tipo_activ = '')
    {
        $this->iid_tipo_activ = $iid_tipo_activ;
    }

    /**
     * Recupera el atributo iserie de TipoActivTarifa
     *
     * @return integer iserie
     */
    function getId_serie()
    {
        if (!isset($this->iid_serie) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->iid_serie;
    }

    /**
     * Establece el valor del atributo iserie de TipoActivTarifa
     *
     * @param integer iserie='' optional
     */
    function setId_serie($iserie = '')
    {
        $this->iid_serie = $iserie;
    }

    public function getArraySerie()
    {
        return [
            self::S_GENERAL => _("general"),
            self::S_ESTUDIANTE => _("estudiante"),
        ];
    }

    /* MÉTODOS GET y SET D'ATRIBUTOS QUE NO SON CAMPOS -----------------------------*/

    /**
     * Retorna una col·lecció d'objectes del tipus DatosCampo
     *
     */
    function getDatosCampos()
    {
        $oTipoActivTarifaSet = new Set();

        $oTipoActivTarifaSet->add($this->getDatosId_schema());
        $oTipoActivTarifaSet->add($this->getDatosId_tarifa());
        $oTipoActivTarifaSet->add($this->getDatosId_tipo_activ());
        $oTipoActivTarifaSet->add($this->getDatosId_serie());
        return $oTipoActivTarifaSet->getTot();
    }


    /**
     * Recupera les propietats de l'atribut iid_schema de TipoActivTarifa
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosId_schema()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'id_schema'));
        $oDatosCampo->setEtiqueta(_("id_schema"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut iid_tarifa de TipoActivTarifa
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosId_tarifa()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'id_tarifa'));
        $oDatosCampo->setEtiqueta(_("id_tarifa"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut iid_tipo_activ de TipoActivTarifa
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosId_tipo_activ()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'id_tipo_activ'));
        $oDatosCampo->setEtiqueta(_("id_tipo_activ"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut iserie de TipoActivTarifa
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosId_serie()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'id_serie'));
        $oDatosCampo->setEtiqueta(_("serie"));
        return $oDatosCampo;
    }
}
