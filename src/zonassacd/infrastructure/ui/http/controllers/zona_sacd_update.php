<?php

use src\shared\infrastructure\DependencyResolver;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;
use src\zonassacd\application\ZonaSacdUpdate;

$selIn = \src\shared\domain\helpers\FuncTablasSupport::inputStringList($_POST, 'sel');
$idZona = \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'id_zona');
$idZonaNew = \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'id_zona_new');
$acumular = \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'acumular');

$selIds = [];
try {
    foreach ($selIn as $capsule) {
        $ctx = HashB::open($capsule, 'zona_sacd_update');
        $selIds[] = \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'id_nom');
        $zonaFirmada = \src\shared\domain\helpers\FuncTablasSupport::inputString($ctx, 'id_zona');
        if ($zonaFirmada !== '') {
            $idZona = $zonaFirmada;
        }
    }
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

if ($selIn !== [] && $selIds === []) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

/** @var ZonaSacdUpdate $useCase */
$useCase = DependencyResolver::get(ZonaSacdUpdate::class);
$resultado = $useCase->execute(
    $idZona,
    $idZonaNew,
    $acumular,
    $selIds,
);

$mensaje = $resultado['mensaje'] ?? '';
ContestarJson::enviar(is_string($mensaje) ? $mensaje : '', 'ok');
