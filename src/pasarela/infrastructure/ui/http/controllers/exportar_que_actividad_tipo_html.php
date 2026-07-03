<?php
use src\shared\infrastructure\DependencyResolver;
use src\shared\domain\helpers\FilterPostGet;

use src\shared\web\ContestarJson;
use src\pasarela\application\ExportarQueActividadTipoHtml;

$input = [
    'id_tipo_activ' => (string)\src\shared\domain\helpers\FilterPostGet::post('id_tipo_activ'),
    'sasistentes' => (string)\src\shared\domain\helpers\FilterPostGet::post('sasistentes'),
    'sactividad' => (string)\src\shared\domain\helpers\FilterPostGet::post('sactividad'),
    'snom_tipo' => (string)\src\shared\domain\helpers\FilterPostGet::post('snom_tipo'),
];

/** @var ExportarQueActividadTipoHtml $useCase */
$useCase = DependencyResolver::get(ExportarQueActividadTipoHtml::class);

$data = $useCase->execute($input);
ContestarJson::enviar('', $data);
