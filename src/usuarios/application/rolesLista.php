<?php

namespace src\usuarios\application;

use src\configuracion\domain\value_objects\ConfigSnapshot;
use src\menus\domain\contracts\GrupMenuRepositoryInterface;
use src\menus\domain\contracts\GrupMenuRoleRepositoryInterface;
use src\shared\config\ConfigGlobal;
use src\usuarios\domain\contracts\RoleRepositoryInterface;
use src\usuarios\domain\contracts\UsuarioRepositoryInterface;
use src\shared\web\ContestarJson;

class rolesLista
{
    public function __construct(
        private UsuarioRepositoryInterface $usuarioRepository,
        private GrupMenuRepositoryInterface $grupMenuRepository,
        private RoleRepositoryInterface $roleRepository,
        private GrupMenuRoleRepositoryInterface $grupMenuRoleRepository,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function execute(): array
    {
        $error_txt = '';

        $oMiUsuario = $this->usuarioRepository->findById(ConfigGlobal::mi_id_usuario());
        if ($oMiUsuario === null) {
            return ContestarJson::respuestaPhp(_('Usuario no encontrado'), '');
        }
        $miRole = $oMiUsuario->getId_role();
        $miSfsv = ConfigGlobal::mi_sfsv();
        $permiso = 0;
        if ($miRole === 1 && ConfigGlobal::mi_sfsv() === 1) {
            $permiso = 1;
        }

        $cGM = $this->grupMenuRepository->getGrupMenus(['_ordre' => 'grup_menu']);
        $aGrupMenus = [];
        $ambito = ($_SESSION['oConfig'] ?? null) instanceof ConfigSnapshot
            ? $_SESSION['oConfig']->getAmbito()
            : '';
        foreach ($cGM as $oGrupMenu) {
            $id_grupmenu = $oGrupMenu->getId_grupmenu();
            $grup_menu = $oGrupMenu->getGrup_menu($ambito);
            $aGrupMenus[$id_grupmenu] = $grup_menu;
        }

        $cRoles = $this->roleRepository->getRoles(['_ordre' => 'role']);

        if ($miRole === 2) {
            $permiso = 2;
        }

        $a_cabeceras = ['role', 'sf', 'sv', 'pau', 'dmz', 'grup menu'];
        $a_botones[] = [
            'txt' => _('modificar'),
            'click' => 'fnjs_modificar("#seleccionados")',
        ];

        if ($permiso > 0) {
            if ($permiso === 1) {
                $a_botones[] = [
                    'txt' => _('borrar'),
                    'click' => 'fnjs_eliminar(this.form)',
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

            $cGMR = $this->grupMenuRoleRepository->getGrupMenuRoles(['id_role' => $id_role]);
            $a_GM = [];
            foreach ($cGMR as $oGrupMenuRole) {
                $id_grupmenu = $oGrupMenuRole->getId_grupmenu();
                if ($id_grupmenu === null || !isset($aGrupMenus[$id_grupmenu])) {
                    continue;
                }
                $grup_menu = $aGrupMenus[$id_grupmenu];
                $a_GM[$id_grupmenu] = $grup_menu;
            }
            sort($a_GM);
            $str_GM = '';
            foreach ($a_GM as $grup_menu) {
                $str_GM .= $str_GM !== '' ? ',' : '';
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

        $data = [
            'a_cabeceras' => $a_cabeceras,
            'a_botones' => $a_botones,
            'a_valores' => $a_valores,
            'permiso' => $permiso,
        ];

        return ContestarJson::respuestaPhp($error_txt, $data);
    }
}
