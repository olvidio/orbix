<?php

use src\shared\infrastructure\DependencyResolver;
use src\ubis\application\TelecoEliminar;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;

try {
    $ctx = HashB::open(
        \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'ctx_eliminar'),
        'teleco_eliminar'
    );
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

$Qobj_pau = \src\shared\domain\helpers\FuncTablasSupport::inputString($ctx, 'obj_pau');
$pkeyRaw = $ctx['pkey'] ?? null;

/** @var list<int|string> $a_pkey */
$a_pkey = [];
if (is_array($pkeyRaw)) {
    foreach (array_values($pkeyRaw) as $item) {
        if (is_int($item) || is_string($item)) {
            $a_pkey[] = $item;
        }
    }
} elseif (is_int($pkeyRaw) || is_string($pkeyRaw)) {
    $a_pkey[] = $pkeyRaw;
}

/** @var TelecoEliminar $useCase */
$useCase = DependencyResolver::get(TelecoEliminar::class);
ContestarJson::enviar('', $useCase->execute($Qobj_pau, $a_pkey));
