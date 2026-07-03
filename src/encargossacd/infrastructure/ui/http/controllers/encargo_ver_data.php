<?php

use src\encargossacd\application\EncargoVerData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use src\shared\domain\helpers\FilterPostGet;

/** @var EncargoVerData $useCase */
$useCase = DependencyResolver::get(EncargoVerData::class);


$que = (string)(\src\shared\domain\helpers\FilterPostGet::post('que') ?? \src\shared\domain\helpers\FilterPostGet::get('que') ?? '');
$id_enc = (int)(\src\shared\domain\helpers\FilterPostGet::post('id_enc') ?? \src\shared\domain\helpers\FilterPostGet::get('id_enc') ?? 0);
$id_tipo_enc = (int)(\src\shared\domain\helpers\FilterPostGet::post('id_tipo_enc') ?? \src\shared\domain\helpers\FilterPostGet::get('id_tipo_enc') ?? 0);
$grupo = (string)(\src\shared\domain\helpers\FilterPostGet::post('grupo') ?? \src\shared\domain\helpers\FilterPostGet::get('grupo') ?? '');
$filtro_ctr = (string)(\src\shared\domain\helpers\FilterPostGet::post('filtro_ctr') ?? \src\shared\domain\helpers\FilterPostGet::get('filtro_ctr') ?? '');
$desc_enc = (string)(\src\shared\domain\helpers\FilterPostGet::post('desc_enc') ?? \src\shared\domain\helpers\FilterPostGet::get('desc_enc') ?? '');
$desc_lugar = (string)(\src\shared\domain\helpers\FilterPostGet::post('desc_lugar') ?? \src\shared\domain\helpers\FilterPostGet::get('desc_lugar') ?? '');
$id_zona = (int)(\src\shared\domain\helpers\FilterPostGet::post('id_zona') ?? \src\shared\domain\helpers\FilterPostGet::get('id_zona') ?? 0);

ContestarJson::enviar('', $useCase->execute(
    $que,
    $id_enc,
    $id_tipo_enc,
    $grupo,
    $filtro_ctr,
    $desc_enc,
    $desc_lugar,
    $id_zona,
));
