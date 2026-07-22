<?php

namespace src\cambios\application;

use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\cambios\domain\contracts\CambioDlRepositoryInterface;
use src\cambios\domain\contracts\CambioRepositoryInterface;
use src\cambios\domain\entity\Cambio;
use src\procesos\domain\contracts\ActividadProcesoTareaRepositoryInterface;
use src\shared\config\ConfigGlobal;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\domain\value_objects\TimeLocal;

/**
 * Caso de uso: registra un cambio en `av_cambios` / `av_cambios_dl`.
 *
 * Se invoca desde `RegistrarCambioListener` como reaccion al evento
 * `EntidadModificada`. Genera una fila (o varias, para UPDATE) con la
 * diferencia entre `$aDadesActuals` y `$aDadesNew`, anotando el tipo de
 * actividad, fases completadas y dl origen para que mas tarde
 * `AvisosGenerarTabla` pueda cruzarlo con las preferencias de aviso.
 *
 * Sucesor del metodo `addCanvi` de la legacy
 * `cambios\model\GestorAvisoCambios`.
 */
class RegistrarCambio
{
    public function __construct(
        private ActividadAllRepositoryInterface $actividadAllRepository,
        private CambioDlRepositoryInterface $cambioDlRepository,
        private CambioRepositoryInterface $cambioRepository,
        private ActividadProcesoTareaRepositoryInterface $actividadProcesoTareaRepository,
    ) {
    }

