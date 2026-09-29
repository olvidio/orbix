<?php

use src\dbextern\application\TrasladarPersonaUseCase;
use src\shared\infrastructure\DependencyResolver;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;
use src\shared\web\ContestarJson;

try {
    $ctx = HashB::open(
        \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'ctx_trasladar'),
        'sincro_trasladar'
    );
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}

$dl = \src\shared\domain\helpers\FuncTablasSupport::inputString($ctx, 'dl');
$tipo_persona = \src\shared\domain\helpers\FuncTablasSupport::inputString($ctx, 'tipo_persona');
$id_nom_orbix = \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'id_nom_orbix');

$jsondata = DependencyResolver::get(TrasladarPersonaUseCase::class)->trasladar($id_nom_orbix, $tipo_persona, $dl);

$error_txt = !empty($jsondata['success']) ? '' : (string)($jsondata['mensaje'] ?? _("Error al trasladar"));
ContestarJson::enviar($error_txt, $jsondata);
