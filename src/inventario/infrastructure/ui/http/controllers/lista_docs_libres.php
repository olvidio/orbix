<?php

/**
 * Documentos del tipo `id_tipo_doc` que hay en el almacén, para añadirlos al equipaje `id_equipaje`.
 * La lógica está en {@see \src\inventario\application\DocumentosLibresData}.
 */

use src\inventario\application\DocumentosLibresData;
use src\shared\domain\helpers\FuncTablasSupport;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var DocumentosLibresData $useCase */
$useCase = DependencyResolver::get(DocumentosLibresData::class);
ContestarJson::enviar('', $useCase->execute(
    FuncTablasSupport::inputInt($_POST, 'id_equipaje'),
    FuncTablasSupport::inputInt($_POST, 'id_tipo_doc'),
));
