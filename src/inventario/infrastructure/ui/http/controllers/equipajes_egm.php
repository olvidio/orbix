<?php

/**
 * Maletas del equipaje `id_equipaje` con sus documentos.
 * La lógica está en {@see \src\inventario\application\EquipajeEgmData::execute()}.
 */

use src\inventario\application\EquipajeEgmData;
use src\shared\domain\helpers\FuncTablasSupport;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var EquipajeEgmData $useCase */
$useCase = DependencyResolver::get(EquipajeEgmData::class);
ContestarJson::enviar('', $useCase->execute(FuncTablasSupport::inputInt($_POST, 'id_equipaje')));
