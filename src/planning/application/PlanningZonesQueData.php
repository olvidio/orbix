<?php

namespace src\planning\application;

use src\shared\config\ConfigGlobal;
use src\usuarios\domain\contracts\RoleRepositoryInterface;
use src\usuarios\domain\contracts\UsuarioRepositoryInterface;
use src\zonassacd\domain\contracts\ZonaRepositoryInterface;

/**
 * Opciones de zona + comprobación de permiso para `planning_zones_que`.
 */
final class PlanningZonesQueData
{
    public function __construct(
        private UsuarioRepositoryInterface $usuarioRepository,
        private RoleRepositoryInterface $roleRepository,
        private ZonaRepositoryInterface $zonaRepository,
    ) {
    }

    /**
     * @return array{error: string, opciones_zonas: array<int|string, string>}
     */
    public function execute(): array
    {
        $id_usuario = ConfigGlobal::mi_id_usuario();
        $oMiUsuario = $this->usuarioRepository->findById((int)$id_usuario);
        if ($oMiUsuario === null) {
            return ['error' => _("No se encuentra el usuario"), 'opciones_zonas' => []];
        }
        $id_role = $oMiUsuario->getId_role();

        $aRoles = $this->roleRepository->getArrayRoles();
        $id_nom_jefe = null;
        if (!empty($aRoles[$id_role]) && $aRoles[$id_role] === 'p-sacd') {
            $oConfig = $_SESSION['oConfig'] ?? null;
            $esJefeCalendario = is_object($oConfig) && method_exists($oConfig, 'is_jefeCalendario')
                && $oConfig->is_jefeCalendario();
            if (!$esJefeCalendario) {
                $id_nom_jefe = (int)$oMiUsuario->getCsvIdPauAsString();
                if (empty($id_nom_jefe)) {
                    return ['error' => _("No tiene permiso para ver esta página"), 'opciones_zonas' => []];
                }
            }
        }

        $aOpciones = $this->zonaRepository->getArrayZonas($id_nom_jefe);
        if (count($aOpciones) < 1) {
            return ['error' => _("No tiene permiso para ver esta página"), 'opciones_zonas' => []];
        }

        return ['error' => '', 'opciones_zonas' => $aOpciones];
    }
}
