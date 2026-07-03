<?php

use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use src\zonassacd\application\ZonaSacdUpdate;
use src\shared\domain\helpers\FuncTablasSupport;
$input = [
    'id_zona' => FuncTablasSupport::inputString($_POST, 'id_zona'),
    'id_zona_new' => FuncTablasSupport::inputString($_POST, 'id_zona_new'),
    'acumular' => FuncTablasSupport::inputInt($_POST, 'acumular'),
    'sel' => FuncTablasSupport::inputStringList($_POST, 'sel'),
];

/** @var ZonaSacdUpdate $useCase */
$useCase = DependencyResolver::get(ZonaSacdUpdate::class);
$resultado = $useCase->execute(
    $input['id_zona'],
    $input['id_zona_new'],
    $input['acumular'],
    $input['sel'],
);

// Errores parciales de persistencia van en `mensaje`; vacío = éxito (`data: "ok"`).
$mensaje = $resultado['mensaje'] ?? '';
ContestarJson::enviar(is_string($mensaje) ? $mensaje : '', 'ok');
