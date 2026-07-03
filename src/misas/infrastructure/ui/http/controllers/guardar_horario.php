<?php
use src\shared\infrastructure\DependencyResolver;
use src\shared\domain\helpers\FilterPostGet;

use src\misas\application\GuardarHorarioTarea;
use src\shared\web\ContestarJson;

/** @var GuardarHorarioTarea $useCase */
$useCase = DependencyResolver::get(GuardarHorarioTarea::class);
$result = $useCase->execute([
    'id_item_h' => FilterPostGet::post('id_item_h'),
    't_start' => FilterPostGet::post('t_start'),
    't_end' => FilterPostGet::post('t_end'),
]);

ContestarJson::enviar($result['error']);
