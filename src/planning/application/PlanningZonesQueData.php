<?php

namespace src\planning\application;

use src\usuarios\domain\contracts\RoleRepositoryInterface;
use src\usuarios\domain\contracts\UsuarioRepositoryInterface;
use src\zonassacd\domain\contracts\ZonaRepositoryInterface;

/**
 * Opciones de zona + comprobación de permiso para `planning_zones_que`.
 */
final class PlanningZonesQueData
{
    /**
     * @return array{error: string, opciones_zonas: array<int|string, string>}
     */
    public static function execute(): array
    {
        $UsuarioRepository = $GLOBALS['container']->get(UsuarioRepositoryInterface::class);
        $oMiUsuario = $UsuarioRepository->findById((int)($_SESSION['session_auth']['id_usuario'] ?? 0));
        $id_role = $oMiUsuario->getId_role();

        $RoleRepository = $GLOBALS['container']->get(RoleRepositoryInterface::class);
        $aRoles = $RoleRepository->getArrayRoles();
        $id_nom_jefe = null;
        if (!empty($aRoles[$id_role]) && $aRoles[$id_role] === 'p-sacd') {
            if (!$_SESSION['oConfig']->is_jefeCalendario()) {
                $id_nom_jefe = (int)$oMiUsuario->getCsvIdPauAsString();
                if (empty($id_nom_jefe)) {
                    return ['error' => _("No tiene permiso para ver esta página"), 'opciones_zonas' => []];
                }
            }
        }

        $ZonaRepository = $GLOBALS['container']->get(ZonaRepositoryInterface::class);
        $aOpciones = $ZonaRepository->getArrayZonas($id_nom_jefe);
        if (count($aOpciones) < 1) {
            return ['error' => _("No tiene permiso para ver esta página"), 'opciones_zonas' => []];
        }

        return ['error' => '', 'opciones_zonas' => $aOpciones];
    }
}
