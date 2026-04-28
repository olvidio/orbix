<?php

namespace src\actividadessacd\application;

use src\usuarios\domain\contracts\RoleRepositoryInterface;
use src\usuarios\domain\contracts\UsuarioRepositoryInterface;

final class ComSacdActivPeriodoPageData
{
    public static function execute(): array
    {
        $perm_mod_txt = true;
        $UsuarioRepository = $GLOBALS['container']->get(UsuarioRepositoryInterface::class);
        $oMiUsuario = $UsuarioRepository->findById((int)($_SESSION['session_auth']['id_usuario'] ?? 0));
        if ($oMiUsuario !== null) {
            $id_role = $oMiUsuario->getId_role();
            $RoleRepository = $GLOBALS['container']->get(RoleRepositoryInterface::class);
            $aRoles = $RoleRepository->getArrayRoles();
            if (!empty($aRoles[$id_role]) && $aRoles[$id_role] === 'p-sacd') {
                $perm_mod_txt = false;
            }
        }

        return ['perm_mod_txt' => $perm_mod_txt];
    }
}
