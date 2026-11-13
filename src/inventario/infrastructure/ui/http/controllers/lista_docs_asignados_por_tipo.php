<?php

/**
 * Centros (y lugares) que tienen el tipo de documento `id_tipo_doc`.
 * La lógica está en {@see \src\inventario\application\DocumentosPorTipoData::asignados()}.
 */

use src\inventario\application\DocumentosPorTipoData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var DocumentosPorTipoData $useCase */
$useCase = DependencyResolver::get(DocumentosPorTipoData::class);
ContestarJson::enviar('', $useCase->asignados(\src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'id_tipo_doc')));
