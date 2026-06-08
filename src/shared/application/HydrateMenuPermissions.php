<?php

declare(strict_types=1);

namespace src\shared\application;

use src\menus\domain\PermisoMenu;
use src\permisos\domain\PermDl;
use src\shared\config\ConfigGlobal;
use src\shared\infrastructure\DependencyResolver;
use src\usuarios\domain\contracts\PermMenuRepositoryInterface;
use src\usuarios\domain\contracts\RoleRepositoryInterface;
use src\usuarios\domain\contracts\UsuarioGrupoRepositoryInterface;
use src\usuarios\domain\contracts\UsuarioRepositoryInterface;
use src\zonassacd\domain\contracts\ZonaRepositoryInterface;

/**
 * Calcula permisos de menú del usuario y los deja en sesión (`iPermMenus`, `oPerm`).
 */
final class HydrateMenuPermissions
{
    public function execute(): void
    {
        if (array_key_exists('iPermMenus', $_SESSION)) {
            if (empty($_SESSION['oPerm'])) {
                $_SESSION['oPerm'] = new PermDl();
                $_SESSION['oPerm']->setAccion((int) $_SESSION['iPermMenus']);
            }

            return;
        }

        $UsuarioGrupoRepository = DependencyResolver::get(UsuarioGrupoRepositoryInterface::class);
        $UsuarioRepository = DependencyResolver::get(UsuarioRepositoryInterface::class);
        $RoleRepository = DependencyResolver::get(RoleRepositoryInterface::class);
        $PermMenuRepository = DependencyResolver::get(PermMenuRepositoryInterface::class);

        $aRoles = $RoleRepository->getArrayRoles();
        $cGrupos = $UsuarioGrupoRepository->getUsuariosGrupos(['id_usuario' => ConfigGlobal::mi_id_usuario()]);
        $iperm_menu = 0;

        foreach ($cGrupos as $UsuarioGrupo) {
            $id_grupo = $UsuarioGrupo->getId_grupo();
            $cPermMenu = $PermMenuRepository->getPermMenus(['id_usuario' => $id_grupo]);
            foreach ($cPermMenu as $oPermMenu) {
                $iperm_menu = $iperm_menu | $oPermMenu->getMenu_perm();
            }
        }

        $id_usuario = ConfigGlobal::mi_id_usuario();
        $oMiUsuario = $UsuarioRepository->findById($id_usuario);
        if ($oMiUsuario !== null) {
            $id_role = $oMiUsuario->getId_role();
            if (!empty($aRoles[$id_role]) && ($aRoles[$id_role] === 'p_sacd')) {
                $id_nom = $oMiUsuario->getCsvIdPauAsString();
                if ($id_nom !== '' && $id_nom !== '0') {
                    $ZonaRepository = DependencyResolver::get(ZonaRepositoryInterface::class);
                    if ($ZonaRepository->isJefeZona((int) $id_nom)) {
                        $oPermisoMenu = new PermisoMenu();
                        $permissions = $oPermisoMenu->omplir();
                        $permJefeZona = $permissions['jefeZona'] ?? 0;
                        if (is_numeric($permJefeZona)) {
                            $iperm_menu |= (int) $permJefeZona;
                        }
                    }
                }
            }
        }

        $_SESSION['iPermMenus'] = $iperm_menu;
        $_SESSION['oPerm'] = new PermDl();
        $_SESSION['oPerm']->setAccion($iperm_menu);
    }
}
