<?php

use src\shared\infrastructure\DependencyResolver;
use src\ubis\application\DireccionesAsignar;
use src\shared\web\ContestarJson;
use src\shared\domain\helpers\FuncTablasSupport;

ContestarJson::enviar('', DependencyResolver::get(DireccionesAsignar::class)->execute(
    FuncTablasSupport::inputInt($_POST, 'id_ubi'),
    FuncTablasSupport::inputString($_POST, 'obj_dir'),
    FuncTablasSupport::inputInt($_POST, 'id_direccion')
));
