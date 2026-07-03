<?php

use src\shared\infrastructure\DependencyResolver;
use src\ubis\application\CalendarioPeriodoEliminar;
use src\shared\web\ContestarJson;
use src\shared\domain\helpers\FuncTablasSupport;

ContestarJson::enviar(
    DependencyResolver::get(CalendarioPeriodoEliminar::class)->execute(FuncTablasSupport::inputInt($_POST, 'id_item')),
    'ok'
);
