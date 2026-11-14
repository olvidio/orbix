<?php

use src\encargossacd\application\EncargoComprobacionesCtr;
use src\encargossacd\application\EncargoComprobacionesCtrData;
use src\shared\domain\helpers\FuncTablasSupport;
use src\shared\infrastructure\DependencyResolver;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;

/**
 * Borra encargos de centros inactivos y asignaciones de sacd de encargos que no existen.
 * Solo con la cápsula `ctx_comprobar` que firma la pantalla de listas de encargos.
 */
try {
    HashB::open(FuncTablasSupport::inputString($_POST, 'ctx_comprobar'), EncargoComprobacionesCtrData::ACCION);
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

/** @var EncargoComprobacionesCtr $useCase */
$useCase = DependencyResolver::get(EncargoComprobacionesCtr::class);

ContestarJson::enviar('', $useCase->ejecutar());
