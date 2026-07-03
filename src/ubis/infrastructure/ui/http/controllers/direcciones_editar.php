<?php

use src\shared\infrastructure\DependencyResolver;
use src\ubis\application\DireccionesEditarData;
use src\shared\web\ContestarJson;

ContestarJson::enviar('', DependencyResolver::get(DireccionesEditarData::class)->execute(
    \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'id_ubi'),
    \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'mod'),
    \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'obj_dir'),
    \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'id_direccion'),
    \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'idx'),
    \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'inc')
));
