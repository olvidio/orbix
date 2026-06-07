<?php

use src\shared\infrastructure\DependencyResolver;
use src\ubis\application\DireccionesAsignar;
use src\shared\web\ContestarJson;

use function src\shared\domain\helpers\input_int;
use function src\shared\domain\helpers\input_string;

ContestarJson::enviar('', DependencyResolver::get(DireccionesAsignar::class)->execute(
    input_int($_POST, 'id_ubi'),
    input_string($_POST, 'obj_dir'),
    input_int($_POST, 'id_direccion')
));
