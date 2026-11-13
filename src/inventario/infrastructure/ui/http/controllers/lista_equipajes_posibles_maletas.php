<?php

/**
 * Maletas del almacén que se pueden añadir al equipaje `id_equipaje`.
 * La lógica está en {@see \src\inventario\application\MaletasPosiblesData::execute()}.
 */

use src\inventario\application\MaletasPosiblesData;
use src\shared\domain\helpers\FuncTablasSupport;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var MaletasPosiblesData $useCase */
$useCase = DependencyResolver::get(MaletasPosiblesData::class);
ContestarJson::enviar('', $useCase->execute(FuncTablasSupport::inputInt($_POST, 'id_equipaje')));
