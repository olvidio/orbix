<?php

namespace src\permisos\domain;

class PermDl extends XPermisos
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
    private function omplir(): void
    {
        $this->permissions = MenuDlPermissionBits::map();
    }
}