<?php
/**
 * Endpoint backend: encola mails de comunicacion de actividades a sacd.
 */

use src\actividadessacd\application\ComunicacionActividadesSacdEnviar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;

try {
    $ctx = HashB::open(
        \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'ctx_enviar'),
        'comunicacion_activ_sacd_enviar'
    );
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

$input = $_POST;
$input['que'] = \src\shared\domain\helpers\FuncTablasSupport::inputString($ctx, 'que');
$input['id_nom'] = \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'id_nom');
$input['propuesta'] = \src\shared\domain\helpers\FuncTablasSupport::inputString($ctx, 'propuesta');

/** @var ComunicacionActividadesSacdEnviar $useCase */
$useCase = DependencyResolver::get(ComunicacionActividadesSacdEnviar::class);
ContestarJson::enviar($useCase->execute($input), 'ok');
