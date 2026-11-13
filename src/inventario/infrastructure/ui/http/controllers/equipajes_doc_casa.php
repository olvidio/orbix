<?php

/**
 * Documentos de la casa donde va el equipaje `id_equipaje`.
 * La lógica está en {@see \src\inventario\application\EquipajeDocCasaData}.
 */

use src\inventario\application\EquipajeDocCasaData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var EquipajeDocCasaData $useCase */
$useCase = DependencyResolver::get(EquipajeDocCasaData::class);
ContestarJson::enviar('', $useCase->execute(\src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'id_equipaje')));
