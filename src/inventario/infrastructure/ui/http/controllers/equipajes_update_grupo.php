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
        \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'ctx_update_grupo'),
        'equipajes_update_grupo'
    );
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

/** @var EquipajeMaletas $useCase */
$useCase = DependencyResolver::get(EquipajeMaletas::class);
$r = $useCase->guardarGrupo(
    \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'id_equipaje'),
    \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'id_grupo'),
    \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'id_lugar'),
    array_values((array) \src\shared\domain\helpers\FilterPostGet::post('sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY)),
);
ContestarJson::enviar($r['error'], ['id_item_egm' => $r['id_item_egm']]);
