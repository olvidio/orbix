<?php
namespace menus\model\entity;

use core;

/**
 * Fitxer amb la Classe que accedeix a la taula $nom_tabla
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 15/01/2014
 */

/**
 * Clase que implementa la entidad $nom_tabla
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 15/01/2014
 */
class GrupMenu extends core\ClasePropiedades
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * aPrimary_key de GrupMenu
     *
     * @var array
     */
    private $aPrimary_key;

    /**
     * aDades de GrupMenu
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
     * Id_grupmenu de GrupMenu
     *
     * @var integer
     */
    private $iid_grupmenu;
    /**
     * Grup_menu de GrupMenu
     *
     * @var string
     */
    private $sgrup_menu;
    /**
     * Orden de GrupMenu
     *
     * @var integer
     */
    private $iorden;
    /* ATRIBUTOS QUE NO SON CAMPOS------------------------------------------------- */

    /**
     * Equivalencias de nomenclatura entre la dl => cr
     *
     * @var array
     */
    private $aEquivalencias = [
        'dre' => 'der',
        'vest' => 'dle',
        'scdl' => 'scr',
        'vcd' => 'vcr',
        'vcsd' => 'vcsr',
    ];

    /* CONSTRUCTOR -------------------------------------------------------------- */

    /**
     * Constructor de la classe.
     * Si només necessita un valor, se li pot passar un integer.
     * En general se li passa un array amb les claus primàries.
     *
     * @param integer|array iid_grupmenu
     *                        $a_id. Un array con los nombres=>valores de las claves primarias.
     */
    function __construct($a_id = '')
    {
        $oDbl = $GLOBALS['oDBE'];
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                if (($nom_id == 'id_grupmenu') && $val_id !== '') $this->iid_grupmenu = (int)$val_id; // evitem SQL injection fent cast a integer
            }
        } else {
            if (isset($a_id) && $a_id !== '') {
                $this->iid_grupmenu = (integer)$a_id; // evitem SQL injection fent cast a integer
                $this->aPrimary_key = array('id_grupmenu' => $this->iid_grupmenu);
            }
        }
        $this->setoDbl($oDbl);
        $this->setNomTabla('aux_grupmenu');
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
        $aDades['grup_menu'] = $this->sgrup_menu;
        $aDades['orden'] = $this->iorden;
        array_walk($aDades, 'core\poner_null');

        if ($bInsert === false) {
            //UPDATE
            $update = "
					grup_menu                = :grup_menu,
					orden                    = :orden";
            if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_grupmenu='$this->iid_grupmenu'")) === false) {
                $sClauError = 'GrupMenu.update.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'GrupMenu.update.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return false;
                }
            }
        } else {
            // INSERT
            $campos = "(grup_menu,orden)";
            $valores = "(:grup_menu,:orden)";
            if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === false) {
                $sClauError = 'GrupMenu.insertar.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'GrupMenu.insertar.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return false;
                }
            }
            $this->id_grupmenu = $oDbl->lastInsertId($nom_tabla . '_id_gm_seq');
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
        if (isset($this->iid_grupmenu)) {
            if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_grupmenu='$this->iid_grupmenu'")) === false) {
                $sClauError = 'GrupMenu.carregar';
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
        if (($oDbl->exec("DELETE FROM $nom_tabla WHERE id_grupmenu='$this->iid_grupmenu'")) === false) {
            $sClauError = 'GrupMenu.eliminar';
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
        if (array_key_exists('id_grupmenu', $aDades)) $this->setId_grupmenu($aDades['id_grupmenu']);
        if (array_key_exists('grup_menu', $aDades)) $this->setGrup_menu($aDades['grup_menu']);
        if (array_key_exists('orden', $aDades)) $this->setOrden($aDades['orden']);
    }

    /**
     * Establece a 'empty' el valor de todos los atributos
     *
     */
    function setNullAllAtributes()
    {
        $aPK = $this->getPrimary_key();
        $this->setId_schema('');
        $this->setId_grupmenu('');
        $this->setGrup_menu('');
        $this->setOrden('');
        $this->setPrimary_key($aPK);
    }


    /* MÉTODOS GET y SET --------------------------------------------------------*/
    /**
     * Recupera todos los atributos de GrupMenu en un array
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
     * Recupera la clave primaria de GrupMenu en un array
     *
     * @return array aPrimary_key
     */
    function getPrimary_key()
    {
        if (!isset($this->aPrimary_key)) {
            $this->aPrimary_key = array('id_grupmenu' => $this->iid_grupmenu);
        }
        return $this->aPrimary_key;
    }

    /**
     * Establece la clave primaria de GrupMenu en un array
     *
     * @return array aPrimary_key
     */
    public function setPrimary_key($a_id = '')
    {
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                if (($nom_id == 'id_grupmenu') && $val_id !== '') $this->iid_grupmenu = (int)$val_id; // evitem SQL injection fent cast a integer
            }
        }
    }

    /**
     * Recupera el atributo iid_grupmenu de GrupMenu
     *
     * @return integer iid_grupmenu
     */
    function getId_grupmenu()
    {
        if (!isset($this->iid_grupmenu) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->iid_grupmenu;
    }

    /**
     * Establece el valor del atributo iid_grupmenu de GrupMenu
     *
     * @param integer iid_grupmenu
     */
    function setId_grupmenu($iid_grupmenu)
    {
        $this->iid_grupmenu = $iid_grupmenu;
    }

    /**
     * Recupera el atributo sgrup_menu de GrupMenu
     *
     * @return string sgrup_menu
     */
    function getGrup_menu($dl_r = 'dl')
    {
        if (!isset($this->sgrup_menu) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        $sgrupmenu = $this->sgrup_menu;
        if ($dl_r == 'r' || $dl_r == 'rstgr') {
            if (!empty($this->aEquivalencias[$this->sgrup_menu])) {
                $sgrupmenu = $this->aEquivalencias[$this->sgrup_menu];
            }
        }
        return $sgrupmenu;
    }

    /**
     * Establece el valor del atributo sgrup_menu de GrupMenu
     *
     * @param string sgrup_menu='' optional
     */
    function setGrup_menu($sgrup_menu = '')
    {
        $this->sgrup_menu = $sgrup_menu;
    }

    /**
     * Recupera el atributo iorden de GrupMenu
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
     * Establece el valor del atributo iorden de GrupMenu
     *
     * @param integer iorden='' optional
     */
    function setOrden($iorden = '')
    {
        $this->iorden = $iorden;
    }
    /* MÉTODOS GET y SET D'ATRIBUTOS QUE NO SON CAMPOS -----------------------------*/

    /**
     * Retorna una col·lecció d'objectes del tipus DatosCampo
     *
     */
    function getDatosCampos()
    {
        $oGrupMenuSet = new core\Set();

        $oGrupMenuSet->add($this->getDatosGrup_menu());
        $oGrupMenuSet->add($this->getDatosOrden());
        return $oGrupMenuSet->getTot();
    }


    /**
     * Recupera les propietats de l'atribut sgrup_menu de GrupMenu
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosGrup_menu()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'grup_menu'));
        $oDatosCampo->setEtiqueta(_("grupmenu"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(30);
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut iorden de GrupMenu
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosOrden()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'orden'));
        $oDatosCampo->setEtiqueta(_("orden"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(10);
        return $oDatosCampo;
    }
}

?>
