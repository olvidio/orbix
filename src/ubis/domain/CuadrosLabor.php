<?php

namespace src\ubis\domain;

use src\shared\config\ConfigGlobal;
use src\permisos\domain\XPermisos;

class CuadrosLabor extends XPermisos
{
    public string $classname = "CuadrosLabor";

    public function generarArrayTraducido(): array
    {
        return CuadrosLaborBits::labeledMap(ConfigGlobal::mi_sfsv());
    }

    public function __construct()
    {
        $this->permissions = CuadrosLaborBits::labeledMap(ConfigGlobal::mi_sfsv());
    }

    public function getTxtTiposLabor(): array
    {
        return array_flip($this->permissions);
    }
}
