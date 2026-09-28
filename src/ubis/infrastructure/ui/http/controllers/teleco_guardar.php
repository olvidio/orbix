<?php

use src\shared\infrastructure\DependencyResolver;
use src\ubis\application\TelecoGuardar;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;

try {
    $ctx = HashB::open(
        \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'ctx_guardar'),
        'teleco_guardar'
    );
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

$Qid_tipo_teleco = \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'id_tipo_teleco');
$Qdesc_teleco = \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'id_desc_teleco');
$Qnum_teleco = \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'num_teleco');
$Qobserv = \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'observ');

$Qobj_pau = \src\shared\domain\helpers\FuncTablasSupport::inputString($ctx, 'obj_pau');
$Qid_ubi = \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'id_ubi');
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

/** @var TelecoGuardar $useCase */
$useCase = DependencyResolver::get(TelecoGuardar::class);
ContestarJson::enviar('', $useCase->execute(
    $Qobj_pau,
    $Qid_ubi,
    $a_pkey,
    $Qid_tipo_teleco,
    $Qdesc_teleco,
    $Qnum_teleco,
    $Qobserv
));
