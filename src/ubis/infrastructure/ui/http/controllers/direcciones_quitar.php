<?php

use src\shared\infrastructure\DependencyResolver;
use src\ubis\application\DireccionesQuitar;
use src\shared\web\ContestarJson;

ContestarJson::enviar('', DependencyResolver::get(DireccionesQuitar::class)->execute(
    \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'id_ubi'),
    \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'idx'),
    \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'obj_dir'),
    \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'id_direccion')
));
