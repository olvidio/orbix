<?php
use src\shared\infrastructure\DependencyResolver;
use src\shared\domain\helpers\FilterPostGet;

use src\misas\application\AnadirCtrTarea;
use src\shared\web\ContestarJson;

/** @var AnadirCtrTarea $useCase */
$useCase = DependencyResolver::get(AnadirCtrTarea::class);
$result = $useCase->execute([
    'que' => FilterPostGet::post('que'),
    'id_ubi' => FilterPostGet::post('id_ubi'),
    'id_tarea' => FilterPostGet::post('id_tarea'),
    'id_item' => FilterPostGet::post('id_item'),
]);

ContestarJson::enviar($result['error']);
