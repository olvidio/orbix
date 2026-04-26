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
use frontend\shared\web\Desplegable;
use src\actividades\domain\entity\TiposActividades;

/**
 * Devuelve todos los datos y fragmentos HTML que el formulario
 * "ver/editar actividad" necesita para renderizarse, sin que la capa
 * frontend acceda a repositorios o entidades del dominio.
 *
 * Si se recibe `id_activ` > 0, carga la actividad y usa sus valores
 * (dl_org, tarifa, nivel_stgr, idioma, id_repeticion, id_ubi, lugar_esp,
 * isfsv) para construir los desplegables; en caso contrario (modo 'nuevo'
 * o 'cambiar_tipo'), usa los valores pasados por el controlador frontend.
 *
 * El controlador HTTP serializa el array devuelto con ContestarJson.
 */
final class ActividadVerDatos
{
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
        if (str_contains($oTipo->getActividad2DigitosText(), 'est')) {
            return NivelStgrId::C1;
        }
        if (str_contains($oTipo->getActividad2DigitosText(), 'repaso')) {
            return NivelStgrId::R;
        }
        if (str_contains($oTipo->getActividad2DigitosText(), 'semestre')) {
            return NivelStgrId::C1;
        }

        return NivelStgrId::N;
    }

    /**
     * @param array $input Claves admitidas (todas opcionales):
     *   - id_activ (int): si > 0, carga actividad por id.
     *   - isfsv (int)
     *   - dl_org (string)
     *   - Bdl (string: 't'|'f')
     *   - tarifa, nivel_stgr, idioma, id_repeticion, id_ubi, lugar_esp
     *   - id_tipo_activ (string): caso 'nuevo', para tarifa por defecto.
     *   - calc_tarifa_inicial (bool)
     */
    public function ejecutar(array $input): array
    {
        $id_activ = (int)($input['id_activ'] ?? 0);
        $isfsv = (int)($input['isfsv'] ?? 0);
        $dl_org = (string)($input['dl_org'] ?? '');
        $Bdl = (string)($input['Bdl'] ?? 't');
        $tarifa = $input['tarifa'] ?? '';
        $idioma = (string)($input['idioma'] ?? '');
        $id_repeticion = (int)($input['id_repeticion'] ?? 0);
        $id_ubi = (int)($input['id_ubi'] ?? 0);
        $lugar_esp = (string)($input['lugar_esp'] ?? '');
        $id_tipo_activ = (string)($input['id_tipo_activ'] ?? '');
        $nivel_stgr = $input['nivel_stgr'] ?? self::nivelStgrPorDefectoParaIdTipoActividad($id_tipo_activ);
        $calcTarifaInicial = !empty($input['calc_tarifa_inicial']);

        $entidad = null;
        if ($id_activ > 0) {
            $ActividadAllRepository = $GLOBALS['container']->get(ActividadAllRepositoryInterface::class);
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
                // Para los desplegables usamos los valores reales de la actividad.
                $dl_org = (string)$entidad['dl_org'];
                $tarifa = $entidad['tarifa'];
                $nivel_stgr = $entidad['nivel_stgr'] ?? self::nivelStgrPorDefectoParaIdTipoActividad((string)$entidad['id_tipo_activ']);
                $idioma = (string)$entidad['idioma'];
                $id_repeticion = (int)$entidad['id_repeticion'];
                $id_ubi = (int)$entidad['id_ubi'];
                $lugar_esp = (string)$entidad['lugar_esp'];
                // isfsv derivado del id_tipo_activ.
                $id_tipo_activ = (string)$entidad['id_tipo_activ'];
                $isfsv = (int)$id_tipo_activ[0];
            }
        }

        $bdlBool = ($Bdl === 't');

        $oDesplDl = Desplegable::desdeOpciones(
            DelegacionDropdown::delegacionesURegiones($isfsv, $bdlBool),
            'dl_org'
        );
        $oDesplDl->setOpcion_sel($dl_org);
        $html_despl_dl_org = $oDesplDl->desplegable();

        $TipoTarifaRepository = $GLOBALS['container']->get(TipoTarifaRepositoryInterface::class);
        $aOpciones = $TipoTarifaRepository->getArrayTipoTarifas($isfsv);
        $oDesplTarifa = new Desplegable();
        $oDesplTarifa->setOpciones($aOpciones);
        $oDesplTarifa->setNombre('id_tarifa');
        $oDesplTarifa->setOpcion_sel($tarifa);
        $html_despl_tarifa = $oDesplTarifa->desplegable();

        $aOpciones = NivelStgrId::getArrayNivelStgr();
        $oDesplNivel = new Desplegable();
        $oDesplNivel->setOpciones($aOpciones);
        $oDesplNivel->setNombre('nivel_stgr');
        $oDesplNivel->setOpcion_sel($nivel_stgr);
        $html_despl_nivel_stgr = $oDesplNivel->desplegable();

        $LocalRepository = $GLOBALS['container']->get(LocalRepositoryInterface::class);
        $aOpciones = $LocalRepository->getArrayLocales();
        $oDesplIdioma = new Desplegable();
        $oDesplIdioma->setBlanco(true);
        $oDesplIdioma->setOpciones($aOpciones);
        $oDesplIdioma->setNombre('idioma');
        $oDesplIdioma->setOpcion_sel($idioma);
        $html_despl_idioma = $oDesplIdioma->desplegable();

        $RepeticionRepository = $GLOBALS['container']->get(RepeticionRepositoryInterface::class);
        $aOpciones = $RepeticionRepository->getArrayRepeticion();
        $oDesplRepeticion = new Desplegable();
        $oDesplRepeticion->setOpciones($aOpciones);
        $oDesplRepeticion->setNombre('id_repeticion');
        $oDesplRepeticion->setOpcion_sel($id_repeticion);
        $html_despl_repeticion = $oDesplRepeticion->desplegable();

        $nombre_ubi = '';
        if (!empty($id_ubi) && $id_ubi !== 1) {
            $oCasa = Ubi::newUbi($id_ubi);
            $nombre_ubi = $oCasa->getNombre_ubi();
            if (empty($nombre_ubi)) {
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
            'html_despl_dl_org' => $html_despl_dl_org,
            'html_despl_tarifa' => $html_despl_tarifa,
            'html_despl_nivel_stgr' => $html_despl_nivel_stgr,
            'html_despl_idioma' => $html_despl_idioma,
            'html_despl_repeticion' => $html_despl_repeticion,
            'nombre_ubi' => $nombre_ubi,
        ];

        if ($calcTarifaInicial && $id_tipo_activ !== '') {
            $RelacionTarifaTipoActividadRepository = $GLOBALS['container']->get(RelacionTarifaTipoActividadRepositoryInterface::class);
            $aWhereT = [
                'id_tipo_activ' => $id_tipo_activ,
                '_ordre' => 'id_serie',
            ];
            $cActiTipoTarifa = $RelacionTarifaTipoActividadRepository->getTipoActivTarifas($aWhereT);
            if (!empty($cActiTipoTarifa) && count($cActiTipoTarifa) > 0) {
                $payload['tarifa_inicial'] = $cActiTipoTarifa[0]->getId_tarifa();
            } else {
                $payload['tarifa_inicial'] = null;
            }
        }

        return $payload;
    }
}
