<?php

/**
 * Guarda la cabecera, la cabecera B, el pie o el texto de una maleta del equipaje de la cápsula.
 * La lógica está en {@see \src\inventario\application\EquipajeTextoGuardar}.
 */

use src\inventario\application\EquipajeTextoGuardar;
use src\shared\domain\helpers\FuncTablasSupport;
use src\shared\infrastructure\DependencyResolver;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;

try {
    $ctx = HashB::open(FuncTablasSupport::inputString($_POST, 'ctx_guardar'), 'equipajes_texto_listado_guardar');
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

/** @var EquipajeTextoGuardar $useCase */
$useCase = DependencyResolver::get(EquipajeTextoGuardar::class);
ContestarJson::enviar($useCase->execute(
    FuncTablasSupport::inputInt($ctx, 'id_equipaje'),
    FuncTablasSupport::inputString($_POST, 'loc'),
    FuncTablasSupport::inputString($_POST, 'texto'),
), 'ok');
