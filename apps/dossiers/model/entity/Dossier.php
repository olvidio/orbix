<?php

namespace dossiers\model\entity;

use core;
use web;

/**
 * Fitxer amb la Classe que accedeix a la taula d_dossiers_abiertos
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 25/11/2014
 */

/**
 * Clase que implementa la entidad d_dossiers_abiertos
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 25/11/2014
 */
class Dossier extends core\ClasePropiedades
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * aPrimary_key de Dossier
     *
     * @var array
     */
    private $aPrimary_key;

    /**
     * aDades de Dossier
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
     * Id_schema de Dossier
     *
     * @var integer
     */
    private $iid_schema;
    /**
     * Tabla de Dossier
     *
     * @var string
     */
    private $stabla;
    /**
     * Id_pau de Dossier
     *
     * @var string
     */
    private $sid_pau;
    /**
     * Id_tipo_dossier de Dossier
     *
     * @var integer
     */
    private $iid_tipo_dossier;
    /**
     * F_ini de Dossier
     *
     * @var web\DateTimeLocal
     */
    private $df_ini;
    /**
     * F_camb_dossier de Dossier
     *
     * @var web\DateTimeLocal
     */
    private $df_camb_dossier;
    /**
     * Status_dossier de Dossier
     *
     * @var boolean
     */
    private $bstatus_dossier;
    /**
     * F_status de Dossier
     *
     * @var web\DateTimeLocal
     */
    private $df_status;
    /* ATRIBUTOS QUE NO SON CAMPOS------------------------------------------------- */
    /**
     * oDbl de Dossier
     *
     * @var object
     */
    protected $oDbl;
    /**
     * NomTabla de Dossier
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
     * @param integer|array stabla,sid_pau,iid_tipo_dossier
     *                        $a_id. Un array con los nombres=>valores de las claves primarias.
     */
    function __construct($a_id = '')
    {
        $oDbl = $GLOBALS['oDB'];
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                if (($nom_id == 'tabla') && $val_id !== '') $this->stabla = (string)$val_id; // evitem SQL injection fent cast a string
                if (($nom_id == 'id_pau') && $val_id !== '') $this->sid_pau = (string)$val_id; // evitem SQL injection fent cast a string
                if (($nom_id == 'id_tipo_dossier') && $val_id !== '') $this->iid_tipo_dossier = (int)$val_id; 
            }
        }
        $this->setoDbl($oDbl);
        $this->setNomTabla('d_dossiers_abiertos');
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
        $aDades['f_ini'] = $this->df_ini;
        $aDades['f_camb_dossier'] = $this->df_camb_dossier;
        $aDades['status_dossier'] = $this->bstatus_dossier;
        $aDades['f_status'] = $this->df_status;
        array_walk($aDades, 'core\poner_null');
        //para el caso de los boolean FALSE, el pdo(+postgresql) pone string '' en vez de 0. Lo arreglo:
        if (core\is_true($aDades['status_dossier'])) {
            $aDades['status_dossier'] = 'true';
        } else {
            $aDades['status_dossier'] = 'false';
        }

        if ($bInsert === false) {
            //UPDATE
            $update = "
					f_ini                    = :f_ini,
					f_camb_dossier           = :f_camb_dossier,
					status_dossier           = :status_dossier,
					f_status                 = :f_status";
            if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE tabla='$this->stabla' AND id_pau='$this->sid_pau' AND id_tipo_dossier='$this->iid_tipo_dossier'")) === false) {
                $sClauError = 'Dossier.update.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'Dossier.update.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return false;
                }
            }
        } else {
            // INSERT
            array_unshift($aDades, $this->stabla, $this->sid_pau, $this->iid_tipo_dossier);
            $campos = "(tabla,id_pau,id_tipo_dossier,f_ini,f_camb_dossier,status_dossier,f_status)";
            $valores = "(:tabla,:id_pau,:id_tipo_dossier,:f_ini,:f_camb_dossier,:status_dossier,:f_status)";
            if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === false) {
                $sClauError = 'Dossier.insertar.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'Dossier.insertar.execute';
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
        if (isset($this->stabla) && isset($this->sid_pau) && isset($this->iid_tipo_dossier)) {
            if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE tabla='$this->stabla' AND id_pau='$this->sid_pau' AND id_tipo_dossier='$this->iid_tipo_dossier'")) === false) {
                $sClauError = 'Dossier.carregar';
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
        if (($oDbl->exec("DELETE FROM $nom_tabla WHERE tabla='$this->stabla' AND id_pau='$this->sid_pau' AND id_tipo_dossier='$this->iid_tipo_dossier'")) === false) {
            $sClauError = 'Dossier.eliminar';
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
    function setAllAtributes($aDades, $convert = FALSE)
    {
        if (!is_array($aDades)) return;
        if (array_key_exists('id_schema', $aDades)) $this->setId_schema($aDades['id_schema']);
        if (array_key_exists('tabla', $aDades)) $this->setTabla($aDades['tabla']);
        if (array_key_exists('id_pau', $aDades)) $this->setId_pau($aDades['id_pau']);
        if (array_key_exists('id_tipo_dossier', $aDades)) $this->setId_tipo_dossier($aDades['id_tipo_dossier']);
        if (array_key_exists('f_ini', $aDades)) $this->setF_ini($aDades['f_ini'], $convert);
        if (array_key_exists('f_camb_dossier', $aDades)) $this->setF_camb_dossier($aDades['f_camb_dossier'], $convert);
        if (array_key_exists('status_dossier', $aDades)) $this->setStatus_dossier($aDades['status_dossier']);
        if (array_key_exists('f_status', $aDades)) $this->setF_status($aDades['f_status'], $convert);
    }

    /**
     * Establece a 'empty' el valor de todos los atributos
     *
     */
    function setNullAllAtributes()
    {
        $aPK = $this->getPrimary_key();
        $this->setId_schema('');
        $this->setTabla('');
        $this->setId_pau('');
        $this->setId_tipo_dossier('');
        $this->setF_ini('');
        $this->setF_camb_dossier('');
        $this->setStatus_dossier('');
        $this->setF_status('');
        $this->setPrimary_key($aPK);
    }


    /* MÉTODOS GET y SET --------------------------------------------------------*/

    /**
     * Posa la data d'avuvi a f_status i status a true
     *
     * @return
     */
    function abrir()
    {
        $oHoy = new web\DateTimeLocal();
        $this->DBCarregar();
        $this->setF_status($oHoy);
        $this->setStatus_dossier('t');
        $this->DBGuardar();
    }

    /**
     * Posa la data d'avuvi a f_status i status a false
     *
     * @return
     */
    function cerrar()
    {
        $oHoy = new web\DateTimeLocal();
        $this->DBCarregar();
        $this->setF_status($oHoy);
        $this->setStatus_dossier('f');
        $this->DBGuardar();
    }

    /**
     * Recupera todos los atributos de Dossier en un array
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
     * Recupera la clave primaria de Dossier en un array
     *
     * @return array aPrimary_key
     */
    function getPrimary_key()
    {
        if (!isset($this->aPrimary_key)) {
            $this->aPrimary_key = array('tabla' => $this->stabla, 'id_pau' => $this->sid_pau, 'id_tipo_dossier' => $this->iid_tipo_dossier);
        }
        return $this->aPrimary_key;
    }

    /**
     * Establece la clave primaria de Dossier en un array
     *
     * @return array aPrimary_key
     */
    public function setPrimary_key($a_id = '')
    {
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                if (($nom_id == 'tabla') && $val_id !== '') $this->stabla = $val_id;
                if (($nom_id == 'id_pau') && $val_id !== '') $this->sid_pau = $val_id;
                if (($nom_id == 'id_tipo_dossier') && $val_id !== '') $this->iid_tipo_dossier = (int)$val_id; 
            }
        }
    }

    /**
     * Recupera el atributo stabla de Dossier
     *
     * @return string stabla
     */
    function getTabla()
    {
        if (!isset($this->stabla) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->stabla;
    }

    /**
     * Establece el valor del atributo stabla de Dossier
     *
     * @param string stabla
     */
    function setTabla($stabla)
    {
        $this->stabla = $stabla;
    }

    /**
     * Recupera el atributo sid_pau de Dossier
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
     * Establece el valor del atributo sid_pau de Dossier
     *
     * @param string sid_pau
     */
    function setId_pau($sid_pau)
    {
        $this->sid_pau = $sid_pau;
    }

    /**
     * Recupera el atributo iid_tipo_dossier de Dossier
     *
     * @return integer iid_tipo_dossier
     */
    function getId_tipo_dossier()
    {
        if (!isset($this->iid_tipo_dossier) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->iid_tipo_dossier;
    }

    /**
     * Establece el valor del atributo iid_tipo_dossier de Dossier
     *
     * @param integer iid_tipo_dossier
     */
    function setId_tipo_dossier($iid_tipo_dossier)
    {
        $this->iid_tipo_dossier = $iid_tipo_dossier;
    }

    /**
     * Recupera el atributo df_ini de Dossier
     *
     * @return web\DateTimeLocal df_ini
     */
    function getF_ini()
    {
        if (!isset($this->df_ini) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        if (empty($this->df_ini)) {
            return new web\NullDateTimeLocal();
        }
        $oConverter = new core\Converter('date', $this->df_ini);
        return $oConverter->fromPg();
    }

    /**
     * Establece el valor del atributo df_ini de Dossier
     * Si df_ini es string, y convert=true se convierte usando el formato webDateTimeLocal->getFormat().
     * Si convert es false, df_ini debe ser un string en formato ISO (Y-m-d). Corresponde al pgstyle de la base de datos.
     *
     * @param date|string df_ini='' optional.
     * @param boolean convert=true optional. Si es false, df_ini debe ser un string en formato ISO (Y-m-d).
     */
    function setF_ini($df_ini = '', $convert = true)
    {
        if ($convert === true && !empty($df_ini)) {
            $oConverter = new core\Converter('date', $df_ini);
            $this->df_ini = $oConverter->toPg();
        } else {
            $this->df_ini = $df_ini;
        }
    }

    /**
     * Recupera el atributo df_camb_dossier de Dossier
     *
     * @return web\DateTimeLocal df_camb_dossier
     */
    function getF_camb_dossier()
    {
        if (!isset($this->df_camb_dossier) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        if (empty($this->df_camb_dossier)) {
            return new web\NullDateTimeLocal();
        }
        $oConverter = new core\Converter('date', $this->df_camb_dossier);
        return $oConverter->fromPg();
    }

    /**
     * Establece el valor del atributo df_camb_dossier de Dossier
     * Si df_camb_dossier es string, y convert=true se convierte usando el formato webDateTimeLocal->getFormat().
     * Si convert es false, df_camb_dossier debe ser un string en formato ISO (Y-m-d). Corresponde al pgstyle de la base de datos.
     *
     * @param date|string df_camb_dossier='' optional.
     * @param boolean convert=true optional. Si es false, df_camb_dossier debe ser un string en formato ISO (Y-m-d).
     */
    function setF_camb_dossier($df_camb_dossier = '', $convert = true)
    {
        if ($convert === true && !empty($df_camb_dossier)) {
            $oConverter = new core\Converter('date', $df_camb_dossier);
            $this->df_camb_dossier = $oConverter->toPg();
        } else {
            $this->df_camb_dossier = $df_camb_dossier;
        }
    }

    /**
     * Recupera el atributo bstatus_dossier de Dossier
     *
     * @return boolean bstatus_dossier
     */
    function getStatus_dossier()
    {
        if (!isset($this->bstatus_dossier) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->bstatus_dossier;
    }

    /**
     * Establece el valor del atributo bstatus_dossier de Dossier
     *
     * @param boolean bstatus_dossier='f' optional
     */
    function setStatus_dossier($bstatus_dossier = 'f')
    {
        $this->bstatus_dossier = $bstatus_dossier;
    }

    /**
     * Recupera el atributo df_status de Dossier
     *
     * @return web\DateTimeLocal df_status
     */
    function getF_status()
    {
        if (!isset($this->df_status) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        if (empty($this->df_status)) {
            return new web\NullDateTimeLocal();
        }
        $oConverter = new core\Converter('date', $this->df_status);
        return $oConverter->fromPg();
    }

    /**
     * Establece el valor del atributo df_status de Dossier
     * Si df_status es string, y convert=true se convierte usando el formato webDateTimeLocal->getFormat().
     * Si convert es false, df_status debe ser un string en formato ISO (Y-m-d). Corresponde al pgstyle de la base de datos.
     *
     * @param date|string df_status='' optional.
     * @param boolean convert=true optional. Si es false, df_status debe ser un string en formato ISO (Y-m-d).
     */
    function setF_status($df_status = '', $convert = true)
    {
        if ($convert === true && !empty($df_status)) {
            $oConverter = new core\Converter('date', $df_status);
            $this->df_status = $oConverter->toPg();
        } else {
            $this->df_status = $df_status;
        }
    }
    /* MÉTODOS GET y SET D'ATRIBUTOS QUE NO SON CAMPOS -----------------------------*/

    /**
     * Retorna una col·lecció d'objectes del tipus DatosCampo
     *
     */
    function getDatosCampos()
    {
        $oDossierSet = new core\Set();

        $oDossierSet->add($this->getDatosId_schema());
        $oDossierSet->add($this->getDatosF_ini());
        $oDossierSet->add($this->getDatosF_camb_dossier());
        $oDossierSet->add($this->getDatosStatus_dossier());
        $oDossierSet->add($this->getDatosF_status());
        return $oDossierSet->getTot();
    }


    /**
     * Recupera les propietats de l'atribut iid_schema de Dossier
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosId_schema()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'id_schema'));
        $oDatosCampo->setEtiqueta(_("id_schema"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut df_ini de Dossier
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosF_ini()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'f_ini'));
        $oDatosCampo->setEtiqueta(_("fecha inicio"));
        $oDatosCampo->setTipo('fecha');
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut df_camb_dossier de Dossier
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosF_camb_dossier()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'f_camb_dossier'));
        $oDatosCampo->setEtiqueta(_("fecha cambio dossier"));
        $oDatosCampo->setTipo('fecha');
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut bstatus_dossier de Dossier
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosStatus_dossier()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'status_dossier'));
        $oDatosCampo->setEtiqueta(_("status dossier"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut df_status de Dossier
     * en una clase del tipus DatosCampo
     *
     * @return core\DatosCampo
     */
    function getDatosF_status()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new core\DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'f_status'));
        $oDatosCampo->setEtiqueta(_("fecha status"));
        $oDatosCampo->setTipo('fecha');
        return $oDatosCampo;
    }
}
