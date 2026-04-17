<?php

namespace src\procesos\domain;

use permisos\model\PermisosActividades;
use permisos\model\XPermisos;

class PermAfectados extends XPermisos
{
    public static string $classname = "CuadrosPermActiv";

    public function __construct($iaccion = 0)
    {
        $this->iaccion = $iaccion;
       $this->permissions= PermisosActividades::AFECTA;
    }
}