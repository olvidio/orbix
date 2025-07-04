<?php

namespace cambios\model\entity;

use actividades\model\entity\ActividadAll;
use actividades\model\entity\GestorNivelStgr;
use actividades\model\entity\GestorRepeticion;
use actividadtarifas\model\entity\GestorTipoTarifa;
use cambios\model\GestorAvisoCambios;
use core\ClasePropiedades;
use core\ConfigGlobal;
use core\ConverterDate;
use core\ConverterJson;
use core\DatosCampo;
use core\Set;
use JsonException;
use personas\model\entity\PersonaSacd;
use procesos\model\entity\GestorActividadFase;
use stdClass;
use ubis\model\entity\Ubi;
use web\DateTimeLocal;
use web\NullDateTimeLocal;
use function core\is_true;

/**
 * Fitxer amb la Classe que accedeix a la taula av_cambios
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 17/4/2019
 */

/**
 * Clase que implementa la entidad av_cambios
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 17/4/2019
 */
class Cambio extends ClasePropiedades
{

    //  tipo cambio constants.
    const TIPO_CMB_INSERT = 1;
    const TIPO_CMB_UPDATE = 2;
    const TIPO_CMB_DELETE = 3;
    const TIPO_CMB_FASE = 4;

    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * aPrimary_key de Cambio
     *
     * @var array
     */
    protected $aPrimary_key;

