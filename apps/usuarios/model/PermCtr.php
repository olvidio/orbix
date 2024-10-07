<?php

namespace usuarios\model;

use permisos\model as permisos;

class PermCtr extends permisos\XPermisos
{
     public function __construct($iaccion = 0)
    {
        $this->iaccion = $iaccion;
        self::$permissions = [
            "nada" => 0,
            "ver" => 1,
            "cl" => 3,
            "sacd" => 7,
            "d" => 15,
        ];
    }
}