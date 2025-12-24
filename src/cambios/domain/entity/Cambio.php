<?php

namespace src\cambios\domain\entity;

use core\ConfigGlobal;
use procesos\model\entity\GestorActividadFase;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividades\domain\contracts\NivelStgrRepositoryInterface;
use src\actividades\domain\contracts\RepeticionRepositoryInterface;
use src\actividadtarifas\domain\contracts\TipoTarifaRepositoryInterface;
use src\cambios\domain\contracts\CambioRepositoryInterface;
use src\cambios\domain\value_objects\TipoCambioId;
use src\personas\domain\contracts\PersonaSacdRepositoryInterface;
use src\ubis\domain\entity\Ubi;
use stdClass;
use web\DateTimeLocal;
use web\NullDateTimeLocal;
use function core\is_true;

/**
 * Clase que implementa la entidad av_cambios_dl
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 19/12/2025
 */
class Cambio
{
    //  tipo cambio constants.
    public const TIPO_CMB_INSERT = 1;
    public const TIPO_CMB_UPDATE = 2;
    public const TIPO_CMB_DELETE = 3;
    public const TIPO_CMB_FASE = 4;

    /**
     * Posa en marxa un procés per generar la taula d'avisos per cada usuari.
     *
     * @return true.
     *
     */
    public function generarTabla(): void
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

    public function getAvisoTxt(): string
    {
        $ActividadAllRepository = $GLOBALS['container']->get(ActividadAllRepositoryInterface::class);

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

        $oActividad = $ActividadAllRepository->findById($iId);
        $DatosCampoStatus = $oActividad->getDatosStatus();
        $aStatus = $DatosCampoStatus->getLista();

        $sNomActiv = $oActividad->getNom_activ();
        if (empty($sNomActiv)) { // se ha eliminado. Busco el nombre en el apunte eliminado
            $bEliminada = true;
            $CambioRepository = $GLOBALS['container']->get(CambioRepositoryInterface::class);
            $sNomActiv = $CambioRepository->getNomActivEliminada($iId);
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
            $PersonaSacdRepository = $GLOBALS['container']->get(PersonaSacdRepositoryInterface::class);
            if (!empty($sValor_old)) {
                //$oPersona = Persona::findPersonaEnGlobal($sValor_old);
                $oPersona = $PersonaSacdRepository->findById($sValor_old);
                $sValor_old = $oPersona->getPrefApellidosNombre();
            }
            if (!empty($sValor_new)) {
                //$oPersona = Persona::findPersonaEnGlobal($sValor_new);
                $oPersona = $PersonaSacdRepository->findById($sValor_new);
                $sValor_new = $oPersona->getPrefApellidosNombre();
            }
        }
        if ($sPropiedad === 'id_ubi') {
            if (!empty($sValor_old)) {
                $oUbi = Ubi::NewUbi($sValor_old);
                if ($oUbi) {
                    $sValor_old = $oUbi->getNombre_ubi();
                }
            }
            if (!empty($sValor_new)) {
                $oUbi = Ubi::NewUbi($sValor_new);
                if ($oUbi) {
                    $sValor_new = $oUbi->getNombre_ubi();
                }
            }
        }
        /* Para poner nombres que se entiendan a los campos de la actividad */
        if ($sObjeto === 'Actividad' ||
            $sObjeto === 'ActividadDl' ||
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
                $TipoTarifaRepository = $GLOBALS['container']->get(TipoTarifaRepositoryInterface::class);
                $aTarifas = $TipoTarifaRepository->getArrayTipoTarifas();
                $sValor_old = empty($sValor_old) ? $sValor_old : $aTarifas[$sValor_old];
                $sValor_new = empty($sValor_new) ? $sValor_new : $aTarifas[$sValor_new];
            }
            if ($sPropiedad === 'id_repeticion') {
                $RepeticionRepository = $GLOBALS['container']->get(RepeticionRepositoryInterface::class);
                $aRepeticion = $RepeticionRepository->getArrayRepeticion();
                $sValor_old = empty($sValor_old) ? $sValor_old : $aRepeticion[$sValor_old];
                $sValor_new = empty($sValor_new) ? $sValor_new : $aRepeticion[$sValor_new];
            }
            if ($sPropiedad === 'nivel_stgr') {
                $NivelStgrRepository = $GLOBALS['container']->get(NivelStgrRepositoryInterface::class);
                $aNivelStgr = $NivelStgrRepository->getArrayNivelesStgr();
                $sValor_old = empty($sValor_old) ? $sValor_old : $aNivelStgr[$sValor_old];
                $sValor_new = empty($sValor_new) ? $sValor_new : $aNivelStgr[$sValor_new];
            }
        }

