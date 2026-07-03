<?php

use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use src\zonassacd\application\ZonaSacdUpdate;
$input = [
    'id_zona' => \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'id_zona'),
    'id_zona_new' => \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'id_zona_new'),
    'acumular' => \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'acumular'),
    'sel' => \src\shared\domain\helpers\FuncTablasSupport::inputStringList($_POST, 'sel'),
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
