<?php

use src\encargossacd\application\EncargoVerData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var EncargoVerData $useCase */
$useCase = DependencyResolver::get(EncargoVerData::class);


$que = (string)(filter_input(INPUT_POST, 'que') ?? filter_input(INPUT_GET, 'que') ?? '');
$id_enc = (int)(filter_input(INPUT_POST, 'id_enc') ?? filter_input(INPUT_GET, 'id_enc') ?? 0);
$id_tipo_enc = (int)(filter_input(INPUT_POST, 'id_tipo_enc') ?? filter_input(INPUT_GET, 'id_tipo_enc') ?? 0);
$grupo = (string)(filter_input(INPUT_POST, 'grupo') ?? filter_input(INPUT_GET, 'grupo') ?? '');
$filtro_ctr = (string)(filter_input(INPUT_POST, 'filtro_ctr') ?? filter_input(INPUT_GET, 'filtro_ctr') ?? '');
$desc_enc = (string)(filter_input(INPUT_POST, 'desc_enc') ?? filter_input(INPUT_GET, 'desc_enc') ?? '');
$desc_lugar = (string)(filter_input(INPUT_POST, 'desc_lugar') ?? filter_input(INPUT_GET, 'desc_lugar') ?? '');
$id_zona = (int)(filter_input(INPUT_POST, 'id_zona') ?? filter_input(INPUT_GET, 'id_zona') ?? 0);

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
