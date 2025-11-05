<?php

namespace procesos\model;

use permisos\model\PermisosActividades;
use permisos\model\XPermisos;

class PermAfectados extends XPermisos
{
    public static $classname = "CuadrosPermActiv";

    public function __construct($iaccion = 0)
    {
        $this->iaccion = $iaccion;
       $this->permissions= PermisosActividades::AFECTA;
    }
}