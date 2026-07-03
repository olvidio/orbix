<?php

namespace src\procesos\application;

use src\shared\config\ConfigGlobal;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\procesos\domain\contracts\ActividadProcesoTareaRepositoryInterface;
use src\permisos\domain\XPermisos;
use src\procesos\domain\contracts\TareaProcesoRepositoryInterface;
use src\shared\domain\helpers\FuncTablasSupport;

/**
 * Caso de uso: aplica setCompletado a la fase nueva para actividades seleccionadas.
 */
class FasesActivCambioUpdate
{
    public function __construct(
        private readonly ActividadAllRepositoryInterface $actividadAllRepository,
        private readonly ActividadProcesoTareaRepositoryInterface $actividadProcesoTareaRepository,
        private readonly TareaProcesoRepositoryInterface $tareaProcesoRepository,
        private readonly ProcesoActividadService $procesoActividadService,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     */
    public function execute(array $input): string
    {
        $Qid_fase_nueva = FuncTablasSupport::inputString($input, 'id_fase_nueva');
        $a_sel = FuncTablasSupport::inputStringList($input, 'sel');
        $Qaccion = FuncTablasSupport::inputString($input, 'accion');

        $txtOut = '';

        foreach ($a_sel as $id_activ_raw) {
            $id_activ = (int)strtok($id_activ_raw, '#');
            $cListaSel = $this->actividadProcesoTareaRepository->getActividadProcesoTareas([
                'id_activ' => $id_activ,
                'id_fase' => $Qid_fase_nueva,
            ]);
            if ($cListaSel === []) {
                $oActividad = $this->actividadAllRepository->findById($id_activ);
                $nom_activ = $oActividad?->getNom_activ() ?? (string)$id_activ;
                $txt = sprintf(_("No se encuentra esta fase %s para esta actividad %s(%s)"), $Qid_fase_nueva, $nom_activ, $id_activ);
                $txt .= '<br>';
                $txt .= _("puede que tenga que regenerar el proceso");
                $txtOut .= $txt;
                continue;
            }
            $oActividadProcesoTarea = $cListaSel[0];
            $id_tipo_proceso = $oActividadProcesoTarea->getId_tipo_proceso();
            $id_fase = $oActividadProcesoTarea->getId_fase();
            $id_tarea = $oActividadProcesoTarea->getId_tarea();
            $cTareasProceso = $this->tareaProcesoRepository->getTareasProceso([
                'id_tipo_proceso' => $id_tipo_proceso,
                'id_fase' => $id_fase,
                'id_tarea' => $id_tarea,
            ]);
            if ($cTareasProceso === []) {
                return sprintf(_("error: La fase del proceso tipo: %s, fase: %s, tarea: %s"), $id_tipo_proceso, $id_fase, $id_tarea);
            }
            $oTareaProceso = $cTareasProceso[0];
            $of_responsable_txt = $oTareaProceso->getOf_responsable_txt();
            $oPerm = $_SESSION['oPerm'] ?? null;
            if ($of_responsable_txt === '' || ($oPerm instanceof XPermisos && $oPerm->have_perm_oficina($of_responsable_txt))) {
                if ($Qaccion === 'desmarcar') {
                    $oActividadProcesoTarea->setCompletado(false);
                } else {
                    $oActividadProcesoTarea->setCompletado(true);
                }
                if ($this->procesoActividadService->guardar($oActividadProcesoTarea) === false) {
                    $err = $this->procesoActividadService->getErrorTxt();
                    if ($err !== '') {
                        $txtOut .= $err;
                    } else {
                        $txtOut .= _("hay un error, no se ha guardado");
                        $txtOut .= "\n" . $this->actividadProcesoTareaRepository->getErrorTxt();
                    }
                    $txtOut .= '<br>';
                }
            } else {
                $txtOut .= _("No tiene permiso para completar la fase, no se ha guardado");
            }
        }

        return $txtOut;
    }
}
