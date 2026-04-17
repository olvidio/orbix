<?php

namespace src\procesos\application;

use core\ConfigGlobal;
use src\actividades\domain\value_objects\StatusId;
use src\menus\domain\PermisoMenu;
use src\procesos\domain\contracts\ActividadFaseRepositoryInterface;
use src\procesos\domain\contracts\TareaProcesoRepositoryInterface;
use src\usuarios\domain\contracts\RoleRepositoryInterface;
use src\usuarios\domain\contracts\UsuarioRepositoryInterface;

/**
 * Caso de uso: devuelve el HTML del arbol de fases del proceso
 * filtrando segun el sfsv/role del usuario.
 */
class ProcesosGet
{
    public function execute(array $input): string
    {
        $Qid_tipo_proceso = (int)($input['id_tipo_proceso'] ?? 0);

        $TareaProcesoRepository = $GLOBALS['container']->get(TareaProcesoRepositoryInterface::class);
        $cTareasProceso = $TareaProcesoRepository->getTareasProceso([
            'id_tipo_proceso' => $Qid_tipo_proceso,
            '_ordre' => 'status,id_of_responsable',
        ]);

        StatusId::getArrayStatus();

        $UsuarioRepository = $GLOBALS['container']->get(UsuarioRepositoryInterface::class);
        $oMiUsuario = $UsuarioRepository->findById(ConfigGlobal::mi_id_usuario());
        $id_role = $oMiUsuario->getId_role();
        $miSfsv = ConfigGlobal::mi_sfsv();

        $RoleRepository = $GLOBALS['container']->get(RoleRepositoryInterface::class);
        $aRoles = $RoleRepository->getArrayRoles();

        $oPermMenus = new PermisoMenu();
        $oPermMenus->lista_array();

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

        $j = 0;
        $aPadres = [];
        $ActividadFaseRepository = $GLOBALS['container']->get(ActividadFaseRepositoryInterface::class);
        foreach ($cTareasProceso as $oTareaProceso) {
            $j++;
            $id_fase = $oTareaProceso->getId_fase();
            $oFase = $ActividadFaseRepository->findById($id_fase);
            $fase = $oFase->getDesc_fase();
            $sf = ($oFase->isSf()) ? 2 : 0;
            $sv = ($oFase->isSv()) ? 1 : 0;
            if (!(($soy & $sf) || ($soy & $sv))) {
                $j--;
                continue;
            }
            $aFases_previas = $oTareaProceso->getJson_fases_previas(true);
            $id_fase_previa = '';
            foreach ($aFases_previas as $oFaseP) {
                $id_fase_previa = $oFaseP['id_fase'];
                if (empty($id_fase_previa)) {
                    continue;
                }
            }
            $id_fase_previa = empty($id_fase_previa) ? 0 : $id_fase_previa;
            $aPadres[$id_fase_previa][$j] = ['id' => $id_fase, 'nom' => $fase];
        }

        return empty($aPadres) ? '' : self::dibujarTree($aPadres);
    }

    public static function dibujarTree(array $aPadres): string
    {
        $html_tree = '<div id="tree">';
        ksort($aPadres);
        foreach ($aPadres[0] as $padre) {
            $id_fase_i = $padre['id'];
            $nom = $padre['nom'];
            if (array_key_exists($id_fase_i, $aPadres)) {
                $html_tree .= '<div class="branch">';
                $html_tree .= '<div class="entry"><span>' . $nom . '</span>';
                $html_tree .= '<div class="branch">';
                $html_tree .= self::dibujarHijos($aPadres, $id_fase_i);
                $html_tree .= "</div>";
                $html_tree .= "</div>";
            } else {
                $html_tree .= '<div class="entry"><span>' . $nom . '</span></div>';
            }
        }
        $html_tree .= '</div>';
        return $html_tree;
    }

    private static function dibujarHijos(array $aPadres, int $id_fase): string
    {
        $html = '';
        foreach ($aPadres[$id_fase] as $padre) {
            $id_fase_i = $padre['id'];
            $nom = $padre['nom'];
            if (array_key_exists($id_fase_i, $aPadres)) {
                $html .= '<div class="branch">';
                $html .= '<div class="entry"><span>' . $nom . '</span>';
                $html .= '<div class="branch">';
                $html .= self::dibujarHijos($aPadres, $id_fase_i);
                $html .= "</div>";
                $html .= "</div>";
            } else {
                $html .= '<div class="entry"><span>' . $nom . '</span></div>';
            }
        }
        return $html;
    }
}