    /**
     * @param string $sObjeto      nombre corto del objeto ('Actividad', 'Asistente', 'CentroEncargado', …).
     * @param string $sTipoCambio  'INSERT' | 'UPDATE' | 'DELETE' | 'FASE'.
     * @param int|null $id_activ   actividad asociada (puede ser `null` en algunos casos edge).
     * @param array<string, mixed>  $aDadesNew    datos resultantes del cambio (para INSERT / UPDATE / FASE).
     * @param array<string, mixed>  $aDadesActuals datos previos (para UPDATE / DELETE).
     */
    public function execute(
        string $sObjeto,
        string $sTipoCambio,
        ?int $id_activ,
        array $aDadesNew,
        array $aDadesActuals
    ): void {
        $id_user = ConfigGlobal::mi_id_usuario();
        $sfsv = ConfigGlobal::mi_sfsv();
        $oAhora = new DateTimeLocal();

        switch ($sObjeto) {
            case 'Actividad':
            case 'ActividadDl':
            case 'ActividadEx':
                $Id_tipo_activ = self::intFromMixed($aDadesNew['id_tipo_activ'] ?? null)
                    ?: self::intFromMixed($aDadesActuals['id_tipo_activ'] ?? null);
                $dl_org = self::stringFromMixed($aDadesActuals['dl_org'] ?? null)
                    ?? self::stringFromMixed($aDadesNew['dl_org'] ?? null)
                    ?? '';
                $id_status = self::intFromMixed($aDadesNew['status'] ?? null)
                    ?: self::intFromMixed($aDadesActuals['status'] ?? null);
                break;
            default:
                if ($id_activ === null) {
                    $Id_tipo_activ = 111111;
                    $dl_org = 'test1';
                    $id_status = 4;
                    break;
                }
                $oActividad = $this->actividadAllRepository->findById($id_activ);
                if ($oActividad === null) {
                    $Id_tipo_activ = 111111;
                    $dl_org = 'test2';
                    $id_status = 4;
                } else {
                    $Id_tipo_activ = $oActividad->getId_tipo_activ();
                    $dl_org = $oActividad->getDl_org() ?? '';
                    $id_status = $oActividad->getStatus();
                }
        }

        if (ConfigGlobal::is_app_installed('cambios')) {
            $CambioRepository = $this->cambioDlRepository;
            if (ConfigGlobal::is_app_installed('procesos') && $id_activ !== null) {
                // El repositorio es compartido en la petición: hay que restaurar
                // la tabla; si no, el siguiente cambio de fase en lote lee/escribe
                // en sf (o sv) por error y solo actualiza la primera actividad.
                $nomTablaProcesoOriginal = $this->actividadProcesoTareaRepository->getNomTabla();
                try {
                    $this->actividadProcesoTareaRepository->setNomTabla('a_actividad_proceso_sv');
                    $aFases_sv = self::normalizeFasesList($this->actividadProcesoTareaRepository->getFasesCompletadas($id_activ));
                    $this->actividadProcesoTareaRepository->setNomTabla('a_actividad_proceso_sf');
                    $aFases_sf = self::normalizeFasesList($this->actividadProcesoTareaRepository->getFasesCompletadas($id_activ));
                } finally {
                    $this->actividadProcesoTareaRepository->setNomTabla($nomTablaProcesoOriginal);
                }
            } else {
                $aFases_sv = [$id_status];
                $aFases_sf = [$id_status];
            }
        } else {
            $CambioRepository = $this->cambioRepository;
            $aFases_sv = [$id_status];
            $aFases_sf = [$id_status];
        }

        switch ($sTipoCambio) {
            case 'INSERT':
                $oActividadCambio = $this->construirBase(
                    $CambioRepository,
                    Cambio::TIPO_CMB_INSERT,
                    $id_activ,
                    $Id_tipo_activ,
                    $aFases_sv,
                    $aFases_sf,
                    $id_status,
                    $dl_org,
                    $sObjeto,
                    $id_user,
                    $sfsv,
                    $oAhora
                );
                $oActividadCambio->setValor_old();
                switch ($sObjeto) {
                    case 'Actividad':
                    case 'ActividadDl':
                    case 'ActividadEx':
                        $oActividadCambio->setPropiedad('nom_activ');
                        $oActividadCambio->setValor_new(self::stringFromMixed($aDadesNew['nom_activ'] ?? null));
                        break;
                    case 'Asistente':
                    case 'AsistenteDl':
                    case 'AsistenteEx':
                    case 'AsistenteOut':
                    case 'ActividadCargoNoSacd':
                    case 'ActividadCargoSacd':
                        $oActividadCambio->setPropiedad('id_nom');
                        $oActividadCambio->setValor_new(self::stringFromMixed($aDadesNew['id_nom'] ?? null));
                        break;
                    case 'CentroEncargado':
                        $oActividadCambio->setPropiedad('id_ubi');
                        $oActividadCambio->setValor_new(self::stringFromMixed($aDadesNew['id_ubi'] ?? null));
                        break;
                }
                $CambioRepository->Guardar($oActividadCambio);
                break;

            case 'UPDATE':
                foreach ($aDadesNew as $key => $value) {
                    if (!array_key_exists($key, $aDadesActuals)) {
                        continue;
                    }
                    $oldValue = $aDadesActuals[$key];
                    if (self::cambioValuesEqual($value, $oldValue)) {
                        continue;
                    }
                    if (!is_null(\src\shared\domain\helpers\FuncTablasSupport::isTrue($value))
                        && \src\shared\domain\helpers\FuncTablasSupport::isTrue($oldValue) === \src\shared\domain\helpers\FuncTablasSupport::isTrue($value)
                    ) {
                        continue;
                    }
                    $oActividadCambio = $this->construirBase(
                        $CambioRepository,
                        Cambio::TIPO_CMB_UPDATE,
                        $id_activ,
                        $Id_tipo_activ,
                        $aFases_sv,
                        $aFases_sf,
                        $id_status,
                        $dl_org,
                        $sObjeto,
                        $id_user,
                        $sfsv,
                        $oAhora
                    );
                    $oActividadCambio->setPropiedad($key);
                    $oActividadCambio->setValor_old(self::stringFromMixed($oldValue));
                    $oActividadCambio->setValor_new(self::stringFromMixed($value));
                    $CambioRepository->Guardar($oActividadCambio);
                }
                break;

            case 'DELETE':
                $oActividadCambio = $this->construirBase(
                    $CambioRepository,
                    Cambio::TIPO_CMB_DELETE,
                    $id_activ,
                    $Id_tipo_activ,
                    $aFases_sv,
                    $aFases_sf,
                    $id_status,
                    $dl_org,
                    $sObjeto,
                    $id_user,
                    $sfsv,
                    $oAhora
                );
                $oActividadCambio->setValor_new();
                switch ($sObjeto) {
                    case 'Actividad':
                    case 'ActividadDl':
                    case 'ActividadEx':
                        $oActividadCambio->setId_activ(0);
                        $oActividadCambio->setPropiedad('nom_activ');
                        $oActividadCambio->setValor_old(self::stringFromMixed($aDadesActuals['nom_activ'] ?? null));
                        break;
                    case 'Asistente':
                    case 'AsistenteDl':
                    case 'AsistenteEx':
                    case 'AsistenteOut':
                    case 'ActividadCargoNoSacd':
                    case 'ActividadCargoSacd':
                        $idNom = $aDadesActuals['id_nom'] ?? null;
                        if ($idNom !== null && $idNom !== '') {
                            $oActividadCambio->setPropiedad('id_nom');
                            $oActividadCambio->setValor_old(self::stringFromMixed($idNom));
                        }
                        break;
                    case 'CentroEncargado':
                        $oActividadCambio->setPropiedad('id_ubi');
                        $oActividadCambio->setValor_old(self::stringFromMixed($aDadesActuals['id_ubi'] ?? null));
                        break;
                }
                $CambioRepository->Guardar($oActividadCambio);
                break;

            case 'FASE':
                $boolCompletadoNew = !empty($aDadesNew['completado']) && \src\shared\domain\helpers\FuncTablasSupport::isTrue($aDadesNew['completado']);
                $boolCompletadoActual = !empty($aDadesActuals['completado']) && \src\shared\domain\helpers\FuncTablasSupport::isTrue($aDadesActuals['completado']);

                if ($boolCompletadoNew === $boolCompletadoActual) {
                    break;
                }

                $oActividadCambio = $this->construirBase(
                    $CambioRepository,
                    Cambio::TIPO_CMB_FASE,
                    $id_activ,
                    $Id_tipo_activ,
                    $aFases_sv,
                    $aFases_sf,
                    $id_status,
                    $dl_org,
                    $sObjeto,
                    $id_user,
                    $sfsv,
                    $oAhora,
                    useStatusVo: false
                );
                $oActividadCambio->setPropiedad('completado');
                $oActividadCambio->setValor_old(self::stringFromMixed($aDadesActuals['id_fase'] ?? null));
                $oActividadCambio->setValor_new($boolCompletadoNew ? 't' : 'f');
                $CambioRepository->Guardar($oActividadCambio);
                break;
        }
    }

