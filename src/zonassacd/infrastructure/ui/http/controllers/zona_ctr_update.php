<?php

use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use src\zonassacd\application\ZonaCtrUpdate;
use src\shared\domain\helpers\FuncTablasSupport;
$input = [
    'id_zona_new' => FuncTablasSupport::inputString($_POST, 'id_zona_new'),
    'sel' => FuncTablasSupport::inputStringList($_POST, 'sel'),
];

/** @var ZonaCtrUpdate $useCase */
$useCase = DependencyResolver::get(ZonaCtrUpdate::class);
$resultado = $useCase->execute($input['id_zona_new'], $input['sel']);

$mensaje = $resultado['mensaje'] ?? '';
ContestarJson::enviar(is_string($mensaje) ? $mensaje : '', 'ok');
