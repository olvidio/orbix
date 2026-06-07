<?php

declare(strict_types=1);

use src\devel_db_admin\application\MigracionesListaData;
use src\devel_db_admin\domain\contracts\MigracionAplicadaRepositoryInterface;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

require_once 'frontend/shared/global_header_front.inc';

/** @var MigracionAplicadaRepositoryInterface $repository */
$repository = DependencyResolver::get(MigracionAplicadaRepositoryInterface::class);
$payload = (new MigracionesListaData($repository))->build();

ContestarJson::enviar('', $payload);
