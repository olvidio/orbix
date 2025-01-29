<?php

namespace encargossacd\model\entity;

use core\ClasePropiedades;
use core\DatosCampo;
use core\Set;

/**
 * Fitxer amb la Classe que accedeix a la taula encargo_tipo
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 11/01/2019
 */

/**
 * Clase que implementa la entidad encargo_tipo
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 11/01/2019
 */
class EncargoTipo extends ClasePropiedades
{
    // modo horario constants.
    //1: opcional , 2:por módulos, 3: por horario
    const HORARIO_OPCIONAL = 1; // opcional
    const HORARIO_POR_MODULOS = 2; // Por módulos (mañana, tarde 1ª hora, tarde 2ª hora).
    const HORARIO_POR_HORAS = 3; // Por horario (día y hora).

    const ARRAY_HORARIO_TXT = [
        self::HORARIO_OPCIONAL => "opcional",
        self::HORARIO_POR_MODULOS => "módulos",
        self::HORARIO_POR_HORAS => "día y hora",
    ];

    //definición de variables globales para las funciones de tipo de encargo
    const GRUPO = [
        "ctr" => 1,
        "cgi" => 2,
        "igl" => 3,
        "stgr" => 4,
        "estudio/descanso" => 5,
        "otros" => 6,
        "personales" => 7,
        "Zona Misas" => 8,
    ];

    // NO se usan, son solo para asegurar que exista la traducción
    private function traduccion()
    {
        $p = _("opcional");
        $a = _("módulos");
        $t = _("día y hora");
        $txt = _("ctr") .
            _("cgi") .
            _("igl") .
            _("stgr") .
            _("estudio/descanso") .
            _("otros") .
            _("personales") .
            _("Zona Misas");

        return $p . $a . $t . $txt;
    }

    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * aPrimary_key de EncargoTipo
     *
     * @var array
     */
    private $aPrimary_key;

