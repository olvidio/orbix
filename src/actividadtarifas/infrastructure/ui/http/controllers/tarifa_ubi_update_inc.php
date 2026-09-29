<?php

use src\shared\domain\helpers\FilterPostGet;
use src\actividadtarifas\application\TarifaUbiUpdateInc;
use src\shared\infrastructure\DependencyResolver;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;

try {
    $ctx = HashB::open(
        (string)\src\shared\domain\helpers\FilterPostGet::post('ctx_inc'),
        'tarifa_ubi_update_inc'
    );
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

$inc_cantidad = \src\shared\domain\helpers\FilterPostGet::post('inc_cantidad', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
$idItems = $ctx['id_items'] ?? [];
$idItemsAllow = [];
if (is_array($idItems)) {
    foreach ($idItems as $idItem) {
        if (is_numeric($idItem)) {
            $idItemsAllow[(int)$idItem] = true;
        }
    }
}

$incFiltrado = [];
if (is_array($inc_cantidad)) {
    foreach ($inc_cantidad as $key => $cantidad) {
        $id_item = (int)strtok((string)$key, '#');
        $id_item = (int)strtok('#');
        if ($id_item > 0 && isset($idItemsAllow[$id_item])) {
            $incFiltrado[$key] = $cantidad;
        }
    }
}

$input = [
    'inc_cantidad' => $incFiltrado,
];

/** @var TarifaUbiUpdateInc $useCase */
$useCase = DependencyResolver::get(TarifaUbiUpdateInc::class);
ContestarJson::enviar($useCase->execute($input), 'ok');
