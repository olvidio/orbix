<?php

/**
 * ¿Tiene plano la dirección? POST `obj_dir`, `id_direccion` → `{existe: bool}`.
 */

use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use src\ubis\application\PlanosDireccion;

$Qobj_dir = \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'obj_dir');
$Qid_direccion = \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'id_direccion');
if ($Qobj_dir === '' || $Qid_direccion <= 0) {
    ContestarJson::enviar(sprintf(_("Faltan parámetros: %s"), 'obj_dir, id_direccion'), 'none');
    return;
}

try {
    $existe = DependencyResolver::get(PlanosDireccion::class)->existe($Qobj_dir, $Qid_direccion);
} catch (InvalidArgumentException $e) {
    ContestarJson::enviar($e->getMessage(), 'none');
    return;
}

ContestarJson::enviar('', ['existe' => $existe]);
