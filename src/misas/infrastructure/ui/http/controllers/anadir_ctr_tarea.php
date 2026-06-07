<?php
use src\shared\infrastructure\DependencyResolver;

use src\misas\application\AnadirCtrTarea;
use src\shared\web\ContestarJson;

/** @var AnadirCtrTarea $useCase */
$useCase = DependencyResolver::get(AnadirCtrTarea::class);
$result = $useCase->execute([
    'que' => filter_input(INPUT_POST, 'que'),
    'id_ubi' => filter_input(INPUT_POST, 'id_ubi'),
    'id_tarea' => filter_input(INPUT_POST, 'id_tarea'),
    'id_item' => filter_input(INPUT_POST, 'id_item'),
]);

ContestarJson::enviar($result['error']);
