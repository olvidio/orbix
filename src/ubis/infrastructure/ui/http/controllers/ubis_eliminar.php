<?php

use src\shared\infrastructure\DependencyResolver;
use src\ubis\application\UbisEliminar;
use src\shared\web\ContestarJson;

use function src\shared\domain\helpers\input_int;
use function src\shared\domain\helpers\input_string;

/** @var UbisEliminar $useCase */
$useCase = DependencyResolver::get(UbisEliminar::class);
$errorTxt = $useCase->execute(
    input_string($_POST, 'obj_pau'),
    input_int($_POST, 'id_ubi')
);
ContestarJson::enviar($errorTxt, 'ok');