        $etiqueta = $sPropiedad;

        /*
        $ObjetoFullPath = GestorAvisoCambios::getFullPathObj($sObjeto);
        $oObject = new $ObjetoFullPath();
        $oDbl = $oObject->getoDbl();
        $cDatosCampos = $oObject->getDatosCampos();
        // para ajustar el nombre del campo y el valor a algo más legible:
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
        */

        // para los asistentes que no son sacd. No tengo su nombre.
        if ($sObjeto === 'Asistente' ||
            $sObjeto === 'AsistenteDl' ||
            $sObjeto === 'AsistenteOut' ||
            $sObjeto === 'AsistenteEx'
        ) {
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

                if (ConfigGlobal::mi_sfsv() === 1) {
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


    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * Id_item_cambio de CambioDl
     *
     * @var int
     */
    private int $iid_item_cambio;
    /**
     * Id_tipo_cambio de CambioDl
     *
     * @var int
     */
    private int $iid_tipo_cambio;
    /**
     * Id_activ de CambioDl
     *
     * @var int
     */
    private int $iid_activ;
    /**
     * Id_tipo_activ de CambioDl
     *
     * @var int
     */
    private int $iid_tipo_activ;
    /**
     * Json_fases_sv de CambioDl
     *
     * @var array|stdClass|null
     */
    private array|stdClass|null $json_fases_sv = null;
    /**
     * Json_fases_sf de CambioDl
     *
     * @var array|stdClass|null
     */
    private array|stdClass|null $json_fases_sf = null;
    /**
     * Id_status de CambioDl
     *
     * @var int|null
     */
    private int|null $iid_status = null;
    /**
     * Dl_org de CambioDl
     *
     * @var string|null
     */
    private string|null $sdl_org = null;
    /**
     * Objeto de CambioDl
     *
     * @var string|null
     */
    private string|null $sobjeto = null;
    /**
     * Propiedad de CambioDl
     *
     * @var string|null
     */
    private string|null $spropiedad = null;
    /**
     * Valor_old de CambioDl
     *
     * @var string|null
     */
    private string|null $svalor_old = null;
    /**
     * Valor_new de CambioDl
     *
     * @var string|null
     */
    private string|null $svalor_new = null;
    /**
     * Quien_cambia de CambioDl
     *
     * @var int|null
     */
    private int|null $iquien_cambia = null;
    /**
     * Sfsv_quien_cambia de CambioDl
     *
     * @var int|null
     */
    private int|null $isfsv_quien_cambia = null;
    /**
     * Timestamp_cambio de CambioDl
     *
     * @var DateTimeLocal|null
     */
    private DateTimeLocal|null $dtimestamp_cambio = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    /**
     * Establece el valor de todos los atributos
     *
     * @param array $aDatos
     * @return Cambio
     */
    public function setAllAttributes(array $aDatos): Cambio
    {
        if (array_key_exists('id_schema', $aDatos)) {
            $this->setId_schema($aDatos['id_schema']);
        }
        if (array_key_exists('id_item_cambio', $aDatos)) {
            $this->setId_item_cambio($aDatos['id_item_cambio']);
        }
        if (array_key_exists('id_tipo_cambio', $aDatos)) {
            $this->setId_tipo_cambio($aDatos['id_tipo_cambio']);
        }
        if (array_key_exists('id_activ', $aDatos)) {
            $this->setId_activ($aDatos['id_activ']);
        }
        if (array_key_exists('id_tipo_activ', $aDatos)) {
            $this->setId_tipo_activ($aDatos['id_tipo_activ']);
        }
        if (array_key_exists('json_fases_sv', $aDatos)) {
            $this->setJson_fases_sv($aDatos['json_fases_sv']);
        }
        if (array_key_exists('json_fases_sf', $aDatos)) {
            $this->setJson_fases_sf($aDatos['json_fases_sf']);
        }
        if (array_key_exists('id_status', $aDatos)) {
            $this->setId_status($aDatos['id_status']);
        }
        if (array_key_exists('dl_org', $aDatos)) {
            $this->setDl_org($aDatos['dl_org']);
        }
        if (array_key_exists('objeto', $aDatos)) {
            $this->setObjeto($aDatos['objeto']);
        }
        if (array_key_exists('propiedad', $aDatos)) {
            $this->setPropiedad($aDatos['propiedad']);
        }
        if (array_key_exists('valor_old', $aDatos)) {
            $this->setValor_old($aDatos['valor_old']);
        }
        if (array_key_exists('valor_new', $aDatos)) {
            $this->setValor_new($aDatos['valor_new']);
        }
        if (array_key_exists('quien_cambia', $aDatos)) {
            $this->setQuien_cambia($aDatos['quien_cambia']);
        }
        if (array_key_exists('sfsv_quien_cambia', $aDatos)) {
            $this->setSfsv_quien_cambia($aDatos['sfsv_quien_cambia']);
        }
        if (array_key_exists('timestamp_cambio', $aDatos)) {
            $this->setTimestamp_cambio($aDatos['timestamp_cambio']);
        }
        return $this;
    }

    /**
     *
     * @return int $iid_schema
     */
    public function getId_schema(): int
    {
        return $this->iid_schema;
    }

    /**
     *
     * @param int $iid_schema
     */
    public function setId_schema(int $iid_schema): void
    {
        $this->iid_schema = $iid_schema;
    }

    /**
     *
     * @return int $iid_item_cambio
     */
    public function getId_item_cambio(): int
    {
        return $this->iid_item_cambio;
    }

    /**
     *
     * @param int $iid_item_cambio
     */
    public function setId_item_cambio(int $iid_item_cambio): void
    {
        $this->iid_item_cambio = $iid_item_cambio;
    }

    /**
     * @return TipoCambioId
     */
    public function getTipoCambioVo(): TipoCambioId
    {
        return new TipoCambioId($this->iid_tipo_cambio);
    }

    /**
     * @param TipoCambioId $tipoCambioId
     */
    public function setTipoCambioVo(TipoCambioId $tipoCambioId): void
    {
        $this->iid_tipo_cambio = $tipoCambioId->value();
    }

    /**
     * @deprecated usar getTipoCambioVo()
     * @return int $iid_tipo_cambio
     */
    public function getId_tipo_cambio(): int
    {
        return $this->iid_tipo_cambio;
    }

    /**
     * @deprecated usar setTipoCambioVo()
     * @param int $iid_tipo_cambio
     */
    public function setId_tipo_cambio(int $iid_tipo_cambio): void
    {
        $this->iid_tipo_cambio = $iid_tipo_cambio;
    }

    /**
     *
     * @return int $iid_activ
     */
    public function getId_activ(): int
    {
        return $this->iid_activ;
    }

    /**
     *
     * @param int $iid_activ
     */
    public function setId_activ(int $iid_activ): void
    {
        $this->iid_activ = $iid_activ;
    }

    /**
     *
     * @return int $iid_tipo_activ
     */
    public function getId_tipo_activ(): int
    {
        return $this->iid_tipo_activ;
    }

    /**
     *
     * @param int $iid_tipo_activ
     */
    public function setId_tipo_activ(int $iid_tipo_activ): void
    {
        $this->iid_tipo_activ = $iid_tipo_activ;
    }

    /**
     *
     * @return array|stdClass|null $json_fases_sv
     */
    public function getJson_fases_sv(): array|stdClass|null
    {
        return $this->json_fases_sv;
    }

    /**
     *
     * @param stdClass|array|null $json_fases_sv
     */
    public function setJson_fases_sv(stdClass|array|null $json_fases_sv = null): void
    {
        $this->json_fases_sv = $json_fases_sv;
    }

    /**
     *
     * @return array|stdClass|null $json_fases_sf
     */
    public function getJson_fases_sf(): array|stdClass|null
    {
        return $this->json_fases_sf;
    }

    /**
     *
     * @param stdClass|array|null $json_fases_sf
     */
    public function setJson_fases_sf(stdClass|array|null $json_fases_sf = null): void
    {
        $this->json_fases_sf = $json_fases_sf;
    }

    /**
     *
     * @return int|null $iid_status
     */
    public function getId_status(): ?int
    {
        return $this->iid_status;
    }

    /**
     *
     * @param int|null $iid_status
     */
    public function setId_status(?int $iid_status = null): void
    {
        $this->iid_status = $iid_status;
    }

    /**
     *
     * @return string|null $sdl_org
     */
    public function getDl_org(): ?string
    {
        return $this->sdl_org;
    }

    /**
     *
     * @param string|null $sdl_org
     */
    public function setDl_org(?string $sdl_org = null): void
    {
        $this->sdl_org = $sdl_org;
    }

    /**
     *
     * @return string|null $sobjeto
     */
    public function getObjeto(): ?string
    {
        return $this->sobjeto;
    }

    /**
     *
     * @param string|null $sobjeto
     */
    public function setObjeto(?string $sobjeto = null): void
    {
        $this->sobjeto = $sobjeto;
    }

    /**
     *
     * @return string|null $spropiedad
     */
    public function getPropiedad(): ?string
    {
        return $this->spropiedad;
    }

    /**
     *
     * @param string|null $spropiedad
     */
    public function setPropiedad(?string $spropiedad = null): void
    {
        $this->spropiedad = $spropiedad;
    }

    /**
     *
     * @return string|null $svalor_old
     */
    public function getValor_old(): ?string
    {
        return $this->svalor_old;
    }

    /**
     *
     * @param string|null $svalor_old
     */
    public function setValor_old(?string $svalor_old = null): void
    {
        $this->svalor_old = $svalor_old;
    }

    /**
     *
     * @return string|null $svalor_new
     */
    public function getValor_new(): ?string
    {
        return $this->svalor_new;
    }

    /**
     *
     * @param string|null $svalor_new
     */
    public function setValor_new(?string $svalor_new = null): void
    {
        $this->svalor_new = $svalor_new;
    }

    /**
     *
     * @return int|null $iquien_cambia
     */
    public function getQuien_cambia(): ?int
    {
        return $this->iquien_cambia;
    }

    /**
     *
     * @param int|null $iquien_cambia
     */
    public function setQuien_cambia(?int $iquien_cambia = null): void
    {
        $this->iquien_cambia = $iquien_cambia;
    }

    /**
     *
     * @return int|null $isfsv_quien_cambia
     */
    public function getSfsv_quien_cambia(): ?int
    {
        return $this->isfsv_quien_cambia;
    }

    /**
     *
     * @param int|null $isfsv_quien_cambia
     */
    public function setSfsv_quien_cambia(?int $isfsv_quien_cambia = null): void
    {
        $this->isfsv_quien_cambia = $isfsv_quien_cambia;
    }

    /**
     *
     * @return DateTimeLocal|NullDateTimeLocal|null $dtimestamp_cambio
     */
    public function getTimestamp_cambio(): DateTimeLocal|NullDateTimeLocal|null
    {
        return $this->dtimestamp_cambio ?? new NullDateTimeLocal;
    }

    /**
     *
     * @param DateTimeLocal|null $dtimestamp_cambio
     */
    public function setTimestamp_cambio(DateTimeLocal|null $dtimestamp_cambio = null): void
    {
        $this->dtimestamp_cambio = $dtimestamp_cambio;
    }
}