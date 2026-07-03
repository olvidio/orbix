<?php
use src\shared\infrastructure\DependencyResolver;
use src\shared\domain\helpers\FilterPostGet;

use src\misas\application\AnadirCtrTarea;
use src\shared\web\ContestarJson;

/** @var AnadirCtrTarea $useCase */
$useCase = DependencyResolver::get(AnadirCtrTarea::class);
$result = $useCase->execute([
    'que' => \src\shared\domain\helpers\FilterPostGet::post('que'),
    'id_ubi' => \src\shared\domain\helpers\FilterPostGet::post('id_ubi'),
    'id_tarea' => \src\shared\domain\helpers\FilterPostGet::post('id_tarea'),
    'id_item' => \src\shared\domain\helpers\FilterPostGet::post('id_item'),
]);

ContestarJson::enviar($result['error']);
