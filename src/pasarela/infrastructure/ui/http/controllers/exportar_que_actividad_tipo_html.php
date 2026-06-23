<?php
use src\shared\infrastructure\DependencyResolver;

use src\shared\web\ContestarJson;
use src\pasarela\application\ExportarQueActividadTipoHtml;

$input = [
    'id_tipo_activ' => (string)filter_post('id_tipo_activ'),
    'sasistentes' => (string)filter_post('sasistentes'),
    'sactividad' => (string)filter_post('sactividad'),
    'snom_tipo' => (string)filter_post('snom_tipo'),
];

/** @var ExportarQueActividadTipoHtml $useCase */
$useCase = DependencyResolver::get(ExportarQueActividadTipoHtml::class);

$data = $useCase->execute($input);
ContestarJson::enviar('', $data);
