<?php

use src\shared\infrastructure\DependencyResolver;
use src\ubis\application\DireccionesTablaData;
use src\shared\web\ContestarJson;

ContestarJson::enviar('', DependencyResolver::get(DireccionesTablaData::class)->execute(
    \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'id_ubi'),
    \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'obj_dir'),
    \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'c_p'),
    \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'ciudad'),
    \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'pais')
));
