<?php

namespace permisos\model;

class PermDl extends Xpermisos
{
    var $classname = "PermDl";

    public function __construct()
    {
        $this->iaccion = $_SESSION['iPermMenus'];
        $this->omplir();
    }

    /**
     * debe ser el mismo valor que en los menus,
     * excepto para los inclusivos (más de uno).
     */
    private function omplir()
    {
        $permission_users = [];
        $permission_users['adl'] = 1;
        $permission_users['pr'] = 1;
        $permission_users['agd'] = 1 << 1; // 2
        $permission_users['aop'] = 1 << 2; //4,
        $permission_users['des'] = 1 << 3; //8,
        $permission_users['est'] = 1 << 4; //16,
        $permission_users['scdl'] = 1 << 5; //32,
        $permission_users['scr'] = 1 << 5; //32,
        $permission_users['sg'] = 1 << 6; //64,
        $permission_users['sm'] = 1 << 7; //128,
        $permission_users['soi'] = 1 << 8; //256,
        $permission_users['sr'] = 1 << 9; //512,
        $permission_users['vcsd'] = 1 << 10; //1024,
        $permission_users['vcsr'] = 1 << 10; //1024,
        $permission_users['dtor'] = 1 << 11; //2048,
        $permission_users['ocs'] = 1 << 12; //4096,
        $permission_users['sddl'] = 1 << 13; //8192,
        $permission_users['nax'] = 1 << 14; //16384,
        //$permission_users['actividades'] =  31735; //31735, // todos menos des(8) y vcsd(1024).
        $permission_users['calendario'] = 1 << 15; //32768,
        $permission_users['ctr'] = 1 << 16; //65536,
        $permission_users['sacd'] = 1 << 18;
        $permission_users['persona'] = 1 << 19;
        $permission_users['casa'] = 1 << 20;

        $permission_users['admin_sf'] = 16776183; // En menus tiene 1<<21, cojo uno mayor
        // (1<<24 uno menos que sv) y le quito 8(des), 1024(vcsd) y 1
        $permission_users['admin_sv'] = -1; //; // todo unos, depende de la máquina, 32 o 64 bits.

        $this->permissions = $permission_users;
    }
}