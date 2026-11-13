<?php

/**
 * Documentos de la maleta (egm) `id_item_egm`.
 * La lógica está en {@see \src\inventario\application\DocumentosDeEgmData}.
 */

use src\inventario\application\DocumentosDeEgmData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var DocumentosDeEgmData $useCase */
$useCase = DependencyResolver::get(DocumentosDeEgmData::class);
ContestarJson::enviar('', $useCase->execute(\src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'id_item_egm')));
