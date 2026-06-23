<?php

use src\encargossacd\application\EncargoHorarioVerData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var EncargoHorarioVerData $useCase */
$useCase = DependencyResolver::get(EncargoHorarioVerData::class);


$mod = (string)(filter_post('mod') ?? filter_get('mod') ?? '');
$id_enc = (int)(filter_post('id_enc') ?? filter_get('id_enc') ?? 0);
$id_item_h = (int)(filter_post('id_item_h') ?? filter_get('id_item_h') ?? 0);

ContestarJson::enviar('', $useCase->cargar($mod, $id_enc, $id_item_h));
