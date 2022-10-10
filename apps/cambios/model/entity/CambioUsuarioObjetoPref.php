<?php

namespace cambios\model\entity;

use core;

/**
 * Fitxer amb la Classe que accedeix a la taula av_cambios_usuario_objeto_pref
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 17/4/2019
 */

/**
 * Clase que implementa la entidad av_cambios_usuario_objeto_pref
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 17/4/2019
 */
class CambioUsuarioObjetoPref extends core\ClasePropiedades
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * aPrimary_key de CambioUsuarioObjetoPref
     *
     * @var array
     */
    private $aPrimary_key;

    /**
     * aDades de CambioUsuarioObjetoPref
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
     * Id_item_usuario_objeto de CambioUsuarioObjetoPref
     *
     * @var integer
     */
    private $iid_item_usuario_objeto;
    /**
     * Id_usuario de CambioUsuarioObjetoPref
     *
     * @var integer
     */
    private $iid_usuario;
    /**
     * Dl_org de CambioUsuarioObjetoPref
     *
     * @var string
     */
    private $sdl_org;
    /**
     * Id_tipo_activ_txt de CambioUsuarioObjetoPref
     *
     * @var string
     */
    private $sid_tipo_activ_txt;
    /**
     * id_fase_ref de CambioUsuarioObjetoPref
     *
     * @var integer
     */
    private $iid_fase_ref;
    /**
     * aviso_off de CambioUsuarioObjetoPref
     *
     * @var boolean
     */
    private $baviso_off;
    /**
     * aviso_on de CambioUsuarioObjetoPref
     *
     * @var boolean JSON
     */
    private $baviso_on;
    /**
     * aviso_outdate de CambioUsuarioObjetoPref
     *
     * @var boolean
     */
    private $baviso_outdate;
    /**
     * Objeto de CambioUsuarioObjetoPref
     *
     * @var string
     */
    private $sobjeto;
    /**
     * Aviso_tipo de CambioUsuarioObjetoPref
     *
     * @var integer
     */
    private $iaviso_tipo;
    /**
     * Id_pau de CambioUsuarioObjetoPref
     *
     * @var string
     */
    private $sid_pau;
    /* ATRIBUTOS QUE NO SON CAMPOS------------------------------------------------- */
    /**
     * oDbl de CambioUsuarioObjetoPref
     *
     * @var object
     */
    protected $oDbl;
    /**
     * NomTabla de CambioUsuarioObjetoPref
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
     * @param integer|array iid_item_usuario_objeto
     *                        $a_id. Un array con los nombres=>valores de las claves primarias.
     */
    function __construct($a_id = '')
    {
        $oDbl = $GLOBALS['oDBE'];
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                if (($nom_id == 'id_item_usuario_objeto') && $val_id !== '') $this->iid_item_usuario_objeto = (int)$val_id; 
            }
        } else {
            if (isset($a_id) && $a_id !== '') {
                $this->iid_item_usuario_objeto = (integer)$a_id; 
                $this->aPrimary_key = array('id_item_usuario_objeto' => $this->iid_item_usuario_objeto);
            }
        }
        $this->setoDbl($oDbl);
        $this->setNomTabla('av_cambios_usuario_objeto_pref');
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
        $aDades['id_usuario'] = $this->iid_usuario;
        $aDades['dl_org'] = $this->sdl_org;
        $aDades['id_tipo_activ_txt'] = $this->sid_tipo_activ_txt;
        $aDades['id_fase_ref'] = $this->iid_fase_ref;
        $aDades['aviso_off'] = $this->baviso_off;
        $aDades['aviso_on'] = $this->baviso_on;
        $aDades['aviso_outdate'] = $this->baviso_outdate;
        $aDades['objeto'] = $this->sobjeto;
        $aDades['aviso_tipo'] = $this->iaviso_tipo;
        $aDades['id_pau'] = $this->sid_pau;
        array_walk($aDades, 'core\poner_null');
        //para el caso de los boolean FALSE, el pdo(+postgresql) pone string '' en vez de 0. Lo arreglo:
        if (core\is_true($aDades['aviso_off'])) {
            $aDades['aviso_off'] = 'true';
        } else {
            $aDades['aviso_off'] = 'false';
        }
        if (core\is_true($aDades['aviso_on'])) {
            $aDades['aviso_on'] = 'true';
        } else {
            $aDades['aviso_on'] = 'false';
        }
        if (core\is_true($aDades['aviso_outdate'])) {
            $aDades['aviso_outdate'] = 'true';
        } else {
            $aDades['aviso_outdate'] = 'false';
        }

        if ($bInsert === FALSE) {
            //UPDATE
            $update = "
					id_usuario               = :id_usuario,
					dl_org                   = :dl_org,
					id_tipo_activ_txt        = :id_tipo_activ_txt,
					id_fase_ref              = :id_fase_ref,
                    aviso_off                = :aviso_off,
                    aviso_on                 = :aviso_on,
                    aviso_outdate            = :aviso_outdate,
					objeto                   = :objeto,
					aviso_tipo               = :aviso_tipo,
					id_pau                   = :id_pau";
            if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_item_usuario_objeto='$this->iid_item_usuario_objeto'")) === FALSE) {
                $sClauError = 'CambioUsuarioObjetoPref.update.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return FALSE;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'CambioUsuarioObjetoPref.update.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return FALSE;
                }
            }
        } else {
            // INSERT
            $campos = "(id_usuario,dl_org,id_tipo_activ_txt,id_fase_ref,aviso_off,aviso_on,aviso_outdate,objeto,aviso_tipo,id_pau)";
            $valores = "(:id_usuario,:dl_org,:id_tipo_activ_txt,:id_fase_ref,:aviso_off,:aviso_on,:aviso_outdate,:objeto,:aviso_tipo,:id_pau)";
            if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === FALSE) {
                $sClauError = 'CambioUsuarioObjetoPref.insertar.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return FALSE;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'CambioUsuarioObjetoPref.insertar.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return FALSE;
                }
            }
            $this->id_item_usuario_objeto = $oDbl->lastInsertId('av_cambios_usuario_objeto_pref_id_item_usuario_objeto_seq');
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
        if (isset($this->iid_item_usuario_objeto)) {
            if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_item_usuario_objeto='$this->iid_item_usuario_objeto'")) === FALSE) {
                $sClauError = 'CambioUsuarioObjetoPref.carregar';
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
        if (($oDbl->exec("DELETE FROM $nom_tabla WHERE id_item_usuario_objeto='$this->iid_item_usuario_objeto'")) === FALSE) {
            $sClauError = 'CambioUsuarioObjetoPref.eliminar';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return FALSE;
        }
        return TRUE;
    }

    /* OTROS MÉTODOS  ----------------------------------------------------------*/

    /**
     * retorna un arary amb els possibles tipus d'avis.
     *
     * @retrun array aTipos_aviso
     */

    public static function getTipos_aviso()
    {
        $aTipos_aviso = [CambioUsuario::TIPO_LISTA => _("anotar en lista"),
            CambioUsuario::TIPO_MAIL => _("e-mail"),
        ];

        return $aTipos_aviso;
    }

    /* MÉTODOS PRIVADOS ----------------------------------------------------------*/

    /**
     * Establece el valor de todos los atributos
     *
     * @param array $aDades
     */
    function setAllAtributes($aDades)
    {
        if (!is_array($aDades)) return;
        if (array_key_exists('id_item_usuario_objeto', $aDades)) $this->setId_item_usuario_objeto($aDades['id_item_usuario_objeto']);
        if (array_key_exists('id_usuario', $aDades)) $this->setId_usuario($aDades['id_usuario']);
        if (array_key_exists('dl_org', $aDades)) $this->setDl_org($aDades['dl_org']);
        if (array_key_exists('id_tipo_activ_txt', $aDades)) $this->setId_tipo_activ_txt($aDades['id_tipo_activ_txt']);
        if (array_key_exists('id_fase_ref', $aDades)) $this->setId_fase_ref($aDades['id_fase_ref']);
        if (array_key_exists('aviso_off', $aDades)) $this->setAviso_off($aDades['aviso_off']);
        if (array_key_exists('aviso_on', $aDades)) $this->setAviso_on($aDades['aviso_on']);
        if (array_key_exists('aviso_outdate', $aDades)) $this->setAviso_outdate($aDades['aviso_outdate']);
        if (array_key_exists('objeto', $aDades)) $this->setObjeto($aDades['objeto']);
        if (array_key_exists('aviso_tipo', $aDades)) $this->setAviso_tipo($aDades['aviso_tipo']);
        if (array_key_exists('id_pau', $aDades)) $this->setId_pau($aDades['id_pau']);
    }

    /**
     * Establece a 'empty' el valor de todos los atributos
     *
     */
    function setNullAllAtributes()
    {
        $aPK = $this->getPrimary_key();
        $this->setId_item_usuario_objeto('');
        $this->setId_usuario('');
        $this->setDl_org('');
        $this->setId_tipo_activ_txt('');
        $this->setId_fase_ref('');
        $this->setAviso_off('');
        $this->setAviso_on('');
        $this->setAviso_outdate('');
        $this->setObjeto('');
        $this->setAviso_tipo('');
        $this->setId_pau('');
        $this->setPrimary_key($aPK);
    }


    /* MÉTODOS GET y SET --------------------------------------------------------*/

    /**
     * Recupera todos los atributos de CambioUsuarioObjetoPref en un array
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
     * Recupera la clave primaria de CambioUsuarioObjetoPref en un array
     *
     * @return array aPrimary_key
     */
    function getPrimary_key()
    {
        if (!isset($this->aPrimary_key)) {
            $this->aPrimary_key = array('id_item_usuario_objeto' => $this->iid_item_usuario_objeto);
        }
        return $this->aPrimary_key;
    }

    /**
     * Establece la clave primaria de CambioUsuarioObjetoPref en un array
     *
     * @return array aPrimary_key
     */
    public function setPrimary_key($a_id = '')
    {
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                if (($nom_id == 'id_item_usuario_objeto') && $val_id !== '') $this->iid_item_usuario_objeto = (int)$val_id; 
            }
        }
    }

    /**
     * Recupera el atributo iid_item_usuario_objeto de CambioUsuarioObjetoPref
     *
     * @return integer iid_item_usuario_objeto
     */
    function getId_item_usuario_objeto()
    {
        if (!isset($this->iid_item_usuario_objeto) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->iid_item_usuario_objeto;
    }

    /**
     * Establece el valor del atributo iid_item_usuario_objeto de CambioUsuarioObjetoPref
     *
     * @param integer iid_item_usuario_objeto
     */
    function setId_item_usuario_objeto($iid_item_usuario_objeto)
    {
        $this->iid_item_usuario_objeto = $iid_item_usuario_objeto;
    }

    /**
     * Recupera el atributo iid_usuario de CambioUsuarioObjetoPref
     *
     * @return integer iid_usuario
     */
    function getId_usuario()
    {
        if (!isset($this->iid_usuario) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->iid_usuario;
    }

    /**
     * Establece el valor del atributo iid_usuario de CambioUsuarioObjetoPref
     *
     * @param integer iid_usuario='' optional
     */
    function setId_usuario($iid_usuario = '')
    {
        $this->iid_usuario = $iid_usuario;
    }

    /**
     * Recupera el atributo sdl_org de CambioUsuarioObjetoPref
     *
     * @return boolean sdl_org
     */
    function getDl_org()
    {
        if (!isset($this->sdl_org) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->sdl_org;
    }

    /**
     * Establece el valor del atributo sdl_org de CambioUsuarioObjetoPref
     *
     * @param string sdl_org='x' optional
     */
    function setDl_org($sdl_org = 'x')
    {
        $this->sdl_org = $sdl_org;
    }

    /**
     * Recupera el atributo sid_tipo_activ_txt de CambioUsuarioObjetoPref
     *
     * @return string sid_tipo_activ_txt
     */
    function getId_tipo_activ_txt()
    {
        if (!isset($this->sid_tipo_activ_txt) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->sid_tipo_activ_txt;
    }

    /**
     * Establece el valor del atributo sid_tipo_activ_txt de CambioUsuarioObjetoPref
     *
     * @param string sid_tipo_activ_txt='' optional
     */
    function setId_tipo_activ_txt($sid_tipo_activ_txt = '')
    {
        $this->sid_tipo_activ_txt = $sid_tipo_activ_txt;
    }

    /**
     * Recupera el atributo id_fase_ref de CambioUsuarioObjetoPref
     *
     * @return integer
     */
    function getId_fase_ref()
    {
        if (!isset($this->iid_fase_ref) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->iid_fase_ref;
    }

    /**
     * Establece el valor del atributo id_fase_ref de CambioUsuarioObjetoPref
     *
     * @param integer
     */
    function setId_fase_ref($id_fase_ref)
    {
        $this->iid_fase_ref = $id_fase_ref;
    }

    /**
     * @return boolean
     */
    public function getAviso_off()
    {
        if (!isset($this->baviso_off) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->baviso_off;
    }

    /**
     * @param boolean $baviso_off
     */
    public function setAviso_off($baviso_off)
    {
        $this->baviso_off = $baviso_off;
    }

    /**
     * @return boolean
     */
    public function getAviso_on()
    {
        if (!isset($this->baviso_on) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->baviso_on;
    }

    /**
     * @param boolean $baviso_on
     */
    public function setAviso_on($baviso_on)
    {
        $this->baviso_on = $baviso_on;
    }

    /**
     * @return boolean
     */
    public function getAviso_outdate()
    {
        if (!isset($this->baviso_outdate) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->baviso_outdate;
    }

    /**
     * @param boolean $baviso_outdate
     */
    public function setAviso_outdate($baviso_outdate)
    {
        $this->baviso_outdate = $baviso_outdate;
    }

    /**
     * Recupera el atributo sobjeto de CambioUsuarioObjetoPref
     *
     * @return string sobjeto
     */
    function getObjeto()
    {
        if (!isset($this->sobjeto) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->sobjeto;
    }

    /**
     * Establece el valor del atributo sobjeto de CambioUsuarioObjetoPref
     *
     * @param string sobjeto='' optional
     */
    function setObjeto($sobjeto = '')
    {
        $this->sobjeto = $sobjeto;
    }

    /**
     * Recupera el atributo iaviso_tipo de CambioUsuarioObjetoPref
     *
     * @return integer iaviso_tipo
     */
    function getAviso_tipo()
    {
        if (!isset($this->iaviso_tipo) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->iaviso_tipo;
    }

    /**
     * Establece el valor del atributo iaviso_tipo de CambioUsuarioObjetoPref
     *
     * @param integer iaviso_tipo='' optional
     */
    function setAviso_tipo($iaviso_tipo = '')
    {
        $this->iaviso_tipo = $iaviso_tipo;
    }

    /**
     * Recupera el atributo sid_pau de CambioUsuarioObjetoPref
     *
     * @return string sid_pau
     */
    function getId_pau()
    {
        if (!isset($this->sid_pau) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->sid_pau;
    }

    /**
     * Establece el valor del atributo sid_pau de CambioUsuarioObjetoPref
     *
     * @param string sid_pau='' optional
     */
    function setId_pau($sid_pau = '')
    {
        $this->sid_pau = $sid_pau;
    }
    /* MÉTODOS GET y SET D'ATRIBUTOS QUE NO SON CAMPOS -----------------------------*/

    /**
     * Retorna una col·lecció d'objectes del tipus DatosCampo
     *
     */
    function getDatosCampos()
    {
        $oCambioUsuarioObjetoPrefSet = new core\Set();

        $oCambioUsuarioObjetoPrefSet->add($this->getDatosId_usuario());
        $oCambioUsuarioObjetoPrefSet->add($this->getDatosDl_org());
        $oCambioUsuarioObjetoPrefSet->add($this->getDatosId_tipo_activ_txt());
        $oCambioUsuarioObjetoPrefSet->add($this->getDatosId_fase_ref());
        $oCambioUsuarioObjetoPrefSet->add($this->getDatosAviso_off());
        $oCambioUsuarioObjetoPrefSet->add($this->getDatosAviso_on());
        $oCambioUsuarioObjetoPrefSet->add($this->getDatosAviso_outdate());
        $oCambioUsuarioObjetoPrefSet->add($this->getDatosObjeto());
        $oCambioUsuarioObjetoPrefSet->add($this->getDatosAviso_tipo());
        $oCambioUsuarioObjetoPrefSet->add($this->getDatosId_pau());
        return $oCambioUsuarioObjetoPrefSet->getTot();
    }


    /**
     * Recupera les propietats de l'atribut iid_usuario de CambioUsuarioObjetoPref
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosId_usuario()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'id_usuario'));
        $oDatosCampo->setEtiqueta(_("id_usuario"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut sdl_org de CambioUsuarioObjetoPref
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosDl_org()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'dl_org'));
        $oDatosCampo->setEtiqueta(_("dl_org"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut sid_tipo_activ_txt de CambioUsuarioObjetoPref
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosId_tipo_activ_txt()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'id_tipo_activ_txt'));
        $oDatosCampo->setEtiqueta(_("id_tipo_activ_txt"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut id_fase_ref de CambioUsuarioObjetoPref
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosId_fase_ref()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'id_fase_ref'));
        $oDatosCampo->setEtiqueta(_("Fase de referencia"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut aviso_off de CambioUsuarioObjetoPref
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosAviso_off()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'aviso_off'));
        $oDatosCampo->setEtiqueta(_("aviso_off"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut aviso_on de CambioUsuarioObjetoPref
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosAviso_on()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'aviso_on'));
        $oDatosCampo->setEtiqueta(_("aviso_on"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut aviso_outdate de CambioUsuarioObjetoPref
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosAviso_outdate()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'aviso_outdate'));
        $oDatosCampo->setEtiqueta(_("aviso_outdate"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut sobjeto de CambioUsuarioObjetoPref
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosObjeto()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'objeto'));
        $oDatosCampo->setEtiqueta(_("objeto"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut iaviso_tipo de CambioUsuarioObjetoPref
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosAviso_tipo()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'aviso_tipo'));
        $oDatosCampo->setEtiqueta(_("aviso_tipo"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut sid_pau de CambioUsuarioObjetoPref
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosId_pau()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'id_pau'));
        $oDatosCampo->setEtiqueta(_("id_pau"));
        return $oDatosCampo;
    }
}
