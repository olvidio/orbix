<?php

use src\encargossacd\application\EncargoVerData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use src\shared\domain\helpers\FilterPostGet;

/** @var EncargoVerData $useCase */
$useCase = DependencyResolver::get(EncargoVerData::class);


$que = (string)(FilterPostGet::post('que') ?? FilterPostGet::get('que') ?? '');
$id_enc = (int)(FilterPostGet::post('id_enc') ?? FilterPostGet::get('id_enc') ?? 0);
$id_tipo_enc = (int)(FilterPostGet::post('id_tipo_enc') ?? FilterPostGet::get('id_tipo_enc') ?? 0);
$grupo = (string)(FilterPostGet::post('grupo') ?? FilterPostGet::get('grupo') ?? '');
$filtro_ctr = (string)(FilterPostGet::post('filtro_ctr') ?? FilterPostGet::get('filtro_ctr') ?? '');
$desc_enc = (string)(FilterPostGet::post('desc_enc') ?? FilterPostGet::get('desc_enc') ?? '');
$desc_lugar = (string)(FilterPostGet::post('desc_lugar') ?? FilterPostGet::get('desc_lugar') ?? '');
$id_zona = (int)(FilterPostGet::post('id_zona') ?? FilterPostGet::get('id_zona') ?? 0);

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
