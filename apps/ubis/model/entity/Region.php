<?php

namespace ubis\model\entity;

use core;

/**
 * Clase que implementa la entidad xu_region
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 20/11/2010
 */
class Region extends core\ClasePropiedades
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * aPrimary_key de Region
     *
     * @var array
     */
    private $aPrimary_key;

    /**
     * aDades de Region
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
     * Id_region de Region
     *
     * @var integer
     */
    private $iid_region;
    /**
     * Region de Region
     *
     * @var string
     */
    private $sregion;
    /**
     * Nombre_region de Region
     *
     * @var string
     */
    private $snombre_region;
    /**
     * Status de Region
     *
     * @var boolean
     */
    private $bstatus;
    /* ATRIBUTOS QUE NO SON CAMPOS------------------------------------------------- */
    /* CONSTRUCTOR -------------------------------------------------------------- */

    /**
     * Constructor de la classe.
     * Si només necessita un valor, se li pot passar un integer.
     * En general se li passa un array amb les claus primàries.
     *
     * @param integer|array iid_region
     *                        $a_id. Un array con los nombres=>valores de las claves primarias.
     */
    function __construct($a_id = '')
    {
        $oDbl = $GLOBALS['oDBPC'];
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                if ($nom_id === 'region') $nom_id = 's' . $nom_id;
                if ($nom_id === 'id_region') $nom_id = 'i' . $nom_id;
                if ($val_id !== '') $this->$nom_id = $val_id;
            }
        } else {
            if (isset($a_id) && $a_id !== '') {
                $this->iid_region = (integer)$a_id;
                $this->aPrimary_key = array('id_region' => $this->iid_region);
            }
        }
        $this->setoDbl($oDbl);
        $this->setNomTabla('xu_region');
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
        $aDades['id_region'] = $this->iid_region;
        $aDades['region'] = $this->sregion;
        $aDades['nombre_region'] = $this->snombre_region;
        $aDades['status'] = $this->bstatus;
        array_walk($aDades, 'core\poner_null');
        //para el caso de los boolean FALSE, el pdo(+postgresql) pone string '' en vez de 0. Lo arreglo:
        if (core\is_true($aDades['status'])) {
            $aDades['status'] = 'true';
        } else {
            $aDades['status'] = 'false';
        }

        if ($bInsert === false) {
            //UPDATE
            $update = "
					id_region                = :id_region,
					region                	 = :region,
					nombre_region            = :nombre_region,
					status                   = :status";
            if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_region=$this->iid_region")) === false) {
                $sClauError = 'Region.update.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'Region.update.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return false;
                }
            }
        } else {
            // INSERT
            array_unshift($aDades, $this->iid_region);
            $campos = "(id_region,region,nombre_region,status)";
            $valores = "(:id_region,:region,:nombre_region,:status)";
            if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === false) {
                $sClauError = 'Region.insertar.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'Region.insertar.execute';
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
        if (isset($this->iid_region)) {
            if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_region='$this->iid_region'")) === false) {
                $sClauError = 'Region.carregar';
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
        if (($oDblSt = $oDbl->exec("DELETE FROM $nom_tabla WHERE id_region=$this->iid_region")) === false) {
            $sClauError = 'Region.eliminar';
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
        if (array_key_exists('id_region', $aDades)) $this->setId_region($aDades['id_region']);
        if (array_key_exists('region', $aDades)) $this->setRegion($aDades['region']);
        if (array_key_exists('nombre_region', $aDades)) $this->setNombre_region($aDades['nombre_region']);
        if (array_key_exists('status', $aDades)) $this->setStatus($aDades['status']);
    }

    /**
     * Establece a 'empty' el valor de todos los atributos
     *
     */
    function setNullAllAtributes()
    {
        $aPK = $this->getPrimary_key();
        $this->setId_region('');
        $this->setRegion('');
        $this->setNombre_region('');
        $this->setStatus('');
        $this->setPrimary_key($aPK);
    }


    /* MÉTODOS GET y SET --------------------------------------------------------*/
    /**
     * Recupera todos los atributos de Region en un array
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
     * Recupera la clave primaria de Region en un array
     *
     * @return array aPrimary_key
     */
    function getPrimary_key()
    {
        if (!isset($this->aPrimary_key)) {
            $this->aPrimary_key = array('id_region' => $this->iid_region);
        }
        return $this->aPrimary_key;
    }

    /**
     * Establece la clave primaria de Region en un array
     *
     * @return array aPrimary_key
     */
    public function setPrimary_key($a_id = '')
    {
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                if (($nom_id == 'id_region') && $val_id !== '') $this->iid_region = (int)$val_id;
            }
        }
    }

    /**
     * Recupera el atributo iid_region de Region
     *
     * @return integer iid_region
     */
    function getId_region()
    {
        if (!isset($this->iid_region) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->iid_region;
    }

    /**
     * Establece el valor del atributo iid_region de Region
     *
     * @param integer iid_region='' optional
     */
    function setId_region($iid_region = '')
    {
        $this->iid_region = $iid_region;
    }

    /**
     * Recupera el atributo sregion de Region
     *
     * @return string sregion
     */
    function getRegion()
    {
        if (!isset($this->sregion) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->sregion;
    }

    /**
     * Establece el valor del atributo sregion de Region
     *
     * @param string sregion
     */
    function setRegion($sregion)
    {
        $this->sregion = $sregion;
    }

    /**
     * Recupera el atributo snombre_region de Region
     *
     * @return string snombre_region
     */
    function getNombre_region()
    {
        if (!isset($this->snombre_region) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->snombre_region;
    }

    /**
     * Establece el valor del atributo snombre_region de Region
     *
     * @param string snombre_region='' optional
     */
    function setNombre_region($snombre_region = '')
    {
        $this->snombre_region = $snombre_region;
    }

    /**
     * Recupera el atributo bstatus de Region
     *
     * @return boolean bstatus
     */
    function getStatus()
    {
        if (!isset($this->bstatus) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->bstatus;
    }

    /**
     * Establece el valor del atributo bstatus de Region
     *
     * @param boolean bstatus='f' optional
     */
    function setStatus($bstatus = 'f')
    {
        $this->bstatus = $bstatus;
    }
    /* MÉTODOS GET y SET D'ATRIBUTOS QUE NO SON CAMPOS -----------------------------*/

    /**
     * Retorna una col·lecció d'objectes del tipus DatosCampo
     *
     */
    function getDatosCampos()
    {
        $oRegionSet = new core\Set();

        $oRegionSet->add($this->getDatosId_region());
        $oRegionSet->add($this->getDatosRegion());
        $oRegionSet->add($this->getDatosNombre_region());
        $oRegionSet->add($this->getDatosStatus());
        return $oRegionSet->getTot();
    }


    /**
     * Recupera les propietats de l'atribut iid_region de Region
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosId_region()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'id_region'));
        $oDatosCampo->setEtiqueta(_("id_region"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(3);
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut iid_region de Region
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosRegion()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'region'));
        $oDatosCampo->setEtiqueta(_("sigla"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(6);
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut snombre_region de Region
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosNombre_region()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'nombre_region'));
        $oDatosCampo->setEtiqueta(_("nombre de la región"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(30);
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut bstatus de Region
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosStatus()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'status'));
        $oDatosCampo->setEtiqueta(_("en activo"));
        $oDatosCampo->setTipo('check');
        return $oDatosCampo;
    }
}
