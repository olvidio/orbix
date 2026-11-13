<?php

/**
 * Documentos marcados como en búsqueda.
 * La lógica está en {@see \src\inventario\application\DocumentosMarcadosData::enBusqueda()}.
 */

use src\inventario\application\DocumentosMarcadosData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var DocumentosMarcadosData $useCase */
$useCase = DependencyResolver::get(DocumentosMarcadosData::class);
ContestarJson::enviar('', $useCase->enBusqueda());
