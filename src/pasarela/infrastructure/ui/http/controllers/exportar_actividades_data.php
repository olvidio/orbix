<?php
use src\shared\infrastructure\DependencyResolver;
use src\shared\domain\helpers\FilterPostGet;

use src\shared\web\ContestarJson;
use src\pasarela\application\ExportarActividadesData;

$input = [
    'id_tipo_activ' => (string)FilterPostGet::post('id_tipo_activ'),
    'isfsv_val' => (string)FilterPostGet::post('isfsv_val'),
    'iasistentes_val' => (string)FilterPostGet::post('iasistentes_val'),
    'iactividad_val' => (string)FilterPostGet::post('iactividad_val'),
    'inicio_iso' => (string)FilterPostGet::post('inicio_iso'),
    'fin_iso' => (string)FilterPostGet::post('fin_iso'),
    'id_cdc' => (array)FilterPostGet::post('id_cdc', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY),
];

/** @var ExportarActividadesData $useCase */
$useCase = DependencyResolver::get(ExportarActividadesData::class);

$data = $useCase->execute($input);
ContestarJson::enviar('', $data);
