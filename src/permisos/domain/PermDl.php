<?php

namespace src\permisos\domain;

class PermDl extends XPermisos
{
    public string $classname = 'PermDl';

    public function __construct()
    {
        $iPermMenus = $_SESSION['iPermMenus'] ?? 0;
        $this->iaccion = is_numeric($iPermMenus) ? (int) $iPermMenus : 0;
        $this->omplir();
    }

    private function omplir(): void
    {
        $this->permissions = MenuDlPermissionBits::map();
    }
}
