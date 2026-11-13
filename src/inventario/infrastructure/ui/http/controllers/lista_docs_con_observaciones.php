<?php

/**
 * Documentos con observaciones (de la dl o del centro).
 * La lógica está en {@see \src\inventario\application\DocumentosMarcadosData::conObservaciones()}.
 */

use src\inventario\application\DocumentosMarcadosData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var DocumentosMarcadosData $useCase */
$useCase = DependencyResolver::get(DocumentosMarcadosData::class);
ContestarJson::enviar('', $useCase->conObservaciones());
