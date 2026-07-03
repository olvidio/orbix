<?php
use src\shared\infrastructure\DependencyResolver;
use src\shared\domain\helpers\FilterPostGet;

use src\misas\application\HorarioTareaData;
use src\shared\web\ContestarJson;

/** @var HorarioTareaData $useCase */
$useCase = DependencyResolver::get(HorarioTareaData::class);
$result = $useCase->getData([
    'id_item_h' => FilterPostGet::post('id_item_h'),
]);

ContestarJson::enviar('', $result);
