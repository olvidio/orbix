<?php

namespace src\cambios\application;

use src\actividades\domain\contracts\TipoDeActividadRepositoryInterface;
use src\actividades\domain\value_objects\StatusId;
use src\procesos\domain\contracts\ActividadFaseRepositoryInterface;
use src\shared\config\ConfigGlobal;

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
    public function __construct(
        private TipoDeActividadRepositoryInterface $tipoDeActividadRepository,
        private ActividadFaseRepositoryInterface $actividadFaseRepository,
    ) {
    }

    /**
     * @param array{
     *   objeto?: string,
     *   id_tipo_activ?: string,
     *   dl_propia?: bool|string,
     * } $input
     * @return array<string, mixed>
     */
    public function execute(array $input): array
    {
        $objeto = (string)($input['objeto'] ?? '');
        $id_tipo_activ = (string)($input['id_tipo_activ'] ?? '');
        $dl_propia = \src\shared\domain\helpers\FuncTablasSupport::isTrue($input['dl_propia'] ?? true) ?? true;

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
            $aTiposDeProcesos = $this->tipoDeActividadRepository->getTiposDeProcesos($id_tipo_activ, $dl_propia);
            $result['aFases'] = $this->actividadFaseRepository->getArrayActividadFases($aTiposDeProcesos);
        } else {
            $a_status = StatusId::getArrayStatus();
            unset($a_status[StatusId::ALL]);
            $result['aFases'] = $a_status;
        }

        return $result;
    }
}
