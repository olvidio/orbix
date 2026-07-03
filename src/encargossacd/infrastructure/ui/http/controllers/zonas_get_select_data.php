<?php

use src\encargossacd\application\EncargoZonasSelectData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use src\shared\domain\helpers\FilterPostGet;

/** @var EncargoZonasSelectData $useCase */
$useCase = DependencyResolver::get(EncargoZonasSelectData::class);


$id_zona = \src\shared\domain\helpers\FilterPostGet::post('id_zona');
if ($id_zona === null) {
    $id_zona = \src\shared\domain\helpers\FilterPostGet::get('id_zona');
}

ContestarJson::enviar('', $useCase->execute(
    $id_zona !== null && $id_zona !== false ? (int)$id_zona : 0
));
