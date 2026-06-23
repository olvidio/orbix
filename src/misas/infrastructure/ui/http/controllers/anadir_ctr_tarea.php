<?php
use src\shared\infrastructure\DependencyResolver;

use src\misas\application\AnadirCtrTarea;
use src\shared\web\ContestarJson;

/** @var AnadirCtrTarea $useCase */
$useCase = DependencyResolver::get(AnadirCtrTarea::class);
$result = $useCase->execute([
    'que' => filter_post('que'),
    'id_ubi' => filter_post('id_ubi'),
    'id_tarea' => filter_post('id_tarea'),
    'id_item' => filter_post('id_item'),
]);

ContestarJson::enviar($result['error']);
