<?php

use src\inventario\application\EquipajeEliminar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use function src\shared\domain\helpers\input_int;

$Qid_equipaje = input_int($_POST, 'id_equipaje');

/** @var EquipajeEliminar $useCase */
$useCase = DependencyResolver::get(EquipajeEliminar::class);
$error_txt = $useCase->execute($Qid_equipaje);

ContestarJson::enviar($error_txt, 'ok');
