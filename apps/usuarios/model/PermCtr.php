<?php

namespace usuarios\model;

use permisos\model as permisos;

class PermCtr extends permisos\XPermisos
{
    public $permissions = array(
        "nada" => 0,
        "ver" => 1,
        "cl" => 3,
        "sacd" => 7,
        "d" => 15,
    );
}