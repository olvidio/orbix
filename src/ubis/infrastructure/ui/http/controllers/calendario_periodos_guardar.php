<?php

use src\shared\infrastructure\DependencyResolver;
use src\ubis\application\CalendarioPeriodoGuardar;
use src\shared\web\ContestarJson;

ContestarJson::enviar(
    DependencyResolver::get(CalendarioPeriodoGuardar::class)->execute(
        \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'id_item'),
        \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'id_ubi'),
        \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'f_ini'),
        \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'f_fin'),
        \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'sfsv')
    ),
    'ok'
);
