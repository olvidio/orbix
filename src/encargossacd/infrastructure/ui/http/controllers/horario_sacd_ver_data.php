<?php

use src\encargossacd\application\EncargoSacdHorarioVerData;
use frontend\shared\web\ContestarJson;

$id_nom = (int)(filter_input(INPUT_POST, 'id_nom') ?? filter_input(INPUT_GET, 'id_nom') ?? 0);
$id_enc = (int)(filter_input(INPUT_POST, 'id_enc') ?? filter_input(INPUT_GET, 'id_enc') ?? 0);
$id_item = (int)(filter_input(INPUT_POST, 'id_item') ?? filter_input(INPUT_GET, 'id_item') ?? 0);
$desc_enc = (string)(filter_input(INPUT_POST, 'desc_enc') ?? filter_input(INPUT_GET, 'desc_enc') ?? '');

ContestarJson::enviar('', EncargoSacdHorarioVerData::cargar($id_nom, $id_enc, $id_item, $desc_enc));