    /**
     * aDades de Cambio
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
     * Id_schema de Cambio
     *
     * @var integer
     */
    protected $iid_schema;
    /**
     * Id_item_cambio de Cambio
     *
     * @var integer
     */
    protected $iid_item_cambio;
    /**
     * Id_tipo_cambio de Cambio
     *
     * @var integer
     */
    protected $iid_tipo_cambio;
    /**
     * Id_activ de Cambio
     *
     * @var integer
     */
    protected $iid_activ;
    /**
     * Id_tipo_activ de Cambio
     *
     * @var integer
     */
    protected $iid_tipo_activ;
    /**
     * JSON json_fases_sv de Cambio
     *
     * @var object JSON
     */
    protected $json_fases_sv;
    /**
     * JSON json_fases_sf de Cambio
     *
     * @var object JSON
     */
    protected $json_fases_sf;
    /**
     * Id_status de Cambio
     *
     * @var integer
     */
    protected $iid_status;
    /**
     * Dl_org de Cambio
     *
     * @var string
     */
    protected $sdl_org;
    /**
     * oPersonaNota de Cambio
     *
     * @var string
     */
    protected $sobjeto;
    /**
     * Propiedad de Cambio
     *
     * @var string
     */
    protected $spropiedad;
    /**
     * Valor_old de Cambio
     *
     * @var string
     */
    protected $svalor_old;
    /**
     * Valor_new de Cambio
     *
     * @var string
     */
    protected $svalor_new;
    /**
     * Quien_cambia de Cambio
     *
     * @var integer
     */
    protected $iquien_cambia;
    /**
     * Sfsv_quien_cambia de Cambio
     *
     * @var integer
     */
    protected $isfsv_quien_cambia;
    /**
     * Timestamp_cambio de Cambio
     *
     * @var integer
     */
    protected $itimestamp_cambio;
    /* ATRIBUTOS QUE NO SON CAMPOS------------------------------------------------- */
    /**
     * oDbl de Cambio
     *
     * @var object
     */
    protected $oDbl;
    /**
     * NomTabla de Cambio
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
     * @param integer|array iid_item_cambio
     *                        $a_id. Un array con los nombres=>valores de las claves primarias.
     */
    function __construct($a_id = '')
    {
        $oDbl = $GLOBALS['oDBPC'];
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                if (($nom_id === 'id_item_cambio') && $val_id !== '') $this->iid_item_cambio = (int)$val_id;
            }
        } else {
            if (isset($a_id) && $a_id !== '') {
                $this->iid_item_cambio = (integer)$a_id;
                $this->aPrimary_key = array('id_item_cambio' => $this->iid_item_cambio);
            }
        }
        $this->setoDbl($oDbl);
        $this->setNomTabla('av_cambios');
    }

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    /**
     * Guarda los atributos de la clase en la base de datos.
     * Si no existe el registro, hace el insert; Si existe hace el update.
     *
     * @param bool optional $quiet : true per que no apunti els canvis. 0 (per defecte) apunta els canvis.
     */
    public function DBGuardar($quiet = 0)
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        if ($this->DBCarregar('guardar') === FALSE) {
            $bInsert = TRUE;
        } else {
            $bInsert = FALSE;
        }
        $aDades = [];
        $aDades['id_tipo_cambio'] = $this->iid_tipo_cambio;
        $aDades['id_activ'] = $this->iid_activ;
        $aDades['id_tipo_activ'] = $this->iid_tipo_activ;
        $aDades['json_fases_sv'] = $this->json_fases_sv;
        $aDades['json_fases_sf'] = $this->json_fases_sf;
        $aDades['id_status'] = $this->iid_status;
        $aDades['dl_org'] = $this->sdl_org;
        $aDades['objeto'] = $this->sobjeto;
        $aDades['propiedad'] = $this->spropiedad;
        $aDades['valor_old'] = $this->svalor_old;
        $aDades['valor_new'] = $this->svalor_new;
        $aDades['quien_cambia'] = $this->iquien_cambia;
        $aDades['sfsv_quien_cambia'] = $this->isfsv_quien_cambia;
        $aDades['timestamp_cambio'] = $this->itimestamp_cambio;
        array_walk($aDades, 'core\poner_null');

        if ($bInsert === FALSE) {
            //UPDATE
            $update = "
					id_tipo_cambio           = :id_tipo_cambio,
					id_activ                 = :id_activ,
					id_tipo_activ            = :id_tipo_activ,
                    json_fases_sv            = :json_fases_sv,
					json_fases_sf            = :json_fases_sf,
					id_status                = :id_status,
					dl_org                   = :dl_org,
					objeto                   = :objeto,
					propiedad                = :propiedad,
					valor_old                = :valor_old,
					valor_new                = :valor_new,
					quien_cambia             = :quien_cambia,
					sfsv_quien_cambia        = :sfsv_quien_cambia,
					timestamp_cambio         = :timestamp_cambio
                    ";
            if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_item_cambio='$this->iid_item_cambio'")) === FALSE) {
                $sClauError = 'Cambio.update.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return FALSE;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'Cambio.update.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return FALSE;
                }
            }
        } else {
            // INSERT
            /* uso el id_schema 3000, que no debería corresponder a ningun esquema (en todo caso a 'public')
             * Es para el caso de las dl que no tienen instalado el módulo de 'cambios'. Para distinguir los cambios
             * debo usar la dl_org. No uso el id_schema correspondiente, porque si más tarde instalan el módulo
             * 'cambios', puede haber conflico con el id_item_cambio.
             */
            $mi_esquema = 3000;
            $campos = "(id_schema,id_tipo_cambio,id_activ,id_tipo_activ,json_fases_sv,json_fases_sf,id_status,dl_org,objeto,propiedad,valor_old,valor_new,quien_cambia,sfsv_quien_cambia,timestamp_cambio)";
            $valores = "($mi_esquema,:id_tipo_cambio,:id_activ,:id_tipo_activ,:json_fases_sv,:json_fases_sf,:id_status,:dl_org,:objeto,:propiedad,:valor_old,:valor_new,:quien_cambia,:sfsv_quien_cambia,:timestamp_cambio)";
            if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === FALSE) {
                $sClauError = 'Cambio.insertar.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return FALSE;
            } else {
                try {
                    $oDblSt->execute($aDades);
                } catch (\PDOException $e) {
                    $err_txt = $e->errorInfo[2];
                    $this->setErrorTxt($err_txt);
                    $sClauError = 'Cambio.insertar.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                    return FALSE;
                }
            }
            $id_seq = $nom_tabla . "_id_item_cambio_seq";
            $this->iid_item_cambio = $oDbl->lastInsertId($id_seq);
        }
        $this->setAllAtributes($aDades);
        // Creo que solo hay que disparar el generador de avisos en las dl que tengan el módulo.
        if (ConfigGlobal::is_app_installed('cambios')) {
            // Para el caso de poner anotado, no debo disparar el generador de avisos.
            if (empty($quiet)) {
                $this->generarTabla();
            }
        }
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
        if (isset($this->iid_item_cambio)) {
            if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_item_cambio='$this->iid_item_cambio'")) === FALSE) {
                $sClauError = 'Cambio.carregar';
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
        if (($oDbl->exec("DELETE FROM $nom_tabla WHERE id_item_cambio='$this->iid_item_cambio'")) === FALSE) {
            $sClauError = 'Cambio.eliminar';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return FALSE;
        }
        return TRUE;
    }

    /* OTROS MÉTODOS  ----------------------------------------------------------*/

    protected function getNomActivEliminada($iId)
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        if (($qRs = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_activ=$iId AND id_tipo_cambio=3")) === false) {
            $sClauError = 'ActividadCambio.NomActivEliminada';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        $aDades = $qRs->fetch(\PDO::FETCH_ASSOC);
        if ($aDades === FALSE) {
            $nomActiv = _('Actividad Eliminada');
        } else {
            $nomActiv = $aDades['valor_old'] . "(" . _('Eliminado') . ")";
        }
        return $nomActiv;
    }

    public function getAvisoTxt()
    {
        $bEliminada = false;
        $sPropiedad = '';
        $sValor_old = '';
        $sValor_new = '';
        $iTipo_cambio = $this->getId_tipo_cambio();
        $sObjeto = $this->getObjeto();
        $iId = $this->getId_activ();
        $sPropiedad = $this->getPropiedad();
        $sValor_old = $this->getValor_old();
        $sValor_new = $this->getValor_new();

        $oActividad = new ActividadAll($iId);
        $DatosCampoStatus = $oActividad->getDatosStatus();
        $aStatus = $DatosCampoStatus->getLista();

        $sNomActiv = $oActividad->getNom_activ();
        if (empty($sNomActiv)) { // se ha eliminado. Busco el nombre en el apunte eliminado
            $bEliminada = true;
            $sNomActiv = $this->getNomActivEliminada($iId);
        }

        $sPropiedad = empty($sPropiedad) ? '-' : $sPropiedad;

        /*
         * De momento, desde el servidor exterior (conexión a mail) solo
         * están accesibles los sacd.
         *
         * Para el resto de id_nom devuelvo false para que no lo ponga en la lista.
         *
         */
        if ($sPropiedad === 'id_nom') {
            if (!empty($sValor_old)) {
                //$oPersona = Persona::NewPersona($sValor_old);
                $oPersona = new PersonaSacd($sValor_old);
                $sValor_old = $oPersona->getPrefApellidosNombre();
            }
            if (!empty($sValor_new)) {
                //$oPersona = Persona::NewPersona($sValor_new);
                $oPersona = new PersonaSacd($sValor_new);
                $sValor_new = $oPersona->getPrefApellidosNombre();
            }
        }
        if ($sPropiedad === 'id_ubi') {
            if (!empty($sValor_old)) {
                $oUbi = Ubi::NewUbi($sValor_old);
                $sValor_old = $oUbi->getNombre_ubi();
            }
            if (!empty($sValor_new)) {
                $oUbi = Ubi::NewUbi($sValor_new);
                $sValor_new = $oUbi->getNombre_ubi();
            }
        }
        /* Per posar noms que s'entenguin als camps de l'activitat */
        if ($sObjeto === 'Actividad' or
            $sObjeto === 'ActividadDl' or
            $sObjeto === 'ActividadEx') {

            if ($sPropiedad === 'status') {
                $sValor_old = $aStatus[$sValor_old];
                $sValor_new = $aStatus[$sValor_new];
            }
            // Caso especial si el campo es fecha.
            if ($sPropiedad === 'f_ini' || $sPropiedad === 'f_fin') {
                $oFOld = new DateTimeLocal($sValor_old);
                $sValor_old = $oFOld->getFromLocal();
                $oFNew = new DateTimeLocal($sValor_new);
                $sValor_new = $oFNew->getFromLocal();
            }
            if ($sPropiedad === 'id_tarifa') {
                $gesTarifas = new GestorTipoTarifa();
                $aTarifas = $gesTarifas->getArrayTipoTarifas();
                $sValor_old = empty($sValor_old) ? $sValor_old : $aTarifas[$sValor_old];
                $sValor_new = empty($sValor_new) ? $sValor_new : $aTarifas[$sValor_new];
            }
            if ($sPropiedad === 'id_repeticion') {
                $gesRepeticion = new GestorRepeticion();
                $aRepeticion = $gesRepeticion->getArrayRepeticion();
                $sValor_old = empty($sValor_old) ? $sValor_old : $aRepeticion[$sValor_old];
                $sValor_new = empty($sValor_new) ? $sValor_new : $aRepeticion[$sValor_new];
            }
            if ($sPropiedad === 'nivel_stgr') {
                $gesNivelStgr = new GestorNivelStgr();
                $aNivelStgr = $gesNivelStgr->getArrayNivelesStgr();
                $sValor_old = empty($sValor_old) ? $sValor_old : $aNivelStgr[$sValor_old];
                $sValor_new = empty($sValor_new) ? $sValor_new : $aNivelStgr[$sValor_new];
            }
        }

        $ObjetoFullPath = GestorAvisoCambios::getFullPathObj($sObjeto);
        $oObject = new $ObjetoFullPath();
        $oDbl = $oObject->getoDbl();
        $cDatosCampos = $oObject->getDatosCampos();
        // para ajustar el nombre del campo y el valor a algo más legible:
        $etiqueta = $sPropiedad;
        foreach ($cDatosCampos as $oDatosCampo) {
            if ($oDatosCampo->getNom_camp() == $sPropiedad) {
                $etiqueta = $oDatosCampo->getEtiqueta();
                // si es boolean, traduzco a true-false:
                $tipo = $oDatosCampo->datos_campo($oDbl, 'tipo');
                if ($tipo === 'bool') {
                    // OJO. Excepción en el caso de completar una fase, el campo es completado (bool), pero en el 
                    // valor_old lo que pongo es el id_fase.
                    if ($sObjeto !== 'ActividadProcesoTarea' && $sPropiedad !== 'completado') {
                        $sValor_old = is_true($sValor_old) ? 'true' : 'false';
                    }
                    $sValor_new = is_true($sValor_new) ? 'true' : 'false';
                }
            }
        }

        // para los asistentes que no son sacd. No tengo su nombre.
        if ($sObjeto === 'Asistente' or
            $sObjeto === 'AsistenteDl' or
            $sObjeto === 'AsistenteOut' or
            $sObjeto === 'AsistenteEx' or
            $sObjeto === 'AsistenteIn') {
            if (empty($sValor_new) && empty($sValor_old)) return FALSE;
        }
        $sValor_old = empty($sValor_old) ? '-' : $sValor_old;
        $sValor_new = empty($sValor_new) ? '-' : $sValor_new;

        $sformat = '';
        switch ($iTipo_cambio) {
            case Cambio::TIPO_CMB_INSERT: // (1) insert.
                switch ($sObjeto) {
                    case 'Actividad':
                    case 'ActividadDl':
                    case 'ActividadEx':
                        $sformat = _("Actividad: se ha creado la actividad \"%1\$s\"");
                        break;
                    case 'ActividadCargo':
                        $sformat = _("Cl: se ha asignado un cargo a \"%4\$s\" a la actividad \"%1\$s\"");
                        break;
                    case 'ActividadCargoSacd':
                        $sformat = _("Sacd: se ha asignado el sacd \"%4\$s\" a la actividad \"%1\$s\"");
                        break;
                    case 'Asistente':
                    case 'AsistenteDl':
                    case 'AsistenteOut':
                    case 'AsistenteEx':
                    case 'AsistenteIn':
                        $sformat = _("Asistencia: \"%4\$s\" se ha incorporado a la actividad \"%1\$s\"");
                        break;
                    case 'CentroEncargado':
                        $sformat = _("Ctr: se ha asignado el ctr \"%4\$s\" a la actividad \"%1\$s\"");
                        break;
                }
                break;
            case Cambio::TIPO_CMB_UPDATE: // (2) update.
                switch ($sObjeto) {
                    case 'Actividad':
                    case 'ActividadDl':
                    case 'ActividadEx':
                        $sformat = _("Actividad: la actividad \"%1\$s\" ha cambiado el campo \"%2\$s\" de \"%3\$s\" a \"%4\$s\"");
                        break;
                    case 'ActividadCargo':
                        $sformat = _("Cl: ha cambiado el cargo en la actividad \"%1\$s\" el campo \"%2\$s\" de \"%3\$s\" a \"%4\$s\"");
                        break;
                    case 'ActividadCargoSacd':
                        $sformat = _("Sacd: ha cambiado el cargo en la actividad \"%1\$s\" el campo \"%2\$s\" de \"%3\$s\" a \"%4\$s\"");
                        break;
                    case 'Asistente':
                    case 'AsistenteDl':
                    case 'AsistenteOut':
                    case 'AsistenteEx':
                    case 'AsistenteIn':
                        $sformat = _("Asistente: ha cambiado la asistencia en la actividad \"%1\$s\" el campo \"%2\$s\" de \"%3\$s\" a \"%4\$s\"");
                        break;
                    case 'CentroEncargado':
                        $sformat = _("Ctr: ctr \"%2\$s\" Ha cambiado a la actividad \"%1\$s\"");
                        break;
                }
                break;
            case Cambio::TIPO_CMB_DELETE: // (3) delete.
                switch ($sObjeto) {
                    case 'Actividad':
                    case 'ActividadDl':
                    case 'ActividadEx':
                        $sformat = _("Actividad: se ha eliminado la actividad \"%3\$s\"");
                        break;
                    case 'ActividadCargo':
                        $sformat = _("Cl: se ha quitado el cargo a \"%3\$s\" de la actividad \"%1\$s\"");
                        break;
                    case 'ActividadCargoSacd':
                        $sformat = _("Sacd: se ha quitado al sacd \"%3\$s\" de la actividad \"%1\$s\"");
                        break;
                    case 'Asistente':
                    case 'AsistenteDl':
                    case 'AsistenteOut':
                    case 'AsistenteEx':
                    case 'AsistenteIn':
                        $sformat = _("Asistencia: \"%3\$s\" se ha borrado de la actividad \"%1\$s\"");
                        break;
                    case 'CentroEncargado':
                        $sformat = _("Ctr: se ha quitado al ctr \"%3\$s\" de la actividad \"%1\$s\"");
                        break;
                }
                break;
            case Cambio::TIPO_CMB_FASE: // (4) cambio de fase o status.
                // en el caso especial de completado fase, uso el valor_old para poner el id_fase, y el new el estado de completado.
                $id_fase = $sValor_old;
                $GesActividadFase = new GestorActividadFase();

                if (ConfigGlobal::mi_sfsv() == 1) {
                    $aFases = $this->getJson_fases_sv();
                } else {
                    $aFases = $this->getJson_fases_sf();
                }
                $idStatus = $this->getId_status();

                if (!$bEliminada) {
                    if (!empty($sPropiedad)) {
                        $cFases = $GesActividadFase->getActividadFases(array('id_fase' => $id_fase));
                        $sFase = $cFases[0]->getDesc_fase();

                        if (is_true($sValor_new)) {
                            $sformat = _("Fase \"%2\$s\" marcada en la actividad \"%1\$s\"");
                        } else {
                            $sformat = _("Fase \"%2\$s\" desmarcada en la actividad \"%1\$s\"");
                        }
                    } else if (!empty($idStatus)) {
                        $sFase = $aStatus[$idStatus];

                        $sformat = _("Fase cambiada en la actividad \"%1\$s\". Status \"%3\$s\"");
                        if ($sValor_old === '-' && $sValor_new == 1) {
                            $sformat = _("Status \"%2\$s\" completado en la actividad \"%1\$s\". Status actual \"%3\$s\"");
                        }
                        if ($sValor_old == 1 && $sValor_new === '-') {
                            $sformat = _("Status \"%2\$s\" eliminada en la actividad \"%1\$s\". Status actual \"%3\$s\"");
                        }
                    }
                } else {
                    $sFase = '';
                    $sformat = _("Fase cambiada en la actividad \"%1\$s\"");
                }
                return sprintf($sformat, $sNomActiv, $sFase);
                break;
        }

        if (empty($sformat)) {
            $sTxt = "$sNomActiv; $etiqueta; $sValor_old; $sValor_new";
        } else {
            $sTxt = sprintf($sformat, $sNomActiv, $etiqueta, $sValor_old, $sValor_new);
        }
        return $sTxt;
    }


    /* MÉTODOS PRIVADOS ----------------------------------------------------------*/

    /**
     * Posa en marxa un procés per generar la taula d'avisos per cada usuari.
     *
     * @return true.
     *
     */
    function generarTabla()
    {
        $program = ConfigGlobal::$directorio . '/apps/cambios/controller/avisos_generar_tabla.php';
        $username = ConfigGlobal::mi_usuario();
        $pwd = ConfigGlobal::mi_pass();
        $err = ConfigGlobal::$directorio . '/log/avisos.err';
        $out = ConfigGlobal::$directorio . '/log/avisos.out';
        /* Hay que pasarle los argumentos que no tienen si se le llama por command line:
         $username;
         $password;
         $dir_web = orbix | pruebas;
         document_root = /home/dani/orbix_local
         $ubicacion = 'sv';
         $esquema_web = 'H-dlbv';
         $private = 'sf'; para el caso del servidor exterior en dlb. puerto distinto.
         $DB_SERVER = 1 o 2; para indicar el servidor dede el que se ejecuta. (ver comentario en clase: CambioAnotado)
         */
        $dirweb = $_SERVER['DIRWEB'];
        $doc_root = $_SERVER['DOCUMENT_ROOT'];
        $ubicacion = getenv('UBICACION');
        $esquema_web = getenv('ESQUEMA');
        $private = getenv('PRIVATE');
        $private = empty($private) ? 'x' : $private;
        $db_server = getenv('DB_SERVER');

        // Si he entrado escogiendo el esquema de un desplegable, no tengo el valor
        if (empty($esquema_web)) {
            $esquema_web = ConfigGlobal::mi_region_dl();
        }

        $command = "nohup /usr/bin/php $program $username $pwd $dirweb $doc_root $ubicacion $esquema_web $private $db_server >> $out 2>> $err < /dev/null &";
        exec($command);
    }

    /**
     * Establece el valor de todos los atributos
     *
     * @param array $aDades
     */
    function setAllAtributes(array $aDades, $convert = FALSE)
    {
        if (!is_array($aDades)) return;
        if (array_key_exists('id_schema', $aDades)) $this->setId_schema($aDades['id_schema']);
        if (array_key_exists('id_item_cambio', $aDades)) $this->setId_item_cambio($aDades['id_item_cambio']);
        if (array_key_exists('id_tipo_cambio', $aDades)) $this->setId_tipo_cambio($aDades['id_tipo_cambio']);
        if (array_key_exists('id_activ', $aDades)) $this->setId_activ($aDades['id_activ']);
        if (array_key_exists('id_tipo_activ', $aDades)) $this->setId_tipo_activ($aDades['id_tipo_activ']);
        if (array_key_exists('json_fases_sv', $aDades)) $this->setJson_fases_sv($aDades['json_fases_sv'], TRUE);
        if (array_key_exists('json_fases_sf', $aDades)) $this->setJson_fases_sf($aDades['json_fases_sf'], TRUE);
        if (array_key_exists('id_status', $aDades)) $this->setId_status($aDades['id_status']);
        if (array_key_exists('dl_org', $aDades)) $this->setDl_org($aDades['dl_org']);
        if (array_key_exists('objeto', $aDades)) $this->setObjeto($aDades['objeto']);
        if (array_key_exists('propiedad', $aDades)) $this->setPropiedad($aDades['propiedad']);
        if (array_key_exists('valor_old', $aDades)) $this->setValor_old($aDades['valor_old']);
        if (array_key_exists('valor_new', $aDades)) $this->setValor_new($aDades['valor_new']);
        if (array_key_exists('quien_cambia', $aDades)) $this->setQuien_cambia($aDades['quien_cambia']);
        if (array_key_exists('sfsv_quien_cambia', $aDades)) $this->setSfsv_quien_cambia($aDades['sfsv_quien_cambia']);
        if (array_key_exists('timestamp_cambio', $aDades)) $this->setTimestamp_cambio($aDades['timestamp_cambio'], $convert);
    }

    /**
     * Establece a 'empty' el valor de todos los atributos
     *
     */
    function setNullAllAtributes()
    {
        $aPK = $this->getPrimary_key();
        $this->setId_schema('');
        $this->setId_item_cambio('');
        $this->setId_tipo_cambio('');
        $this->setId_activ('');
        $this->setId_tipo_activ('');
        $this->setJson_fases_sv('');
        $this->setJson_fases_sf('');
        $this->setId_status('');
        $this->setDl_org('');
        $this->setObjeto('');
        $this->setPropiedad('');
        $this->setValor_old('');
        $this->setValor_new('');
        $this->setQuien_cambia('');
        $this->setSfsv_quien_cambia('');
        $this->setTimestamp_cambio('');
        $this->setPrimary_key($aPK);
    }


    /* MÉTODOS GET y SET --------------------------------------------------------*/

    /**
     * Recupera todos los atributos de Cambio en un array
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
     * Recupera la clave primaria de Cambio en un array
     *
     * @return array aPrimary_key
     */
    function getPrimary_key()
    {
        if (!isset($this->aPrimary_key)) {
            $this->aPrimary_key = array('id_item_cambio' => $this->iid_item_cambio);
        }
        return $this->aPrimary_key;
    }

    /**
     * Establece la clave primaria de Cambio en un array
     *
     * @return array aPrimary_key
     */
    public function setPrimary_key($a_id = '')
    {
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                if (($nom_id === 'id_item_cambio') && $val_id !== '') $this->iid_item_cambio = (int)$val_id;
            }
        }
    }

    /**
     * Recupera el atributo iid_item_cambio de Cambio
     *
     * @return integer iid_item_cambio
     */
    function getId_item_cambio()
    {
        if (!isset($this->iid_item_cambio) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->iid_item_cambio;
    }

    /**
     * Establece el valor del atributo iid_item_cambio de Cambio
     *
     * @param integer iid_item_cambio
     */
    function setId_item_cambio($iid_item_cambio)
    {
        $this->iid_item_cambio = $iid_item_cambio;
    }

    /**
     * Recupera el atributo iid_tipo_cambio de Cambio
     *
     * @return integer iid_tipo_cambio
     */
    function getId_tipo_cambio()
    {
        if (!isset($this->iid_tipo_cambio) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->iid_tipo_cambio;
    }

    /**
     * Establece el valor del atributo iid_tipo_cambio de Cambio
     *
     * @param integer iid_tipo_cambio='' optional
     */
    function setId_tipo_cambio($iid_tipo_cambio = '')
    {
        $this->iid_tipo_cambio = $iid_tipo_cambio;
    }

    /**
     * Recupera el atributo iid_activ de Cambio
     *
     * @return integer iid_activ
     */
    function getId_activ()
    {
        if (!isset($this->iid_activ) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->iid_activ;
    }

    /**
     * Establece el valor del atributo iid_activ de Cambio
     *
     * @param integer iid_activ='' optional
     */
    function setId_activ($iid_activ = '')
    {
        $this->iid_activ = $iid_activ;
    }

    /**
     * Recupera el atributo iid_tipo_activ de Cambio
     *
     * @return integer iid_tipo_activ
     */
    function getId_tipo_activ()
    {
        if (!isset($this->iid_tipo_activ) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->iid_tipo_activ;
    }

    /**
     * Establece el valor del atributo iid_tipo_activ de Cambio
     *
     * @param integer iid_tipo_activ='' optional
     */
    function setId_tipo_activ($iid_tipo_activ = '')
    {
        $this->iid_tipo_activ = $iid_tipo_activ;
    }

    /**
     * Recupera el atributo json_fases_sv de Cambio
     *
     * @param boolean $bArray si hay que devolver un array en vez de un objeto.
     * @throws JsonException
     */
    public function getJson_fases_sv(bool $bArray = FALSE): array|stdClass|null
    {
        if (!isset($this->json_fases_sv) && !$this->bLoaded) {
            $this->DBCarregar();
        }

        return (new ConverterJson($this->json_fases_sv, $bArray))->fromPg();
    }

    /**
     * @throws JsonException
     */
    public function setJson_fases_sv(string|array|null $oJSON, bool $db = FALSE): void
    {
        $this->json_fases_sv = (new ConverterJson($oJSON, FALSE))->toPg($db);
    }

    /**
     * Recupera el atributo json_fases_sf de Cambio
     *
     * @param boolean $bArray si hay que devolver un array en vez de un objeto.
     * @throws JsonException
     */
    function getJson_fases_sf(bool $bArray = FALSE): array|stdClass|null
    {
        if (!isset($this->json_fases_sf) && !$this->bLoaded) {
            $this->DBCarregar();
        }

        return (new ConverterJson($this->json_fases_sf, $bArray))->fromPg();
    }

    /**
     * @throws JsonException
     */
    public function setJson_fases_sf(string|array|null $oJSON, bool $db = FALSE): void
    {
        $this->json_fases_sf = (new ConverterJson($oJSON, FALSE))->toPg($db);
    }

    /**
     * Recupera el atributo iid_status de Cambio
     *
     * @return integer iid_status
     */
    function getId_status()
    {
        if (!isset($this->iid_status) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->iid_status;
    }

    /**
     * Establece el valor del atributo iid_status de Cambio
     *
     * @param integer iid_status='' optional
     */
    function setId_status($iid_status = '')
    {
        $this->iid_status = $iid_status;
    }

    /**
     * Recupera el atributo sdl_org de Cambio
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
     * Establece el valor del atributo sdl_org de Cambio
     *
     * @param boolean sdl_org='f' optional
     */
    function setDl_org($sdl_org = 'f')
    {
        $this->sdl_org = $sdl_org;
    }

    /**
     * Recupera el atributo sobjeto de Cambio
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
     * Establece el valor del atributo sobjeto de Cambio
     *
     * @param string sobjeto='' optional
     */
    function setObjeto($sobjeto = '')
    {
        $this->sobjeto = $sobjeto;
    }

    /**
     * Recupera el atributo spropiedad de Cambio
     *
     * @return string spropiedad
     */
    function getPropiedad()
    {
        if (!isset($this->spropiedad) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->spropiedad;
    }

    /**
     * Establece el valor del atributo spropiedad de Cambio
     *
     * @param string spropiedad='' optional
     */
    function setPropiedad($spropiedad = '')
    {
        $this->spropiedad = $spropiedad;
    }

    /**
     * Recupera el atributo svalor_old de Cambio
     *
     * @return string svalor_old
     */
    function getValor_old()
    {
        if (!isset($this->svalor_old) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->svalor_old;
    }

    /**
     * Establece el valor del atributo svalor_old de Cambio
     *
     * @param string svalor_old='' optional
     */
    function setValor_old($svalor_old = '')
    {
        $this->svalor_old = $svalor_old;
    }

    /**
     * Recupera el atributo svalor_new de Cambio
     *
     * @return string svalor_new
     */
    function getValor_new()
    {
        if (!isset($this->svalor_new) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->svalor_new;
    }

    /**
     * Establece el valor del atributo svalor_new de Cambio
     *
     * @param string svalor_new='' optional
     */
    function setValor_new($svalor_new = '')
    {
        $this->svalor_new = $svalor_new;
    }

    /**
     * Recupera el atributo iquien_cambia de Cambio
     *
     * @return integer iquien_cambia
     */
    function getQuien_cambia()
    {
        if (!isset($this->iquien_cambia) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->iquien_cambia;
    }

    /**
     * Establece el valor del atributo iquien_cambia de Cambio
     *
     * @param integer iquien_cambia='' optional
     */
    function setQuien_cambia($iquien_cambia = '')
    {
        $this->iquien_cambia = $iquien_cambia;
    }

    /**
     * Recupera el atributo isfsv_quien_cambia de Cambio
     *
     * @return integer isfsv_quien_cambia
     */
    function getSfsv_quien_cambia()
    {
        if (!isset($this->isfsv_quien_cambia) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        return $this->isfsv_quien_cambia;
    }

    /**
     * Establece el valor del atributo isfsv_quien_cambia de Cambio
     *
     * @param integer isfsv_quien_cambia='' optional
     */
    function setSfsv_quien_cambia($isfsv_quien_cambia = '')
    {
        $this->isfsv_quien_cambia = $isfsv_quien_cambia;
    }

    /**
     * Recupera el atributo itiimestamp de Cambio
     *
     * @return DateTimeLocal|NullDateTimeLocal
     */
    function getTimestamp_cambio()
    {
        if (!isset($this->itimestamp_cambio) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        if (empty($this->itimestamp_cambio)) {
            return new NullDateTimeLocal();
        }
        $oConverter = new ConverterDate('timestamp', $this->itimestamp_cambio);
        return $oConverter->fromPg();
    }

    /**
     * Establece el valor del atributo itiimestamp de Cambio
     * Si itiimestamp es string, y convert=true se convierte usando el formato web\DateTimeLocal->getFormat().
     * Si convert es false, df_ini debe ser un string en formato ISO (Y-m-d). Corresponde al pgstyle de la base de datos.
     *
     * @param string itiimestamp='' optional.
     * @param boolean convert=true optional. Si es false, itiimestamp debe ser un string en formato ISO (Y-m-d).
     */
    function setTimestamp_cambio($itimestamp_cambio = '', $convert = false)
    {
        if ($convert === true && !empty($itimestamp_cambio)) {
            $oConverter = new ConverterDate('datetime', $itimestamp_cambio);
            $this->itimestamp_cambio = $oConverter->toPg();
        } else {
            $this->itimestamp_cambio = $itimestamp_cambio;
        }
    }
    /* MÉTODOS GET y SET D'ATRIBUTOS QUE NO SON CAMPOS -----------------------------*/

    /**
     * Retorna una col·lecció d'objectes del tipus DatosCampo
     *
     */
    function getDatosCampos()
    {
        $oCambioSet = new Set();

        $oCambioSet->add($this->getDatosId_tipo_cambio());
        $oCambioSet->add($this->getDatosId_activ());
        $oCambioSet->add($this->getDatosId_tipo_activ());
        $oCambioSet->add($this->getDatosJson_fases_sv());
        $oCambioSet->add($this->getDatosJson_fases_sf());
        $oCambioSet->add($this->getDatosDl_org());
        $oCambioSet->add($this->getDatosObjeto());
        $oCambioSet->add($this->getDatosPropiedad());
        $oCambioSet->add($this->getDatosValor_old());
        $oCambioSet->add($this->getDatosValor_new());
        $oCambioSet->add($this->getDatosQuien_cambia());
        $oCambioSet->add($this->getDatosTimestamp_cambio());
        return $oCambioSet->getTot();
    }


    /**
     * Recupera les propietats de l'atribut iid_tipo_cambio de Cambio
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosId_tipo_cambio()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'id_tipo_cambio'));
        $oDatosCampo->setEtiqueta(_("id_tipo_cambio"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut iid_activ de Cambio
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosId_activ()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'id_activ'));
        $oDatosCampo->setEtiqueta(_("id_activ"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut iid_tipo_activ de Cambio
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosId_tipo_activ()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'id_tipo_activ'));
        $oDatosCampo->setEtiqueta(_("id_tipo_activ"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut json_fases_sv de Cambio
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosJson_fases_sv()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'json_fases_sv'));
        $oDatosCampo->setEtiqueta(_("id fase sv"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut json_fases_sf de Cambio
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosJson_fases_sf()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'json_fases_sf'));
        $oDatosCampo->setEtiqueta(_("id fase sf"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut iid_status de Cambio
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosId_status()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'id_status'));
        $oDatosCampo->setEtiqueta(_("id_status"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut sdl_org de Cambio
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosDl_org()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'dl_org'));
        $oDatosCampo->setEtiqueta(_("dl_org"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut sobjeto de Cambio
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosObjeto()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'objeto'));
        $oDatosCampo->setEtiqueta(_("objeto"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut spropiedad de Cambio
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosPropiedad()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'propiedad'));
        $oDatosCampo->setEtiqueta(_("propiedad"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut svalor_old de Cambio
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosValor_old()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'valor_old'));
        $oDatosCampo->setEtiqueta(_("valor_old"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut svalor_new de Cambio
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosValor_new()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'valor_new'));
        $oDatosCampo->setEtiqueta(_("valor_new"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut iquien_cambia de Cambio
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosQuien_cambia()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'quien_cambia'));
        $oDatosCampo->setEtiqueta(_("quien_cambia"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut isfsv_quien_cambia de Cambio
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosSfsv_quien_cambia()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'sfsv_quien_cambia'));
        $oDatosCampo->setEtiqueta(_("sección de quien cambia"));
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut itimestamp_cambio de Cambio
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosTimestamp_cambio()
    {
        $nom_tabla = $this->getNomTabla();
        $oDatosCampo = new DatosCampo(array('nom_tabla' => $nom_tabla, 'nom_camp' => 'timestamp_cambio'));
        $oDatosCampo->setEtiqueta(_("timestamp_cambio"));
        return $oDatosCampo;
    }
}
