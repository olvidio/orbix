<?php
namespace actividades\model\entity;

use core\ClasePropiedades;

/**
 * Fitxer amb la Classe que accedeix a la taula $nom_tabla
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 04/02/2011
 */

/**
 * Clase que implementa la entidad $nom_tabla
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 04/02/2011
 */
class Importada extends ClasePropiedades
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * aPrimary_key de Importada
     *
     * @var array
     */
    private $aPrimary_key;

    /**
     * aDades de Importada
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
     * Id_activ de Importada
     *
     * @var integer
     */
    private $iid_activ;
    /* ATRIBUTOS QUE NO SON CAMPOS------------------------------------------------- */

    /* CONSTRUCTOR -------------------------------------------------------------- */

    /**
     * Constructor de la classe.
     * Si només necessita un valor, se li pot passar un integer.
     * En general se li passa un array amb les claus primàries.
     *
     * @param integer|array iid_activ
     *                        $a_id. Un array con los nombres=>valores de las claves primarias.
     */
    function __construct($a_id = '')
    {
        $oDbl = $GLOBALS['oDBC'];
        $oDbl_Select = $GLOBALS['oDBC_Select'];
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                if (($nom_id === 'id_activ') && $val_id !== '') $this->iid_activ = (int)$val_id;
            }
        } else {
            if (isset($a_id) && $a_id !== '') {
                $this->iid_activ = (integer)$a_id; 
                $this->aPrimary_key = array('id_activ' => $this->iid_activ);
            }
        }
        $this->setoDbl($oDbl);
        $this->setoDbl_Select($oDbl_Select);
        $this->setNomTabla('a_importadas');
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
        $aDades['id_activ'] = $this->iid_activ;

        if ($bInsert === false) { // ya está importada
            //UPDATE
        } else {
            // INSERT
            $campos = "(id_activ)";
            $valores = "(:id_activ)";
            if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === false) {
                $sClauError = 'Importada.insertar.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'Importada.insertar.execute';
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
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        if (isset($this->iid_activ)) {
            if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_activ='$this->iid_activ'")) === false) {
                $sClauError = 'Importada.carregar';
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
        if (($oDbl->exec("DELETE FROM $nom_tabla WHERE id_activ='$this->iid_activ'")) === false) {
            $sClauError = 'Importada.eliminar';
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
    function setAllAtributes(array $aDades)
    {
        if (!is_array($aDades)) return;
        if (array_key_exists('id_schema', $aDades)) $this->setId_schema($aDades['id_schema']);
        if (array_key_exists('id_activ', $aDades)) $this->setId_activ($aDades['id_activ']);
    }

    /**
     * Establece a 'empty' el valor de todos los atributos
     *
     */
    function setNullAllAtributes()
    {
        $aPK = $this->getPrimary_key();
        $this->setId_schema('');
        $this->setId_activ('');
        $this->setPrimary_key($aPK);
    }


    /* MÉTODOS GET y SET --------------------------------------------------------*/
    /**
     * Recupera todos los atributos de Importada en un array
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
     * Recupera la clave primaria de Importada en un array
     *
     * @return array aPrimary_key
     */
    function getPrimary_key()
    {
        if (!isset($this->aPrimary_key)) {
            $this->aPrimary_key = array('id_activ' => $this->iid_activ);
        }
        return $this->aPrimary_key;
    }

    /**
     * Establece la clave primaria de Importada en un array
     *
     * @return array aPrimary_key
     */
    public function setPrimary_key($a_id = '')
    {
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                if (($nom_id === 'id_activ') && $val_id !== '') $this->iid_activ = (int)$val_id;
            }
        }
    }

    /**
     * Recupera el atributo iid_activ de Importada
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
     * Establece el valor del atributo iid_activ de Importada
     *
     * @param integer iid_activ
     */
    function setId_activ($iid_activ)
    {
        $this->iid_activ = $iid_activ;
    }
    /* MÉTODOS GET y SET D'ATRIBUTOS QUE NO SON CAMPOS -----------------------------*/

}

?>
