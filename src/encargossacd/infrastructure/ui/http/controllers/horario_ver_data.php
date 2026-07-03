<?php

use src\encargossacd\application\EncargoHorarioVerData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use src\shared\domain\helpers\FilterPostGet;

/** @var EncargoHorarioVerData $useCase */
$useCase = DependencyResolver::get(EncargoHorarioVerData::class);


$mod = (string)(FilterPostGet::post('mod') ?? FilterPostGet::get('mod') ?? '');
$id_enc = (int)(FilterPostGet::post('id_enc') ?? FilterPostGet::get('id_enc') ?? 0);
$id_item_h = (int)(FilterPostGet::post('id_item_h') ?? FilterPostGet::get('id_item_h') ?? 0);

ContestarJson::enviar('', $useCase->cargar($mod, $id_enc, $id_item_h));
