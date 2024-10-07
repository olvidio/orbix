<?php

namespace procesos\model;

use permisos\model as permisos;

class PermAccion extends permisos\XPermisos
{
    public function __construct($iaccion = 0)
    {
        $this->iaccion = $iaccion;
        self::$permissions = [
            "nada" => 0,
            "ocupado" => 1,
            "ver" => 3,
            "modificar" => 7,
            "crear" => 15,
            "borrar" => 31
        ];
    }
}