<?php

/**
 * Resumen de las actividades elegidas (`sel`) en la casa `id_cdc` para crear un equipaje.
 * La lógica está en {@see \src\inventario\application\EquipajeActividadesData::seleccion()}.
 */

use src\inventario\application\EquipajeActividadesData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var EquipajeActividadesData $useCase */
$useCase = DependencyResolver::get(EquipajeActividadesData::class);
ContestarJson::enviar('', $useCase->seleccion(
    array_values((array) \src\shared\domain\helpers\FilterPostGet::post('sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY)),
    \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'id_cdc'),
));
