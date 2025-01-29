<?php

namespace notas\model\entity;

use core\ClasePropiedades;
use core\DatosCampo;
use core\Set;
use function core\is_true;

/**
 * Fitxer amb la Classe que accedeix a la taula e_notas_situacion
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 07/04/2014
 */

/**
 * Clase que implementa la entidad e_notas_situacion
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 07/04/2014
 */
class Nota extends ClasePropiedades
{
    // Al final de hecho deberían ser todo constantes, porque en demasiados sitios se tiene en 
    // Cuenta que es.
    /*
    comun=# select * from e_notas_situacion order by id_situacion;;
    id_situacion |   descripcion   | superada | breve
    --------------+-----------------+----------+-------
    0 | desconocido     | f        | ?
    1 | superada        | t        | s
    2 | cursada         | f        | c
    3 | Magna cum laude | t        | M
    4 | Summa cum laude | t        | S
    5 | convalidada     | t        | x
    6 | prevista ca     | f        | p
    7 | prevista inv    | f        | p
    8 | no hecha ca     | f        | n
    9 | no hecha inv    | f        | n
    10 | nota numérica   | t        | nm
    11 | Exento          | t        | e
    12 | examinado       | f        | ex
    13 | falta certificado | f      | fc
    */

    // tipo constantes.
    const DESCONOCIDO = 0;
    const SUPERADA = 1;
    const CURSADA = 2;
    const MAGNA = 3;
    const SUMMA = 4;
    const CONVALIDADA = 5;
    const PREVISTA_CA = 6;
    const PREVISTA_INV = 7;
    const NO_HECHA_CA = 8;
    const NO_HECHA_INV = 9;
    const NUMERICA = 10;
    const EXENTO = 11;
    const EXAMINADO = 12;
    const FALTA_CERTIFICADO = 13;
    //
    // Para que la variable stgr_posibles coja las traducciones, hay
    // que ejecutar la funcion 'traduccion_init()'. Cosa que se hace justo
    // al final de la definicion de la clase: Nota::traduccion_init();
    static $array_status_txt;

    static function traduccion_init()
    {
        self::$array_status_txt = [
            self::DESCONOCIDO => _("desconocido"),
            self::SUPERADA => _("superada"),
            self::CURSADA => _("cursada"),
            self::MAGNA => _("Magna cum laude"),
            self::SUMMA => _("Summa cum laude"),
            self::CONVALIDADA => _("convalidada"),
            self::PREVISTA_CA => _("prevista ca"),
            self::PREVISTA_INV => _("prevista inv"),
            self::NO_HECHA_CA => _("no hecha ca"),
            self::NO_HECHA_INV => _("no hecha inv"),
            self::NUMERICA => _("nota numérica"),
            self::EXENTO => _("Exento"),
            self::EXAMINADO => _("examinado"),
            self::FALTA_CERTIFICADO => _("falta certificado"),
        ];
    }

    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * aPrimary_key de Nota
     *
     * @var array
     */
    protected $aPrimary_key;

    /**
     * aDades de Nota
     *
     * @var array
     */
    protected $aDades;

    /**
     * bLoaded
     *
     * @var boolean
     */
    protected $bLoaded = FALSE;

