<?php

declare(strict_types=1);

/**
 * Modificación en lote de los documentos seleccionados (solo los campos marcados).
 * La lógica está en {@see \src\inventario\application\DocumentosModificar}.
 */

use src\inventario\application\DocumentosModificar;
use src\shared\domain\helpers\FuncTablasSupport;
use src\shared\infrastructure\DependencyResolver;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;

try {
    HashB::open(FuncTablasSupport::inputString($_POST, 'ctx_guardar'), 'documentos_guardar');
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

/** @var DocumentosModificar $useCase */
$useCase = DependencyResolver::get(DocumentosModificar::class);
ContestarJson::enviar($useCase->execute($_POST), 'ok');
