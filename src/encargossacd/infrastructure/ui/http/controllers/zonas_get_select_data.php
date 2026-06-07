<?php

use src\encargossacd\application\EncargoZonasSelectData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var EncargoZonasSelectData $useCase */
$useCase = DependencyResolver::get(EncargoZonasSelectData::class);


$id_zona = filter_input(INPUT_POST, 'id_zona');
if ($id_zona === null) {
    $id_zona = filter_input(INPUT_GET, 'id_zona');
}

ContestarJson::enviar('', $useCase->execute(
    $id_zona !== null && $id_zona !== false ? (int)$id_zona : 0
));
