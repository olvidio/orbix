<?php
use src\shared\infrastructure\DependencyResolver;

use src\misas\application\HorarioTareaData;
use src\shared\web\ContestarJson;

/** @var HorarioTareaData $useCase */
$useCase = DependencyResolver::get(HorarioTareaData::class);
$result = $useCase->getData([
    'id_item_h' => filter_post('id_item_h'),
]);

ContestarJson::enviar('', $result);
