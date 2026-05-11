<?php

namespace src\procesos\domain;


use src\permisos\domain\XPermisos;

class PermAccion extends XPermisos
{
    public function __construct(int $iaccion = 0)
    {
        $this->iaccion = $iaccion;
        $this->permissions = PermAccionBits::map();
    }
}