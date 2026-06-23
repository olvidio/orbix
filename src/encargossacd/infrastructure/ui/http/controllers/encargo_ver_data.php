<?php

use src\encargossacd\application\EncargoVerData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var EncargoVerData $useCase */
$useCase = DependencyResolver::get(EncargoVerData::class);


$que = (string)(filter_post('que') ?? filter_get('que') ?? '');
$id_enc = (int)(filter_post('id_enc') ?? filter_get('id_enc') ?? 0);
$id_tipo_enc = (int)(filter_post('id_tipo_enc') ?? filter_get('id_tipo_enc') ?? 0);
$grupo = (string)(filter_post('grupo') ?? filter_get('grupo') ?? '');
$filtro_ctr = (string)(filter_post('filtro_ctr') ?? filter_get('filtro_ctr') ?? '');
$desc_enc = (string)(filter_post('desc_enc') ?? filter_get('desc_enc') ?? '');
$desc_lugar = (string)(filter_post('desc_lugar') ?? filter_get('desc_lugar') ?? '');
$id_zona = (int)(filter_post('id_zona') ?? filter_get('id_zona') ?? 0);

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
