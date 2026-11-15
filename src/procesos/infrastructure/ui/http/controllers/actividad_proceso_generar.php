<?php

use src\procesos\application\ActividadProcesoGenerar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use src\shared\security\HashB;
use src\shared\security\HashBInvalidException;

/** @var ActividadProcesoGenerar $useCase */
$useCase = DependencyResolver::get(ActividadProcesoGenerar::class);

// Solo con la cápsula que firma la pantalla del proceso; la actividad sale de ella.
try {
    $ctx = HashB::open(
        \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'ctx_generar'),
        'actividad_proceso_generar'
    );
} catch (HashBInvalidException $e) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}
ContestarJson::enviar($useCase->execute(['id_activ' => \src\shared\domain\helpers\FuncTablasSupport::inputInt($ctx, 'id_activ')]));
