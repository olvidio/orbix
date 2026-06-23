<?php
use src\shared\infrastructure\DependencyResolver;

use src\misas\application\GuardarEncargoZona;
use src\shared\web\ContestarJson;

$input = [
    'id_enc' => filter_post('id_enc'),
    'id_tipo_enc' => filter_post('id_tipo_enc'),
    'id_ubi' => filter_post('id_ubi'),
    'id_zona' => filter_post('id_zona'),
    'orden' => filter_post('orden'),
    'prioridad' => filter_post('prioridad'),
    'descripcion_lugar' => filter_post('descripcion_lugar'),
    'encargo' => filter_post('encargo'),
    'idioma_enc' => filter_post('idioma_enc'),
    'observ' => filter_post('observ'),
];

/** @var GuardarEncargoZona $useCase */
$useCase = DependencyResolver::get(GuardarEncargoZona::class);
$result = $useCase->execute($input);

ContestarJson::enviar($result['error'], $result['data']);
