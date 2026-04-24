<?php

declare(strict_types=1);

namespace src\misas\application\support;

use src\shared\config\ConfigGlobal;
use src\usuarios\domain\contracts\RoleRepositoryInterface;
use src\usuarios\domain\contracts\UsuarioRepositoryInterface;

/**
 * Resuelve el `id_nom_jefe` (filtro de zonas) del usuario actual.
 *
 * - Si el rol NO es `p-sacd` → `id_nom_jefe = null` (ve todas las zonas).
 * - Si el rol es `p-sacd` y ES jefe de calendario → `id_nom_jefe = null`.
 * - Si el rol es `p-sacd` y NO es jefe → `id_nom_jefe` = `csvIdPau` del usuario.
 *   - Si `csvIdPau` es 0 → error "sin permiso".
 *
 * Centraliza el bloque de 13 líneas duplicado en cinco clases
 * `*Data` del módulo misas. No hace `exit()` ni lanza excepciones: los
 * consumidores deciden cómo representar el error (excepción, JSON, etc.).
 */
class IdNomJefeResolver
{
    /**
     * @return array{id_nom_jefe: ?int, error: string}
     */
    public static function resolve(): array
    {
        $container = $GLOBALS['container'];

        $usuarioRepo = $container->get(UsuarioRepositoryInterface::class);
        $oMiUsuario = $usuarioRepo->findById(ConfigGlobal::mi_id_usuario());
        $id_role = $oMiUsuario->getId_role();

        $roleRepo = $container->get(RoleRepositoryInterface::class);
        $aRoles = $roleRepo->getArrayRoles();

        $role_nom = $aRoles[$id_role] ?? '';
        if ($role_nom !== 'p-sacd') {
            return ['id_nom_jefe' => null, 'error' => ''];
        }

        if (!empty($_SESSION['oConfig']) && $_SESSION['oConfig']->is_jefeCalendario()) {
            return ['id_nom_jefe' => null, 'error' => ''];
        }

        $id_nom_jefe = (int)$oMiUsuario->getCsvIdPauAsString();
        if ($id_nom_jefe === 0) {
            return ['id_nom_jefe' => null, 'error' => _('No tiene permiso para ver esta página')];
        }

        return ['id_nom_jefe' => $id_nom_jefe, 'error' => ''];
    }
}
