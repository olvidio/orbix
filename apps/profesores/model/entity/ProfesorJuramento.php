<?php

namespace profesores\model\entity;

use core\ClasePropiedades;
use core\ConverterDate;
use core\DatosCampo;
use core\Set;
use web\DateTimeLocal;
use web\NullDateTimeLocal;

/**
 * Fitxer amb la Classe que accedeix a la taula d_profesor_juramento
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 08/04/2014
 */

/**
 * Clase que implementa la entidad d_profesor_juramento
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 08/04/2014
 */
class ProfesorJuramento extends ClasePropiedades
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * aPrimary_key de ProfesorJuramento
     *
     * @var array
     */
    private $aPrimary_key;

    /**
     * aDades de ProfesorJuramento
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
     * Id_item de ProfesorJuramento
     *
     * @var integer
     */
    private $iid_item;
    /**
     * Id_nom de ProfesorJuramento
     *
     * @var integer
     */
    private $iid_nom;
    /**
     * F_juramento de ProfesorJuramento
     *
     * @varDateTimeLocal
     */
    private $df_juramento;
    /* ATRIBUTOS QUE NO SON CAMPOS------------------------------------------------- */
    /**
     * oDbl de ProfesorJuramento
     *
     * @var object
     */
    protected $oDbl;
    /**
     * NomTabla de ProfesorJuramento
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
     * @param integer|array iid_item,iid_nom
     *                        $a_id. Un array con los nombres=>valores de las claves primarias.
     */
    function __construct($a_id = '')
    {
        $oDbl = $GLOBALS['oDB'];
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                if (($nom_id === 'id_item') && $val_id !== '') $this->iid_item = (int)$val_id;
                if (($nom_id === 'id_nom') && $val_id !== '') $this->iid_nom = (int)$val_id;
            }
        }
        $this->setoDbl($oDbl);
        $this->setNomTabla('d_profesor_juramento');
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
        $aDades['f_juramento'] = $this->df_juramento;
        array_walk($aDades, 'core\poner_null');

        if ($bInsert === false) {
            //UPDATE
            $update = "
					f_juramento              = :f_juramento";
            if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_nom=$this->iid_nom")) === false) {
                $sClauError = 'ProfesorJuramento.update.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'ProfesorJuramento.update.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return false;
                }
            }
        } else {
            // INSERT
            array_unshift($aDades, $this->iid_nom);
            $campos = "(id_nom,f_juramento)";
            $valores = "(:id_nom,:f_juramento)";
            if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === false) {
                $sClauError = 'ProfesorJuramento.insertar.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'ProfesorJuramento.insertar.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return false;
                }
            }
            $this->iid_item = $oDbl->lastInsertId('d_profesor_juramento_id_item_seq');
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
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        if (isset($this->iid_nom)) {
            if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_nom=$this->iid_nom")) === false) {
                $sClauError = 'ProfesorJuramento.carregar';
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
        if (($oDblSt = $oDbl->exec("DELETE FROM $nom_tabla WHERE id_nom=$this->iid_nom")) === false) {
            $sClauError = 'ProfesorJuramento.eliminar';
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
    function setAllAtributes(array $aDades, $convert = FALSE)
    {
        if (!is_array($aDades)) return;
        if (array_key_exists('id_schema', $aDades)) $this->setId_schema($aDades['id_schema']);
        if (array_key_exists('id_item', $aDades)) $this->setId_item($aDades['id_item']);
        if (array_key_exists('id_nom', $aDades)) $this->setId_nom($aDades['id_nom']);
        if (array_key_exists('f_juramento', $aDades)) $this->setF_juramento($aDades['f_juramento'], $convert);
    }

    /**
     * Establece a 'empty' el valor de todos los atributos
     *
     */
    function setNullAllAtributes()
    {
        $aPK = $this->getPrimary_key();
        $this->setId_schema('');
        $this->setId_item('');
        $this->setId_nom('');
        $this->setF_juramento('');
        $this->setPrimary_key($aPK);
    }


    /* MÉTODOS GET y SET --------------------------------------------------------*/

    /**
     * Recupera todos los atributos de ProfesorJuramento en un array
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
     * Recupera la clave primaria de ProfesorJuramento en un array
     *
     * @return array aPrimary_key
     */
    function getPrimary_key()
    {
        if (!isset($this->aPrimary_key)) {
            $this->aPrimary_key = array('id_item' => $this->iid_item, 'id_nom' => $this->iid_nom);
        }
        return $this->aPrimary_key;
    }

    /**
     * Establece la clave primaria de ProfesorJuramento en un array
     *
     * @return array aPrimary_key
     */
    public function setPrimary_key($a_id = '')
    {
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                if (($nom_id === 'id_item') && $val_id !== '') $this->iid_item = (int)$val_id;
                if (($nom_id === 'id_nom') && $val_id !== '') $this->iid_nom = (int)$val_id;
            }
        }
    }

    /**
     * Recupera el atributo iid_item de ProfesorJuramento
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
     * Establece el valor del atributo iid_item de ProfesorJuramento
     *
     * @param integer iid_item
     */
    function setId_item($iid_item)
    {
        $this->iid_item = $iid_item;
    }

    /**
     * Recupera el atributo iid_nom de ProfesorJuramento
     *
     * @return integer iid_nom
     */
    function getId_nom()
    {
        if (!isset($this->iid_nom) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->iid_nom;
    }

    /**
     * Establece el valor del atributo iid_nom de ProfesorJuramento
     *
     * @param integer iid_nom
     */
    function setId_nom($iid_nom)
    {
        $this->iid_nom = $iid_nom;
    }

    /**
     * Recupera el atributo df_juramento de ProfesorJuramento
     *
     * @returnDateTimeLocal df_juramento
     */
    function getF_juramento()
    {
        if (!isset($this->df_juramento) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        if (empty($this->df_juramento)) {
            return new NullDateTimeLocal();
        }
        $oConverter = new ConverterDate('date', $this->df_juramento);
        return $oConverter->fromPg();
    }

    /**
     * Establece el valor del atributo df_juramento de ProfesorJuramento
     * Si df_juramento es string, y convert=true se convierte usando el formato webDateTimeLocal->getFormat().
     * Si convert es false, df_juramento debe ser un string en formato ISO (Y-m-d). Corresponde al pgstyle de la base de datos.
     *
     * @param DateTimeLocal|string df_juramento='' optional.
     * @param boolean convert=true optional. Si es false, df_juramento debe ser un string en formato ISO (Y-m-d).
     */
    function setF_juramento($df_juramento = '', $convert = true)
    {
        if ($convert === true && !empty($df_juramento)) {
            $oConverter = new ConverterDate('date', $df_juramento);
            $this->df_juramento = $oConverter->toPg();
        } else {
            $this->df_juramento = $df_juramento;
        }
    }
    /* MÉTODOS GET y SET D'ATRIBUTOS QUE NO SON CAMPOS -----------------------------*/

    /**
     * Retorna una col·lecció d'objectes del tipus DatosCampo
     *
     */
    function getDatosCampos()
    {
        $oProfesorJuramentoSet = new Set();

        $oProfesorJuramentoSet->add($this->getDatosF_juramento());
        return $oProfesorJuramentoSet->getTot();
    }


    /**
     * Recupera les propietats de l'atribut df_juramento de ProfesorJuramento
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosF_juramento()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'f_juramento'));
        $oDatosCampo->setEtiqueta(_("fecha del juramento"));
        $oDatosCampo->setTipo('fecha');
        return $oDatosCampo;
    }
}