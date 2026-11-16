<?php

use src\pasarela\application\ExportarQueActividadTipoData;
use src\shared\domain\helpers\FilterPostGet;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

$input = [
    'id_tipo_activ' => (string) FilterPostGet::post('id_tipo_activ'),
    'sasistentes' => (string) FilterPostGet::post('sasistentes'),
    'sactividad' => (string) FilterPostGet::post('sactividad'),
    'snom_tipo' => (string) FilterPostGet::post('snom_tipo'),
];

/** @var ExportarQueActividadTipoData $useCase */
$useCase = DependencyResolver::get(ExportarQueActividadTipoData::class);

ContestarJson::enviar('', $useCase->execute($input));
