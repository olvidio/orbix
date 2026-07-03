<?php

use src\encargossacd\application\EncargoHorarioSelectData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use src\shared\domain\helpers\FilterPostGet;

/** @var EncargoHorarioSelectData $useCase */
$useCase = DependencyResolver::get(EncargoHorarioSelectData::class);


$id_enc = (int)(\src\shared\domain\helpers\FilterPostGet::post('id_enc') ?? \src\shared\domain\helpers\FilterPostGet::get('id_enc') ?? 0);

ContestarJson::enviar('', $useCase->execute($id_enc));
