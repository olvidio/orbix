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
    /**
     * dw1 de ZonaSacd
     *
     * @var boolean
     */
    private $bdw1;
    private $bdw2;
    private $bdw3;
    private $bdw4;
    private $bdw5;
    private $bdw6;
    private $bdw7;
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
        $oDbl_Select = $GLOBALS['oDBE_Select'];
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
        $this->setoDbl_Select($oDbl_Select);
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
        $aDades['dw1'] = $this->bdw1;
        $aDades['dw2'] = $this->bdw2;
        $aDades['dw3'] = $this->bdw3;
        $aDades['dw4'] = $this->bdw4;
        $aDades['dw5'] = $this->bdw5;
        $aDades['dw6'] = $this->bdw6;
        $aDades['dw7'] = $this->bdw7;
        array_walk($aDades, 'core\poner_null');
        //para el caso de los boolean FALSE, el pdo(+postgresql) pone string '' en vez de 0. Lo arreglo:
        if (core\is_true($aDades['propia'])) {
            $aDades['propia'] = 'true';
        } else {
            $aDades['propia'] = 'false';
        }
        if (core\is_true($aDades['dw1'])) {
            $aDades['dw1'] = 'true';
        } else {
            $aDades['dw1'] = 'false';
        }
        if (core\is_true($aDades['dw2'])) {
            $aDades['dw2'] = 'true';
        } else {
            $aDades['dw2'] = 'false';
        }
        if (core\is_true($aDades['dw3'])) {
            $aDades['dw3'] = 'true';
        } else {
            $aDades['dw3'] = 'false';
        }
        if (core\is_true($aDades['dw4'])) {
            $aDades['dw4'] = 'true';
        } else {
            $aDades['dw4'] = 'false';
        }
        if (core\is_true($aDades['dw5'])) {
            $aDades['dw5'] = 'true';
        } else {
            $aDades['dw5'] = 'false';
        }
        if (core\is_true($aDades['dw6'])) {
            $aDades['dw6'] = 'true';
        } else {
            $aDades['dw6'] = 'false';
        }
        if (core\is_true($aDades['dw7'])) {
            $aDades['dw7'] = 'true';
        } else {
            $aDades['dw7'] = 'false';
        }

        if ($bInsert === FALSE) {
            //UPDATE
            $update = "
					id_nom                   = :id_nom,
					id_zona                  = :id_zona,
					propia                   = :propia,
                    dw1                      = :dw1,
                    dw2                      = :dw2,
                    dw3                      = :dw3,
                    dw4                      = :dw4,
                    dw5                      = :dw5,
                    dw6                      = :dw6,
                    dw7                      = :dw7";
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
            $campos = "(id_nom,id_zona,propia,dw1,dw2,dw3,dw4,dw5,dw6,dw7)";
            $valores = "(:id_nom,:id_zona,:propia,:dw1,:dw2,:dw3,:dw4,:dw5,:dw6,:dw7)";
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
        $oDbl = $this->getoDbl_Select();
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
        if (array_key_exists('dw1', $aDades)) $this->setDw1($aDades['dw1']);
        if (array_key_exists('dw2', $aDades)) $this->setDw2($aDades['dw2']);
        if (array_key_exists('dw3', $aDades)) $this->setDw3($aDades['dw3']);
        if (array_key_exists('dw4', $aDades)) $this->setDw4($aDades['dw4']);
        if (array_key_exists('dw5', $aDades)) $this->setDw5($aDades['dw5']);
        if (array_key_exists('dw6', $aDades)) $this->setDw6($aDades['dw6']);
        if (array_key_exists('dw7', $aDades)) $this->setDw7($aDades['dw7']);
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
        $this->setDw1('');
        $this->setDw2('');
        $this->setDw3('');
        $this->setDw4('');
        $this->setDw5('');
        $this->setDw6('');
        $this->setDw7('');
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

    /**
     * Recupera el atributo bdw1 de ZonaSacd
     *
     * @return boolean bdw1
     */
    function getDw1()
    {
        if (!isset($this->bdw1) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->bdw1;
    }
    /**
     * Establece el valor del atributo bdw1 de ZonaSacd
     *
     * @param boolean bdw1='t' optional
     */
    function setDw1($bdw1 = 't')
    {
        $this->bdw1 = $bdw1;
    }

    /**
     * Recupera el atributo bdw2 de ZonaSacd
     *
     * @return boolean bdw2
     */
    function getDw2()
    {
        if (!isset($this->bdw2) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->bdw2;
    }
    /**
     * Establece el valor del atributo bdw2 de ZonaSacd
     *
     * @param boolean bdw2='t' optional
     */
    function setDw2($bdw2 = 't')
    {
        $this->bdw2 = $bdw2;
    }

    /**
     * Recupera el atributo bdw3 de ZonaSacd
     *
     * @return boolean bdw3
     */
    function getDw3()
    {
        if (!isset($this->bdw3) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->bdw3;
    }
    /**
     * Establece el valor del atributo bdw3 de ZonaSacd
     *
     * @param boolean bdw3='t' optional
     */
    function setDw3($bdw3 = 't')
    {
        $this->bdw3 = $bdw3;
    }

    /**
     * Recupera el atributo bdw4 de ZonaSacd
     *
     * @return boolean bdw4
     */
    function getDw4()
    {
        if (!isset($this->bdw4) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->bdw4;
    }
    /**
     * Establece el valor del atributo bdw4 de ZonaSacd
     *
     * @param boolean bdw4='t' optional
     */
    function setDw4($bdw4 = 't')
    {
        $this->bdw4 = $bdw4;
    }

    /**
     * Recupera el atributo bdw5 de ZonaSacd
     *
     * @return boolean bdw5
     */
    function getDw5()
    {
        if (!isset($this->bdw5) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->bdw5;
    }
    /**
     * Establece el valor del atributo bdw5 de ZonaSacd
     *
     * @param boolean bdw5='t' optional
     */
    function setDw5($bdw5 = 't')
    {
        $this->bdw5 = $bdw5;
    }

    /**
     * Recupera el atributo bdw6 de ZonaSacd
     *
     * @return boolean bdw6
     */
    function getDw6()
    {
        if (!isset($this->bdw6) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->bdw6;
    }
    /**
     * Establece el valor del atributo bdw6 de ZonaSacd
     *
     * @param boolean bdw6='t' optional
     */
    function setDw6($bdw6 = 't')
    {
        $this->bdw6 = $bdw6;
    }

    /**
     * Recupera el atributo bdw7 de ZonaSacd
     *
     * @return boolean bdw7
     */
    function getDw7()
    {
        if (!isset($this->bdw7) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->bdw7;
    }
    /**
     * Establece el valor del atributo bdw7 de ZonaSacd
     *
     * @param boolean bdw7='t' optional
     */
    function setDw7($bdw7 = 't')
    {
        $this->bdw7 = $bdw7;
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
