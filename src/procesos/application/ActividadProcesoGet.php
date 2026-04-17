<?php

namespace src\procesos\application;

use core\ConfigGlobal;
use src\procesos\domain\contracts\ActividadFaseRepositoryInterface;
use src\procesos\domain\contracts\ActividadProcesoTareaRepositoryInterface;
use src\procesos\domain\contracts\ActividadTareaRepositoryInterface;
use src\procesos\domain\contracts\TareaProcesoRepositoryInterface;
use function core\is_true;

/**
 * Caso de uso: devuelve la tabla HTML con las tareas del proceso
 * para un id_activ, incluyendo el control de completado y observaciones
 * segun los permisos de oficina del responsable.
 */
class ActividadProcesoGet
{
    public function execute(array $input): string
    {
        $Qid_activ = (int)($input['id_activ'] ?? 0);

        $aWhere = [
            'id_activ' => $Qid_activ,
            '_ordre' => 'id_fase',
        ];
        $ActividadProcesoTareaRepository = $GLOBALS['container']->get(ActividadProcesoTareaRepositoryInterface::class);
        $oLista = $ActividadProcesoTareaRepository->getActividadProcesoTareas($aWhere);
        $txt = '<table>';
        $txt .= '<tr><th>' . _("ok") . '</th><th>' . _("fase (tarea)") . '</th><th>' . _("responsable") . '</th><th>' . _("observaciones") . '</th><th></th></tr>';
        $i = 0;
        $ActividadFaseRepository = $GLOBALS['container']->get(ActividadFaseRepositoryInterface::class);
        $ActividadTareaRepository = $GLOBALS['container']->get(ActividadTareaRepositoryInterface::class);
        $TareaProcesoRepository = $GLOBALS['container']->get(TareaProcesoRepositoryInterface::class);
        foreach ($oLista as $oActividadProcesoTarea) {
            $id_item = $oActividadProcesoTarea->getId_item();
            $id_tipo_proceso = $oActividadProcesoTarea->getId_tipo_proceso(ConfigGlobal::mi_sfsv());
            $id_fase = $oActividadProcesoTarea->getId_fase();
            $id_tarea = $oActividadProcesoTarea->getId_tarea();
            $completado = $oActividadProcesoTarea->isCompletado();
            $observ = $oActividadProcesoTarea->getObserv();

            $oFase = $ActividadFaseRepository->findById($id_fase);
            $fase = $oFase->getDesc_fase();
            if (empty($fase)) {
                continue;
            }
            $oTarea = $ActividadTareaRepository->findById($id_tarea);
            $tarea = $oTarea->getDesc_tarea();
            $chk = is_true($completado) ? 'checked' : '';
            $cTareasProceso = $TareaProcesoRepository->getTareasProceso([
                'id_tipo_proceso' => $id_tipo_proceso,
                'id_fase' => $id_fase,
                'id_tarea' => $id_tarea,
            ]);
            if (!empty($cTareasProceso)) {
                $oTareaProceso = $cTareasProceso[0];
            } else {
                return sprintf(_("error: La fase del proceso tipo: %s, fase: %s, tarea: %s"), $id_tipo_proceso, $id_fase, $id_tarea);
            }
            $of_responsable_txt = $oTareaProceso->getOf_responsable_txt();

            $clase = ($i % 2) ? 'tono1' : 'tono3';
            $i++;
            $txt .= "<tr class='$clase'>";
            if (empty($of_responsable_txt) || ($_SESSION['oPerm']->have_perm_oficina($of_responsable_txt))) {
                $txt .= "<td style='text-align: center;' ><input type='checkbox' id='comp$id_item' name='completado' $chk></td>";
                $obs = "<td><input type='text' id='observ$id_item' name='observ' value='$observ' ></td>";
            } else {
                if (is_true($completado)) {
                    $icon = '<img src="' . ConfigGlobal::getWeb_icons() . '/checkbox-checked.png" title="ok">';
                } else {
                    $icon = '<img src="' . ConfigGlobal::getWeb_icons() . '/check-box-outline-blank.png" title="">';
                }
                $txt .= "<td style='text-align: center;' >$icon</td>";
                $obs = "<td></td>";
            }
            $txt_fase = empty($tarea) ? '' : "($tarea)";
            $txt .= "<td style='text-align: left;' >$fase $txt_fase</td>";
            $txt .= "<td>$of_responsable_txt</td>";
            $txt .= $obs;
            if (empty($of_responsable_txt) || ($_SESSION['oPerm']->have_perm_oficina($of_responsable_txt))) {
                $txt .= "<td><input type='button' name='b_guardar' value='" . _("guardar") . "' onclick='fnjs_guardar($id_item)'></td>";
            } else {
                $txt .= '<td></td>';
            }
            $txt .= '</tr>';
        }
        $txt .= '</table>';

        return $txt;
    }
}
