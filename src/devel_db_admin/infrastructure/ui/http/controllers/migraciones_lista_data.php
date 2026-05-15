<?php

declare(strict_types=1);

use src\devel_db_admin\application\MigracionesListaData;
use src\devel_db_admin\domain\contracts\MigracionAplicadaRepositoryInterface;
use src\shared\web\ContestarJson;

require_once 'frontend/shared/global_header_front.inc';

$repository = $GLOBALS['container']->get(MigracionAplicadaRepositoryInterface::class);
$payload = (new MigracionesListaData($repository))->build();

ContestarJson::enviar('', $payload);
