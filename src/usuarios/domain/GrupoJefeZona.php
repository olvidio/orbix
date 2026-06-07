<?php

namespace src\usuarios\domain;

use src\menus\domain\PermisoMenuBits;
use src\usuarios\domain\contracts\PermMenuRepositoryInterface;
use src\usuarios\domain\contracts\RoleRepositoryInterface;
use src\usuarios\domain\contracts\UsuarioGrupoRepositoryInterface;
use src\usuarios\domain\contracts\UsuarioRepositoryInterface;
use src\usuarios\domain\entity\UsuarioGrupo;
use src\usuarios\domain\value_objects\PauType;
use src\zonassacd\domain\contracts\ZonaRepositoryInterface;

class GrupoJefeZona
{
    private int $id_grupo_jefe_zona = 0;

    public function __construct(
        private UsuarioGrupoRepositoryInterface $usuarioGrupoRepository,
        private RoleRepositoryInterface $roleRepository,
        private UsuarioRepositoryInterface $usuarioRepository,
        private ZonaRepositoryInterface $zonaRepository,
        private PermMenuRepositoryInterface $permMenuRepository,
    ) {
    }

    public function reAsignar(): void
    {
        $a_miembros_jefeZona = $this->miembrosJefeZona();
        $a_jefes_zona = $this->arrayJefesZona();
        $a_usuarios_sacd = $this->usuariosSacd();

        foreach ($a_jefes_zona as $id_nom) {
            $idNomKey = (string) $id_nom;
            if (!array_key_exists($idNomKey, $a_miembros_jefeZona)) {
                if (!empty($a_usuarios_sacd[$idNomKey])) {
                    $id_usuario = $a_usuarios_sacd[$idNomKey];
                    $this->addGrupoUsuario($id_usuario, $this->id_grupo_jefe_zona);
                }
            }
        }
        foreach ($a_usuarios_sacd as $id_nom => $id_usuario) {
            if (!in_array($id_nom, $a_jefes_zona, true)) {
                $this->quitarGrupoUsuario($id_usuario, $this->id_grupo_jefe_zona);
            }
        }
    }

    private function addGrupoUsuario(int $id_usuario, int $id_grupo): string
    {
        $error_txt = '';
        $oUsuarioGrupo = new UsuarioGrupo();
        $oUsuarioGrupo->setId_usuario($id_usuario);
        $oUsuarioGrupo->setId_grupo($id_grupo);
        if ($this->usuarioGrupoRepository->Guardar($oUsuarioGrupo) === false) {
            $error_txt .= _('hay un error, no se ha guardado');
            $error_txt .= "\n" . $this->usuarioGrupoRepository->getErrorTxt();
        }

        return $error_txt;
    }

    private function quitarGrupoUsuario(int $id_usuario, int $id_grupo): string
    {
        $error_txt = '';
        $cUsuarioGrupo = $this->usuarioGrupoRepository->getUsuariosGrupos([
            'id_usuario' => $id_usuario,
            'id_grupo' => $id_grupo,
        ]);
        if ($cUsuarioGrupo !== []) {
            $oUsuarioGrupo = $cUsuarioGrupo[0];
            if ($this->usuarioGrupoRepository->Eliminar($oUsuarioGrupo) === false) {
                $error_txt .= _('hay un error, no se ha eliminado');
                $error_txt .= "\n" . $this->usuarioGrupoRepository->getErrorTxt();
            }
        }

        return $error_txt;
    }

    /**
     * @return array<string, int>
     */
    private function usuariosSacd(): array
    {
        $cRoles = $this->roleRepository->getRoles(['pau' => PauType::PAU_SACD]);
        $RoleSacd = $cRoles[0];
        $id_role = $RoleSacd->getId_role();

        $aWhere = ['id_role' => $id_role, 'id_pau' => 'x'];
        $aOperador = ['id_pau' => 'IS NOT NULL'];
        $cUsuarios = $this->usuarioRepository->getUsuarios($aWhere, $aOperador);
        $a_usuarios_sacd = [];
        foreach ($cUsuarios as $oUsuario) {
            $id_nom = $oUsuario->getCsvIdPauAsString();
            $id_usuario = $oUsuario->getId_usuario();
            if ($id_nom !== null) {
                $a_usuarios_sacd[$id_nom] = $id_usuario;
            }
        }

        return $a_usuarios_sacd;
    }

    /**
     * @return list<string>
     */
    private function arrayJefesZona(): array
    {
        $cZonas = $this->zonaRepository->getZonas();
        $a_jefes_zona = [];
        foreach ($cZonas as $oZona) {
            $idNom = $oZona->getId_nom();
            if ($idNom !== null && $idNom !== 0) {
                $a_jefes_zona[] = (string) $idNom;
            }
        }

        return $a_jefes_zona;
    }

    /**
     * @return array<string, int>
     */
    private function miembrosJefeZona(): array
    {
        $this->setIdGrupoConPermisoJefeZona();

        $cUsuarios = $this->usuarioRepository->getUsuarios(['id_usuario' => $this->id_grupo_jefe_zona]);
        $a_miembros_jefeZona = [];
        foreach ($cUsuarios as $oUsuario) {
            $csvIdPau = $oUsuario->getCsvIdPauAsString();
            if ($csvIdPau !== null) {
                $a_miembros_jefeZona[$csvIdPau] = $oUsuario->getId_usuario();
            }
        }

        return $a_miembros_jefeZona;
    }

    private function setIdGrupoConPermisoJefeZona(): void
    {
        $perm_jefe_zona = PermisoMenuBits::map()['jefeZona'];

        $cGruposPermMenu = $this->permMenuRepository->getPermMenus();
        $id_grupo_jefe_zona = 0;
        foreach ($cGruposPermMenu as $permMenu) {
            $iperm_menu = $permMenu->getMenu_perm();
            if ($iperm_menu === ($iperm_menu & $perm_jefe_zona)) {
                $id_grupo_jefe_zona = $permMenu->getId_usuario();
            }
        }
        $this->id_grupo_jefe_zona = $id_grupo_jefe_zona;
    }
}
