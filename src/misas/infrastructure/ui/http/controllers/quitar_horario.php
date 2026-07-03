<?php
use src\shared\infrastructure\DependencyResolver;
use src\shared\domain\helpers\FilterPostGet;

use src\misas\application\QuitarHorarioPlantilla;
use src\shared\web\ContestarJson;

/** @var QuitarHorarioPlantilla $useCase */
$useCase = DependencyResolver::get(QuitarHorarioPlantilla::class);
$result = $useCase->execute([
    'id_item' => \src\shared\domain\helpers\FilterPostGet::post('id_item'),
]);

ContestarJson::enviar($result['error']);
