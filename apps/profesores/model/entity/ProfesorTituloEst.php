<?php
namespace profesores\model\entity;

use core;

/**
 * Fitxer amb la Classe que accedeix a la taula d_titulo_est
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 04/09/2015
 */

/**
 * Clase que implementa la entidad d_titulo_est
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 04/09/2015
 */
class ProfesorTituloEst extends core\ClasePropiedades
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * aPrimary_key de ProfesorTituloEst
     *
     * @var array
     */
    private $aPrimary_key;

    /**
     * aDades de ProfesorTituloEst
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
     * Id_item de ProfesorTituloEst
     *
     * @var integer
     */
    private $iid_item;
    /**
     * Id_nom de ProfesorTituloEst
     *
     * @var integer
     */
    private $iid_nom;
    /**
     * Titulo de ProfesorTituloEst
     *
     * @var string
     */
    private $stitulo;
    /**
     * Centro_dnt de ProfesorTituloEst
     *
     * @var string
     */
    private $scentro_dnt;
    /**
     * Eclesiastico de ProfesorTituloEst
     *
     * @var boolean
     */
    private $beclesiastico;
    /**
     * Year de ProfesorTituloEst
     *
     * @var integer
     */
    private $iyear;
    /* ATRIBUTOS QUE NO SON CAMPOS------------------------------------------------- */
    /**
     * oDbl de ProfesorTituloEst
     *
     * @var object
     */
    protected $oDbl;
    /**
     * NomTabla de ProfesorTituloEst
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
     * @param integer|array iid_item,iid_nom
     *                        $a_id. Un array con los nombres=>valores de las claves primarias.
     */
    function __construct($a_id = '')
    {
        $oDbl = $GLOBALS['oDB'];
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                if (($nom_id == 'id_item') && $val_id !== '') $this->iid_item = (int)$val_id;
                if (($nom_id == 'id_nom') && $val_id !== '') $this->iid_nom = (int)$val_id;
            }
        }
        $this->setoDbl($oDbl);
        $this->setNomTabla('d_titulo_est');
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
        $aDades['titulo'] = $this->stitulo;
        $aDades['centro_dnt'] = $this->scentro_dnt;
        $aDades['eclesiastico'] = $this->beclesiastico;
        $aDades['year'] = $this->iyear;
        array_walk($aDades, 'core\poner_null');
        //para el caso de los boolean FALSE, el pdo(+postgresql) pone string '' en vez de 0. Lo arreglo:
        if (core\is_true($aDades['eclesiastico'])) {
            $aDades['eclesiastico'] = 'true';
        } else {
            $aDades['eclesiastico'] = 'false';
        }

        if ($bInsert === false) {
            //UPDATE
            $update = "
					titulo                   = :titulo,
					centro_dnt               = :centro_dnt,
					eclesiastico             = :eclesiastico,
					year                     = :year";
            if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_item=$this->iid_item")) === false) {
                $sClauError = 'ProfesorTituloEst.update.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'ProfesorTituloEst.update.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return false;
                }
            }
        } else {
            // INSERT
            array_unshift($aDades, $this->iid_nom);
            $campos = "(id_nom,titulo,centro_dnt,eclesiastico,year)";
            $valores = "(:id_nom,:titulo,:centro_dnt,:eclesiastico,:year)";
            if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === false) {
                $sClauError = 'ProfesorTituloEst.insertar.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'ProfesorTituloEst.insertar.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return false;
                }
            }
            $this->id_item = $oDbl->lastInsertId('d_titulo_est_id_item_seq');
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
                $sClauError = 'ProfesorTituloEst.carregar';
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
        if (($oDblSt = $oDbl->exec("DELETE FROM $nom_tabla WHERE id_item=$this->iid_item")) === false) {
            $sClauError = 'ProfesorTituloEst.eliminar';
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
        if (array_key_exists('id_item', $aDades)) $this->setId_item($aDades['id_item']);
        if (array_key_exists('id_nom', $aDades)) $this->setId_nom($aDades['id_nom']);
        if (array_key_exists('titulo', $aDades)) $this->setTitulo($aDades['titulo']);
        if (array_key_exists('centro_dnt', $aDades)) $this->setCentro_dnt($aDades['centro_dnt']);
        if (array_key_exists('eclesiastico', $aDades)) $this->setEclesiastico($aDades['eclesiastico']);
        if (array_key_exists('year', $aDades)) $this->setYear($aDades['year']);
    }

    /**
     * Establece a 'empty' el valor de todos los atributos
     *
     */
    function setNullAllAtributes()
    {
        $aPK = $this->getPrimary_key();
        $this->setId_schema('');
        $this->setId_item('');
        $this->setId_nom('');
        $this->setTitulo('');
        $this->setCentro_dnt('');
        $this->setEclesiastico('');
        $this->setYear('');
        $this->setPrimary_key($aPK);
    }


    /* MÉTODOS GET y SET --------------------------------------------------------*/

    /**
     * Recupera todos los atributos de ProfesorTituloEst en un array
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
     * Recupera la clave primaria de ProfesorTituloEst en un array
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
     * Establece la clave primaria de ProfesorTituloEst en un array
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
     * Recupera el atributo iid_item de ProfesorTituloEst
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
     * Establece el valor del atributo iid_item de ProfesorTituloEst
     *
     * @param integer iid_item
     */
    function setId_item($iid_item)
    {
        $this->iid_item = $iid_item;
    }

    /**
     * Recupera el atributo iid_nom de ProfesorTituloEst
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
     * Establece el valor del atributo iid_nom de ProfesorTituloEst
     *
     * @param integer iid_nom
     */
    function setId_nom($iid_nom)
    {
        $this->iid_nom = $iid_nom;
    }

    /**
     * Recupera el atributo stitulo de ProfesorTituloEst
     *
     * @return string stitulo
     */
    function getTitulo()
    {
        if (!isset($this->stitulo) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->stitulo;
    }

    /**
     * Establece el valor del atributo stitulo de ProfesorTituloEst
     *
     * @param string stitulo='' optional
     */
    function setTitulo($stitulo = '')
    {
        $this->stitulo = $stitulo;
    }

    /**
     * Recupera el atributo scentro_dnt de ProfesorTituloEst
     *
     * @return string scentro_dnt
     */
    function getCentro_dnt()
    {
        if (!isset($this->scentro_dnt) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->scentro_dnt;
    }

    /**
     * Establece el valor del atributo scentro_dnt de ProfesorTituloEst
     *
     * @param string scentro_dnt='' optional
     */
    function setCentro_dnt($scentro_dnt = '')
    {
        $this->scentro_dnt = $scentro_dnt;
    }

    /**
     * Recupera el atributo beclesiastico de ProfesorTituloEst
     *
     * @return boolean beclesiastico
     */
    function getEclesiastico()
    {
        if (!isset($this->beclesiastico) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->beclesiastico;
    }

    /**
     * Establece el valor del atributo beclesiastico de ProfesorTituloEst
     *
     * @param boolean beclesiastico='f' optional
     */
    function setEclesiastico($beclesiastico = 'f')
    {
        $this->beclesiastico = $beclesiastico;
    }

    /**
     * Recupera el atributo iyear de ProfesorTituloEst
     *
     * @return integer iyear
     */
    function getYear()
    {
        if (!isset($this->iyear) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->iyear;
    }

    /**
     * Establece el valor del atributo iyear de ProfesorTituloEst
     *
     * @param integer iyear='' optional
     */
    function setYear($iyear = '')
    {
        $this->iyear = $iyear;
    }
    /* MÉTODOS GET y SET D'ATRIBUTOS QUE NO SON CAMPOS -----------------------------*/

    /**
     * Retorna una col·lecció d'objectes del tipus DatosCampo
     *
     */
    function getDatosCampos()
    {
        $oProfesorTituloEstSet = new core\Set();

        $oProfesorTituloEstSet->add($this->getDatosTitulo());
        $oProfesorTituloEstSet->add($this->getDatosCentro_dnt());
        $oProfesorTituloEstSet->add($this->getDatosEclesiastico());
        $oProfesorTituloEstSet->add($this->getDatosYear());
        return $oProfesorTituloEstSet->getTot();
    }

    /**
     * Recupera les propietats de l'atribut stitulo de ProfesorTituloEst
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosTitulo()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'titulo'));
        $oDatosCampo->setEtiqueta(_("título"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(25);
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut scentro_dnt de ProfesorTituloEst
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosCentro_dnt()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'centro_dnt'));
        $oDatosCampo->setEtiqueta(_("centro docente"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(25);
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut beclesiastico de ProfesorTituloEst
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosEclesiastico()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'eclesiastico'));
        $oDatosCampo->setEtiqueta(_("eclesiástico"));
        $oDatosCampo->setTipo('check');
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut iyear de ProfesorTituloEst
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosYear()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'year'));
        $oDatosCampo->setEtiqueta(_("año"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(5);
        return $oDatosCampo;
    }
}

?>
