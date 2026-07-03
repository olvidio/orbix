<?php

use src\shared\infrastructure\DependencyResolver;
use src\ubis\application\UbisEliminar;
use src\shared\web\ContestarJson;
use src\shared\domain\helpers\FuncTablasSupport;

/** @var UbisEliminar $useCase */
$useCase = DependencyResolver::get(UbisEliminar::class);
$errorTxt = $useCase->execute(
    FuncTablasSupport::inputString($_POST, 'obj_pau'),
    FuncTablasSupport::inputInt($_POST, 'id_ubi')
);
ContestarJson::enviar($errorTxt, 'ok');
