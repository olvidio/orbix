<?php

declare(strict_types=1);

namespace src\misas\application\support;

use src\shared\config\ConfigGlobal;
use src\usuarios\domain\contracts\RoleRepositoryInterface;
use src\usuarios\domain\contracts\UsuarioRepositoryInterface;

/**
 * Resuelve el `id_nom_jefe` (filtro de zonas) del usuario actual.
 */
class IdNomJefeResolver
{
    public function __construct(
        private readonly UsuarioRepositoryInterface $usuarioRepository,
        private readonly RoleRepositoryInterface $roleRepository,
    ) {
    }

    /**
     * @return array{id_nom_jefe: ?int, error: string}
     */
    public function resolve(): array
    {
        $oMiUsuario = $this->usuarioRepository->findById(ConfigGlobal::mi_id_usuario());
        if ($oMiUsuario === null) {
            return ['id_nom_jefe' => null, 'error' => _('Usuario no encontrado')];
        }

        $id_role = $oMiUsuario->getId_role();
        $aRoles = $this->roleRepository->getArrayRoles();

        $role_nom = $aRoles[$id_role] ?? '';
        if ($role_nom !== 'p-sacd') {
            return ['id_nom_jefe' => null, 'error' => ''];
        }

        $oConfig = $_SESSION['oConfig'] ?? null;
        $esJefeCalendario = is_object($oConfig)
            && method_exists($oConfig, 'is_jefeCalendario')
            && $oConfig->is_jefeCalendario();

        if ($esJefeCalendario) {
            return ['id_nom_jefe' => null, 'error' => ''];
        }

        $csvIdPau = $oMiUsuario->getCsvIdPauAsString();
        $id_nom_jefe = is_numeric($csvIdPau) ? (int) $csvIdPau : 0;
        if ($id_nom_jefe === 0) {
            return ['id_nom_jefe' => null, 'error' => _('No tiene permiso para ver esta página')];
        }

        return ['id_nom_jefe' => $id_nom_jefe, 'error' => ''];
    }
}
