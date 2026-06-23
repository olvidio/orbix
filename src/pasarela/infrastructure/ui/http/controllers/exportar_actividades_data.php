<?php
use src\shared\infrastructure\DependencyResolver;

use src\shared\web\ContestarJson;
use src\pasarela\application\ExportarActividadesData;

$input = [
    'id_tipo_activ' => (string)filter_post('id_tipo_activ'),
    'isfsv_val' => (string)filter_post('isfsv_val'),
    'iasistentes_val' => (string)filter_post('iasistentes_val'),
    'iactividad_val' => (string)filter_post('iactividad_val'),
    'inicio_iso' => (string)filter_post('inicio_iso'),
    'fin_iso' => (string)filter_post('fin_iso'),
    'id_cdc' => (array)filter_post('id_cdc', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY),
];

/** @var ExportarActividadesData $useCase */
$useCase = DependencyResolver::get(ExportarActividadesData::class);

$data = $useCase->execute($input);
ContestarJson::enviar('', $data);
