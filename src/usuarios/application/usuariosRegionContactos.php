<?php

namespace src\usuarios\application;

use src\permisos\domain\MenuDlPermissionBits;
use src\permisos\domain\PermDl;
use src\shared\infrastructure\persistence\ConfigDB;
use src\shared\infrastructure\persistence\DBConnection;
use src\usuarios\domain\contracts\PermMenuRepositoryInterface;
use src\usuarios\domain\contracts\UsuarioGrupoRepositoryInterface;
use src\usuarios\domain\contracts\UsuarioRepositoryInterface;

class usuariosRegionContactos
{
    public function __construct(
        private UsuarioGrupoRepositoryInterface $usuarioGrupoRepository,
        private UsuarioRepositoryInterface $usuarioRepository,
        private PermMenuRepositoryInterface $permMenuRepository,
    ) {
    }

    /**
     * @return array{error: string, data: array<string, mixed>}
     */
    public function execute(string $region = ''): array
    {
        $error_txt = '';
        $esquema = $region . 'v';
        $oConfigDB = new ConfigDB('sv-e_select');
        $config = $oConfigDB->getEsquema($esquema);
        $oConexion = new DBConnection($config);
        try {
            $oDevelPC = $oConexion->getPDO();
        } catch (\Throwable $e) {
            $error_txt = 'Error al obtener la conexión a la base de datos: ' . $e->getMessage();

            return ['error' => $error_txt, 'data' => []];
        }

        $this->usuarioGrupoRepository->setoDbl_select($oDevelPC);
        $this->usuarioRepository->setoDbl_select($oDevelPC);
        $cUsuariosRegion = $this->usuarioRepository->getUsuarios(['id_role' => 3], ['id_role' => '>']);

        $aContactos = [];
        foreach ($cUsuariosRegion as $oUsuario) {
            $email = $oUsuario->getEmailAsString();
            if ($email === null || $email === '') {
                continue;
            }
            $id_usuario = $oUsuario->getId_usuario();
            $usuario = $oUsuario->getUsuarioAsString();
            $nom_usuario = $oUsuario->getNomUsuarioAsString() ?? $usuario;

            $cGrupos = $this->usuarioGrupoRepository->getUsuariosGrupos(['id_usuario' => $id_usuario]);
            $iperm_menu = 0;
            $this->permMenuRepository->setoDbl_select($oDevelPC);
            foreach ($cGrupos as $UsuarioGrupo) {
                $id_grupo = $UsuarioGrupo->getId_grupo();
                $cPermMenu = $this->permMenuRepository->getPermMenus(['id_usuario' => $id_grupo]);
                foreach ($cPermMenu as $oPermMenu) {
                    $iperm_menu = $iperm_menu | $oPermMenu->getMenu_perm();
                }
            }
            $_SESSION['iPermMenus'] = $iperm_menu;
            $oPerm = new PermDl();
            $oPerm->setAccion($iperm_menu);

            if ($oPerm->have_perm_oficina('est') === true
                || $oPerm->have_perm_oficina('sm') === true
                || $oPerm->have_perm_oficina('agd') === true) {
                $aContactos[$nom_usuario] = [
                    'email' => $email,
                    'cargo' => MenuDlPermissionBits::listaTxt2($iperm_menu),
                ];
            }
        }

        return [
            'error' => $error_txt,
            'data' => [
                'success' => true,
                'contactos' => $aContactos,
            ],
        ];
    }
}
