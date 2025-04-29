<?php

namespace casas\model\entity;

use core\ClasePropiedades;
use core\ConverterDate;
use core\DatosCampo;
use core\Set;
use web\DateTimeLocal;

/**
 * Fitxer amb la Classe que accedeix a la taula du_gastos_dl
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 26/6/2019
 */

/**
 * Clase que implementa la entidad du_gastos_dl
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 26/6/2019
 */
class UbiGasto extends ClasePropiedades
{

    // tipo constants.
    const TIPO_APORTACION_SV = 1; // aportación sv.
    const TIPO_APORTACION_SF = 2; // aportación sf.
    const TIPO_GASTO = 3; // gastos.

    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * aPrimary_key de UbiGasto
     *
     * @var array
     */
    private $aPrimary_key;

    /**
     * aDades de UbiGasto
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
     * Id_item de UbiGasto
     *
     * @var integer
     */
    private $iid_item;
    /**
     * Id_ubi de UbiGasto
     *
     * @var integer
     */
    private $iid_ubi;
    /**
     * F_gasto de UbiGasto
     *
     * @var DateTimeLocal
     */
    private $df_gasto;
    /**
     * Tipo de UbiGasto
     *
     * @var integer
     */
    private $itipo;
    /**
     * Cantidad de UbiGasto
     *
     * @var float
     */
    private $icantidad;
    /* ATRIBUTOS QUE NO SON CAMPOS------------------------------------------------- */
    /**
     * oDbl de UbiGasto
     *
     * @var object
     */
    protected $oDbl;
    /**
     * NomTabla de UbiGasto
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
                if (($nom_id === 'id_item') && $val_id !== '') $this->iid_item = (int)$val_id;
            }
        } else {
            if (isset($a_id) && $a_id !== '') {
                $this->iid_item = (integer)$a_id; 
                $this->aPrimary_key = array('id_item' => $this->iid_item);
            }
        }
        $this->setoDbl($oDbl);
        $this->setoDbl_Select($oDbl_Select);
        $this->setNomTabla('du_gastos_dl');
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
        $aDades['id_ubi'] = $this->iid_ubi;
        $aDades['f_gasto'] = $this->df_gasto;
        $aDades['tipo'] = $this->itipo;
        $aDades['cantidad'] = $this->icantidad;
        array_walk($aDades, 'core\poner_null');

        if ($bInsert === FALSE) {
            //UPDATE
            $update = "
					id_ubi                   = :id_ubi,
					f_gasto                  = :f_gasto,
					tipo                     = :tipo,
					cantidad                 = :cantidad";
            if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_item='$this->iid_item'")) === FALSE) {
                $sClauError = 'UbiGasto.update.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return FALSE;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'UbiGasto.update.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return FALSE;
                }
            }
        } else {
            // INSERT
            $campos = "(id_ubi,f_gasto,tipo,cantidad)";
            $valores = "(:id_ubi,:f_gasto,:tipo,:cantidad)";
            if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === FALSE) {
                $sClauError = 'UbiGasto.insertar.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return FALSE;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'UbiGasto.insertar.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return FALSE;
                }
            }
            $this->iid_item = $oDbl->lastInsertId('du_gastos_dl_id_item_seq');
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
        if (isset($this->iid_item)) {
            if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_item='$this->iid_item'")) === FALSE) {
                $sClauError = 'UbiGasto.carregar';
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
            $sClauError = 'UbiGasto.eliminar';
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
    function setAllAtributes(array $aDades, $convert = FALSE)
    {
        if (!is_array($aDades)) return;
        if (array_key_exists('id_item', $aDades)) $this->setId_item($aDades['id_item']);
        if (array_key_exists('id_ubi', $aDades)) $this->setId_ubi($aDades['id_ubi']);
        if (array_key_exists('f_gasto', $aDades)) $this->setF_gasto($aDades['f_gasto'], $convert);
        if (array_key_exists('tipo', $aDades)) $this->setTipo($aDades['tipo']);
        if (array_key_exists('cantidad', $aDades)) $this->setCantidad($aDades['cantidad']);
    }

    /**
     * Establece a 'empty' el valor de todos los atributos
     *
     */
    function setNullAllAtributes()
    {
        $aPK = $this->getPrimary_key();
        $this->setId_item('');
        $this->setId_ubi('');
        $this->setF_gasto('');
        $this->setTipo('');
        $this->setCantidad('');
        $this->setPrimary_key($aPK);
    }


