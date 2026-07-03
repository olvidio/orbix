<?php

use src\shared\infrastructure\DependencyResolver;
use src\ubis\application\CalendarioPeriodoGuardar;
use src\shared\web\ContestarJson;
use src\shared\domain\helpers\FuncTablasSupport;

ContestarJson::enviar(
    DependencyResolver::get(CalendarioPeriodoGuardar::class)->execute(
        FuncTablasSupport::inputInt($_POST, 'id_item'),
        FuncTablasSupport::inputInt($_POST, 'id_ubi'),
        FuncTablasSupport::inputString($_POST, 'f_ini'),
        FuncTablasSupport::inputString($_POST, 'f_fin'),
        FuncTablasSupport::inputInt($_POST, 'sfsv')
    ),
    'ok'
);
