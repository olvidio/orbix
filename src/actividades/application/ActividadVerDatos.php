<?php

namespace src\actividades\application;

use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividades\domain\contracts\RepeticionRepositoryInterface;
use src\actividades\domain\value_objects\NivelStgrId;
use src\actividadtarifas\domain\contracts\RelacionTarifaTipoActividadRepositoryInterface;
use src\actividadtarifas\domain\contracts\TipoTarifaRepositoryInterface;
use src\ubis\application\services\DelegacionDropdown;
use src\ubis\domain\entity\Ubi;
use src\usuarios\domain\contracts\LocalRepositoryInterface;
use src\actividades\domain\entity\TiposActividades;
use src\shared\domain\helpers\FuncTablasSupport;

/**
 * Devuelve los datos que el formulario "ver/editar actividad" necesita para
 * renderizarse, sin que la capa frontend acceda a repositorios o entidades del dominio.
 *
 * Los desplegables se devuelven como payloads JSON estándar (`select_*`);
 * el frontend construye el HTML con {@see \frontend\shared\web\Desplegable}.
 *
 * Si se recibe `id_activ` > 0, carga la actividad y usa sus valores
 * (dl_org, tarifa, nivel_stgr, idioma, id_repeticion, id_ubi, lugar_esp,
 * isfsv); en caso contrario (modo 'nuevo' o 'cambiar_tipo'), usa los valores
 * pasados por el controlador frontend.
 *
 * El controlador HTTP serializa el array devuelto con ContestarJson.
 */
final class ActividadVerDatos
{
    public function __construct(
        private ActividadAllRepositoryInterface $actividadAllRepository,
        private TipoTarifaRepositoryInterface $tipoTarifaRepository,
        private LocalRepositoryInterface $localRepository,
        private RepeticionRepositoryInterface $repeticionRepository,
        private RelacionTarifaTipoActividadRepositoryInterface $relacionTarifaTipoActividadRepository,
        private DelegacionDropdown $delegacionDropdown,
    ) {
    }

    /**
     * Nivel STGR por defecto en formulario según `id_tipo_activ` (p. ej. cursos de repaso → R).
     * Usa parsing extendido de {@see TiposActividades} para reconocer `ca-repaso`, `cv-repaso`, etc.
     */
    public static function nivelStgrPorDefectoParaIdTipoActividad(string $idTipoActiv): int
    {
        if ($idTipoActiv === '') {
            return NivelStgrId::N;
        }
        $oTipo = new TiposActividades($idTipoActiv, true);
        $actividad2 = $oTipo->getActividad2DigitosText();
        if (str_contains($actividad2, 'est')) {
            return NivelStgrId::C1;
        }
        if (str_contains($actividad2, 'repaso')) {
            return NivelStgrId::R;
        }
        if (str_contains($actividad2, 'semestre')) {
            return NivelStgrId::C1;
        }

        return NivelStgrId::N;
    }

