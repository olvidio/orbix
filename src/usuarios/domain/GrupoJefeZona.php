<?php

namespace src\usuarios\domain;

use src\menus\domain\PermisoMenu;
use src\usuarios\domain\contracts\PermMenuRepositoryInterface;
use src\usuarios\domain\contracts\RoleRepositoryInterface;
use src\usuarios\domain\contracts\UsuarioGrupoRepositoryInterface;
use src\usuarios\domain\contracts\UsuarioRepositoryInterface;
use src\usuarios\domain\entity\Role;
use src\usuarios\domain\entity\UsuarioGrupo;
use src\zonassacd\domain\contracts\ZonaRepositoryInterface;

class GrupoJefeZona
{

    private $id_grupo_jefe_zona;

    public function reAsignar()
    {
        $a_miembros_jefeZona = $this->miembrosJefeZona();
        $a_jefes_zona = $this->arrayJefesZona();
        $a_usuarios_sacd = $this->usuariosSacd();

        // añadir el grupo a los jefes de zona
        foreach ($a_jefes_zona as $id_nom) {
            if (!array_key_exists($id_nom, $a_miembros_jefeZona)) {
                if (!empty($a_usuarios_sacd[$id_nom])) {
                    $id_usuario = $a_usuarios_sacd[$id_nom];
                    $this->addGrupoUsuario($id_usuario, $this->id_grupo_jefe_zona);
                }
            }
        }
        // quitar el grupo  los que no son jefes de zona
        foreach ($a_usuarios_sacd as $id_nom => $id_usuario) {
            if (!in_array($id_nom, $a_jefes_zona)) {
                $this->quitarGrupoUsuario($id_usuario, $this->id_grupo_jefe_zona);
            }
        }
    }

    private function addGrupoUsuario($id_usuario, $id_grupo)
    {
        $error_txt = '';
        // añado el grupo de permisos al usuario.
        $UsuarioGrupoRepository = $GLOBALS['container']->get(UsuarioGrupoRepositoryInterface::class);
        $oUsuarioGrupo = new UsuarioGrupo();
        $oUsuarioGrupo->setId_usuario($id_usuario);
        $oUsuarioGrupo->setId_grupo($id_grupo);
        if ($UsuarioGrupoRepository->Guardar($oUsuarioGrupo) === false) {
            $error_txt .= _("hay un error, no se ha guardado");
            $error_txt .= "\n" . $UsuarioGrupoRepository->getErrorTxt();
        }
        return $error_txt;
    }

    private function quitarGrupoUsuario($id_usuario, $id_grupo)
    {
        $error_txt = '';
        // elimino el grupo de permisos al usuario.
        $UsuarioGrupoRepository = $GLOBALS['container']->get(UsuarioGrupoRepositoryInterface::class);
        $cUsuarioGrupo = $UsuarioGrupoRepository->getUsuariosGrupos(['id_usuario' => $id_usuario, 'id_grupo' => $id_grupo]);
        if (!empty($cUsuarioGrupo)) {
            $oUsuarioGrupo = $cUsuarioGrupo[0];
            if (($oUsuarioGrupo !== null) && $UsuarioGrupoRepository->Eliminar($oUsuarioGrupo) === false) {
                $error_txt .= _("hay un error, no se ha eliminado");
                $error_txt .= "\n" . $UsuarioGrupoRepository->getErrorTxt();
            }
        }
        return $error_txt;
    }

    private function usuariosSacd()
    {
        $RoleRepository = $GLOBALS['container']->get(RoleRepositoryInterface::class);
        $cRoles = $RoleRepository->getRoles(['pau' => Role::PAU_SACD]);
        $RoleSacd = $cRoles[0];
        $id_role = $RoleSacd->getId_role();

        $UsuarioRepository = $GLOBALS['container']->get(UsuarioRepositoryInterface::class);
        $aWhere['id_role'] = $id_role;
        $aWhere['id_pau'] = 'x';
        $aOperador['id_pau'] = 'IS NOT NULL';
        $cUsuarios = $UsuarioRepository->getUsuarios($aWhere, $aOperador);
        $a_usuarios_sacd = [];
        foreach ($cUsuarios as $oUsuario) {
            $id_nom = $oUsuario->getId_pauAsString();
            $id_usuario = $oUsuario->getId_usuario();
            $a_usuarios_sacd[$id_nom] = $id_usuario;
        }
        return $a_usuarios_sacd;
    }

    private function arrayJefesZona()
    {
        $ZonaRepository = $GLOBALS['container']->get(ZonaRepositoryInterface::class);
        $cZonas = $ZonaRepository->getZonas();
        $a_jefes_zona = [];
        foreach ($cZonas as $oZona) {
            if (!empty($oZona->getId_nom())) {
                $a_jefes_zona[] = $oZona->getId_nom();
            }
        }
        return $a_jefes_zona;
    }

    private function miembrosJefeZona()
    {
        $this->setIdGrupoConPermisoJefeZona();

        $UsuarioRepository = $GLOBALS['container']->get(UsuarioRepositoryInterface::class);
        $cUsuarios = $UsuarioRepository->getUsuarios(['id_usuario' => $this->id_grupo_jefe_zona]);
        $a_miembros_jefeZona = [];
        foreach ($cUsuarios as $oUsuario) {
            $a_miembros_jefeZona[$oUsuario->getId_pau()] = $oUsuario->getId_usuario();
        }
        return $a_miembros_jefeZona;
    }

    private function setIdGrupoConPermisoJefeZona()
    {
        $oPermisoMenu = new PermisoMenu();
        $permissions = $oPermisoMenu->omplir();
        $perm_jefe_zona = $permissions['jefeZona'];

        $PermMenuRepository = $GLOBALS['container']->get(PermMenuRepositoryInterface::class);
        $cGruposPermMenu = $PermMenuRepository->getPermMenus();
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