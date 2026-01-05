<?php

use core\ConfigGlobal;
use src\procesos\domain\contracts\ActividadFaseRepositoryInterface;
use src\procesos\domain\contracts\ActividadProcesoTareaRepositoryInterface;
use src\procesos\domain\contracts\ActividadTareaRepositoryInterface;
use src\procesos\domain\contracts\TareaProcesoRepositoryInterface;
use function core\is_true;


// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qque = (string)filter_input(INPUT_POST, 'que');
$Qid_activ = (integer)filter_input(INPUT_POST, 'id_activ');

switch ($Qque) {
    case 'generar':
        $ActividadProcesoTareaRepository = $GLOBALS['container']->get(ActividadProcesoTareaRepositoryInterface::class);
        $ActividadProcesoTareaRepository->generarProceso($Qid_activ, ConfigGlobal::mi_sfsv(), TRUE);
        break;
    case 'get':
        $aWhere = [
            'id_activ' => $Qid_activ,
            '_ordre' => 'id_fase',
        ];
        $ActividadProcesoTareaRepository = $GLOBALS['container']->get(ActividadProcesoTareaRepositoryInterface::class);
        $oLista = $ActividadProcesoTareaRepository->getActividadProcesoTareas($aWhere);
        $txt = '<table>';
        $txt .= '<tr><th>' . _("ok") . '</th><th>' . _("fase (tarea)") . '</th><th>' . _("responsable") . '</th><th>' . _("observaciones") . '</th><th></th></tr>';
        $i = 0;
        $ActividadFseRepository = $GLOBALS['container']->get(ActividadFaseRepositoryInterface::class);
        $ActividadTareaRepository = $GLOBALS['container']->get(ActividadTareaRepositoryInterface::class);
        $TareaProcesoRepository = $GLOBALS['container']->get(TareaProcesoRepositoryInterface::class);
        foreach ($oLista as $oActividadProcesoTarea) {
            $id_item = $oActividadProcesoTarea->getId_item();
            $id_tipo_proceso = $oActividadProcesoTarea->getId_tipo_proceso(ConfigGlobal::mi_sfsv());
            $id_fase = $oActividadProcesoTarea->getId_fase();
            $id_tarea = $oActividadProcesoTarea->getId_tarea();
            $completado = $oActividadProcesoTarea->getCompletado();
            $observ = $oActividadProcesoTarea->getObserv();

            $oFase = $ActividadFseRepository->findById($id_fase);
            $fase = $oFase->getDesc_fase();
            if (empty($fase)) {
                continue;
            } // No existe
            $oTarea = $ActividadTareaRepository->findById($id_tarea);
            $tarea = $oTarea->getDesc_tarea();
            $chk = is_true($completado)? 'checked' : '';
            //buscar of responsable
            $cTareasProceso = $TareaProcesoRepository->getTareasProceso(['id_tipo_proceso' => $id_tipo_proceso,
                'id_fase' => $id_fase,
                'id_tarea' => $id_tarea]);
            // sólo debería haber uno
            if (!empty($cTareasProceso)) {
                $oTareaProceso = $cTareasProceso[0];
            } else {
                $msg_err = sprintf(_("error: La fase del proceso tipo: %s, fase: %s, tarea: %s"), $id_tipo_proceso, $id_fase, $id_tarea);
                exit($msg_err);
            }
            $of_responsable_txt = $oTareaProceso->getOf_responsable_txt();

            $clase = "tono1";
            $i % 2 ? 0 : $clase = "tono3";
            $i++;
            $txt .= "<tr  class='$clase'>";
            if (empty($of_responsable_txt) || ($_SESSION['oPerm']->have_perm_oficina($of_responsable_txt))) {
                $txt .= "<td style='text-align: center;' ><input type='checkbox' id='comp$id_item' name='completado' $chk></td>";
                $obs = "<td><input type='text' id='observ$id_item' name='observ' value='$observ' ></td>";
            } else {
                $icon = '';
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
        echo $txt;
        break;
    case 'update':
        $Qid_item = (integer)filter_input(INPUT_POST, 'id_item');
        $Qcompletado = (string)filter_input(INPUT_POST, 'completado');
        $Qobserv = (string)filter_input(INPUT_POST, 'observ');
        $Qforce = (string)filter_input(INPUT_POST, 'force');
        $ProcesoActividadService = $GLOBALS['container']->get(ProcesoActividadService::class);
        $ActividadProcesoTareaRepository = $GLOBALS['container']->get(ActividadProcesoTareaRepositoryInterface::class);
        $oFicha = $ActividadProcesoTareaRepository->findById($Qid_item);
        $oFicha->setCompletado($Qcompletado);
        $oFicha->setObserv($Qobserv);
        //$oFicha->setForce($Qforce); // Esto no parece existir en la entidad nueva, pero ProcesoActividadService puede manejar force
        if ($ProcesoActividadService->guardar($oFicha) === false) {
            echo _("hay un error, no se ha guardado");
            echo "\n" . $ActividadProcesoTareaRepository->getErrorTxt();
        }
        break;
}
