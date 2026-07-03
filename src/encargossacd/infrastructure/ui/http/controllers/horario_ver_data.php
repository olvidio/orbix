<?php

use src\encargossacd\application\EncargoHorarioVerData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use src\shared\domain\helpers\FilterPostGet;

/** @var EncargoHorarioVerData $useCase */
$useCase = DependencyResolver::get(EncargoHorarioVerData::class);


$mod = (string)(\src\shared\domain\helpers\FilterPostGet::post('mod') ?? \src\shared\domain\helpers\FilterPostGet::get('mod') ?? '');
$id_enc = (int)(\src\shared\domain\helpers\FilterPostGet::post('id_enc') ?? \src\shared\domain\helpers\FilterPostGet::get('id_enc') ?? 0);
$id_item_h = (int)(\src\shared\domain\helpers\FilterPostGet::post('id_item_h') ?? \src\shared\domain\helpers\FilterPostGet::get('id_item_h') ?? 0);

ContestarJson::enviar('', $useCase->cargar($mod, $id_enc, $id_item_h));