    /* MÉTODOS GET y SET --------------------------------------------------------*/

    /**
     * Recupera todos los atributos de UbiGasto en un array
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
     * Recupera la clave primaria de UbiGasto en un array
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
     * Establece la clave primaria de UbiGasto en un array
     *
     * @return array aPrimary_key
     */
    public function setPrimary_key($a_id = '')
    {
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                if (($nom_id === 'id_item') && $val_id !== '') $this->iid_item = (int)$val_id;
            }
        }
    }

    /**
     * Recupera el atributo iid_item de UbiGasto
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
     * Establece el valor del atributo iid_item de UbiGasto
     *
     * @param integer iid_item
     */
    function setId_item($iid_item)
    {
        $this->iid_item = $iid_item;
    }

    /**
     * Recupera el atributo iid_ubi de UbiGasto
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
     * Establece el valor del atributo iid_ubi de UbiGasto
     *
     * @param integer iid_ubi='' optional
     */
    function setId_ubi($iid_ubi = '')
    {
        $this->iid_ubi = $iid_ubi;
    }

    /**
     * Recupera el atributo df_gasto de UbiGasto
     *
     * @return DateTimeLocal df_gasto
     */
    function getF_gasto()
    {
        if (!isset($this->df_gasto) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        $oConverter = new ConverterDate('date', $this->df_gasto);
        return $oConverter->fromPg();
    }

    /**
     * Establece el valor del atributo df_gasto de UbiGasto
     * Si df_gasto es string, y convert=true se convierte usando el formato web\DateTimeLocal->getForamat().
     * Si convert es false, df_gasto debe ser un string en formato ISO (Y-m-d). Corresponde al pgstyle de la base de datos.
     *
     * @param DateTimeLocal|string df_gasto='' optional.
     * @param boolean convert=TRUE optional. Si es false, df_ini debe ser un string en formato ISO (Y-m-d).
     */
    function setF_gasto($df_gasto = '', $convert = TRUE)
    {
        if ($convert === TRUE && !empty($df_gasto)) {
            $oConverter = new ConverterDate('date', $df_gasto);
            $this->df_gasto = $oConverter->toPg();
        } else {
            $this->df_gasto = $df_gasto;
        }
    }

    /**
     * Recupera el atributo itipo de UbiGasto
     *
     * @return integer itipo
     */
    function getTipo()
    {
        if (!isset($this->itipo) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->itipo;
    }

    /**
     * Establece el valor del atributo itipo de UbiGasto
     *
     * @param integer itipo='' optional
     */
    function setTipo($itipo = '')
    {
        $this->itipo = $itipo;
    }

    /**
     * Recupera el atributo icantidad de UbiGasto
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
     * Establece el valor del atributo icantidad de UbiGasto
     *
     * @param float icantidad='' optional
     */
    function setCantidad($icantidad = '')
    {
        $this->icantidad = $icantidad;
    }
    /* MÉTODOS GET y SET D'ATRIBUTOS QUE NO SON CAMPOS -----------------------------*/

    /**
     * Retorna una col·lecció d'objectes del tipus DatosCampo
     *
     */
    function getDatosCampos()
    {
        $oUbiGastoSet = new Set();

        $oUbiGastoSet->add($this->getDatosId_ubi());
        $oUbiGastoSet->add($this->getDatosF_gasto());
        $oUbiGastoSet->add($this->getDatosTipo());
        $oUbiGastoSet->add($this->getDatosCantidad());
        return $oUbiGastoSet->getTot();
    }


    /**
     * Recupera les propietats de l'atribut iid_ubi de UbiGasto
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosId_ubi()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'id_ubi'));
        $oDatosCampo->setEtiqueta(_("id_ubi"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut df_gasto de UbiGasto
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosF_gasto()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'f_gasto'));
        $oDatosCampo->setEtiqueta(_("f_gasto"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut itipo de UbiGasto
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosTipo()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'tipo'));
        $oDatosCampo->setEtiqueta(_("tipo"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut icantidad de UbiGasto
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
}