    /**
     * @param array<string, mixed> $input Claves admitidas (todas opcionales):
     *   - id_activ (int): si > 0, carga actividad por id.
     *   - isfsv (int)
     *   - dl_org (string)
     *   - Bdl (string: 't'|'f')
     *   - tarifa, nivel_stgr, idioma, id_repeticion, id_ubi, lugar_esp
     *   - id_tipo_activ (string): caso 'nuevo', para tarifa por defecto.
     *   - calc_tarifa_inicial (bool)
     * @return array<string, mixed>
     */
    public function ejecutar(array $input): array
    {
        $id_activ = FuncTablasSupport::inputInt($input, 'id_activ');
        $isfsv = FuncTablasSupport::inputInt($input, 'isfsv');
        $dl_org = FuncTablasSupport::inputString($input, 'dl_org');
        $Bdl = FuncTablasSupport::inputString($input, 'Bdl', 't');
        $tarifa = is_scalar($input['tarifa'] ?? '') ? (string) ($input['tarifa'] ?? '') : '';
        $idioma = FuncTablasSupport::inputString($input, 'idioma');
        $id_repeticion = FuncTablasSupport::inputInt($input, 'id_repeticion');
        $id_ubi = FuncTablasSupport::inputInt($input, 'id_ubi');
        $lugar_esp = FuncTablasSupport::inputString($input, 'lugar_esp');
        $id_tipo_activ = FuncTablasSupport::inputString($input, 'id_tipo_activ');
        $nivel_stgr_raw = $input['nivel_stgr'] ?? self::nivelStgrPorDefectoParaIdTipoActividad($id_tipo_activ);
        $nivel_stgr = is_scalar($nivel_stgr_raw) ? (string) $nivel_stgr_raw : '';
        $calcTarifaInicial = !empty($input['calc_tarifa_inicial']);

        $entidad = null;
        if ($id_activ > 0) {
            $ActividadAllRepository = $this->actividadAllRepository;
            $oActividad = $ActividadAllRepository->findById($id_activ);
            if ($oActividad !== null) {
                $entidad = [
                    'id_tipo_activ' => $oActividad->getId_tipo_activ(),
                    'dl_org' => $oActividad->getDl_org(),
                    'nom_activ' => $oActividad->getNom_activ(),
                    'id_ubi' => $oActividad->getId_ubi(),
                    'f_ini' => $oActividad->getF_ini()?->getFromLocal() ?? '',
                    'h_ini' => $oActividad->getH_ini()?->format('H:i') ?? '',
                    'f_fin' => $oActividad->getF_fin()?->getFromLocal() ?? '',
                    'h_fin' => $oActividad->getH_fin()?->format('H:i') ?? '',
                    'precio' => $oActividad->getPrecio(),
                    'status' => $oActividad->getStatus(),
                    'observ' => $oActividad->getObserv(),
                    'nivel_stgr' => $oActividad->getNivel_stgr(),
                    'lugar_esp' => $oActividad->getLugar_esp(),
                    'tarifa' => $oActividad->getTarifa(),
                    'id_repeticion' => $oActividad->getId_repeticion(),
                    'publicado' => $oActividad->isPublicado(),
                    'plazas' => $oActividad->getPlazas(),
                    'idioma' => $oActividad->getIdiomaVo()?->value() ?? '',
                ];
                $dl_org = (string)$entidad['dl_org'];
                $tarifa = $entidad['tarifa'];
                $nivel_stgr = $entidad['nivel_stgr'] ?? self::nivelStgrPorDefectoParaIdTipoActividad((string)$entidad['id_tipo_activ']);
                $idioma = (string)$entidad['idioma'];
                $id_repeticion = (int)$entidad['id_repeticion'];
                $id_ubi = (int)$entidad['id_ubi'];
                $lugar_esp = (string)$entidad['lugar_esp'];
                $id_tipo_activ = (string)$entidad['id_tipo_activ'];
                $isfsv = (int)$id_tipo_activ[0];
            }
        }

        $bdlBool = ($Bdl === 't');

        $nombre_ubi = '';
        if (!empty($id_ubi) && $id_ubi !== 1) {
            $oCasa = Ubi::newUbi($id_ubi);
            if ($oCasa !== null) {
                $nombre_ubi = $oCasa->getNombre_ubi();
            }
            if ($nombre_ubi === '') {
                $nombre_ubi = _("ya no existe: cambiarlo");
            }
        } else {
            if ($id_ubi === 1 && $lugar_esp !== '') {
                $nombre_ubi = $lugar_esp;
            }
            if (!$id_ubi && $lugar_esp === '') {
                $nombre_ubi = _("sin determinar");
            }
        }

        $payload = [
            'entidad' => $entidad,
            'isfsv' => $isfsv,
            'select_dl_org' => [
                'id' => 'dl_org',
                'opciones' => $this->delegacionDropdown->delegacionesURegiones($isfsv, $bdlBool),
                'selected' => (string) $dl_org,
                'blanco' => true,
            ],
            'select_tarifa' => [
                'id' => 'id_tarifa',
                'opciones' => $this->tipoTarifaRepository->getArrayTipoTarifas($isfsv),
                'selected' => (string) $tarifa,
                'blanco' => false,
            ],
            'select_nivel_stgr' => [
                'id' => 'nivel_stgr',
                'opciones' => NivelStgrId::getArrayNivelStgr(),
                'selected' => (string) $nivel_stgr,
                'blanco' => false,
            ],
            'select_idioma' => [
                'id' => 'idioma',
                'opciones' => $this->localRepository->getArrayLocales(),
                'selected' => (string) $idioma,
                'blanco' => true,
            ],
            'select_repeticion' => [
                'id' => 'id_repeticion',
                'opciones' => $this->repeticionRepository->getArrayRepeticion(),
                'selected' => (string) $id_repeticion,
                'blanco' => false,
            ],
            'nombre_ubi' => $nombre_ubi,
        ];

        if ($entidad !== null && $id_tipo_activ !== '') {
            $oTipoActiv = new TiposActividades($id_tipo_activ);
            $payload['ssfsv'] = $oTipoActiv->getSfsvText();
            $payload['sasistentes'] = $oTipoActiv->getAsistentesText();
            $payload['sactividad'] = $oTipoActiv->getActividadText();
            $payload['snom_tipo'] = $oTipoActiv->getNom_tipoText();
        }

        if ($calcTarifaInicial && $id_tipo_activ !== '') {
            $aWhereT = [
                'id_tipo_activ' => $id_tipo_activ,
                '_ordre' => 'id_serie',
            ];
            $cActiTipoTarifa = $this->relacionTarifaTipoActividadRepository->getTipoActivTarifas($aWhereT);
            if ($cActiTipoTarifa !== []) {
                $payload['tarifa_inicial'] = $cActiTipoTarifa[0]->getId_tarifa();
            } else {
                $payload['tarifa_inicial'] = null;
            }
        }

        return $payload;
    }
}
