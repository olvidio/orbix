<?php

use src\encargossacd\application\EncargoHorarioVerData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var EncargoHorarioVerData $useCase */
$useCase = DependencyResolver::get(EncargoHorarioVerData::class);


$mod = (string)(filter_input(INPUT_POST, 'mod') ?? filter_input(INPUT_GET, 'mod') ?? '');
$id_enc = (int)(filter_input(INPUT_POST, 'id_enc') ?? filter_input(INPUT_GET, 'id_enc') ?? 0);
$id_item_h = (int)(filter_input(INPUT_POST, 'id_item_h') ?? filter_input(INPUT_GET, 'id_item_h') ?? 0);

ContestarJson::enviar('', $useCase->cargar($mod, $id_enc, $id_item_h));
