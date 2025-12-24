<?php

namespace src\usuarios\application;

use core\ConfigGlobal;
use src\menus\domain\contracts\GrupMenuRepositoryInterface;
use src\menus\domain\contracts\GrupMenuRoleRepositoryInterface;
use src\usuarios\domain\contracts\RoleRepositoryInterface;
use src\usuarios\domain\contracts\UsuarioRepositoryInterface;
use web\ContestarJson;

class rolesLista
{

    public static function rolesLista(): array
    {
        $error_txt = '';
        $jsondata = [];

        $UsuarioRepository = $GLOBALS['container']->get(UsuarioRepositoryInterface::class);
        $oMiUsuario = $UsuarioRepository->findById(ConfigGlobal::mi_id_usuario());
        $miRole = $oMiUsuario->getId_role();
        $miSfsv = ConfigGlobal::mi_sfsv();
        // Sólo puede manipular los roles el superadmin (id_role=1).
        // y desde el sv
        $permiso = 0;
        if ($miRole === 1 && ConfigGlobal::mi_sfsv() === 1) {
            $permiso = 1;
        }

        // todos los posibles GrupMenu
        $GrupMuenuRepository = $GLOBALS['container']->get(GrupMenuRepositoryInterface::class);
        $cGM = $GrupMuenuRepository->getGrupMenus(array('_ordre' => 'grup_menu'));
        $aGrupMenus = [];
        foreach ($cGM as $oGrupMenu) {
            $id_grupmenu = $oGrupMenu->getId_grupmenu();
            $grup_menu = $oGrupMenu->getGrup_menu($_SESSION['oConfig']->getAmbito());
            $aGrupMenus[$id_grupmenu] = $grup_menu;
        }

        $RoleRepository = $GLOBALS['container']->get(RoleRepositoryInterface::class);
        $cRoles = $RoleRepository->getRoles(['_ordre' => 'role']);

        // Para admin, puede modificar los grupmenus que tiene cada rol, pero no
        // crear ni borrar
        if ($miRole === 2) {
            $permiso = 2;
        }

        $a_cabeceras = ['role', 'sf', 'sv', 'pau', 'dmz', 'grup menu'];
        $a_botones[] = ['txt' => _("modificar"),
            'click' => "fnjs_modificar(\"#seleccionados\")",
        ];

        if ($permiso > 0) {
            if ($permiso === 1) {
                $a_botones[] = ['txt' => _("borrar"),
                    'click' => "fnjs_eliminar(this.form)",
                ];
            }
        } else {
            $a_botones = [];
        }

        $a_valores = [];
        $i = 0;
        foreach ($cRoles as $oRole) {
            $id_role = $oRole->getId_role();
            $role = $oRole->getRoleAsString();
            $isSf = $oRole->isSf();
            $isSv = $oRole->isSv();
            $pau = $oRole->getPauAsString();
            $dmz = $oRole->isDmz();

            if (($permiso !== 1) && (($miSfsv === 2 && !$isSf) || ($miSfsv === 1 && !$isSv))) {
                continue;
            }
            $i++;

            $GrupMuenuRoleRepository = $GLOBALS['container']->get(GrupMenuRoleRepositoryInterface::class);
            $cGMR = $GrupMuenuRoleRepository->getGrupMenuRoles(array('id_role' => $id_role));
            // intentar ordenar por el nombre del grupmenu
            $a_GM = [];
            foreach ($cGMR as $oGrupMenuRole) {
                $id_grupmenu = $oGrupMenuRole->getId_grupmenu();
                $grup_menu = $aGrupMenus[$id_grupmenu];
                $a_GM[$id_grupmenu] = $grup_menu;
            }
            sort($a_GM);
            // pasar a texto:
            $str_GM = '';
            foreach ($a_GM as $grup_menu) {
                $str_GM .= !empty($str_GM) ? ',' : '';
                $str_GM .= $grup_menu;
            }

            $a_valores[$i][1] = $role;
            $a_valores[$i][2] = $isSf;
            $a_valores[$i][3] = $isSv;
            $a_valores[$i][4] = $pau;
            $a_valores[$i][5] = $dmz;
            $a_valores[$i][6] = $str_GM;
            if ($permiso > 0) {
                $a_valores[$i]['sel'] = "$id_role#";
            }
        }

        $data = ['a_cabeceras' => $a_cabeceras,
            'a_botones' => $a_botones,
            'a_valores' => $a_valores,
            'permiso' => $permiso,
        ];

        return ContestarJson::respuestaPhp($error_txt, $data);
    }
}