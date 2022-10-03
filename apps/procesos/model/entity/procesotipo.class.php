<?php

namespace procesos\model\entity;

use core;

/**
 * Fitxer amb la Classe que accedeix a la taula a_tipos_proceso
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 07/12/2018
 */

/**
 * Clase que implementa la entidad a_tipos_proceso
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 07/12/2018
 */
class ProcesoTipo extends core\ClasePropiedades
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * aPrimary_key de ProcesoTipo
     *
     * @var array
     */
    private $aPrimary_key;

    /**
     * aDades de ProcesoTipo
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
     * Id_tipo_proceso de ProcesoTipo
     *
     * @var integer
     */
    private $iid_tipo_proceso;
    /**
     * Nom_proceso de ProcesoTipo
     *
     * @var string
     */
    private $snom_proceso;
    /**
     * sfsv de ProcesoTipo
     *
     * @var integer
     */
    private $isfsv;
    /* ATRIBUTOS QUE NO SON CAMPOS------------------------------------------------- */
    /**
     * oDbl de ProcesoTipo
     *
     * @var object
     */
    protected $oDbl;
    /**
     * NomTabla de ProcesoTipo
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
     * @param integer|array iid_tipo_proceso
     *                        $a_id. Un array con los nombres=>valores de las claves primarias.
     */
    function __construct($a_id = '')
    {
        $oDbl = $GLOBALS['oDBC'];
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                if (($nom_id == 'id_tipo_proceso') && $val_id !== '') $this->iid_tipo_proceso = (int)$val_id; // evitem SQL injection fent cast a integer
            }
        } else {
            if (isset($a_id) && $a_id !== '') {
                $this->iid_tipo_proceso = (integer)$a_id; // evitem SQL injection fent cast a integer
                $this->aPrimary_key = array('id_tipo_proceso' => $this->iid_tipo_proceso);
            }
        }
        $this->setoDbl($oDbl);
        $this->setNomTabla('a_tipos_proceso');
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
        $aDades['nom_proceso'] = $this->snom_proceso;
        $aDades['sfsv'] = $this->isfsv;
        array_walk($aDades, 'core\poner_null');

        if ($bInsert === FALSE) {
            //UPDATE
            $update = "
					nom_proceso       = :nom_proceso,
					sfsv              = :sfsv";
            if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_tipo_proceso='$this->iid_tipo_proceso'")) === FALSE) {
                $sClauError = 'ProcesoTipo.update.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return FALSE;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'ProcesoTipo.update.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return FALSE;
                }
            }
        } else {
            // INSERT
            $campos = "(nom_proceso,sfsv)";
            $valores = "(:nom_proceso,:sfsv)";
            if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === FALSE) {
                $sClauError = 'ProcesoTipo.insertar.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return FALSE;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'ProcesoTipo.insertar.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return FALSE;
                }
            }
            $this->id_tipo_proceso = $oDbl->lastInsertId('a_tipos_proceso_id_tipo_proceso_seq');
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
        if (isset($this->iid_tipo_proceso)) {
            if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_tipo_proceso='$this->iid_tipo_proceso'")) === FALSE) {
                $sClauError = 'ProcesoTipo.carregar';
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
        if (($oDbl->exec("DELETE FROM $nom_tabla WHERE id_tipo_proceso='$this->iid_tipo_proceso'")) === FALSE) {
            $sClauError = 'ProcesoTipo.eliminar';
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
        if (array_key_exists('id_tipo_proceso', $aDades)) $this->setId_tipo_proceso($aDades['id_tipo_proceso']);
        if (array_key_exists('nom_proceso', $aDades)) $this->setNom_proceso($aDades['nom_proceso']);
        if (array_key_exists('sfsv', $aDades)) $this->setSfsv($aDades['sfsv']);
    }

    /**
     * Establece a 'empty' el valor de todos los atributos
     *
     */
    function setNullAllAtributes()
    {
        $aPK = $this->getPrimary_key();
        $this->setId_tipo_proceso('');
        $this->setNom_proceso('');
        $this->setSfsv('');
        $this->setPrimary_key($aPK);
    }


    /* MÉTODOS GET y SET --------------------------------------------------------*/

    /**
     * Recupera todos los atributos de ProcesoTipo en un array
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
     * Recupera la clave primaria de ProcesoTipo en un array
     *
     * @return array aPrimary_key
     */
    function getPrimary_key()
    {
        if (!isset($this->aPrimary_key)) {
            $this->aPrimary_key = array('id_tipo_proceso' => $this->iid_tipo_proceso);
        }
        return $this->aPrimary_key;
    }

    /**
     * Establece la clave primaria de ProcesoTipo en un array
     *
     * @return array aPrimary_key
     */
    public function setPrimary_key($a_id = '')
    {
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                if (($nom_id == 'id_tipo_proceso') && $val_id !== '') $this->iid_tipo_proceso = (int)$val_id; // evitem SQL injection fent cast a integer
            }
        }
    }

    /**
     * Recupera el atributo iid_tipo_proceso de ProcesoTipo
     *
     * @return integer iid_tipo_proceso
     */
    function getId_tipo_proceso()
    {
        if (!isset($this->iid_tipo_proceso) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->iid_tipo_proceso;
    }

    /**
     * Establece el valor del atributo iid_tipo_proceso de ProcesoTipo
     *
     * @param integer iid_tipo_proceso
     */
    function setId_tipo_proceso($iid_tipo_proceso)
    {
        $this->iid_tipo_proceso = $iid_tipo_proceso;
    }

    /**
     * Recupera el atributo snom_proceso de ProcesoTipo
     *
     * @return string snom_proceso
     */
    function getNom_proceso()
    {
        if (!isset($this->snom_proceso) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->snom_proceso;
    }

    /**
     * Establece el valor del atributo snom_proceso de ProcesoTipo
     *
     * @param string snom_proceso='' optional
     */
    function setNom_proceso($snom_proceso = '')
    {
        $this->snom_proceso = $snom_proceso;
    }

    /**
     * Recupera el atributo isfsv de ProcesoTipo
     *
     * @return integer isfsv
     */
    function getSfsv()
    {
        if (!isset($this->isfsv) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->isfsv;
    }

    /**
     * Establece el valor del atributo isfsv de ProcesoTipo
     *
     * @param integer isfsv='' optional
     */
    function setSfsv($isfsv = '')
    {
        $this->isfsv = $isfsv;
    }
    /* MÉTODOS GET y SET D'ATRIBUTOS QUE NO SON CAMPOS -----------------------------*/

    /**
     * Retorna una col·lecció d'objectes del tipus DatosCampo
     *
     */
    function getDatosCampos()
    {
        $oProcesoTipoSet = new core\Set();

        $oProcesoTipoSet->add($this->getDatosNom_proceso());
        $oProcesoTipoSet->add($this->getDatosSfsv());
        return $oProcesoTipoSet->getTot();
    }


    /**
     * Recupera les propietats de l'atribut snom_proceso de ProcesoTipo
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosNom_proceso()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'nom_proceso'));
        $oDatosCampo->setEtiqueta(_("nombre del proceso"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument('30');
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut isfsv de ProcesoTipo
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosSfsv()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'sfsv'));
        $oDatosCampo->setEtiqueta(_("sf / sv"));
        $oDatosCampo->setTipo('array');
        $oDatosCampo->setLista([1 => _("sv"), 2 => _("sf")]);
        return $oDatosCampo;
    }
}
