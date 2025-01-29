<?php

namespace procesos\model;


use permisos\model\XPermisos;

class PermAccion extends XPermisos
{
    public function __construct($iaccion = 0)
    {
        $this->iaccion = $iaccion;
       $this->permissions= [
            "nada" => 0,
            "ocupado" => 1,
            "ver" => 3,
            "modificar" => 7,
            "crear" => 15,
            "borrar" => 31
        ];
    }
}