    /**
     * Id_situacion de Nota
     *
     * @var integer
     */
    protected $iid_situacion;
    /**
     * Descripcion de Nota
     *
     * @var string
     */
    protected $sdescripcion;
    /**
     * Superada de Nota
     *
     * @var boolean
     */
    protected $bsuperada;
    /**
     * Breve de Nota
     *
     * @var string
     */
    protected $sbreve;
    /* ATRIBUTOS QUE NO SON CAMPOS------------------------------------------------- */
    /**
     * oDbl de Nota
     *
     * @var object
     */
    protected $oDbl;
    /**
     * NomTabla de Nota
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
     * @param integer|array iid_situacion
     *                        $a_id. Un array con los nombres=>valores de las claves primarias.
     */
    function __construct($a_id = '')
    {
        $oDbl = $GLOBALS['oDBPC'];
        $oDbl_Select = $GLOBALS['oDBPC_Select'];
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                if (($nom_id === 'id_situacion') && $val_id !== '') $this->iid_situacion = (int)$val_id;
            }
        } else {
            if (isset($a_id) && $a_id !== '') {
                $this->iid_situacion = (integer)$a_id; 
                $this->aPrimary_key = array('id_situacion' => $this->iid_situacion);
            }
        }
        $this->setoDbl($oDbl);
        $this->setoDbl_Select($oDbl_Select);
        $this->setNomTabla('e_notas_situacion');
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
        $aDades['descripcion'] = $this->sdescripcion;
        $aDades['superada'] = $this->bsuperada;
        $aDades['breve'] = $this->sbreve;
        array_walk($aDades, 'core\poner_null');
        //para el caso de los boolean FALSE, el pdo(+postgresql) pone string '' en vez de 0. Lo arreglo:
        if (is_true($aDades['superada'])) {
            $aDades['superada'] = 'true';
        } else {
            $aDades['superada'] = 'false';
        }

        if ($bInsert === false) {
            //UPDATE
            $update = "
					descripcion              = :descripcion,
					superada                 = :superada,
					breve                    = :breve";
            if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_situacion='$this->iid_situacion'")) === false) {
                $sClauError = 'Nota.update.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'Nota.update.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return false;
                }
            }
        } else {
            // INSERT
            array_unshift($aDades, $this->iid_situacion);
            $campos = "(id_situacion,descripcion,superada,breve)";
            $valores = "(:id_situacion,:descripcion,:superada,:breve)";
            if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === false) {
                $sClauError = 'Nota.insertar.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'Nota.insertar.execute';
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
        if (isset($this->iid_situacion)) {
            if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_situacion='$this->iid_situacion'")) === false) {
                $sClauError = 'Nota.carregar';
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
        if (($oDblSt = $oDbl->exec("DELETE FROM $nom_tabla WHERE id_situacion='$this->iid_situacion'")) === false) {
            $sClauError = 'Nota.eliminar';
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
        if (array_key_exists('id_situacion', $aDades)) $this->setId_situacion($aDades['id_situacion']);
        if (array_key_exists('descripcion', $aDades)) $this->setDescripcion($aDades['descripcion']);
        if (array_key_exists('superada', $aDades)) $this->setSuperada($aDades['superada']);
        if (array_key_exists('breve', $aDades)) $this->setBreve($aDades['breve']);
    }

    /**
     * Establece a 'empty' el valor de todos los atributos
     *
     */
    function setNullAllAtributes()
    {
        $aPK = $this->getPrimary_key();
        $this->setId_schema('');
        $this->setId_situacion('');
        $this->setDescripcion('');
        $this->setSuperada('');
        $this->setBreve('');
        $this->setPrimary_key($aPK);
    }


    /* MÉTODOS GET y SET --------------------------------------------------------*/

    /**
     * Recupera todos los atributos de Nota en un array
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
     * Recupera la clave primaria de Nota en un array
     *
     * @return array aPrimary_key
     */
    function getPrimary_key()
    {
        if (!isset($this->aPrimary_key)) {
            $this->aPrimary_key = array('id_situacion' => $this->iid_situacion);
        }
        return $this->aPrimary_key;
    }

    /**
     * Establece la clave primaria de Nota en un array
     *
     * @return array aPrimary_key
     */
    public function setPrimary_key($a_id = '')
    {
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                if (($nom_id === 'id_situacion') && $val_id !== '') $this->iid_situacion = (int)$val_id;
            }
        }
    }

    /**
     * Recupera el atributo iid_situacion de Nota
     *
     * @return integer iid_situacion
     */
    function getId_situacion()
    {
        if (!isset($this->iid_situacion) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->iid_situacion;
    }

    /**
     * Establece el valor del atributo iid_situacion de Nota
     *
     * @param integer iid_situacion
     */
    function setId_situacion($iid_situacion)
    {
        $this->iid_situacion = $iid_situacion;
    }

    /**
     * Recupera el atributo sdescripcion de Nota
     *
     * @return string sdescripcion
     */
    function getDescripcion()
    {
        if (!isset($this->sdescripcion) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->sdescripcion;
    }

    /**
     * Establece el valor del atributo sdescripcion de Nota
     *
     * @param string sdescripcion='' optional
     */
    function setDescripcion($sdescripcion = '')
    {
        $this->sdescripcion = $sdescripcion;
    }

    /**
     * Recupera el atributo bsuperada de Nota
     *
     * @return boolean bsuperada
     */
    function getSuperada()
    {
        if (!isset($this->bsuperada) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->bsuperada;
    }

    /**
     * Establece el valor del atributo bsuperada de Nota
     *
     * @param boolean bsuperada='f' optional
     */
    function setSuperada($bsuperada = 'f')
    {
        $this->bsuperada = $bsuperada;
    }

    /**
     * Recupera el atributo sbreve de Nota
     *
     * @return string sbreve
     */
    function getBreve()
    {
        if (!isset($this->sbreve) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->sbreve;
    }

    /**
     * Establece el valor del atributo sbreve de Nota
     *
     * @param string sbreve='' optional
     */
    function setBreve($sbreve = '')
    {
        $this->sbreve = $sbreve;
    }
    /* MÉTODOS GET y SET D'ATRIBUTOS QUE NO SON CAMPOS -----------------------------*/

    /**
     * Retorna una col·lecció d'objectes del tipus DatosCampo
     *
     */
    function getDatosCampos()
    {
        $oNotaSet = new Set();

        $oNotaSet->add($this->getDatosDescripcion());
        $oNotaSet->add($this->getDatosSuperada());
        $oNotaSet->add($this->getDatosBreve());
        return $oNotaSet->getTot();
    }


    /**
     * Recupera les propietats de l'atribut sdescripcion de Nota
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosDescripcion()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'descripcion'));
        $oDatosCampo->setEtiqueta(_("descripción"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut bsuperada de Nota
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosSuperada()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'superada'));
        $oDatosCampo->setEtiqueta(_("superada"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut sbreve de Nota
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosBreve()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'breve'));
        $oDatosCampo->setEtiqueta(_("breve"));
        return $oDatosCampo;
    }
}

Nota::traduccion_init();
