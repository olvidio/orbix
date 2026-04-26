<?php

namespace src\cambios\application;

use src\shared\config\ConfigGlobal;
use src\actividades\domain\contracts\TipoDeActividadRepositoryInterface;
use src\actividades\domain\value_objects\StatusId;
use src\procesos\domain\contracts\ActividadFaseRepositoryInterface;
use function src\shared\domain\helpers\is_true;

/**
 * Data builder: lista de fases posibles para un `id_tipo_activ` (usado al
 * cambiar el desplegable de fases cuando el usuario cambia `objeto` o el
 * tipo de actividad).
 *
 * Sucesor de la rama `av_fases` del dispatcher legacy
 * `apps/cambios/controller/usuario_avisos_pref_ajax.php`.
 */
final class CambioUsuarioObjetoPrefFasesData
{
    /**
     * @param array{
     *   objeto?: string,
     *   id_tipo_activ?: string,
     *   dl_propia?: bool|string,
     * } $input
     * @return array
     */
    public static function execute(array $input): array
    {
        $objeto = (string)($input['objeto'] ?? '');
        $id_tipo_activ = (string)($input['id_tipo_activ'] ?? '');
        $dl_propia = is_true($input['dl_propia'] ?? true);

        $result = [
            'error' => '',
            'objeto' => $objeto,
            'aFases' => [],
            'fases_usa_procesos' => ConfigGlobal::is_app_installed('procesos'),
        ];

        if ($objeto === '') {
            $result['error'] = (string)_("primero debe elegir un objeto sobre el que mirar los cambios");
            return $result;
        }

        if ($result['fases_usa_procesos']) {
            $TipoDeActividadRepository = $GLOBALS['container']->get(TipoDeActividadRepositoryInterface::class);
            $aTiposDeProcesos = $TipoDeActividadRepository->getTiposDeProcesos($id_tipo_activ, $dl_propia);
            $ActividadFaseRepository = $GLOBALS['container']->get(ActividadFaseRepositoryInterface::class);
            $result['aFases'] = $ActividadFaseRepository->getArrayActividadFases($aTiposDeProcesos);
        } else {
            $a_status = StatusId::getArrayStatus();
            unset($a_status[StatusId::ALL]);
            $result['aFases'] = array_flip($a_status);
        }

        return $result;
    }
}
