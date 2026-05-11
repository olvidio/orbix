<?php

use src\shared\web\ContestarJson;
use src\pasarela\application\ExportarActividadesData;

$input = [
    'id_tipo_activ' => (string)filter_input(INPUT_POST, 'id_tipo_activ'),
    'isfsv_val' => (string)filter_input(INPUT_POST, 'isfsv_val'),
    'iasistentes_val' => (string)filter_input(INPUT_POST, 'iasistentes_val'),
    'iactividad_val' => (string)filter_input(INPUT_POST, 'iactividad_val'),
    'inicio_iso' => (string)filter_input(INPUT_POST, 'inicio_iso'),
    'fin_iso' => (string)filter_input(INPUT_POST, 'fin_iso'),
    'id_cdc' => (array)filter_input(INPUT_POST, 'id_cdc', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY),
];

$data = ExportarActividadesData::execute($input);
ContestarJson::enviar('', $data);
