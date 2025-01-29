<?php

namespace usuarios\model;

use permisos\model\XPermisos;

class PermCtr extends XPermisos
{
     public function __construct($iaccion = 0)
    {
        $this->iaccion = $iaccion;
       $this->permissions= [
            "nada" => 0,
            "ver" => 1,
            "cl" => 3,
            "sacd" => 7,
            "d" => 15,
        ];
    }
}