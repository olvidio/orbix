<?php

use src\encargossacd\application\EncargoHorarioSelectData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var EncargoHorarioSelectData $useCase */
$useCase = DependencyResolver::get(EncargoHorarioSelectData::class);


$id_enc = (int)(filter_input(INPUT_POST, 'id_enc') ?? filter_input(INPUT_GET, 'id_enc') ?? 0);

ContestarJson::enviar('', $useCase->execute($id_enc));
