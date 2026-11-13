<?php

/**
 * Actividades de la casa `id_cdc` en un periodo, para elegir las de un equipaje.
 * La lógica está en {@see \src\inventario\application\EquipajeActividadesData::porPeriodo()}; aquí
 * solo se resuelve el rango de fechas del periodo.
 */

use src\inventario\application\EquipajeActividadesData;
use src\inventario\infrastructure\ui\http\RangoPeriodoRequest;
use src\shared\domain\helpers\FuncTablasSupport;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

$Qid_cdc = FuncTablasSupport::inputInt($_POST, 'id_cdc');

if (empty($Qid_cdc)) {
    ContestarJson::enviar(_("debe seleccionar un lugar"), ['a_valores' => [], 'nombre_ubi' => '']);
    return;
}
$rango = RangoPeriodoRequest::resolver($_POST);
if (is_string($rango)) {
    ContestarJson::enviar($rango, 'none');
    return;
}

/** @var EquipajeActividadesData $useCase */
$useCase = DependencyResolver::get(EquipajeActividadesData::class);
ContestarJson::enviar('', $useCase->porPeriodo($Qid_cdc, $rango['inicio'], $rango['fin']));
