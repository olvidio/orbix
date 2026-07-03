<?php

use src\shared\infrastructure\DependencyResolver;
use src\ubis\application\DireccionesTablaData;
use src\shared\web\ContestarJson;
use src\shared\domain\helpers\FuncTablasSupport;

ContestarJson::enviar('', DependencyResolver::get(DireccionesTablaData::class)->execute(
    FuncTablasSupport::inputInt($_POST, 'id_ubi'),
    FuncTablasSupport::inputString($_POST, 'obj_dir'),
    FuncTablasSupport::inputString($_POST, 'c_p'),
    FuncTablasSupport::inputString($_POST, 'ciudad'),
    FuncTablasSupport::inputString($_POST, 'pais')
));
