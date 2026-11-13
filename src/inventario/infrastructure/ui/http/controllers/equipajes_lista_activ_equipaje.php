<?php

/**
 * Nombres de las actividades del equipaje `id_equipaje`.
 * La lógica está en {@see \src\inventario\application\EquipajeActividadesData::deEquipaje()}.
 */

use src\inventario\application\EquipajeActividadesData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var EquipajeActividadesData $useCase */
$useCase = DependencyResolver::get(EquipajeActividadesData::class);
ContestarJson::enviar('', $useCase->deEquipaje(\src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'id_equipaje')));
