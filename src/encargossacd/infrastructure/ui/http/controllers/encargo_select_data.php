<?php

use src\encargossacd\application\EncargoSelectData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use src\shared\domain\helpers\FilterPostGet;

/** @var EncargoSelectData $useCase */
$useCase = DependencyResolver::get(EncargoSelectData::class);


$desc_enc = (string)(\src\shared\domain\helpers\FilterPostGet::post('desc_enc') ?? \src\shared\domain\helpers\FilterPostGet::get('desc_enc') ?? '');
$id_tipo_enc = (int)(\src\shared\domain\helpers\FilterPostGet::post('id_tipo_enc') ?? \src\shared\domain\helpers\FilterPostGet::get('id_tipo_enc') ?? 0);

ContestarJson::enviar('', $useCase->execute($desc_enc, $id_tipo_enc));
