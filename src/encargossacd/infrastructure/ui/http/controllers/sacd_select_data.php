<?php

use src\encargossacd\application\SacdSelectData;
use frontend\shared\web\ContestarJson;

$filtro_sacd = (string)(filter_input(INPUT_POST, 'filtro_sacd') ?? filter_input(INPUT_GET, 'filtro_sacd') ?? '');
$id_nom = (int)(filter_input(INPUT_POST, 'id_nom') ?? filter_input(INPUT_GET, 'id_nom') ?? 0);

ContestarJson::enviar('', SacdSelectData::execute($filtro_sacd, $id_nom));
