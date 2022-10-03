<?php

namespace actividadplazas\model\entity;

use core;

/**
 * Fitxer amb la Classe que accedeix a la taula dap_plazas_peticion_dl
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 22/11/2016
 */

/**
 * Clase que implementa la entidad dap_plazas_peticion_dl
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 22/11/2016
 */
class PlazaPeticion extends core\ClasePropiedades
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * aPrimary_key de PlazaPeticion
     *
     * @var array
     */
    private $aPrimary_key;

    /**
     * aDades de PlazaPeticion
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
     * Id_schema de PlazaPeticion
     *
     * @var integer
     */
    private $iid_schema;
    /**
     * Id_nom de PlazaPeticion
     *
     * @var integer
     */
    private $iid_nom;
    /**
     * Id_activ de PlazaPeticion
     *
     * @var integer
     */
    private $iid_activ;
    /**
     * Orden de PlazaPeticion
     *
     * @var integer
     */
    private $iorden;
    /**
     * Tipo de PlazaPeticion
     *
     * @var string
     */
    private $stipo;
    /* ATRIBUTOS QUE NO SON CAMPOS------------------------------------------------- */
    /**
     * oDbl de PlazaPeticion
     *
     * @var object
     */
    protected $oDbl;
    /**
     * NomTabla de PlazaPeticion
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
     * @param integer|array iid_nom,iid_activ
     *                        $a_id. Un array con los nombres=>valores de las claves primarias.
     */
    function __construct($a_id = '')
    {
        $oDbl = $GLOBALS['oDB'];
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                if (($nom_id == 'id_nom') && $val_id !== '') $this->iid_nom = (int)$val_id; // evitem SQL injection fent cast a integer
                if (($nom_id == 'id_activ') && $val_id !== '') $this->iid_activ = (int)$val_id; // evitem SQL injection fent cast a integer
            }
        }
        $this->setoDbl($oDbl);
        $this->setNomTabla('dap_plazas_peticion_dl');
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
        if ($this->DBCarregar('guardar') === false) {
            $bInsert = true;
        } else {
            $bInsert = false;
        }
        $aDades = array();
        $aDades['orden'] = $this->iorden;
        $aDades['tipo'] = $this->stipo;
        array_walk($aDades, 'core\poner_null');

        if ($bInsert === false) {
            //UPDATE
            $update = "
					orden                    = :orden,
					tipo                     = :tipo";
            if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_nom=$this->iid_nom AND id_activ='$this->iid_activ'")) === false) {
                $sClauError = 'PlazaPeticion.update.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'PlazaPeticion.update.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return false;
                }
            }
        } else {
            // INSERT
            array_unshift($aDades, $this->iid_nom, $this->iid_activ);
            $campos = "(id_nom,id_activ,orden,tipo)";
            $valores = "(:id_nom,:id_activ,:orden,:tipo)";
            if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === false) {
                $sClauError = 'PlazaPeticion.insertar.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'PlazaPeticion.insertar.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return false;
                }
            }
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
        if (isset($this->iid_nom) && isset($this->iid_activ)) {
            if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_nom=$this->iid_nom AND id_activ='$this->iid_activ'")) === false) {
                $sClauError = 'PlazaPeticion.carregar';
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
        if (($oDbl->exec("DELETE FROM $nom_tabla WHERE id_nom=$this->iid_nom AND id_activ='$this->iid_activ'")) === false) {
            $sClauError = 'PlazaPeticion.eliminar';
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
    function setAllAtributes($aDades)
    {
        if (!is_array($aDades)) return;
        if (array_key_exists('id_schema', $aDades)) $this->setId_schema($aDades['id_schema']);
        if (array_key_exists('id_nom', $aDades)) $this->setId_nom($aDades['id_nom']);
        if (array_key_exists('id_activ', $aDades)) $this->setId_activ($aDades['id_activ']);
        if (array_key_exists('orden', $aDades)) $this->setOrden($aDades['orden']);
        if (array_key_exists('tipo', $aDades)) $this->setTipo($aDades['tipo']);
    }

    /**
     * Establece a 'empty' el valor de todos los atributos
     *
     */
    function setNullAllAtributes()
    {
        $aPK = $this->getPrimary_key();
        $this->setId_schema('');
        $this->setId_nom('');
        $this->setId_activ('');
        $this->setOrden('');
        $this->setTipo('');
        $this->setPrimary_key($aPK);
    }


    /* MÉTODOS GET y SET --------------------------------------------------------*/

    /**
     * Recupera todos los atributos de PlazaPeticion en un array
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
     * Recupera la clave primaria de PlazaPeticion en un array
     *
     * @return array aPrimary_key
     */
    function getPrimary_key()
    {
        if (!isset($this->aPrimary_key)) {
            $this->aPrimary_key = array('id_nom' => $this->iid_nom, 'id_activ' => $this->iid_activ);
        }
        return $this->aPrimary_key;
    }

    /**
     * Establece la clave primaria de PlazaPeticion en un array
     *
     * @return array aPrimary_key
     */
    public function setPrimary_key($a_id = '')
    {
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                if (($nom_id == 'id_nom') && $val_id !== '') $this->iid_nom = (int)$val_id; // evitem SQL injection fent cast a integer
                if (($nom_id == 'id_activ') && $val_id !== '') $this->iid_activ = (int)$val_id; // evitem SQL injection fent cast a integer
            }
        }
    }

    /**
     * Recupera el atributo iid_nom de PlazaPeticion
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
     * Establece el valor del atributo iid_nom de PlazaPeticion
     *
     * @param integer iid_nom
     */
    function setId_nom($iid_nom)
    {
        $this->iid_nom = $iid_nom;
    }

    /**
     * Recupera el atributo iid_activ de PlazaPeticion
     *
     * @return integer iid_activ
     */
    function getId_activ()
    {
        if (!isset($this->iid_activ) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->iid_activ;
    }

    /**
     * Establece el valor del atributo iid_activ de PlazaPeticion
     *
     * @param integer iid_activ
     */
    function setId_activ($iid_activ)
    {
        $this->iid_activ = $iid_activ;
    }

    /**
     * Recupera el atributo iorden de PlazaPeticion
     *
     * @return integer iorden
     */
    function getOrden()
    {
        if (!isset($this->iorden) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->iorden;
    }

    /**
     * Establece el valor del atributo iorden de PlazaPeticion
     *
     * @param integer iorden='' optional
     */
    function setOrden($iorden = '')
    {
        $this->iorden = $iorden;
    }

    /**
     * Recupera el atributo stipo de PlazaPeticion
     *
     * @return string stipo
     */
    function getTipo()
    {
        if (!isset($this->stipo) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->stipo;
    }

    /**
     * Establece el valor del atributo stipo de PlazaPeticion
     *
     * @param string stipo='' optional
     */
    function setTipo($stipo = '')
    {
        $this->stipo = $stipo;
    }
    /* MÉTODOS GET y SET D'ATRIBUTOS QUE NO SON CAMPOS -----------------------------*/

    /**
     * Retorna una col·lecció d'objectes del tipus DatosCampo
     *
     */
    function getDatosCampos()
    {
        $oPlazaPeticionSet = new core\Set();

        $oPlazaPeticionSet->add($this->getDatosId_schema());
        $oPlazaPeticionSet->add($this->getDatosOrden());
        $oPlazaPeticionSet->add($this->getDatosTipo());
        return $oPlazaPeticionSet->getTot();
    }


    /**
     * Recupera les propietats de l'atribut iid_schema de PlazaPeticion
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosId_schema()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'id_schema'));
        $oDatosCampo->setEtiqueta(_("id_schema"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut iorden de PlazaPeticion
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosOrden()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'orden'));
        $oDatosCampo->setEtiqueta(_("orden"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut stipo de PlazaPeticion
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosTipo()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'tipo'));
        $oDatosCampo->setEtiqueta(_("tipo"));
        return $oDatosCampo;
    }
}

?>