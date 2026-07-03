<?php

use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use src\zonassacd\application\ZonaCtrUpdate;
$input = [
    'id_zona_new' => \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'id_zona_new'),
    'sel' => \src\shared\domain\helpers\FuncTablasSupport::inputStringList($_POST, 'sel'),
];

/** @var ZonaCtrUpdate $useCase */
$useCase = DependencyResolver::get(ZonaCtrUpdate::class);
$resultado = $useCase->execute($input['id_zona_new'], $input['sel']);

$mensaje = $resultado['mensaje'] ?? '';
ContestarJson::enviar(is_string($mensaje) ? $mensaje : '', 'ok');
