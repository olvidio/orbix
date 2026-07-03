<?php

use src\encargossacd\application\EncargoLstTipoEncData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use src\shared\domain\helpers\FilterPostGet;

/** @var EncargoLstTipoEncData $useCase */
$useCase = DependencyResolver::get(EncargoLstTipoEncData::class);


$grupo = (string)(\src\shared\domain\helpers\FilterPostGet::post('grupo') ?? \src\shared\domain\helpers\FilterPostGet::get('grupo') ?? '');
$id_tipo_enc = \src\shared\domain\helpers\FilterPostGet::post('id_tipo_enc');
if ($id_tipo_enc === null) {
    $id_tipo_enc = \src\shared\domain\helpers\FilterPostGet::get('id_tipo_enc');
}

ContestarJson::enviar('', $useCase->execute($grupo, $id_tipo_enc !== null && $id_tipo_enc !== false ? (string)$id_tipo_enc : null));
