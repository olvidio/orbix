<?php

use src\encargossacd\application\EncargoZonasSelectData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var EncargoZonasSelectData $useCase */
$useCase = DependencyResolver::get(EncargoZonasSelectData::class);


$id_zona = filter_post('id_zona');
if ($id_zona === null) {
    $id_zona = filter_get('id_zona');
}

ContestarJson::enviar('', $useCase->execute(
    $id_zona !== null && $id_zona !== false ? (int)$id_zona : 0
));
