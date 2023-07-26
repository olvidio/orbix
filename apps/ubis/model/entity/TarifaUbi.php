<?php

namespace ubis\model\entity;

use core;
use core\DatosCampo;
use core\Set;

/**
 * Fitxer amb la Classe que accedeix a la taula du_tarifas
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 6/10/2022
 */

/**
 * Clase que implementa la entidad du_tarifas
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 6/10/2022
 */
class TarifaUbi extends core\ClasePropiedades
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * aPrimary_key de Tarifa
     *
     * @var array
     */
    private $aPrimary_key;

    /**
     * aDades de Tarifa
     *
     * @var array
     */
    private $aDades;

    /**
     * bLoaded de Tarifa
     *
     * @var boolean
     */
    private $bLoaded = FALSE;

    /**
     * Id_schema de Tarifa
     *
     * @var integer
     */
    private $iid_schema;

    /**
     * Id_item de Tarifa
     *
     * @var integer
     */
    private $iid_item;
    /**
     * Id_ubi de Tarifa
     *
     * @var integer
     */
    private $iid_ubi;
    /**
     * Id_tarifa de Tarifa
     *
     * @var integer
     */
    private $iid_tarifa;
    /**
     * Year de Tarifa
     *
     * @var integer
     */
    private $iyear;
    /**
     * Cantidad de Tarifa
     *
     * @var float
     */
    private $icantidad;
    /**
     * Observ de Tarifa
     *
     * @var string
     */
    private $sobserv;
    /**
     * Id_serie de Tarifa
     *
     * @var integer
     */
    private $iid_serie;
    /* ATRIBUTOS QUE NO SON CAMPOS------------------------------------------------- */
    /**
     * oDbl de Tarifa
     *
     * @var object
     */
    protected $oDbl;
    /**
     * NomTabla de Tarifa
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
     * @param integer|array iid_ubi,iid_tarifa,iyear,iid_serie
     *                        $a_id. Un array con los nombres=>valores de las claves primarias.
     */
    function __construct($a_id = '')
    {
        $oDbl = $GLOBALS['oDBC'];
        $oDbl_Select = $GLOBALS['oDBC_Select'];
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                if (($nom_id === 'id_item') && $val_id !== '') {
                    $this->iid_item = (int)$val_id;
                }
            }
        } else {
            if (isset($a_id) && $a_id !== '') {
                $this->iid_item = (integer)$a_id;
                $this->aPrimary_key = array('id_item' => $this->iid_item);
            }
        }
        $this->setoDbl($oDbl);
        $this->setoDbl_Select($oDbl_Select);
        $this->setNomTabla('du_tarifas');
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
        $aDades['id_ubi'] = $this->iid_ubi;
        $aDades['id_tarifa'] = $this->iid_tarifa;
        $aDades['year'] = $this->iyear;
        $aDades['id_serie'] = $this->iid_serie;
        $aDades['cantidad'] = $this->icantidad;
        $aDades['observ'] = $this->sobserv;
        array_walk($aDades, 'core\poner_null');

        if ($bInsert === FALSE) {
            //UPDATE
            $update = "
					id_ubi                 = :id_ubi,
					id_tarifa                 = :id_tarifa,
					year                 = :year,
					id_serie                 = :id_serie,
					cantidad                 = :cantidad,
					observ                   = :observ";
            if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_item='$this->iid_item' ")) === FALSE) {
                $sClauError = 'Tarifa.update.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return FALSE;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'Tarifa.update.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return FALSE;
                }
            }
        } else {
            // INSERT
            $campos = "(id_ubi,id_tarifa,year,id_serie,cantidad,observ)";
            $valores = "(:id_ubi,:id_tarifa,:year,:id_serie,:cantidad,:observ)";
            if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === FALSE) {
                $sClauError = 'Tarifa.insertar.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return FALSE;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'Tarifa.insertar.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return FALSE;
                }
            }
            $this->id_item = $oDbl->lastInsertId('du_tarifas_id_item_seq');
        }
        $this->setAllAtributes($aDades);
        return TRUE;
    }

    /**
     * Carrega els camps de la base de dades com atributs de l'objecte.
     *
     */
    public function DBCarregar($que = null)
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        if (isset($this->iid_item)) {
            if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_item='$this->iid_item' ")) === FALSE) {
                $sClauError = 'Tarifa.carregar';
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
     * Elimina el registre de la base de dades corresponent a l'objecte.
     *
     */
    public function DBEliminar()
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        if (($oDbl->exec("DELETE FROM $nom_tabla WHERE id_item='$this->iid_item' ")) === FALSE) {
            $sClauError = 'Tarifa.eliminar';
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
        if (array_key_exists('id_schema', $aDades)) $this->setId_schema($aDades['id_schema']);
        if (array_key_exists('id_item', $aDades)) $this->setId_item($aDades['id_item']);
        if (array_key_exists('id_ubi', $aDades)) $this->setId_ubi($aDades['id_ubi']);
        if (array_key_exists('id_tarifa', $aDades)) $this->setId_tarifa($aDades['id_tarifa']);
        if (array_key_exists('year', $aDades)) $this->setYear($aDades['year']);
        if (array_key_exists('cantidad', $aDades)) $this->setCantidad($aDades['cantidad']);
        if (array_key_exists('observ', $aDades)) $this->setObserv($aDades['observ']);
        if (array_key_exists('id_serie', $aDades)) $this->setId_serie($aDades['id_serie']);
    }

    /**
     * Establece a empty el valor de todos los atributos
     *
     */
    function setNullAllAtributes()
    {
        $aPK = $this->getPrimary_key();
        $this->setId_schema('');
        $this->setId_item('');
        $this->setId_ubi('');
        $this->setId_tarifa('');
        $this->setYear('');
        $this->setCantidad('');
        $this->setObserv('');
        $this->setId_serie('');
        $this->setPrimary_key($aPK);
    }

    /* MÉTODOS GET y SET --------------------------------------------------------*/

    /**
     * Recupera todos los atributos de Tarifa en un array
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
     * Recupera la clave primaria de Tarifa en un array
     *
     * @return array aPrimary_key
     */
    function getPrimary_key()
    {
        if (!isset($this->aPrimary_key)) {
            $this->aPrimary_key = array('id_ubi' => $this->iid_ubi, 'id_tarifa' => $this->iid_tarifa, 'year' => $this->iyear, 'id_serie' => $this->iid_serie);
        }
        return $this->aPrimary_key;
    }

    /**
     * Establece la clave primaria de Tarifa en un array
     *
     */
    public function setPrimary_key($a_id = '')
    {
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                if (($nom_id == 'id_ubi') && $val_id !== '') $this->iid_ubi = (int)$val_id;
                if (($nom_id == 'id_tarifa') && $val_id !== '') $this->iid_tarifa = (int)$val_id;
                if (($nom_id == 'year') && $val_id !== '') $this->iyear = (int)$val_id;
                if (($nom_id == 'id_serie') && $val_id !== '') $this->iid_serie = (int)$val_id;
            }
        }
    }


    /**
     * Recupera l'atribut iid_item de Tarifa
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
     * estableix el valor de l'atribut iid_item de Tarifa
     *
     * @param integer iid_item='' optional
     */
    function setId_item($iid_item = '')
    {
        $this->iid_item = $iid_item;
    }

    /**
     * Recupera l'atribut iid_ubi de Tarifa
     *
     * @return integer iid_ubi
     */
    function getId_ubi()
    {
        if (!isset($this->iid_ubi) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->iid_ubi;
    }

    /**
     * estableix el valor de l'atribut iid_ubi de Tarifa
     *
     * @param integer iid_ubi
     */
    function setId_ubi($iid_ubi)
    {
        $this->iid_ubi = $iid_ubi;
    }

    /**
     * Recupera l'atribut iid_tarifa de Tarifa
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
     * estableix el valor de l'atribut iid_tarifa de Tarifa
     *
     * @param integer iid_tarifa
     */
    function setId_tarifa($iid_tarifa)
    {
        $this->iid_tarifa = $iid_tarifa;
    }

    /**
     * Recupera l'atribut iyear de Tarifa
     *
     * @return integer iyear
     */
    function getYear()
    {
        if (!isset($this->iyear) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->iyear;
    }

    /**
     * estableix el valor de l'atribut iyear de Tarifa
     *
     * @param integer iyear
     */
    function setYear($iyear)
    {
        $this->iyear = $iyear;
    }

    /**
     * Recupera l'atribut icantidad de Tarifa
     *
     * @return float icantidad
     */
    function getCantidad()
    {
        if (!isset($this->icantidad) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->icantidad;
    }

    /**
     * estableix el valor de l'atribut icantidad de Tarifa
     *
     * @param float icantidad='' optional
     */
    function setCantidad($icantidad = '')
    {
        $this->icantidad = $icantidad;
    }

    /**
     * Recupera l'atribut sobserv de Tarifa
     *
     * @return string sobserv
     */
    function getObserv()
    {
        if (!isset($this->sobserv) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->sobserv;
    }

    /**
     * estableix el valor de l'atribut sobserv de Tarifa
     *
     * @param string sobserv='' optional
     */
    function setObserv($sobserv = '')
    {
        $this->sobserv = $sobserv;
    }

    /**
     * Recupera l'atribut iid_serie de Tarifa
     *
     * @return integer iid_serie
     */
    function getId_serie()
    {
        if (!isset($this->iid_serie) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->iid_serie;
    }

    /**
     * estableix el valor de l'atribut iid_serie de Tarifa
     *
     * @param integer iid_serie
     */
    function setId_serie($iid_serie)
    {
        $this->iid_serie = $iid_serie;
    }
    /* MÉTODOS GET y SET D'ATRIBUTOS QUE NO SON CAMPOS -----------------------------*/

    /**
     * Retorna una col·lecció d'objectes del tipus DatosCampo
     *
     */
    function getDatosCampos()
    {
        $oTarifaSet = new Set();

        $oTarifaSet->add($this->getDatosId_item());
        $oTarifaSet->add($this->getDatosCantidad());
        $oTarifaSet->add($this->getDatosObserv());
        return $oTarifaSet->getTot();
    }


    /**
     * Recupera les propietats de l'atribut iid_item de Tarifa
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosId_item()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'id_item'));
        $oDatosCampo->setEtiqueta(_("id_item"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut icantidad de Tarifa
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosCantidad()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'cantidad'));
        $oDatosCampo->setEtiqueta(_("cantidad"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut sobserv de Tarifa
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosObserv()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'observ'));
        $oDatosCampo->setEtiqueta(_("observ"));
        return $oDatosCampo;
    }
}
