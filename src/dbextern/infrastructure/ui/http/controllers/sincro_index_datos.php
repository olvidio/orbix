<?php

use src\dbextern\application\SincroIndexData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use function src\shared\domain\helpers\input_string;

$tipo_persona = input_string($_POST, 'tipo');

$data = DependencyResolver::get(SincroIndexData::class)($tipo_persona);

$error_txt = is_string($data['error'] ?? null) ? $data['error'] : '';
if ($error_txt !== '') {
    unset($data['error']);
}

ContestarJson::enviar($error_txt, $data);
