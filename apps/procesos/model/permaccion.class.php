<?php

namespace procesos\model;

use permisos\model as permisos;

class PermAccion extends permisos\Xpermisos
{
    public $permissions = array(
        "nada" => 0,
        "ocupado" => 1,
        "ver" => 3,
        "modificar" => 7,
        "crear" => 15,
        "borrar" => 31
    );
}