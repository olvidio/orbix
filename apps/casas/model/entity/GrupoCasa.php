<?php

namespace casas\model\entity;

use core;

/**
 * Fitxer amb la Classe que accedeix a la taula du_grupos_dl
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 1/7/2019
 */

/**
 * Clase que implementa la entidad du_grupos_dl
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 1/7/2019
 */
class GrupoCasa extends core\ClasePropiedades
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * aPrimary_key de GrupoCasa
     *
     * @var array
     */
    private $aPrimary_key;

    /**
     * aDades de GrupoCasa
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
     * Id_item de GrupoCasa
     *
     * @var integer
     */
    private $iid_item;
    /**
     * Id_ubi_padre de GrupoCasa
     *
     * @var integer
     */
    private $iid_ubi_padre;
    /**
     * Id_ubi_hijo de GrupoCasa
     *
     * @var integer
     */
    private $iid_ubi_hijo;
    /* ATRIBUTOS QUE NO SON CAMPOS------------------------------------------------- */
    /**
     * oDbl de GrupoCasa
     *
     * @var object
     */
    protected $oDbl;
    /**
     * NomTabla de GrupoCasa
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
        $this->setNomTabla('du_grupos_dl');
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
        $aDades = array();
        $aDades['id_ubi_padre'] = $this->iid_ubi_padre;
        $aDades['id_ubi_hijo'] = $this->iid_ubi_hijo;
        array_walk($aDades, 'core\poner_null');

        if ($bInsert === FALSE) {
            //UPDATE
            $update = "
					id_ubi_padre             = :id_ubi_padre,
					id_ubi_hijo              = :id_ubi_hijo";
            if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_item='$this->iid_item'")) === FALSE) {
                $sClauError = 'GrupoCasa.update.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return FALSE;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'GrupoCasa.update.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return FALSE;
                }
            }
        } else {
            // INSERT
            $campos = "(id_ubi_padre,id_ubi_hijo)";
            $valores = "(:id_ubi_padre,:id_ubi_hijo)";
            if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === FALSE) {
                $sClauError = 'GrupoCasa.insertar.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return FALSE;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'GrupoCasa.insertar.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return FALSE;
                }
            }
            $this->id_item = $oDbl->lastInsertId('du_grupos_dl_id_item_seq');
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
                $sClauError = 'GrupoCasa.carregar';
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
            $sClauError = 'GrupoCasa.eliminar';
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
        if (array_key_exists('id_ubi_padre', $aDades)) $this->setId_ubi_padre($aDades['id_ubi_padre']);
        if (array_key_exists('id_ubi_hijo', $aDades)) $this->setId_ubi_hijo($aDades['id_ubi_hijo']);
    }

    /**
     * Establece a 'empty' el valor de todos los atributos
     *
     */
    function setNullAllAtributes()
    {
        $aPK = $this->getPrimary_key();
        $this->setId_item('');
        $this->setId_ubi_padre('');
        $this->setId_ubi_hijo('');
        $this->setPrimary_key($aPK);
    }


    /* MÉTODOS GET y SET --------------------------------------------------------*/

    /**
     * Recupera todos los atributos de GrupoCasa en un array
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
     * Recupera la clave primaria de GrupoCasa en un array
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
     * Establece la clave primaria de GrupoCasa en un array
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
     * Recupera el atributo iid_item de GrupoCasa
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
     * Establece el valor del atributo iid_item de GrupoCasa
     *
     * @param integer iid_item
     */
    function setId_item($iid_item)
    {
        $this->iid_item = $iid_item;
    }

    /**
     * Recupera el atributo iid_ubi_padre de GrupoCasa
     *
     * @return integer iid_ubi_padre
     */
    function getId_ubi_padre()
    {
        if (!isset($this->iid_ubi_padre) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->iid_ubi_padre;
    }

    /**
     * Establece el valor del atributo iid_ubi_padre de GrupoCasa
     *
     * @param integer iid_ubi_padre='' optional
     */
    function setId_ubi_padre($iid_ubi_padre = '')
    {
        $this->iid_ubi_padre = $iid_ubi_padre;
    }

    /**
     * Recupera el atributo iid_ubi_hijo de GrupoCasa
     *
     * @return integer iid_ubi_hijo
     */
    function getId_ubi_hijo()
    {
        if (!isset($this->iid_ubi_hijo) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->iid_ubi_hijo;
    }

    /**
     * Establece el valor del atributo iid_ubi_hijo de GrupoCasa
     *
     * @param integer iid_ubi_hijo='' optional
     */
    function setId_ubi_hijo($iid_ubi_hijo = '')
    {
        $this->iid_ubi_hijo = $iid_ubi_hijo;
    }
    /* MÉTODOS GET y SET D'ATRIBUTOS QUE NO SON CAMPOS -----------------------------*/

    /**
     * Retorna una col·lecció d'objectes del tipus DatosCampo
     *
     */
    function getDatosCampos()
    {
        $oGrupoCasaSet = new core\Set();

        $oGrupoCasaSet->add($this->getDatosId_ubi_padre());
        $oGrupoCasaSet->add($this->getDatosId_ubi_hijo());
        return $oGrupoCasaSet->getTot();
    }


    /**
     * Recupera les propietats de l'atribut iid_ubi_padre de GrupoCasa
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosId_ubi_padre()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'id_ubi_padre'));
        $oDatosCampo->setEtiqueta(_("id_ubi_padre"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut iid_ubi_hijo de GrupoCasa
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosId_ubi_hijo()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'id_ubi_hijo'));
        $oDatosCampo->setEtiqueta(_("id_ubi_hijo"));
        return $oDatosCampo;
    }
}
