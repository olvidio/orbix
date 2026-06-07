<?php

use src\shared\infrastructure\DependencyResolver;
use src\ubis\application\CalendarioPeriodoGuardar;
use src\shared\web\ContestarJson;

use function src\shared\domain\helpers\input_int;
use function src\shared\domain\helpers\input_string;

ContestarJson::enviar(
    DependencyResolver::get(CalendarioPeriodoGuardar::class)->execute(
        input_int($_POST, 'id_item'),
        input_int($_POST, 'id_ubi'),
        input_string($_POST, 'f_ini'),
        input_string($_POST, 'f_fin'),
        input_int($_POST, 'sfsv')
    ),
    'ok'
);
