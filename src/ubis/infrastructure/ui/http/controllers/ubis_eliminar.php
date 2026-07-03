<?php

use src\shared\infrastructure\DependencyResolver;
use src\ubis\application\UbisEliminar;
use src\shared\web\ContestarJson;

/** @var UbisEliminar $useCase */
$useCase = DependencyResolver::get(UbisEliminar::class);
$errorTxt = $useCase->execute(
    \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'obj_pau'),
    \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'id_ubi')
);
ContestarJson::enviar($errorTxt, 'ok');
