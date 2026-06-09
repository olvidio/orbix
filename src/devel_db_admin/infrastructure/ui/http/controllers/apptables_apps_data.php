<?php

declare(strict_types=1);

/**
 * JSON con el mapa `id_app` → nombre para {@see frontend\devel_db_admin\controller\apptables}.
 */

use src\shared\web\ContestarJson;
use src\devel_db_admin\application\ApptablesAppsData;
use src\shared\infrastructure\DependencyResolver;


/** @var ApptablesAppsData $data */
$data = DependencyResolver::get(ApptablesAppsData::class);
$payload = $data->build();

ContestarJson::enviar('', $payload);
