<?php

/**
 * Casas con actividades de la dl en un periodo, para preparar un equipaje.
 * La lógica está en {@see \src\inventario\application\EquipajeActividadesData::casasConActividades()};
 * aquí solo se resuelve el rango de fechas del periodo.
 */

use src\inventario\application\EquipajeActividadesData;
use src\inventario\infrastructure\ui\http\RangoPeriodoRequest;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

$rango = RangoPeriodoRequest::resolver($_POST);
if (is_string($rango)) {
    ContestarJson::enviar($rango, 'none');
    return;
}

/** @var EquipajeActividadesData $useCase */
$useCase = DependencyResolver::get(EquipajeActividadesData::class);
ContestarJson::enviar('', $useCase->casasConActividades($rango['inicio'], $rango['fin']));
