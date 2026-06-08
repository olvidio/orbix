<?php

use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use src\zonassacd\application\ZonaCtrUpdate;
use function src\shared\domain\helpers\input_string;
use function src\shared\domain\helpers\input_string_list;

$input = [
    'id_zona_new' => input_string($_POST, 'id_zona_new'),
    'sel' => input_string_list($_POST, 'sel'),
];

/** @var ZonaCtrUpdate $useCase */
$useCase = DependencyResolver::get(ZonaCtrUpdate::class);
$resultado = $useCase->execute($input['id_zona_new'], $input['sel']);

$mensaje = $resultado['mensaje'] ?? '';
ContestarJson::enviar(is_string($mensaje) ? $mensaje : '', 'ok');