    /**
     * @param list<int|string> $aFases_sv
     * @param list<int|string> $aFases_sf
     */
    private function construirBase(
        CambioRepositoryInterface|CambioDlRepositoryInterface $CambioRepository,
        int $tipoCambio,
        ?int $id_activ,
        int $Id_tipo_activ,
        array $aFases_sv,
        array $aFases_sf,
        int $id_status,
        string $dl_org,
        string $sObjeto,
        int $id_user,
        int $sfsv,
        DateTimeLocal $oAhora,
        bool $useStatusVo = true
    ): Cambio {
        $newIdItem = $CambioRepository->getNewId();
        $oCambio = new Cambio();
        $oCambio->setId_item_cambio($newIdItem);
        $oCambio->setId_tipo_cambio($tipoCambio);
        $oCambio->setId_activ($id_activ ?? 0);
        $oCambio->setId_tipo_activ($Id_tipo_activ);
        $oCambio->setJson_fases_sv(self::fasesToJsonArray($aFases_sv));
        $oCambio->setJson_fases_sf(self::fasesToJsonArray($aFases_sf));
        if ($useStatusVo) {
            $oCambio->setIdStatusVo($id_status);
        } else {
            $oCambio->setId_status($id_status);
        }
        $oCambio->setDl_org($dl_org);
        $oCambio->setObjeto($sObjeto);
        $oCambio->setQuien_cambia($id_user);
        $oCambio->setSfsv_quien_cambia($sfsv);
        $oCambio->setTimestamp_cambio($oAhora);
        return $oCambio;
    }

    /**
     * @param array<mixed> $fases
     * @return list<int|string>
     */
    private static function normalizeFasesList(array $fases): array
    {
        $result = [];
        foreach ($fases as $fase) {
            if (is_int($fase) || is_string($fase)) {
                $result[] = $fase;
            }
        }

        return $result;
    }

    /**
     * @param list<int|string> $fases
     * @return array<string, mixed>
     */
    private static function fasesToJsonArray(array $fases): array
    {
        if ($fases === []) {
            return [];
        }

        $values = $fases;
        /** @var list<string> $keys */
        $keys = array_map(
            static fn (int $position): string => (string) $position,
            range(0, count($values) - 1)
        );

        return array_combine($keys, $values) ?: [];
    }

    private static function intFromMixed(mixed $value): int
    {
        if (is_int($value)) {
            return $value;
        }
        if (is_string($value) && is_numeric($value)) {
            return (int) $value;
        }
        if (is_float($value)) {
            return (int) $value;
        }

        return 0;
    }

    private static function cambioValuesEqual(mixed $a, mixed $b): bool
    {
        if ($a === $b) {
            return true;
        }

        $sa = self::stringFromMixed($a);
        $sb = self::stringFromMixed($b);

        return $sa !== null && $sb !== null && $sa === $sb;
    }

    private static function stringFromMixed(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }
        if ($value instanceof TimeLocal) {
            return $value->toDatabaseString();
        }
        if ($value instanceof DateTimeLocal) {
            return $value->format('Y-m-d');
        }
        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d');
        }
        if (is_string($value)) {
            return $value;
        }
        if (is_int($value) || is_float($value) || is_bool($value)) {
            return (string) $value;
        }

        return null;
    }
}
