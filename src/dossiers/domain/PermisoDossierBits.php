<?php

namespace src\dossiers\domain;

use src\shared\config\ConfigGlobal;

/**
 * Mapa de permisos para dossiers (subconjunto condicionado por sv/sf). Ver {@see PermisoDossier}.
 */
final class PermisoDossierBits
{
    /**
     * @return array<string, int>
     */
    public static function labeledMap(): array
    {
        $permissions = [];
        if (ConfigGlobal::mi_sfsv() == 1) {
            $permissions['adl'] = 1;
        }
        if (ConfigGlobal::mi_sfsv() == 2) {
            $permissions['pr'] = 1;
        }
        $permissions['agd'] = 1 << 1;
        $permissions['aop'] = 1 << 2;
        $permissions['des'] = 1 << 3;
        $permissions['est'] = 1 << 4;
        $permissions['scdl'] = 1 << 5;
        $permissions['scr'] = 1 << 5;
        $permissions['sg'] = 1 << 6;
        $permissions['sm'] = 1 << 7;
        $permissions['soi'] = 1 << 8;
        $permissions['sr'] = 1 << 9;
        $permissions['vcsd'] = 1 << 10;
        $permissions['vcsr'] = 1 << 10;
        $permissions['dtor'] = 1 << 11;
        $permissions['ocs'] = 1 << 12;
        $permissions['sddl'] = 1 << 13;
        $permissions['nax'] = 1 << 14;

        return $permissions;
    }
}
