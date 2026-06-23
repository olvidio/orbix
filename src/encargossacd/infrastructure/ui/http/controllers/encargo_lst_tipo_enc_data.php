<?php

use src\encargossacd\application\EncargoLstTipoEncData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var EncargoLstTipoEncData $useCase */
$useCase = DependencyResolver::get(EncargoLstTipoEncData::class);


$grupo = (string)(filter_post('grupo') ?? filter_get('grupo') ?? '');
$id_tipo_enc = filter_post('id_tipo_enc');
if ($id_tipo_enc === null) {
    $id_tipo_enc = filter_get('id_tipo_enc');
}

ContestarJson::enviar('', $useCase->execute($grupo, $id_tipo_enc !== null && $id_tipo_enc !== false ? (string)$id_tipo_enc : null));
