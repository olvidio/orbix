<?php

use src\shared\infrastructure\DependencyResolver;
use src\ubis\application\DireccionesEditarData;
use src\shared\web\ContestarJson;
use src\shared\domain\helpers\FuncTablasSupport;

ContestarJson::enviar('', DependencyResolver::get(DireccionesEditarData::class)->execute(
    FuncTablasSupport::inputInt($_POST, 'id_ubi'),
    FuncTablasSupport::inputString($_POST, 'mod'),
    FuncTablasSupport::inputString($_POST, 'obj_dir'),
    FuncTablasSupport::inputString($_POST, 'id_direccion'),
    FuncTablasSupport::inputInt($_POST, 'idx'),
    FuncTablasSupport::inputString($_POST, 'inc')
));
