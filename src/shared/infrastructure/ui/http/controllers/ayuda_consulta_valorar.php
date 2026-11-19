<?php

/**
 * Endpoint JSON: quien preguntó a la ayuda dice si la respuesta le sirvió.
 *
 * Entrada: `ctx_valorar` (cápsula que firmó `/src/shared/ayuda_preguntar` con la consulta),
 * `util` (`1` sirvió, `0` no sirvió) y `comentario` (opcional: qué buscaba).
 */

use src\shared\application\AyudaConsultaValorar;
use src\shared\domain\helpers\FilterPostGet;
use src\shared\domain\helpers\FuncTablasSupport;
use src\shared\infrastructure\DependencyResolver;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;

try {
    $ctx = HashB::open((string) FilterPostGet::post('ctx_valorar'), 'ayuda_consulta_valorar');
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

$util = (string) FilterPostGet::post('util');
if ($util !== '0' && $util !== '1') {
    ContestarJson::enviar(sprintf(_("Faltan parámetros: %s"), 'util'), 'none');
    return;
}

$error_txt = DependencyResolver::get(AyudaConsultaValorar::class)->execute(
    FuncTablasSupport::inputString($ctx, 'uuid_item'),
    $util === '1',
    (string) FilterPostGet::post('comentario')
);

ContestarJson::enviar($error_txt, 'ok');
