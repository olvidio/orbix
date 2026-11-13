<?php

/**
 * Movimientos de documentos para preparar los equipajes de `sel`.
 * La lógica está en {@see \src\inventario\application\EquipajesMovimientosData}.
 */

use src\inventario\application\EquipajesMovimientosData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

$a_sel = (array) \src\shared\domain\helpers\FilterPostGet::post('sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

/** @var EquipajesMovimientosData $useCase */
$useCase = DependencyResolver::get(EquipajesMovimientosData::class);
ContestarJson::enviar('', $useCase->execute(array_values($a_sel)));
