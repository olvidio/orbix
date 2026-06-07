<?php
use src\shared\infrastructure\DependencyResolver;

use src\misas\application\GuardarHorarioTarea;
use src\shared\web\ContestarJson;

/** @var GuardarHorarioTarea $useCase */
$useCase = DependencyResolver::get(GuardarHorarioTarea::class);
$result = $useCase->execute([
    'id_item_h' => filter_input(INPUT_POST, 'id_item_h'),
    't_start' => filter_input(INPUT_POST, 't_start'),
    't_end' => filter_input(INPUT_POST, 't_end'),
]);

ContestarJson::enviar($result['error']);
