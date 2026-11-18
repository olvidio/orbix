<?php

declare(strict_types=1);

use src\devel_db_admin\application\MigracionesListaData;
use src\devel_db_admin\domain\contracts\MigracionAplicadaRepositoryInterface;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use src\devel_db_admin\infrastructure\ui\http\PermisoHerramientasBdGuard;

// Solo quien ve las herramientas de base de datos en el menú (PermisoHerramientasBd).
if (!PermisoHerramientasBdGuard::permiteMigraciones()) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}


/** @var MigracionAplicadaRepositoryInterface $repository */
$repository = DependencyResolver::get(MigracionAplicadaRepositoryInterface::class);
$payload = (new MigracionesListaData($repository))->build();

ContestarJson::enviar('', $payload);
