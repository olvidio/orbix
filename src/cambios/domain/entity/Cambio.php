<?php

namespace src\cambios\domain\entity;

use core\ConfigGlobal;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividades\domain\contracts\NivelStgrRepositoryInterface;
use src\actividades\domain\contracts\RepeticionRepositoryInterface;
use src\actividades\domain\value_objects\ActividadTipoId;
use src\actividades\domain\value_objects\StatusId;
use src\actividadtarifas\domain\contracts\TipoTarifaRepositoryInterface;
use src\cambios\domain\contracts\CambioRepositoryInterface;
use src\cambios\domain\value_objects\ObjetoNombre;
use src\cambios\domain\value_objects\PropiedadNombre;
use src\cambios\domain\value_objects\TipoCambioId;
use src\personas\domain\contracts\PersonaSacdRepositoryInterface;
use src\procesos\domain\contracts\ActividadFaseRepositoryInterface;
use src\shared\domain\traits\Hydratable;
use src\ubis\domain\entity\Ubi;
use src\ubis\domain\value_objects\DelegacionCode;
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
    use Hydratable;

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
                $ActividadFaseRepository = $GLOBALS['container']->get(ActividadFaseRepositoryInterface::class);

                if (ConfigGlobal::mi_sfsv() === 1) {
                    $aFases = $this->getJson_fases_sv();
                } else {
                    $aFases = $this->getJson_fases_sf();
                }
                $idStatus = $this->getId_status();

                if (!$bEliminada) {
                    if (!empty($sPropiedad)) {
                        $cFases = $ActividadFaseRepository->getActividadFases(array('id_fase' => $id_fase));
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

    private int $id_schema;

    private int $id_item_cambio;

    private TipoCambioId $id_tipo_cambio;

    private int $id_activ;

    private ActividadTipoId $id_tipo_activ;

    private array|stdClass|null $json_fases_sv = null;

    private array|stdClass|null $json_fases_sf = null;

    private ?StatusId $id_status = null;

    private DelegacionCode|null $dl_org = null;

    private ObjetoNombre|null $objeto = null;

    private PropiedadNombre|null $propiedad = null;

    private ?string $valor_old = null;

    private ?string $valor_new = null;

    private ?int $quien_cambia = null;

    private ?int $sfsv_quien_cambia = null;

   private ?DateTimeLocal $timestamp_cambio = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/


    public function getId_schema(): int
    {
        return $this->id_schema;
    }


    public function setId_schema(int $id_schema): void
    {
        $this->id_schema = $id_schema;
    }


    public function getId_item_cambio(): int
    {
        return $this->id_item_cambio;
    }


    public function setId_item_cambio(int $id_item_cambio): void
    {
        $this->id_item_cambio = $id_item_cambio;
    }


    public function getTipoCambioVo(): TipoCambioId
    {
        return $this->id_tipo_cambio;
    }


    public function setTipoCambioVo(TipoCambioId|int $valor): void
    {
        $this->id_tipo_cambio = $valor instanceof TipoCambioId
            ? $valor
            : TipoCambioId::fromNullable($valor);
    }

    /**
     * @deprecated usar getTipoCambioVo()
     */
    public function getId_tipo_cambio(): int
    {
        return $this->id_tipo_cambio->value();
    }

    /**
     * @deprecated usar setTipoCambioVo()
     */
    public function setId_tipo_cambio(int $id_tipo_cambio): void
    {
        $this->id_tipo_cambio = TipoCambioId::fromNullable($id_tipo_cambio);
    }


    public function getId_activ(): int
    {
        return $this->id_activ;
    }


    public function setId_activ(int $id_activ): void
    {
        $this->id_activ = $id_activ;
    }

    /**
     * @deprecated Usar `getIdTipoActivVo(): ActividadTipoId` en su lugar.
     */
    public function getId_tipo_activ(): int
    {
        return $this->id_tipo_activ->value();
    }

    public function getIdTipoActivVo(): ActividadTipoId
    {
        return $this->id_tipo_activ;
    }

    /**
     * @deprecated Usar `getIdTipoActivVo(): ActividadTipoId` en su lugar.
     */
    public function setId_tipo_activ(int $id_tipo_activ): void
    {
        $this->id_tipo_activ = ActividadTipoId::fromString($id_tipo_activ);
    }

    public function setIdTipoActivVo(ActividadTipoId|string|int|null $texto): void
    {
        $this->id_tipo_activ = $texto instanceof ActividadTipoId
            ? $texto
            : ActividadTipoId::fromString($texto);
    }

    public function getJson_fases_sv(): array|stdClass|null
    {
        return $this->json_fases_sv;
    }


    public function setJson_fases_sv(stdClass|array|null $json_fases_sv = null): void
    {
        $this->json_fases_sv = $json_fases_sv;
    }


    public function getJson_fases_sf(): array|stdClass|null
    {
        return $this->json_fases_sf;
    }


    public function setJson_fases_sf(stdClass|array|null $json_fases_sf = null): void
    {
        $this->json_fases_sf = $json_fases_sf;
    }


    /**
     * @deprecated Usar `getIdStatusVo(): ?StatusId` en su lugar.
     */
    public function getId_status(): ?string
    {
        return $this->id_status?->value();
    }

    public function getIdStatusVo(): ?StatusId
    {
        return $this->id_status;
    }

    /**
     * @deprecated Usar `setIdStatusVo(?StatusId $vo): void` en su lugar.
     */
    public function setId_status(?int $id_status = null): void
    {
        $this->id_status = $id_status;
    }

    public function setIdStatusVo(StatusId|int|null $texto): void
    {
        $this->id_status = $texto instanceof StatusId
            ? $texto
            : StatusId::fromNullable($texto);
    }


    /**
     * @deprecated Usar `getDlOrgVo(): ?DelegacionCode` en su lugar.
     */
    public function getDl_org(): ?string
    {
        return $this->dl_org?->value();
    }


    /**
     * @deprecated Usar `setDlOrgVo(?DelegacionCode $vo): void` en su lugar.
     */
    public function setDl_org(?string $dl_org = null): void
    {
        $this->dl_org = DelegacionCode::fromNullableString($dl_org);
    }

    public function getDlOrgVo(): ?DelegacionCode
    {
        return $this->dl_org;
    }

    public function setDlOrgVo(DelegacionCode|string|null $texto): void
    {
        $this->dl_org = $texto instanceof DelegacionCode
            ? $texto
            : DelegacionCode::fromNullableString($texto);
    }


    /**
     * @deprecated Usar `getObjetoVo(): ?ObjetoNombre` en su lugar.
     */
    public function getObjeto(): ?string
    {
        return $this->objeto?->value();
    }


    /**
     * @deprecated Usar `setObjetoVo(?ObjetoNombre $vo): void` en su lugar.
     */
    public function setObjeto(?string $objeto = null): void
    {
        $this->objeto = ObjetoNombre::fromNullableString($objeto);
    }

    public function getObjetoVo(): ?ObjetoNombre
    {
        return $this->objeto;
    }

    public function setObjetoVo(ObjetoNombre|string|null $texto): void
    {
        $this->objeto = $texto instanceof ObjetoNombre
            ? $texto
            : ObjetoNombre::fromNullableString($texto);
    }


    /**
     * @deprecated Usar `getPropiedadVo(): ?PropiedadNombre` en su lugar.
     */
    public function getPropiedad(): ?string
    {
        return $this->propiedad?->value();
    }


    /**
     * @deprecated Usar `setPropiedadVo(?PropiedadNombre $vo): void` en su lugar.
     */
    public function setPropiedad(?string $propiedad = null): void
    {
        $this->propiedad = PropiedadNombre::fromNullableString($propiedad);
    }

    public function getPropiedadVo(): ?PropiedadNombre
    {
        return $this->propiedad;
    }

    public function setPropiedadVo(PropiedadNombre|string|null $texto): void
    {
        $this->propiedad = $texto instanceof PropiedadNombre
            ? $texto
            : PropiedadNombre::fromNullableString($texto);
    }


    public function getValor_old(): ?string
    {
        return $this->valor_old;
    }


    public function setValor_old(?string $valor_old = null): void
    {
        $this->valor_old = $valor_old;
    }


    public function getValor_new(): ?string
    {
        return $this->valor_new;
    }


    public function setValor_new(?string $valor_new = null): void
    {
        $this->valor_new = $valor_new;
    }


    public function getQuien_cambia(): ?int
    {
        return $this->quien_cambia;
    }


    public function setQuien_cambia(?int $quien_cambia = null): void
    {
        $this->quien_cambia = $quien_cambia;
    }


    public function getSfsv_quien_cambia(): ?int
    {
        return $this->sfsv_quien_cambia;
    }


    public function setSfsv_quien_cambia(?int $sfsv_quien_cambia = null): void
    {
        $this->sfsv_quien_cambia = $sfsv_quien_cambia;
    }


    public function getTimestamp_cambio(): DateTimeLocal|NullDateTimeLocal|null
    {
        return $this->timestamp_cambio ?? new NullDateTimeLocal;
    }


    public function setTimestamp_cambio(DateTimeLocal|null $timestamp_cambio = null): void
    {
        $this->timestamp_cambio = $timestamp_cambio;
    }
}