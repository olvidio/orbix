<?php

use src\encargossacd\application\EncargoLstTipoEncData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use src\shared\domain\helpers\FilterPostGet;

/** @var EncargoLstTipoEncData $useCase */
$useCase = DependencyResolver::get(EncargoLstTipoEncData::class);


$grupo = (string)(FilterPostGet::post('grupo') ?? FilterPostGet::get('grupo') ?? '');
$id_tipo_enc = FilterPostGet::post('id_tipo_enc');
if ($id_tipo_enc === null) {
    $id_tipo_enc = FilterPostGet::get('id_tipo_enc');
}

ContestarJson::enviar('', $useCase->execute($grupo, $id_tipo_enc !== null && $id_tipo_enc !== false ? (string)$id_tipo_enc : null));
