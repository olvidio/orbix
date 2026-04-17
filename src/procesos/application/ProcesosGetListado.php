<?php

namespace src\procesos\application;

use core\ConfigGlobal;
use src\actividades\domain\value_objects\StatusId;
use src\menus\domain\PermisoMenu;
use src\procesos\domain\contracts\ActividadFaseRepositoryInterface;
use src\procesos\domain\contracts\ActividadTareaRepositoryInterface;
use src\procesos\domain\contracts\TareaProcesoRepositoryInterface;
use src\usuarios\domain\contracts\RoleRepositoryInterface;
use src\usuarios\domain\contracts\UsuarioRepositoryInterface;

/**
 * Caso de uso: devuelve la tabla HTML con el listado de fases del
 * proceso (responsable, tarea, fase previa) filtrando por sfsv/role.
 */
class ProcesosGetListado
{
    public function execute(array $input): string
    {
        $Qid_tipo_proceso = (int)($input['id_tipo_proceso'] ?? 0);
        $a_status = StatusId::getArrayStatus();

        $UsuarioRepository = $GLOBALS['container']->get(UsuarioRepositoryInterface::class);
        $oMiUsuario = $UsuarioRepository->findById(ConfigGlobal::mi_id_usuario());
        $id_role = $oMiUsuario->getId_role();
        $miSfsv = ConfigGlobal::mi_sfsv();

        $RoleRepository = $GLOBALS['container']->get(RoleRepositoryInterface::class);
        $aRoles = $RoleRepository->getArrayRoles();

        $oPermMenus = new PermisoMenu();
        $aOpcionesOficinas = $oPermMenus->lista_array();

        if (!empty($aRoles[$id_role]) && ($aRoles[$id_role] === 'SuperAdmin')) {
            $soy = 3;
        } else {
            $soy = 0;
            switch ($miSfsv) {
                case 1:
                    $soy = 1;
                    break;
                case 2:
                    $soy = 2;
                    break;
            }
        }

        $TareaProcesoRepository = $GLOBALS['container']->get(TareaProcesoRepositoryInterface::class);
        $cTareasProceso = $TareaProcesoRepository->getTareasProceso([
            'id_tipo_proceso' => $Qid_tipo_proceso,
            '_ordre' => 'status,id_of_responsable',
        ]);
        $txt = '<table>';
        $txt .= '<tr><th>' . _("status") . '</th><th>' . _("responsable") . '</th>';
        $txt .= '<th colspan=3>' . _("fase - tarea") . '</th><th>' . _("modificar") . '</th><th>' . _("eliminar") . '</th></tr>';
        $i = 0;
        $ActividadFaseRepository = $GLOBALS['container']->get(ActividadFaseRepositoryInterface::class);
        $ActividadTareaRepository = $GLOBALS['container']->get(ActividadTareaRepositoryInterface::class);
        foreach ($cTareasProceso as $oTareaProceso) {
            $i++;
            $clase = ($i % 2 === 0) ? 'tono2' : 'tono4';
            $id_item = $oTareaProceso->getId_item();
            $status = $oTareaProceso->getStatus();
            $status_txt = $a_status[$status] ?? '';
            $id_of_responsable = $oTareaProceso->getId_of_responsable();
            $responsable = empty($aOpcionesOficinas[$id_of_responsable]) ? '' : $aOpcionesOficinas[$id_of_responsable];

            $id_fase = $oTareaProceso->getId_fase();
            $oFase = $ActividadFaseRepository->findById($id_fase);
            $fase = $oFase->getDesc_fase();
            $sf = ($oFase->isSf()) ? 2 : 0;
            $sv = ($oFase->isSv()) ? 1 : 0;
            if (!(($soy & $sf) || ($soy & $sv))) {
                $i--;
                continue;
            }
            $id_tarea = $oTareaProceso->getId_tarea();
            $oTarea = $ActividadTareaRepository->findById($id_tarea);
            $tarea = $oTarea->getDesc_tarea();
            $tarea_txt = empty($tarea) ? '' : "($tarea)";
            $fase_previa = '';
            $tarea_previa_txt = '';
            $aFases_previas = $oTareaProceso->getJson_fases_previas(true);
            foreach ($aFases_previas as $oFaseP) {
                $id_fase_previa = $oFaseP['id_fase'];
                if (empty($id_fase_previa)) {
                    continue;
                }
                $oFase_previa = $ActividadFaseRepository->findById($id_fase_previa);
                $fase_previa .= empty($fase_previa) ? '' : ' ' . _("y") . ' ';
                $fase_previa .= $oFase_previa->getDesc_fase();
            }

            $mod = "<span class=link onclick=fnjs_modificar($id_item) title='" . _("modificar") . "' >" . _("modificar") . "</span>";
            $drop = "<span class=link onclick=fnjs_eliminar($id_item) title='" . _("eliminar") . "' >" . _("eliminar") . "</span>";

            $txt .= "<tr class=$clase><td>($status_txt)</td><td>$responsable</td><td colspan=3>$fase $tarea_txt</td><td>$mod</td><td>$drop</td></tr>";
            $txt .= "<tr><td></td><td></td><td>&nbsp;&nbsp;&nbsp;" . _("requisito") . ":</td><td>$fase_previa $tarea_previa_txt</td></tr>";
        }
        $txt .= '</table>';

        return $txt;
    }
}
