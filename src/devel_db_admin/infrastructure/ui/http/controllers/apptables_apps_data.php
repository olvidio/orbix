<?php

declare(strict_types=1);

/**
 * JSON con el mapa `id_app` → nombre para {@see frontend\devel_db_admin\controller\apptables}.
 */

use src\shared\web\ContestarJson;
use src\devel_db_admin\application\ApptablesAppsData;
use src\shared\infrastructure\DependencyResolver;
use src\devel_db_admin\infrastructure\ui\http\PermisoHerramientasBdGuard;

// Solo quien ve las herramientas de base de datos en el menú (PermisoHerramientasBd).
if (!PermisoHerramientasBdGuard::permiteEsquemas()) {
    ContestarJson::enviar(_("Operación no autorizada"), 'none');
    return;
}


/** @var ApptablesAppsData $data */
$data = DependencyResolver::get(ApptablesAppsData::class);
$payload = $data->build();

ContestarJson::enviar('', $payload);
