<?php
use src\shared\infrastructure\DependencyResolver;
use src\shared\domain\helpers\FilterPostGet;

use src\shared\web\ContestarJson;
use src\pasarela\application\ExportarQueActividadTipoHtml;

$input = [
    'id_tipo_activ' => (string)FilterPostGet::post('id_tipo_activ'),
    'sasistentes' => (string)FilterPostGet::post('sasistentes'),
    'sactividad' => (string)FilterPostGet::post('sactividad'),
    'snom_tipo' => (string)FilterPostGet::post('snom_tipo'),
];

/** @var ExportarQueActividadTipoHtml $useCase */
$useCase = DependencyResolver::get(ExportarQueActividadTipoHtml::class);

$data = $useCase->execute($input);
ContestarJson::enviar('', $data);
