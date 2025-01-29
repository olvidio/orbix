<?php

namespace encargossacd\model\entity;

use core\ClasePropiedades;
use core\DatosCampo;
use core\Set;

/**
 * Fitxer amb la Classe que accedeix a la taula encargos
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 11/01/2019
 */

/**
 * Clase que implementa la entidad encargos
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 11/01/2019
 */
class Encargo extends ClasePropiedades
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * aPrimary_key de Encargo
     *
     * @var array
     */
    private $aPrimary_key;

    /**
     * aDades de Encargo
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
     * Id_enc de Encargo
     *
     * @var integer
     */
    private $iid_enc;
    /**
     * Id_tipo_enc de Encargo
     *
     * @var integer
     */
    private $iid_tipo_enc;
    /**
     * Sf_sv de Encargo
     *
     * @var integer
     */
    private $isf_sv;
    /**
     * Id_ubi de Encargo
     *
     * @var integer
     */
    private $iid_ubi;
    /**
     * Id_zona de Encargo
     *
     * @var integer
     */
    private $iid_zona;
    /**
     * Desc_enc de Encargo
     *
     * @var string
     */
    private $sdesc_enc;
    /**
     * Idioma_enc de Encargo
     *
     * @var string
     */
    private $sidioma_enc;
    /**
     * Desc_lugar de Encargo
     *
     * @var string
     */
    private $sdesc_lugar;
    /**
     * Observ de Encargo
     *
     * @var string
     */
    private $sobserv;
    /**
     * Orden de Encargo
     *
     * @var integer
     */
    private $iorden;
    /**
     * Prioridad de Encargo
     *
     * @var integer
     */
    private $iprioridad;

    /* ATRIBUTOS QUE NO SON CAMPOS------------------------------------------------- */
    /**
     * oDbl de Encargo
     *
     * @var object
     */
    protected $oDbl;
    /**
     * NomTabla de Encargo
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
     * @param integer|array iid_enc
     *                        $a_id. Un array con los nombres=>valores de las claves primarias.
     */
    function __construct($a_id = '')
    {
        $oDbl = $GLOBALS['oDBE'];
        $oDbl_Select = $GLOBALS['oDBE_Select'];
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                if (($nom_id === 'id_enc') && $val_id !== '') $this->iid_enc = (int)$val_id;
            }
        } else {
            if (isset($a_id) && $a_id !== '') {
                $this->iid_enc = (integer)$a_id;
                $this->aPrimary_key = array('id_enc' => $this->iid_enc);
            }
        }
        $this->setoDbl($oDbl);
        $this->setoDbl_Select($oDbl_Select);
        $this->setNomTabla('encargos');
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
        $aDades['id_tipo_enc'] = $this->iid_tipo_enc;
        $aDades['sf_sv'] = $this->isf_sv;
        $aDades['id_ubi'] = $this->iid_ubi;
        $aDades['id_zona'] = $this->iid_zona;
        $aDades['desc_enc'] = $this->sdesc_enc;
        $aDades['idioma_enc'] = $this->sidioma_enc;
        $aDades['desc_lugar'] = $this->sdesc_lugar;
        $aDades['observ'] = $this->sobserv;
        $aDades['orden'] = $this->iorden;
        $aDades['prioridad'] = $this->iprioridad;
        array_walk($aDades, 'core\poner_null');

        if ($bInsert === FALSE) {
            //UPDATE
            $update = "
					id_tipo_enc              = :id_tipo_enc,
					sf_sv                    = :sf_sv,
					id_ubi                   = :id_ubi,
					id_zona                  = :id_zona,
					desc_enc                 = :desc_enc,
					idioma_enc               = :idioma_enc,
					desc_lugar               = :desc_lugar,
					observ                   = :observ,
                    orden                    = :orden,
					prioridad                = :prioridad";
            if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_enc='$this->iid_enc'")) === FALSE) {
                $sClauError = 'Encargo.update.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return FALSE;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'Encargo.update.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return FALSE;
                }
            }
        } else {
            // INSERT
            array_unshift($aDades, $this->iid_enc);
            $campos = "(id_tipo_enc,sf_sv,id_ubi,id_zona,desc_enc,idioma_enc,desc_lugar,observ,orden,prioridad)";
            $valores = "(:id_tipo_enc,:sf_sv,:id_ubi,:id_zona,:desc_enc,:idioma_enc,:desc_lugar,:observ,:orden,:prioridad)";
            if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === FALSE) {
                $sClauError = 'Encargo.insertar.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return FALSE;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'Encargo.insertar.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return FALSE;
                }
            }
            $this->iid_enc = $oDbl->lastInsertId('encargos_id_enc_seq');
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
        if (isset($this->iid_enc)) {
            if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_enc='$this->iid_enc'")) === FALSE) {
                $sClauError = 'Encargo.carregar';
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
        if (($oDbl->exec("DELETE FROM $nom_tabla WHERE id_enc='$this->iid_enc'")) === FALSE) {
            $sClauError = 'Encargo.eliminar';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return FALSE;
        }
        // Eliminar os objetos dependientes:
        $this->DBEliminarRestricciones();
        return TRUE;
    }

    /**
     * Elimina los objetos relacionados con llave foránea
     * En teoría debería hacerse a nivel de la base de datos, por foreign key,
     * pero al sincronizar con bucardo se ha acabado perdiendo esta referencia.
     *
     */
    public function DBEliminarRestricciones()
    {
        $id_enc = $this->getId_enc();
        $aWhere = ['id_enc' => $id_enc];
        // encargo_horario
        $gesEncargoHorario = new GestorEncargoHorario();
        $cEncargoHorario = $gesEncargoHorario->getEncargoHorarios($aWhere);
        foreach ($cEncargoHorario as $oEncargoHorario) {
            $oEncargoHorario->DBEliminar();
        }
        // encargo_horario_excepcion
        $gesEncargoHorarioExcepcion = new GestorEncargoHorarioExcepcion();
        $cEncargoHorarioExcepcion = $gesEncargoHorarioExcepcion->getEncargoHorarioExcepciones($aWhere);
        foreach ($cEncargoHorarioExcepcion as $oEncargoHorarioExcepcion) {
            $oEncargoHorarioExcepcion->DBEliminar();
        }
        // encargos_sacd
        $gesEncargoSacd = new GestorEncargoSacd();
        $cEncargosSacd = $gesEncargoSacd->getEncargosSacd($aWhere);
        foreach ($cEncargosSacd as $oEncargoSacd) {
            $oEncargoSacd->DBEliminar();
        }
        // encargo_sacd_horario
        $gesEncargoSacdHorario = new GestorEncargoSacdHorario();
        $cEncargosSacdHorario = $gesEncargoSacdHorario->getEncargoSacdHorarios($aWhere);
        foreach ($cEncargosSacdHorario as $oEncargoSacdHorario) {
            $oEncargoSacdHorario->DBEliminar();
        }
        //encargo_sacd_horario_excepcion
        $gesEncargoSacdHorarioExcepcion = new GestorEncargoSacdHorarioExcepcion();
        $cEncargosSacdHorarioExcepcion = $gesEncargoSacdHorarioExcepcion->getEncargoSacdHorarioExcepciones($aWhere);
        foreach ($cEncargosSacdHorarioExcepcion as $oEncargoSacdHorarioExcepcion) {
            $oEncargoSacdHorarioExcepcion->DBEliminar();
        }

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
        if (array_key_exists('id_enc', $aDades)) $this->setId_enc($aDades['id_enc']);
        if (array_key_exists('id_tipo_enc', $aDades)) $this->setId_tipo_enc($aDades['id_tipo_enc']);
        if (array_key_exists('sf_sv', $aDades)) $this->setSf_sv($aDades['sf_sv']);
        if (array_key_exists('id_ubi', $aDades)) $this->setId_ubi($aDades['id_ubi']);
        if (array_key_exists('id_zona', $aDades)) $this->setId_zona($aDades['id_zona']);
        if (array_key_exists('desc_enc', $aDades)) $this->setDesc_enc($aDades['desc_enc']);
        if (array_key_exists('idioma_enc', $aDades)) $this->setIdioma_enc($aDades['idioma_enc']);
        if (array_key_exists('desc_lugar', $aDades)) $this->setDesc_lugar($aDades['desc_lugar']);
        if (array_key_exists('observ', $aDades)) $this->setObserv($aDades['observ']);
        if (array_key_exists('orden', $aDades)) $this->setOrden($aDades['orden']);
        if (array_key_exists('prioridad', $aDades)) $this->setPrioridad($aDades['prioridad']);
    }

    /**
     * Establece a 'empty' el valor de todos los atributos
     *
     */
    function setNullAllAtributes()
    {
        $aPK = $this->getPrimary_key();
        $this->setId_enc('');
        $this->setId_tipo_enc('');
        $this->setSf_sv('');
        $this->setId_ubi('');
        $this->setId_zona('');
        $this->setDesc_enc('');
        $this->setIdioma_enc('');
        $this->setDesc_lugar('');
        $this->setObserv('');
        $this->setPrimary_key($aPK);
    }


    /* MÉTODOS GET y SET --------------------------------------------------------*/

    /**
     * Recupera todos los atributos de Encargo en un array
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
     * Recupera la clave primaria de Encargo en un array
     *
     * @return array aPrimary_key
     */
    function getPrimary_key()
    {
        if (!isset($this->aPrimary_key)) {
            $this->aPrimary_key = array('id_enc' => $this->iid_enc);
        }
        return $this->aPrimary_key;
    }

    /**
     * Establece la clave primaria de Encargo en un array
     *
     * @return array aPrimary_key
     */
    public function setPrimary_key($a_id = '')
    {
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                if (($nom_id === 'id_enc') && $val_id !== '') $this->iid_enc = (int)$val_id;
            }
        }
    }

    /**
     * Recupera el atributo iid_enc de Encargo
     *
     * @return integer iid_enc
     */
    function getId_enc()
    {
        if (!isset($this->iid_enc) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->iid_enc;
    }

    /**
     * Establece el valor del atributo iid_enc de Encargo
     *
     * @param integer iid_enc='' optional
     */
    function setId_enc($iid_enc = '')
    {
        $this->iid_enc = $iid_enc;
    }

    /**
     * Recupera el atributo iid_tipo_enc de Encargo
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
     * Establece el valor del atributo iid_tipo_enc de Encargo
     *
     * @param integer iid_tipo_enc
     */
    function setId_tipo_enc($iid_tipo_enc)
    {
        $this->iid_tipo_enc = $iid_tipo_enc;
    }

    /**
     * Recupera el atributo isf_sv de Encargo
     *
     * @return integer isf_sv
     */
    function getSf_sv()
    {
        if (!isset($this->isf_sv) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->isf_sv;
    }

    /**
     * Establece el valor del atributo isf_sv de Encargo
     *
     * @param integer isf_sv='' optional
     */
    function setSf_sv($isf_sv = '')
    {
        $this->isf_sv = $isf_sv;
    }

    /**
     * Recupera el atributo iid_ubi de Encargo
     *
     * @return integer iid_ubi
     */
    function getId_ubi()
    {
        if (!isset($this->iid_ubi) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->iid_ubi;
    }

    /**
     * Establece el valor del atributo iid_ubi de Encargo
     *
     * @param integer iid_ubi='' optional
     */
    function setId_ubi($iid_ubi = '')
    {
        $this->iid_ubi = $iid_ubi;
    }

    /**
     * Recupera el atributo iid_zona de Encargo
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
     * Establece el valor del atributo iid_zona de Encargo
     *
     * @param integer iid_zona='' optional
     */
    function setId_zona($iid_zona = '')
    {
        $this->iid_zona = $iid_zona;
    }

    /**
     * Recupera el atributo sdesc_enc de Encargo
     *
     * @return string sdesc_enc
     */
    function getDesc_enc()
    {
        if (!isset($this->sdesc_enc) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->sdesc_enc;
    }

    /**
     * Establece el valor del atributo sdesc_enc de Encargo
     *
     * @param string sdesc_enc='' optional
     */
    function setDesc_enc($sdesc_enc = '')
    {
        $this->sdesc_enc = $sdesc_enc;
    }

    /**
     * Recupera el atributo sidioma_enc de Encargo
     *
     * @return string sidioma_enc
     */
    function getIdioma_enc()
    {
        if (!isset($this->sidioma_enc) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->sidioma_enc;
    }

    /**
     * Establece el valor del atributo sidioma_enc de Encargo
     *
     * @param string sidioma_enc='' optional
     */
    function setIdioma_enc($sidioma_enc = '')
    {
        $this->sidioma_enc = $sidioma_enc;
    }

    /**
     * Recupera el atributo sdesc_lugar de Encargo
     *
     * @return string sdesc_lugar
     */
    function getDesc_lugar()
    {
        if (!isset($this->sdesc_lugar) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->sdesc_lugar;
    }

    /**
     * Establece el valor del atributo sdesc_lugar de Encargo
     *
     * @param string sdesc_lugar='' optional
     */
    function setDesc_lugar($sdesc_lugar = '')
    {
        $this->sdesc_lugar = $sdesc_lugar;
    }

    /**
     * Recupera el atributo sobserv de Encargo
     *
     * @return string sobserv
     */
    function getObserv()
    {
        if (!isset($this->sobserv) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->sobserv;
    }

    /**
     * Establece el valor del atributo sobserv de Encargo
     *
     * @param string sobserv='' optional
     */
    function setObserv($sobserv = '')
    {
        $this->sobserv = $sobserv;
    }
    /**
     * Recupera el atributo iorden de Encargo
     *
     * @return int iorden
     */
    function getOrden()
    {
        if (!isset($this->iorden) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->iorden;
    }

    /**
     * Establece el valor del atributo iorden de Encargo
     *
     * @param int iorden='' optional
     */
    function setOrden($iorden = '')
    {
        $this->iorden = $iorden;
    }
    /**
     * Recupera el atributo iprioridad de Encargo
     *
     * @return int iprioridad
     */
    function getPrioridad()
    {
        if (!isset($this->iprioridad) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->iprioridad;
    }

    /**
     * Establece el valor del atributo iprioridad de Encargo
     *
     * @param int iprioridad='' optional
     */
    function setPrioridad($iprioridad = '')
    {
        $this->iprioridad = $iprioridad;
    }
    /* MÉTODOS GET y SET D'ATRIBUTOS QUE NO SON CAMPOS -----------------------------*/

    /**
     * Retorna una col·lecció d'objectes del tipus DatosCampo
     *
     */
    function getDatosCampos()
    {
        $oEncargoSet = new Set();

        $oEncargoSet->add($this->getDatosId_enc());
        $oEncargoSet->add($this->getDatosSf_sv());
        $oEncargoSet->add($this->getDatosId_ubi());
        $oEncargoSet->add($this->getDatosId_zona());
        $oEncargoSet->add($this->getDatosDesc_enc());
        $oEncargoSet->add($this->getDatosIdioma_enc());
        $oEncargoSet->add($this->getDatosDesc_lugar());
        $oEncargoSet->add($this->getDatosObserv());
        return $oEncargoSet->getTot();
    }


    /**
     * Recupera les propietats de l'atribut iid_enc de Encargo
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosId_enc()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'id_enc'));
        $oDatosCampo->setEtiqueta(_("id_enc"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut isf_sv de Encargo
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosSf_sv()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'sf_sv'));
        $oDatosCampo->setEtiqueta(_("sf_sv"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut iid_ubi de Encargo
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosId_ubi()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'id_ubi'));
        $oDatosCampo->setEtiqueta(_("id_ubi"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut iid_zona de Encargo
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosId_zona()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'id_zona'));
        $oDatosCampo->setEtiqueta(_("id_zona"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut sdesc_enc de Encargo
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosDesc_enc()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'desc_enc'));
        $oDatosCampo->setEtiqueta(_("desc_enc"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut sidioma_enc de Encargo
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosIdioma_enc()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'idioma_enc'));
        $oDatosCampo->setEtiqueta(_("idioma_enc"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut sdesc_lugar de Encargo
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosDesc_lugar()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'desc_lugar'));
        $oDatosCampo->setEtiqueta(_("desc_lugar"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut sobserv de Encargo
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosObserv()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'observ'));
        $oDatosCampo->setEtiqueta(_("observ"));
        return $oDatosCampo;
    }
}
