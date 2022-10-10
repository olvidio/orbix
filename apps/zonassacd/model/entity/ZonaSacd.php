<?php

namespace zonassacd\model\entity;

use core;

/**
 * Fitxer amb la Classe que accedeix a la taula zonas_sacd
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 01/03/2019
 */

/**
 * Clase que implementa la entidad zonas_sacd
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 01/03/2019
 */
class ZonaSacd extends core\ClasePropiedades
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * aPrimary_key de ZonaSacd
     *
     * @var array
     */
    private $aPrimary_key;

    /**
     * aDades de ZonaSacd
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
     * Id_item de ZonaSacd
     *
     * @var integer
     */
    private $iid_item;
    /**
     * Id_nom de ZonaSacd
     *
     * @var integer
     */
    private $iid_nom;
    /**
     * Id_zona de ZonaSacd
     *
     * @var integer
     */
    private $iid_zona;
    /**
     * Propia de ZonaSacd
     *
     * @var boolean
     */
    private $bpropia;
    /* ATRIBUTOS QUE NO SON CAMPOS------------------------------------------------- */
    /**
     * oDbl de ZonaSacd
     *
     * @var object
     */
    protected $oDbl;
    /**
     * NomTabla de ZonaSacd
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
        $oDbl = $GLOBALS['oDBE'];
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
        $this->setNomTabla('zonas_sacd');
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
        $aDades['id_nom'] = $this->iid_nom;
        $aDades['id_zona'] = $this->iid_zona;
        $aDades['propia'] = $this->bpropia;
        array_walk($aDades, 'core\poner_null');
        //para el caso de los boolean FALSE, el pdo(+postgresql) pone string '' en vez de 0. Lo arreglo:
        if (core\is_true($aDades['propia'])) {
            $aDades['propia'] = 'true';
        } else {
            $aDades['propia'] = 'false';
        }

        if ($bInsert === FALSE) {
            //UPDATE
            $update = "
					id_nom                   = :id_nom,
					id_zona                  = :id_zona,
					propia                   = :propia";
            if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_item='$this->iid_item'")) === FALSE) {
                $sClauError = 'ZonaSacd.update.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return FALSE;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'ZonaSacd.update.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return FALSE;
                }
            }
        } else {
            // INSERT
            $campos = "(id_nom,id_zona,propia)";
            $valores = "(:id_nom,:id_zona,:propia)";
            if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === FALSE) {
                $sClauError = 'ZonaSacd.insertar.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return FALSE;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'ZonaSacd.insertar.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return FALSE;
                }
            }
            $this->id_item = $oDbl->lastInsertId('zonas_sacd_id_item_seq');
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
                $sClauError = 'ZonaSacd.carregar';
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
            $sClauError = 'ZonaSacd.eliminar';
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
        if (array_key_exists('id_nom', $aDades)) $this->setId_nom($aDades['id_nom']);
        if (array_key_exists('id_zona', $aDades)) $this->setId_zona($aDades['id_zona']);
        if (array_key_exists('propia', $aDades)) $this->setPropia($aDades['propia']);
    }

    /**
     * Establece a 'empty' el valor de todos los atributos
     *
     */
    function setNullAllAtributes()
    {
        $aPK = $this->getPrimary_key();
        $this->setId_item('');
        $this->setId_nom('');
        $this->setId_zona('');
        $this->setPropia('');
        $this->setPrimary_key($aPK);
    }


    /* MÉTODOS GET y SET --------------------------------------------------------*/

    /**
     * Recupera todos los atributos de ZonaSacd en un array
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
     * Recupera la clave primaria de ZonaSacd en un array
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
     * Establece la clave primaria de ZonaSacd en un array
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
     * Recupera el atributo iid_item de ZonaSacd
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
     * Establece el valor del atributo iid_item de ZonaSacd
     *
     * @param integer iid_item
     */
    function setId_item($iid_item)
    {
        $this->iid_item = $iid_item;
    }

    /**
     * Recupera el atributo iid_nom de ZonaSacd
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
     * Establece el valor del atributo iid_nom de ZonaSacd
     *
     * @param integer iid_nom='' optional
     */
    function setId_nom($iid_nom = '')
    {
        $this->iid_nom = $iid_nom;
    }

    /**
     * Recupera el atributo iid_zona de ZonaSacd
     *
     * @return integer iid_zona
     */
    function getId_zona()
    {
        if (!isset($this->iid_zona) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->iid_zona;
    }

    /**
     * Establece el valor del atributo iid_zona de ZonaSacd
     *
     * @param integer iid_zona='' optional
     */
    function setId_zona($iid_zona = '')
    {
        $this->iid_zona = $iid_zona;
    }

    /**
     * Recupera el atributo bpropia de ZonaSacd
     *
     * @return boolean bpropia
     */
    function getPropia()
    {
        if (!isset($this->bpropia) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->bpropia;
    }

    /**
     * Establece el valor del atributo bpropia de ZonaSacd
     *
     * @param boolean bpropia='f' optional
     */
    function setPropia($bpropia = 'f')
    {
        $this->bpropia = $bpropia;
    }
    /* MÉTODOS GET y SET D'ATRIBUTOS QUE NO SON CAMPOS -----------------------------*/

    /**
     * Retorna una col·lecció d'objectes del tipus DatosCampo
     *
     */
    function getDatosCampos()
    {
        $oZonaSacdSet = new core\Set();

        $oZonaSacdSet->add($this->getDatosId_nom());
        $oZonaSacdSet->add($this->getDatosId_zona());
        $oZonaSacdSet->add($this->getDatosPropia());
        return $oZonaSacdSet->getTot();
    }


    /**
     * Recupera les propietats de l'atribut iid_nom de ZonaSacd
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosId_nom()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'id_nom'));
        $oDatosCampo->setEtiqueta(_("id_nom"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut iid_zona de ZonaSacd
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosId_zona()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'id_zona'));
        $oDatosCampo->setEtiqueta(_("id_zona"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut bpropia de ZonaSacd
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosPropia()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'propia'));
        $oDatosCampo->setEtiqueta(_("propia"));
        return $oDatosCampo;
    }
}
