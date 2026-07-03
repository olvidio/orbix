<?php

use src\encargossacd\application\EncargoSelectData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use src\shared\domain\helpers\FilterPostGet;

/** @var EncargoSelectData $useCase */
$useCase = DependencyResolver::get(EncargoSelectData::class);


$desc_enc = (string)(FilterPostGet::post('desc_enc') ?? FilterPostGet::get('desc_enc') ?? '');
$id_tipo_enc = (int)(FilterPostGet::post('id_tipo_enc') ?? FilterPostGet::get('id_tipo_enc') ?? 0);

ContestarJson::enviar('', $useCase->execute($desc_enc, $id_tipo_enc));
