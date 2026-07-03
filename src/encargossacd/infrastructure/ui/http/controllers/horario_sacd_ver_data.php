<?php

use src\encargossacd\application\EncargoSacdHorarioVerData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use src\shared\domain\helpers\FilterPostGet;

/** @var EncargoSacdHorarioVerData $useCase */
$useCase = DependencyResolver::get(EncargoSacdHorarioVerData::class);


$id_nom = (int)(\src\shared\domain\helpers\FilterPostGet::post('id_nom') ?? \src\shared\domain\helpers\FilterPostGet::get('id_nom') ?? 0);
$id_enc = (int)(\src\shared\domain\helpers\FilterPostGet::post('id_enc') ?? \src\shared\domain\helpers\FilterPostGet::get('id_enc') ?? 0);
$id_item = (int)(\src\shared\domain\helpers\FilterPostGet::post('id_item') ?? \src\shared\domain\helpers\FilterPostGet::get('id_item') ?? 0);
$desc_enc = (string)(\src\shared\domain\helpers\FilterPostGet::post('desc_enc') ?? \src\shared\domain\helpers\FilterPostGet::get('desc_enc') ?? '');

ContestarJson::enviar('', $useCase->cargar($id_nom, $id_enc, $id_item, $desc_enc));
