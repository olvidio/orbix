<?php
namespace asignaturas\model\entity;

use core;

/**
 * Fitxer amb la Classe que accedeix a la taula $nom_tabla
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 01/12/2010
 */

/**
 * Clase que implementa la entidad $nom_tabla
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 01/12/2010
 */
class Sector extends core\ClasePropiedades
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * aPrimary_key de Sector
     *
     * @var array
     */
    private $aPrimary_key;

    /**
     * aDades de Sector
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
     * Id_sector de Sector
     *
     * @var integer
     */
    private $iid_sector;
    /**
     * Id_departamento de Sector
     *
     * @var integer
     */
    private $iid_departamento;
    /**
     * Sector de Sector
     *
     * @var string
     */
    private $ssector;
    /* ATRIBUTOS QUE NO SON CAMPOS------------------------------------------------- */
    /* CONSTRUCTOR -------------------------------------------------------------- */

    /**
     * Constructor de la classe.
     * Si només necessita un valor, se li pot passar un integer.
     * En general se li passa un array amb les claus primàries.
     *
     * @param integer|array iid_sector
     *                        $a_id. Un array con los nombres=>valores de las claves primarias.
     */
    function __construct($a_id = '')
    {
        $oDbl = $GLOBALS['oDBPC'];
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                if (($nom_id === 'id_sector') && $val_id !== '') $this->iid_sector = (int)$val_id; // evitem SQL injection fent cast a integer
            }
        } else {
            if (isset($a_id) && $a_id !== '') {
                $this->iid_sector = (integer)$a_id; // evitem SQL injection fent cast a integer
                $this->aPrimary_key = array('id_sector' => $this->iid_sector);
            }
        }
        $this->setoDbl($oDbl);
        $this->setNomTabla('xe_sectores');
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
        $aDades['id_departamento'] = $this->iid_departamento;
        $aDades['sector'] = $this->ssector;
        array_walk($aDades, 'core\poner_null');

        if ($bInsert === false) {
            //UPDATE
            $update = "
					id_departamento          = :id_departamento,
					sector                   = :sector";
            if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_sector='$this->iid_sector'")) === false) {
                $sClauError = 'Sector.update.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'Sector.update.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return false;
                }
            }
        } else {
            // INSERT
            $campos = "(id_departamento,sector)";
            $valores = "(:id_departamento,:sector)";
            if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === false) {
                $sClauError = 'Sector.insertar.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'Sector.insertar.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return false;
                }
            }
            $aDades['id_sector'] = $oDbl->lastInsertId($nom_tabla . '_id_sector_seq');
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
        if (isset($this->iid_sector)) {
            if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_sector='$this->iid_sector'")) === false) {
                $sClauError = 'Sector.carregar';
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
        if (($oDblSt = $oDbl->exec("DELETE FROM $nom_tabla WHERE id_sector='$this->iid_sector'")) === false) {
            $sClauError = 'Sector.eliminar';
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
        if (array_key_exists('id_sector', $aDades)) $this->setId_sector($aDades['id_sector']);
        if (array_key_exists('id_departamento', $aDades)) $this->setId_departamento($aDades['id_departamento']);
        if (array_key_exists('sector', $aDades)) $this->setSector($aDades['sector']);
    }

    /**
     * Establece a 'empty' el valor de todos los atributos
     *
     */
    function setNullAllAtributes()
    {
        $aPK = $this->getPrimary_key();
        $this->setId_sector('');
        $this->setId_departamento('');
        $this->setSector('');
        $this->setPrimary_key($aPK);
    }


    /* MÉTODOS GET y SET --------------------------------------------------------*/
    /**
     * Recupera todos los atributos de Sector en un array
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
     * Recupera la clave primaria de Sector en un array
     *
     * @return array aPrimary_key
     */
    function getPrimary_key()
    {
        if (!isset($this->aPrimary_key)) {
            $this->aPrimary_key = array('id_sector' => $this->iid_sector);
        }
        return $this->aPrimary_key;
    }

    /**
     * Establece la clave primaria de Sector en un array
     *
     * @return array aPrimary_key
     */
    public function setPrimary_key($a_id = '')
    {
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                if (($nom_id == 'id_sector') && $val_id !== '') $this->iid_sector = (int)$val_id; // evitem SQL injection fent cast a integer
            }
        }
    }

    /**
     * Recupera el atributo iid_sector de Sector
     *
     * @return integer iid_sector
     */
    function getId_sector()
    {
        if (!isset($this->iid_sector) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->iid_sector;
    }

    /**
     * Establece el valor del atributo iid_sector de Sector
     *
     * @param integer iid_sector
     */
    function setId_sector($iid_sector)
    {
        $this->iid_sector = $iid_sector;
    }

    /**
     * Recupera el atributo iid_departamento de Sector
     *
     * @return integer iid_departamento
     */
    function getId_departamento()
    {
        if (!isset($this->iid_departamento) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->iid_departamento;
    }

    /**
     * Establece el valor del atributo iid_departamento de Sector
     *
     * @param integer iid_departamento='' optional
     */
    function setId_departamento($iid_departamento = '')
    {
        $this->iid_departamento = $iid_departamento;
    }

    /**
     * Recupera el atributo ssector de Sector
     *
     * @return string ssector
     */
    function getSector()
    {
        if (!isset($this->ssector) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->ssector;
    }

    /**
     * Establece el valor del atributo ssector de Sector
     *
     * @param string ssector='' optional
     */
    function setSector($ssector = '')
    {
        $this->ssector = $ssector;
    }
    /* MÉTODOS GET y SET D'ATRIBUTOS QUE NO SON CAMPOS -----------------------------*/

    /**
     * Retorna una col·lecció d'objectes del tipus DatosCampo
     *
     */
    function getDatosCampos()
    {
        $oSectorSet = new core\Set();

        $oSectorSet->add($this->getDatosId_departamento());
        $oSectorSet->add($this->getDatosSector());
        return $oSectorSet->getTot();
    }


    /**
     * Recupera les propietats de l'atribut iid_departamento de Sector
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosId_departamento()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'id_departamento'));
        $oDatosCampo->setEtiqueta(_("departamento"));
        $oDatosCampo->setTipo('opciones');
        $oDatosCampo->setArgument('asignaturas\model\entity\Departamento');
        $oDatosCampo->setArgument2('getDepartamento'); // método para obtener el valor a mostrar del objeto relacionado.
        $oDatosCampo->setArgument3('getListaDepartamentos');
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut ssector de Sector
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosSector()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'sector'));
        $oDatosCampo->setEtiqueta(_("sector"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(30);
        return $oDatosCampo;
    }
}

?>
