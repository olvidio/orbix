<?php

use src\shared\infrastructure\DependencyResolver;
use src\ubis\application\CalendarioPeriodoEliminar;
use src\shared\web\ContestarJson;

ContestarJson::enviar(
    DependencyResolver::get(CalendarioPeriodoEliminar::class)->execute(\src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'id_item')),
    'ok'
);
