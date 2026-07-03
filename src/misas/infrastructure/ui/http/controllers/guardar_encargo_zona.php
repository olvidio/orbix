<?php
use src\shared\infrastructure\DependencyResolver;
use src\shared\domain\helpers\FilterPostGet;

use src\misas\application\GuardarEncargoZona;
use src\shared\web\ContestarJson;

$input = [
    'id_enc' => FilterPostGet::post('id_enc'),
    'id_tipo_enc' => FilterPostGet::post('id_tipo_enc'),
    'id_ubi' => FilterPostGet::post('id_ubi'),
    'id_zona' => FilterPostGet::post('id_zona'),
    'orden' => FilterPostGet::post('orden'),
    'prioridad' => FilterPostGet::post('prioridad'),
    'descripcion_lugar' => FilterPostGet::post('descripcion_lugar'),
    'encargo' => FilterPostGet::post('encargo'),
    'idioma_enc' => FilterPostGet::post('idioma_enc'),
    'observ' => FilterPostGet::post('observ'),
];

/** @var GuardarEncargoZona $useCase */
$useCase = DependencyResolver::get(GuardarEncargoZona::class);
$result = $useCase->execute($input);

ContestarJson::enviar($result['error'], $result['data']);
