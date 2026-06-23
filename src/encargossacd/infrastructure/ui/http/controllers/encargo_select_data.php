<?php

use src\encargossacd\application\EncargoSelectData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var EncargoSelectData $useCase */
$useCase = DependencyResolver::get(EncargoSelectData::class);


$desc_enc = (string)(filter_post('desc_enc') ?? filter_get('desc_enc') ?? '');
$id_tipo_enc = (int)(filter_post('id_tipo_enc') ?? filter_get('id_tipo_enc') ?? 0);

ContestarJson::enviar('', $useCase->execute($desc_enc, $id_tipo_enc));
