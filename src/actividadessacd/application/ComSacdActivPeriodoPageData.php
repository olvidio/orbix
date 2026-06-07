<?php

namespace src\actividadessacd\application;

use src\usuarios\domain\contracts\RoleRepositoryInterface;
use src\usuarios\domain\contracts\UsuarioRepositoryInterface;
final class ComSacdActivPeriodoPageData
{
    public function __construct(
        private UsuarioRepositoryInterface $usuarioRepository,
        private RoleRepositoryInterface $roleRepository,
    ) {
    }

    /**
     * @return array{perm_mod_txt: bool}
     */
    public function execute(): array
    {
        $perm_mod_txt = true;
        $sessionAuth = $_SESSION['session_auth'] ?? null;
        $idUsuario = 0;
        if (is_array($sessionAuth) && isset($sessionAuth['id_usuario']) && is_numeric($sessionAuth['id_usuario'])) {
            $idUsuario = (int)$sessionAuth['id_usuario'];
        }
        $oMiUsuario = $this->usuarioRepository->findById($idUsuario);
        if ($oMiUsuario !== null) {
            $id_role = $oMiUsuario->getId_role();
            $aRoles = $this->roleRepository->getArrayRoles();
            if (!empty($aRoles[$id_role]) && $aRoles[$id_role] === 'p-sacd') {
                $perm_mod_txt = false;
            }
        }

        return ['perm_mod_txt' => $perm_mod_txt];
    }
}
