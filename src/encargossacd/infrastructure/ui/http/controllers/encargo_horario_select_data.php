<?php

use src\encargossacd\application\EncargoHorarioSelectData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var EncargoHorarioSelectData $useCase */
$useCase = DependencyResolver::get(EncargoHorarioSelectData::class);


$id_enc = (int)(filter_post('id_enc') ?? filter_get('id_enc') ?? 0);

ContestarJson::enviar('', $useCase->execute($id_enc));
