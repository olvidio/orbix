<?php

use src\shared\infrastructure\DependencyResolver;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;
use src\zonassacd\application\ZonaCtrUpdate;

$selIn = \src\shared\domain\helpers\FuncTablasSupport::inputStringList($_POST, 'sel');
$idZonaNew = \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'id_zona_new');

$selIds = [];
try {
    foreach ($selIn as $capsule) {
        $ctx = HashB::open($capsule, 'zona_ctr_update');
        $idUbi = \src\shared\domain\helpers\FuncTablasSupport::inputString($ctx, 'id_ubi');
        if ($idUbi === '') {
            ContestarJson::enviar(_("Operación no autorizada"), 'none');
            return;
        }
        $selIds[] = $idUbi;
    }
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

/** @var ZonaCtrUpdate $useCase */
$useCase = DependencyResolver::get(ZonaCtrUpdate::class);
$resultado = $useCase->execute($idZonaNew, $selIds);

$mensaje = $resultado['mensaje'] ?? '';
ContestarJson::enviar(is_string($mensaje) ? $mensaje : '', 'ok');
