<?php
use src\shared\infrastructure\DependencyResolver;

use src\misas\application\GuardarHorarioTarea;
use src\shared\web\ContestarJson;

/** @var GuardarHorarioTarea $useCase */
$useCase = DependencyResolver::get(GuardarHorarioTarea::class);
$result = $useCase->execute([
    'id_item_h' => filter_post('id_item_h'),
    't_start' => filter_post('t_start'),
    't_end' => filter_post('t_end'),
]);

ContestarJson::enviar($result['error']);
