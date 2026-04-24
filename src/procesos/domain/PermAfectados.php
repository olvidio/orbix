<?php

namespace src\procesos\domain;

use src\permisos\domain\PermisosActividades;
use src\permisos\domain\XPermisos;

class PermAfectados extends XPermisos
{
    public static string $classname = "CuadrosPermActiv";

    public function __construct($iaccion = 0)
    {
        $this->iaccion = $iaccion;
       $this->permissions= PermisosActividades::AFECTA;
    }
}