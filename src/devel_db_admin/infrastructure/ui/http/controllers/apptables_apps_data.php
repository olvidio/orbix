<?php

declare(strict_types=1);

/**
 * JSON con el mapa `id_app` → nombre para {@see frontend\devel_db_admin\controller\apptables}.
 */

use frontend\shared\web\ContestarJson;
use src\configuracion\domain\contracts\AppRepositoryInterface;
use src\devel_db_admin\application\ApptablesAppsData;

require_once 'frontend/shared/global_header_front.inc';

$appRepository = $GLOBALS['container']->get(AppRepositoryInterface::class);
$payload = ApptablesAppsData::build($appRepository);

ContestarJson::enviar('', $payload);
