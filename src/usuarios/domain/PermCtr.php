<?php

namespace src\usuarios\domain;

use src\permisos\domain\XPermisos;

class PermCtr extends XPermisos
{
    public function __construct(int $iaccion = 0)
    {
        $this->iaccion = $iaccion;
        $this->permissions = PermCtrBits::map();
    }
}