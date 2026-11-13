<?php

/**
 * Inventario de documentos para imprimir (centros de `sel`, JSON).
 * La lógica está en {@see \src\inventario\application\InventarioDocumentosData::porUbis()}.
 */

use src\inventario\application\InventarioDocumentosData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

$a_sel = json_decode(\src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'sel'), true);
if (!is_array($a_sel)) {
    $a_sel = [];
}

/** @var InventarioDocumentosData $useCase */
$useCase = DependencyResolver::get(InventarioDocumentosData::class);
ContestarJson::enviar('', $useCase->porUbis(array_values($a_sel)));
