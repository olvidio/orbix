<?php

namespace src\procesos\application;

use src\shared\config\ConfigGlobal;
use src\actividades\domain\value_objects\StatusId;
use src\procesos\domain\contracts\ActividadFaseRepositoryInterface;
use src\procesos\domain\contracts\TareaProcesoRepositoryInterface;
use src\usuarios\domain\contracts\RoleRepositoryInterface;
use src\usuarios\domain\contracts\UsuarioRepositoryInterface;

/**
 * Caso de uso: devuelve la estructura de padres/hijos del arbol de fases
 * del proceso filtrando segun el sfsv/role del usuario.
 *
 * Retorna un array donde cada clave es el id de fase padre (0 = raiz) y
 * cada valor es una lista de ['id', 'nom']. El render HTML del árbol está en
 * {@see dibujarTree()} y lo invoca el controlador frontend.
 */
class ProcesosGet
{
    /**
     * HTML del árbol de fases (misma estructura que la clave `aPadres` de
     * {@see execute()}). Centralizado aquí para tests unitarios sin HTTP.
     */
    public static function dibujarTree(array $aPadres): string
    {
        if ($aPadres === []) {
            return '';
        }
        ksort($aPadres);
        $html = '<div id="tree">';
        if (!empty($aPadres[0])) {
            foreach ($aPadres[0] as $padre) {
                $id_fase_i = (int)$padre['id'];
                $nom = $padre['nom'];
                if (array_key_exists($id_fase_i, $aPadres)) {
                    $html .= '<div class="branch">';
                    $html .= '<div class="entry"><span>' . $nom . '</span>';
                    $html .= '<div class="branch">';
                    $html .= self::dibujarTreeHijos($aPadres, $id_fase_i);
                    $html .= '</div>';
                    $html .= '</div>';
                } else {
                    $html .= '<div class="entry"><span>' . $nom . '</span></div>';
                }
            }
        }
        $html .= '</div>';

        return $html;
    }

    private static function dibujarTreeHijos(array $aPadres, int $id_fase): string
    {
        if (empty($aPadres[$id_fase])) {
            return '';
        }
        $html = '';
        foreach ($aPadres[$id_fase] as $padre) {
            $id_fase_i = (int)$padre['id'];
            $nom = $padre['nom'];
            if (array_key_exists($id_fase_i, $aPadres)) {
                $html .= '<div class="branch">';
                $html .= '<div class="entry"><span>' . $nom . '</span>';
                $html .= '<div class="branch">';
                $html .= self::dibujarTreeHijos($aPadres, $id_fase_i);
                $html .= '</div>';
                $html .= '</div>';
            } else {
                $html .= '<div class="entry"><span>' . $nom . '</span></div>';
            }
        }

        return $html;
    }

    public function execute(array $input): array
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
            $id_fase_previa = empty($id_fase_previa) ? 0 : (int)$id_fase_previa;
            $aPadres[$id_fase_previa][$j] = ['id' => (int)$id_fase, 'nom' => $fase];
        }

        return ['aPadres' => $aPadres];
    }
}
