<?php

/**
 * Endpoint JSON: marca como resueltas, descartadas o de nuevo pendientes las consultas a la
 * ayuda de una fila de la pantalla de revisión.
 *
 * Entrada: `ctx_estado`, la cápsula de la fila que firmó `/src/shared/ayuda_consultas_lista_data`
 * (lleva las consultas y el estado nuevo). Solo administradores.
 */

use src\shared\application\AyudaConsultasCambiarEstado;
use src\shared\application\AyudaConsultasListaData;
use src\shared\domain\helpers\FilterPostGet;
use src\shared\infrastructure\DependencyResolver;
use src\shared\infrastructure\ui\http\PermisoRevisarAyudaGuard;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;

try {
    $ctx = HashB::open((string) FilterPostGet::post('ctx_estado'), AyudaConsultasListaData::ACCION_ESTADO);
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}
if (!PermisoRevisarAyudaGuard::permite()) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

$uuids = [];
foreach (is_array($ctx['uuids'] ?? null) ? $ctx['uuids'] : [] as $uuid) {
    if (is_string($uuid)) {
        $uuids[] = $uuid;
    }
}

$error_txt = DependencyResolver::get(AyudaConsultasCambiarEstado::class)->execute(
    $uuids,
    is_numeric($ctx['estado'] ?? null) ? (int) $ctx['estado'] : 0
);

ContestarJson::enviar($error_txt, 'ok');
