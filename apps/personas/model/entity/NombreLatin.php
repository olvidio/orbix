<?php
namespace personas\model\entity;

use core;

/**
 * Fitxer amb la Classe que accedeix a la taula xe_nombre_latin
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 07/04/2014
 */

/**
 * Clase que implementa la entidad xe_nombre_latin
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 07/04/2014
 */
class NombreLatin extends core\ClasePropiedades
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * aPrimary_key de NombreLatin
     *
     * @var array
     */
    private $aPrimary_key;

    /**
     * aDades de NombreLatin
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
     * Id_item de NombreLatin
     *
     * @var integer
     */
    private $iid_item;
    /**
     * Nom de NombreLatin
     *
     * @var string
     */
    private $snom;
    /**
     * Nominativo de NombreLatin
     *
     * @var string
     */
    private $snominativo;
    /**
     * Genitivo de NombreLatin
     *
     * @var string
     */
    private $sgenitivo;
    /* ATRIBUTOS QUE NO SON CAMPOS------------------------------------------------- */
    /**
     * oDbl de NombreLatin
     *
     * @var object
     */
    protected $oDbl;
    /**
     * NomTabla de NombreLatin
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
     * @param integer|array snom
     *                        $a_id. Un array con los nombres=>valores de las claves primarias.
     */
    function __construct($a_id = '')
    {
        $oDbl = $GLOBALS['oDBP'];
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                if (($nom_id == 'id_item') && $val_id !== '') $this->iid_item = (int)$val_id; // evitem SQL injection fent cast a string
            }
        } else {
            if (isset($a_id) && $a_id !== '') {
                $this->iid_item = (int)$a_id; // evitem SQL injection fent cast a string
                $this->aPrimary_key = array('id_item' => $this->iid_item);
            }
        }
        $this->setoDbl($oDbl);
        $this->setNomTabla('xe_nombre_latin');
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
        $aDades = array();
        $aDades['nom'] = $this->snom;
        $aDades['nominativo'] = $this->snominativo;
        $aDades['genitivo'] = $this->sgenitivo;
        array_walk($aDades, 'core\poner_null');

        if ($bInsert === false) {
            //UPDATE
            $update = "
					nom               		 = :nom,
					nominativo               = :nominativo,
					genitivo                 = :genitivo";
            if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_item=$this->iid_item")) === false) {
                $sClauError = 'NombreLatin.update.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'NombreLatin.update.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return false;
                }
            }
        } else {
            // INSERT
            array_unshift($aDades, $this->iid_item);
            $campos = "(nom,nominativo,genitivo)";
            $valores = "(:nom,:nominativo,:genitivo)";
            if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === false) {
                $sClauError = 'NombreLatin.insertar.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'NombreLatin.insertar.execute';
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
        if (isset($this->iid_item)) {
            if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_item=$this->iid_item")) === false) {
                $sClauError = 'NombreLatin.carregar';
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
        if (($oDbl->exec("DELETE FROM $nom_tabla WHERE id_item=$this->iid_item")) === false) {
            $sClauError = 'NombreLatin.eliminar';
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
        if (array_key_exists('nom', $aDades)) $this->setNom($aDades['nom']);
        if (array_key_exists('nominativo', $aDades)) $this->setNominativo($aDades['nominativo']);
        if (array_key_exists('genitivo', $aDades)) $this->setGenitivo($aDades['genitivo']);
    }

    /**
     * Establece a 'empty' el valor de todos los atributos
     *
     */
    function setNullAllAtributes()
    {
        $aPK = $this->getPrimary_key();
        $this->setNom('');
        $this->setNominativo('');
        $this->setGenitivo('');
        $this->setPrimary_key($aPK);
    }


    /* MÉTODOS GET y SET --------------------------------------------------------*/

    /**
     * Recupera todos los atributos de NombreLatin en un array
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
     * Recupera la clave primaria de NombreLatin en un array
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
     * Establece la clave primaria de NombreLatin en un array
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
     * Recupera el atributo snom de NombreLatin
     *
     * @return string snom
     */
    function getId_item()
    {
        if (!isset($this->iid_item) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->iid_item;
    }

    /**
     * Recupera el atributo snom de NombreLatin
     *
     * @return string snom
     */
    function getNom()
    {
        if (!isset($this->snom) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->snom;
    }

    /**
     * Establece el valor del atributo snom de NombreLatin
     *
     * @param string snom
     */
    function setNom($snom)
    {
        $this->snom = $snom;
    }

    /**
     * Recupera el atributo snominativo de NombreLatin
     *
     * @return string snominativo
     */
    function getNominativo()
    {
        if (!isset($this->snominativo) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->snominativo;
    }

    /**
     * Establece el valor del atributo snominativo de NombreLatin
     *
     * @param string snominativo='' optional
     */
    function setNominativo($snominativo = '')
    {
        $this->snominativo = $snominativo;
    }

    /**
     * Recupera el atributo sgenitivo de NombreLatin
     *
     * @return string sgenitivo
     */
    function getGenitivo()
    {
        if (!isset($this->sgenitivo) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->sgenitivo;
    }

    /**
     * Establece el valor del atributo sgenitivo de NombreLatin
     *
     * @param string sgenitivo='' optional
     */
    function setGenitivo($sgenitivo = '')
    {
        $this->sgenitivo = $sgenitivo;
    }
    /* MÉTODOS GET y SET D'ATRIBUTOS QUE NO SON CAMPOS -----------------------------*/

    /**
     * Retorna una col·lecció d'objectes del tipus DatosCampo
     *
     */
    function getDatosCampos()
    {
        $oNombreLatinSet = new core\Set();

        $oNombreLatinSet->add($this->getDatosNom());
        $oNombreLatinSet->add($this->getDatosNominativo());
        $oNombreLatinSet->add($this->getDatosGenitivo());
        return $oNombreLatinSet->getTot();
    }


    /**
     * Recupera les propietats de l'atribut snom de NombreLatin
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosNom()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'nom'));
        $oDatosCampo->setEtiqueta(_("vernácula"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument('50');
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut snominativo de NombreLatin
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosNominativo()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'nominativo'));
        $oDatosCampo->setEtiqueta(_("nominativo"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument('50');
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut sgenitivo de NombreLatin
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosGenitivo()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'genitivo'));
        $oDatosCampo->setEtiqueta(_("genitivo"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument('50');
        return $oDatosCampo;
    }
}

?>
