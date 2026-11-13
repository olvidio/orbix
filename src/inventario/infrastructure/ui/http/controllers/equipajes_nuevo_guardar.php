<?php

/**
 * La lógica está en {@see \src\inventario\application\EquipajeNuevo}; aquí solo se abre la cápsula.
 */

use src\inventario\application\EquipajeNuevo;
use src\shared\infrastructure\DependencyResolver;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;

try {
    $ctx = HashB::open(
        \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'ctx_nuevo'),
        'equipajes_nuevo_guardar'
    );
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

/** @var EquipajeNuevo $useCase */
$useCase = DependencyResolver::get(EquipajeNuevo::class);
ContestarJson::enviar($useCase->execute(
    \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'id_ubi_activ'),
    \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'nom_equipaje'),
    \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'ids_activ'),
    \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'f_ini'),
    \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'f_fin'),
    \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'lugar'),
), 'ok');
