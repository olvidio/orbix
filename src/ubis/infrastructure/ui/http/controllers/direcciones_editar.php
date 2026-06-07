<?php

use src\shared\infrastructure\DependencyResolver;
use src\ubis\application\DireccionesEditarData;
use src\shared\web\ContestarJson;

use function src\shared\domain\helpers\input_int;
use function src\shared\domain\helpers\input_string;

ContestarJson::enviar('', DependencyResolver::get(DireccionesEditarData::class)->execute(
    input_int($_POST, 'id_ubi'),
    input_string($_POST, 'mod'),
    input_string($_POST, 'obj_dir'),
    input_string($_POST, 'id_direccion'),
    input_int($_POST, 'idx'),
    input_string($_POST, 'inc')
));