    /**
     * aDades de EncargoTipo
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
     * Id_tipo_enc de EncargoTipo
     *
     * @var integer
     */
    private $iid_tipo_enc;
    /**
     * Tipo_enc de EncargoTipo
     *
     * @var string
     */
    private $stipo_enc;
    /**
     * Mod_horario de EncargoTipo
     *
     * @var integer
     */
    private $imod_horario;
    /* ATRIBUTOS QUE NO SON CAMPOS------------------------------------------------- */
    /**
     * oDbl de EncargoTipo
     *
     * @var object
     */
    protected $oDbl;
    /**
     * NomTabla de EncargoTipo
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
     * @param integer|array iid_tipo_enc
     *                        $a_id. Un array con los nombres=>valores de las claves primarias.
     */
    function __construct($a_id = '')
    {
        $oDbl = $GLOBALS['oDBE'];
        $oDbl_Select = $GLOBALS['oDBE_Select'];
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                if (($nom_id === 'id_tipo_enc') && $val_id !== '') $this->iid_tipo_enc = (int)$val_id;
            }
        } else {
            if (isset($a_id) && $a_id !== '') {
                $this->iid_tipo_enc = (integer)$a_id; 
                $this->aPrimary_key = array('id_tipo_enc' => $this->iid_tipo_enc);
            }
        }
        $this->setoDbl($oDbl);
        $this->setoDbl_Select($oDbl_Select);
        $this->setNomTabla('encargo_tipo');
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
        $aDades['tipo_enc'] = $this->stipo_enc;
        $aDades['mod_horario'] = $this->imod_horario;
        array_walk($aDades, 'core\poner_null');

        if ($bInsert === FALSE) {
            //UPDATE
            $update = "
					tipo_enc                 = :tipo_enc,
					mod_horario              = :mod_horario";
            if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_tipo_enc='$this->iid_tipo_enc'")) === FALSE) {
                $sClauError = 'EncargoTipo.update.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return FALSE;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'EncargoTipo.update.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return FALSE;
                }
            }
        } else {
            // INSERT
            array_unshift($aDades, $this->iid_tipo_enc);
            $campos = "(id_tipo_enc,tipo_enc,mod_horario)";
            $valores = "(:id_tipo_enc,:tipo_enc,:mod_horario)";
            if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === FALSE) {
                $sClauError = 'EncargoTipo.insertar.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return FALSE;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'EncargoTipo.insertar.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return FALSE;
                }
            }
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
        if (isset($this->iid_tipo_enc)) {
            if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_tipo_enc='$this->iid_tipo_enc'")) === FALSE) {
                $sClauError = 'EncargoTipo.carregar';
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
        if (($oDbl->exec("DELETE FROM $nom_tabla WHERE id_tipo_enc='$this->iid_tipo_enc'")) === FALSE) {
            $sClauError = 'EncargoTipo.eliminar';
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
    function setAllAtributes(array $aDades)
    {
        if (!is_array($aDades)) return;
        if (array_key_exists('id_tipo_enc', $aDades)) $this->setId_tipo_enc($aDades['id_tipo_enc']);
        if (array_key_exists('tipo_enc', $aDades)) $this->setTipo_enc($aDades['tipo_enc']);
        if (array_key_exists('mod_horario', $aDades)) $this->setMod_horario($aDades['mod_horario']);
    }

    /**
     * Establece a 'empty' el valor de todos los atributos
     *
     */
    function setNullAllAtributes()
    {
        $aPK = $this->getPrimary_key();
        $this->setId_tipo_enc('');
        $this->setTipo_enc('');
        $this->setMod_horario('');
        $this->setPrimary_key($aPK);
    }


    /* MÉTODOS GET y SET --------------------------------------------------------*/

    /**
     * Recupera todos los atributos de EncargoTipo en un array
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
     * Recupera la clave primaria de EncargoTipo en un array
     *
     * @return array aPrimary_key
     */
    function getPrimary_key()
    {
        if (!isset($this->aPrimary_key)) {
            $this->aPrimary_key = array('id_tipo_enc' => $this->iid_tipo_enc);
        }
        return $this->aPrimary_key;
    }

    /**
     * Establece la clave primaria de EncargoTipo en un array
     *
     * @return array aPrimary_key
     */
    public function setPrimary_key($a_id = '')
    {
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                if (($nom_id === 'id_tipo_enc') && $val_id !== '') $this->iid_tipo_enc = (int)$val_id;
            }
        }
    }

    /**
     * Recupera el atributo iid_tipo_enc de EncargoTipo
     *
     * @return integer iid_tipo_enc
     */
    function getId_tipo_enc()
    {
        if (!isset($this->iid_tipo_enc) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->iid_tipo_enc;
    }

    /**
     * Establece el valor del atributo iid_tipo_enc de EncargoTipo
     *
     * @param integer iid_tipo_enc
     */
    function setId_tipo_enc($iid_tipo_enc)
    {
        $this->iid_tipo_enc = $iid_tipo_enc;
    }

    /**
     * Recupera el atributo stipo_enc de EncargoTipo
     *
     * @return string stipo_enc
     */
    function getTipo_enc()
    {
        if (!isset($this->stipo_enc) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->stipo_enc;
    }

    /**
     * Establece el valor del atributo stipo_enc de EncargoTipo
     *
     * @param string stipo_enc='' optional
     */
    function setTipo_enc($stipo_enc = '')
    {
        $this->stipo_enc = $stipo_enc;
    }

    /**
     * Recupera el atributo imod_horario de EncargoTipo
     *
     * @return integer imod_horario
     */
    function getMod_horario()
    {
        if (!isset($this->imod_horario) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->imod_horario;
    }

    /**
     * Establece el valor del atributo imod_horario de EncargoTipo
     *
     * @param integer imod_horario='' optional
     */
    function setMod_horario($imod_horario = '')
    {
        $this->imod_horario = $imod_horario;
    }
    /* MÉTODOS GET y SET D'ATRIBUTOS QUE NO SON CAMPOS -----------------------------*/

    /**
     * Retorna una col·lecció d'objectes del tipus DatosCampo
     *
     */
    function getDatosCampos()
    {
        $oEncargoTipoSet = new Set();

        $oEncargoTipoSet->add($this->getDatosId_tipo_enc());
        $oEncargoTipoSet->add($this->getDatosTipo_enc());
        $oEncargoTipoSet->add($this->getDatosMod_horario());
        return $oEncargoTipoSet->getTot();
    }


    /**
     * Recupera les propietats de l'atribut stipo_enc de EncargoTipo
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosId_tipo_enc()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'id_tipo_enc'));
        $oDatosCampo->setEtiqueta(_("id tipo de encargo"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(10);
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut stipo_enc de EncargoTipo
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosTipo_enc()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'tipo_enc'));
        $oDatosCampo->setEtiqueta(_("tipo de encargo"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(30);
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut imod_horario de EncargoTipo
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosMod_horario()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'mod_horario'));
        $oDatosCampo->setEtiqueta(_("tipo de horario"));
        $oDatosCampo->setTipo('array');
        $oDatosCampo->setLista(self::ARRAY_HORARIO_TXT);

        return $oDatosCampo;
    }
}
