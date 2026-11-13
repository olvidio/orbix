<?php

/**
 * La lógica está en {@see \src\inventario\application\EquipajeMaletas}; aquí solo se abre la cápsula.
 */

use src\inventario\application\EquipajeMaletas;
use src\shared\infrastructure\DependencyResolver;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;

try {
    $ctx = HashB::open(
        \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'ctx_eliminar_grupo'),
        'equipajes_eliminar_grupo'
    );
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

/** @var EquipajeMaletas $useCase */
$useCase = DependencyResolver::get(EquipajeMaletas::class);
ContestarJson::enviar($useCase->eliminarGrupo(\src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'id_equipaje'), \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'id_grupo')), 'ok');
