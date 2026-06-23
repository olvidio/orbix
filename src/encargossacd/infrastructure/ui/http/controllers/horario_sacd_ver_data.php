<?php

use src\encargossacd\application\EncargoSacdHorarioVerData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var EncargoSacdHorarioVerData $useCase */
$useCase = DependencyResolver::get(EncargoSacdHorarioVerData::class);


$id_nom = (int)(filter_post('id_nom') ?? filter_get('id_nom') ?? 0);
$id_enc = (int)(filter_post('id_enc') ?? filter_get('id_enc') ?? 0);
$id_item = (int)(filter_post('id_item') ?? filter_get('id_item') ?? 0);
$desc_enc = (string)(filter_post('desc_enc') ?? filter_get('desc_enc') ?? '');

ContestarJson::enviar('', $useCase->cargar($id_nom, $id_enc, $id_item, $desc_enc));
